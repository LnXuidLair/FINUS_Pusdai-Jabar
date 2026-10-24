<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\ZiswafPenerimaan;
use App\Services\TransactionProofStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class PemasukanController extends Controller
{
    /**
     * Semua golongan / jenis ZISWAF dan penerimaan yang tersedia.
     */
    public static function golonganLabels(): array
    {
        return [
            'zakat_maal'        => 'Zakat Maal',
            'zakat_penghasilan' => 'Zakat Penghasilan',
            'infaq'             => 'Infak',
            'wakaf'             => 'Wakaf',
            'parkir'            => 'Parkir',
        ];
    }

    /**
     * Warna badge per golongan (bg, text, border).
     */
    public static function golonganColors(): array
    {
        return [
            'zakat_maal'        => ['bg' => '#ecfdf5', 'text' => '#065f46', 'border' => '#a7f3d0', 'dot' => '#059669'],
            'zakat_penghasilan' => ['bg' => '#f0fdfa', 'text' => '#134e4a', 'border' => '#99f6e4', 'dot' => '#0d9488'],
            'infaq'             => ['bg' => '#eff6ff', 'text' => '#1e40af', 'border' => '#bfdbfe', 'dot' => '#2563eb'],
            'wakaf'             => ['bg' => '#fff7ed', 'text' => '#9a3412', 'border' => '#fed7aa', 'dot' => '#ea580c'],
            'parkir'            => ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#a7f3d0', 'dot' => '#10b981'],
        ];
    }

    /**
     * Label metode pembayaran.
     */
    public static function metodeLabels(): array
    {
        return [
            'manual_transfer' => 'Transfer Bank',
            'qris_manual'     => 'QRIS',
            'qris_tunai'      => 'QRIS + Tunai / Cash',
            'tunai'           => 'Tunai',
            'qris'            => 'QRIS Gateway',
            'other_qris'      => 'QRIS',
            'virtual_account' => 'Virtual Account',
            'e_money'         => 'E-Wallet',
            'bca_va'          => 'BCA Virtual Account',
            'bni_va'          => 'BNI Virtual Account',
            'bri_va'          => 'BRI Virtual Account',
            'permata_va'      => 'Permata Virtual Account',
            'gopay'           => 'GoPay',
            'shopeepay'       => 'ShopeePay',
        ];
    }

    /**
     * Label status verifikasi.
     */
    public static function statusLabels(): array
    {
        return [
            'pending'  => 'Menunggu',
            'diterima' => 'Diterima',
            'ditolak'  => 'Ditolak',
        ];
    }

    public static function paymentStatusLabels(): array
    {
        return [
            'manual_pending' => 'Menunggu verifikasi manual', 'manual_paid' => 'Manual dibayar',
            'pending' => 'Menunggu pembayaran', 'settlement' => 'Berhasil', 'capture' => 'Berhasil',
            'manual_rejected' => 'Manual ditolak', 'deny' => 'Ditolak gateway', 'cancel' => 'Dibatalkan',
            'expire' => 'Kedaluwarsa', 'failure' => 'Gagal',
        ];
    }

    /**
     * Tampilkan halaman daftar pemasukan.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'tab' => ['nullable', Rule::in(['all', 'pending', 'history'])],
            'payment_status' => ['nullable', 'string', 'max:50'],
            'q'                 => ['nullable', 'string', 'max:100'],
            'golongan'          => ['nullable', 'string', 'max:50'],
            'status_verifikasi' => ['nullable', 'string', 'max:50'],
            'metode'            => ['nullable', 'string', 'max:50'],
            'tanggal_dari'      => ['nullable', 'date'],
            'tanggal_sampai'    => ['nullable', 'date'],
        ]);

        $activeTab = $filters['tab'] ?? 'all';
        $query = ZiswafPenerimaan::with(['muzakki', 'pegawai', 'jurnal'])
            ->latest('tanggal')->latest('id');
        if ($activeTab === 'pending') {
            $query->awaitingManualVerification();
        } elseif ($activeTab === 'history') {
            $query->where(function (Builder $q) {
                $q->whereIn('status_verifikasi', ['diterima', 'ditolak', 'dibatalkan'])
                    ->orWhereIn('payment_status', ['settlement', 'capture', 'manual_paid', 'manual_rejected', 'deny', 'cancel', 'expire', 'failure']);
            });
        }
        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function (Builder $q) use ($search) {
                $q->where('keterangan', 'like', '%' . $search . '%')
                    ->orWhere('order_id', 'like', '%' . $search . '%')
                    ->orWhereHas('muzakki', function (Builder $uq) use ($search) {
                        $uq->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if (!empty($filters['golongan'])) {
            if ($filters['golongan'] === 'parkir') {
                $query->whereIn('jenis_ziswaf', ['parkir', 'hasil_parkir']);
            } else {
                $query->where('jenis_ziswaf', $filters['golongan']);
            }
        }

        if (!empty($filters['status_verifikasi'])) {
            $query->where('status_verifikasi', $filters['status_verifikasi']);
        }

        if (!empty($filters['metode'])) {
            $query->where('metode_pembayaran', $filters['metode']);
        }

        if (!empty($filters['tanggal_dari'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_dari']);
        }

        if (!empty($filters['tanggal_sampai'])) {
            $query->whereDate('tanggal', '<=', $filters['tanggal_sampai']);
        }

        /* ── Summary cards ── */
        $summaryTotal = [
            'total'     => (int) ZiswafPenerimaan::where('status_verifikasi', 'diterima')->sum('nominal'),
            'pending' => ZiswafPenerimaan::query()->awaitingManualVerification()->count(),
            'bulan_ini' => (int) ZiswafPenerimaan::where('status_verifikasi', 'diterima')
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('nominal'),
        ];

        /* ── Total per golongan (hanya yang diterima) ── */
        $summaryPerGolongan = [];
        foreach (self::golonganLabels() as $key => $label) {
            $summaryPerGolongan[$key] = (int) ZiswafPenerimaan::where('status_verifikasi', 'diterima')
                ->where(function ($q) use ($key) {
                    if ($key === 'parkir') {
                        $q->whereIn('jenis_ziswaf', ['parkir', 'hasil_parkir']);
                    } else {
                        $q->where('jenis_ziswaf', $key);
                    }
                })
                ->sum('nominal');
        }

        $transaksi = $query->paginate(15)->withQueryString();

        return view('admin.pemasukan.index', [
            'activeTab' => $activeTab,
            'paymentStatusLabels' => self::paymentStatusLabels(),
            'receiptLabels' => self::golonganLabels() + ['zakat_fitrah' => 'Zakat Fitrah', 'shadaqah' => 'Sedekah', 'sedekah' => 'Sedekah', 'fidyah' => 'Fidyah'],
            'fundTypeLabels' => config('transaction_categories.fund_types') + ['amil' => 'Dana Amil', 'nazhir' => 'Dana Nazhir'],
            'transaksi'          => $transaksi,
            'filters'            => $filters,
            'summaryTotal'       => $summaryTotal,
            'summaryPerGolongan' => $summaryPerGolongan,
            'golonganLabels'     => self::golonganLabels(),
            'golonganColors'     => self::golonganColors(),
            'metodeLabels'       => self::metodeLabels(),
            'statusLabels'       => self::statusLabels(),
            'pegawaiPenanggungJawab' => Pegawai::query()
                ->petugasParkir()
                ->orderBy('nama_pegawai')
                ->get(['id', 'nama_pegawai', 'jabatan']),
        ]);
    }

    /**
     * Form pemasukan manual untuk admin dan pegawai bagian keuangan.
     * Pembayaran otomatis Midtrans tetap melalui alur transaksi jamaah.
     */
    public function create()
    {
        return view('admin.pemasukan.create', [
            'golonganLabels' => self::golonganLabels(),
            'pegawaiPenanggungJawab' => Pegawai::query()
                ->petugasParkir()
                ->orderBy('nama_pegawai')
                ->get(['id', 'nama_pegawai', 'jabatan']),
        ]);
    }

    /**
     * Simpan pemasukan manual yang diinput oleh admin.
     */
    public function store(Request $request)
    {
        $isParkir = $request->input('jenis_ziswaf') === 'parkir';
        $isMixed = $isParkir && $request->input('metode_pembayaran') === 'qris_tunai';
        if ($isMixed) {
            $parts = $request->validate([
                'nominal_qris' => ['required', 'integer', 'min:1', 'max:999999999999'],
                'nominal_tunai' => ['required', 'integer', 'min:1', 'max:999999999999'],
            ]);
            $request->merge(['nominal' => (int) $parts['nominal_qris'] + (int) $parts['nominal_tunai']]);
        }

        $validated = $request->validate([
            'jenis_ziswaf'      => ['required', 'string', 'in:' . implode(',', array_keys(self::golonganLabels()))],
            'wakaf_type'        => [
                Rule::requiredIf(fn (): bool => $request->input('jenis_ziswaf') === 'wakaf'),
                'nullable',
                Rule::in(['permanen', 'hasil_pengelolaan']),
            ],
            'wakaf_return_date' => [
                Rule::requiredIf(fn (): bool => $request->input('wakaf_type') === 'temporer'),
                'nullable',
                'date',
                'after:tanggal',
            ],
            'persentase_nazhir' => [
                Rule::requiredIf(fn (): bool => $request->input('wakaf_type') === 'hasil_pengelolaan'),
                'nullable',
                'numeric',
                'min:0',
                'max:10',
            ],
            'nominal'           => ['required', 'integer', 'min:1000'],
            'tanggal'           => ['required', 'date', 'before_or_equal:today'],
            'metode_pembayaran' => ['required', 'string', Rule::in($isParkir
                ? ['qris_manual', 'tunai', 'qris_tunai']
                : ['manual_transfer', 'qris_manual', 'tunai'])],
            'nama_donatur'      => ['nullable', 'string', 'max:255'],
            'keterangan'        => ['nullable', 'string', 'max:1000'],
            'id_pegawai'        => [
                'exclude_unless:jenis_ziswaf,parkir',
                Rule::requiredIf($isParkir),
                'nullable',
                Rule::in(Pegawai::query()->petugasParkir()->pluck('id')->all()),
            ],
            'shift'             => [Rule::requiredIf($isParkir), 'nullable', Rule::in(['pagi', 'siang', 'malam'])],
            'nomor_rekap'       => ['nullable', 'string', 'max:80'],
            'bukti_pembayaran'  => [Rule::requiredIf($isParkir), 'nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:2048'],
        ], [
            'id_pegawai.required' => 'Pilih petugas parkir sebagai penanggung jawab shift.',
            'id_pegawai.in' => 'Penanggung jawab harus petugas parkir terverifikasi dari masjid ini.',
            'metode_pembayaran.in' => $isParkir
                ? 'Pilih QRIS, Tunai / Cash, atau QRIS + Tunai / Cash untuk parkir.'
                : 'Metode pembayaran tidak valid.',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')
                ->store('bukti_pemasukan', app(TransactionProofStorage::class)->disk());
        }

        try {
            DB::transaction(function () use ($request, $validated, $isParkir, $buktiPath): void {
                $shiftTimes = [
                    'pagi' => ['06:00', '14:00'],
                    'siang' => ['14:00', '22:00'],
                    'malam' => ['22:00', '06:00'],
                ];

                if ($isParkir) {
                    [$waktuMulai, $waktuSelesai] = $shiftTimes[$validated['shift']];
                    $keterangan = 'Setoran parkir shift '.ucfirst($validated['shift']);
                    if (! empty($validated['keterangan'])) {
                        $keterangan .= ' - '.$validated['keterangan'];
                    }
                    $rincianPerhitungan = array_filter([
                        'shift' => $validated['shift'],
                        'waktu_mulai' => $waktuMulai,
                        'waktu_selesai' => $waktuSelesai,
                        'nomor_rekap' => $validated['nomor_rekap'] ?? null,
                        'pembagian_pembayaran' => $validated['metode_pembayaran'] === 'qris_tunai'
                            ? ['qris' => (int) $request->input('nominal_qris'), 'tunai' => (int) $request->input('nominal_tunai')]
                            : null,
                    ], static fn ($value): bool => $value !== null && $value !== '');
                } elseif (! empty($validated['nama_donatur'])) {
                    $keterangan = '[Donatur: '.$validated['nama_donatur'].'] '.($validated['keterangan'] ?? '');
                    $rincianPerhitungan = null;
                } else {
                    $keterangan = $validated['keterangan'] ?? null;
                    $rincianPerhitungan = null;
                }

                $pemasukan = ZiswafPenerimaan::create([
                    'id_pegawai' => $isParkir ? $validated['id_pegawai'] : null,
                    'jenis_ziswaf' => $validated['jenis_ziswaf'],
                    'restriction_type' => null,
                    'wakaf_type' => $validated['jenis_ziswaf'] === 'wakaf'
                        ? $validated['wakaf_type']
                        : null,
                    'wakaf_return_date' => ($validated['wakaf_type'] ?? null) === 'temporer'
                        ? $validated['wakaf_return_date']
                        : null,
                    'persentase_nazhir' => ($validated['wakaf_type'] ?? null) === 'hasil_pengelolaan'
                        ? (float) $validated['persentase_nazhir']
                        : 0,
                    'nominal' => (int) $validated['nominal'],
                    'tanggal' => $validated['tanggal'],
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'muzakki_id' => null,
                    'keterangan' => $keterangan,
                    'rincian_perhitungan' => $rincianPerhitungan,
                    'bukti_pembayaran' => $buktiPath,
                    'status_verifikasi' => 'diterima',
                    'verified_by' => $request->user()->id,
                    'verified_at' => now(),
                    'payment_status' => 'manual_paid',
                ]);

                app(\App\Services\Accounting\Psak109PostingService::class)->postPenerimaan($pemasukan);
            });
        } catch (Throwable $exception) {
            if ($buktiPath) {
                app(TransactionProofStorage::class)->delete($buktiPath);
            }

            throw $exception;
        }

        return redirect()
            ->route($this->indexRoute($request))
            ->with('success', $isParkir
                ? 'Pemasukan parkir berhasil ditambahkan dan dijurnal.'
                : 'Pemasukan berhasil ditambahkan dan dijurnal.');
    }

    /**
     * Verifikasi pemasukan dari jamaah (terima / tolak).
     */
    public function verifikasi(Request $request, ZiswafPenerimaan $pemasukan)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['terima', 'tolak'])],
            'catatan_verifikasi' => [Rule::requiredIf($request->input('action') === 'tolak'), 'nullable', 'string', 'max:1000'],
        ]);
        DB::transaction(function () use ($request, $pemasukan, $validated) {
            $receipt = ZiswafPenerimaan::lockForUpdate()->findOrFail($pemasukan->id);
            if (! $receipt->can_verify_manually) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'action' => 'Transaksi ini tidak menunggu verifikasi manual. Pembayaran gateway mengikuti konfirmasi penyedia pembayaran.',
                ]);
            }
            $accepted = $validated['action'] === 'terima';
            $actor = $request->user('admin') ?? $request->user('pegawai');
            $receipt->fill([
                'payment_status' => $accepted ? 'manual_paid' : 'manual_rejected',
                'status_verifikasi' => $accepted ? 'diterima' : 'ditolak',
                'catatan_verifikasi' => $validated['catatan_verifikasi'] ?? 'Diverifikasi oleh petugas keuangan.',
                'verified_by' => $actor->id,
                'verified_at' => now(),
            ]);
            if ($accepted) $receipt->paid_at = $receipt->paid_at ?? now();
            $receipt->save();
            if ($accepted) app(\App\Services\Accounting\Psak109PostingService::class)->postPenerimaan($receipt);
        });
        return back()->with('success', $validated['action'] === 'terima'
            ? 'Pemasukan berhasil diterima dan dijurnal.' : 'Pemasukan berhasil ditolak.');
    }

    /**
     * Hapus pemasukan manual (bukan dari jamaah).
     */
    public function destroy(ZiswafPenerimaan $pemasukan)
    {
        if ($pemasukan->muzakki_id !== null) {
            return back()->withErrors([
                'hapus' => 'Pemasukan dari jamaah tidak dapat dihapus melalui menu ini.',
            ]);
        }

        $buktiPath = $pemasukan->bukti_pembayaran;

        if ($pemasukan->jurnal_id) {
            app(\App\Services\Accounting\Psak109PostingService::class)->reverseJurnal($pemasukan->jurnal_id, 'Pemasukan dihapus');
        }

        $pemasukan->delete();
        if ($buktiPath) {
            app(TransactionProofStorage::class)->delete($buktiPath);
        }

        return back()->with('success', 'Pemasukan berhasil dihapus.');
    }

    /**
     * Mendapatkan nama route index berdasarkan role/prefix user yang sedang login.
     */
    private function indexRoute(Request $request): string
    {
        return $request->routeIs('pegawai.keuangan.*')
            ? 'pegawai.keuangan.pemasukan.index'
            : 'admin.pemasukan.index';
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\KebijakanMustahik;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Services\Accounting\Psak109PostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PengeluaranController extends Controller
{
    public function index()
    {
        /*
         * Pengeluaran manual/operasional diambil dari tabel pengeluaran.
         * Record gaji lama yang mungkin pernah disimpan di tabel ini
         * dikecualikan agar tidak tampil dua kali dengan data penggajian.
         */
        $pengeluaranManual = Pengeluaran::query()
            ->with('zakatPenyaluran')
            ->whereNull('id_penggajian')
            ->whereNull('referensi_penggajian_id')
            ->where(function ($query): void {
                $query->whereNull('jenis')
                    ->orWhere('jenis', '!=', 'gaji');
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Pengeluaran $item) {
                $item->is_gaji = false;

                return $item;
            });

        /*
         * Gaji hanya menjadi pengeluaran setelah statusnya Sudah Dibayar.
         * Data ini tidak diduplikasi ke tabel pengeluaran; ditampilkan sebagai
         * representasi dari tabel penggajian agar sumber datanya tetap satu.
         */
        $pengeluaranGaji = Penggajian::query()
            ->where('status_penggajian', 'sudah_dibayar')
            ->whereNotNull('tanggal')
            ->with('pegawai')
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Penggajian $gaji): object {
                return (object) [
                    'id' => 'gaji_'.$gaji->id,
                    'kategori' => 'Beban Gaji dan Honorarium',
                    'deskripsi' => 'Gaji '
                        .($gaji->pegawai?->nama_pegawai ?? 'Pegawai')
                        .' periode '
                        .$gaji->periode,
                    'jumlah' => (int) $gaji->total_gaji,
                    'tanggal' => $gaji->tanggal,
                    'bukti_pembayaran' => $gaji->bukti_pembayaran,
                    'status_verifikasi' => 'diterima',
                    'is_gaji' => true,
                    'created_at' => $gaji->created_at,
                    'updated_at' => $gaji->updated_at,
                ];
            });

        $allPengeluaran = $pengeluaranManual
            ->concat($pengeluaranGaji)
            ->sortByDesc(function ($item): string {
                $tanggal = (string) ($item->tanggal ?? '');
                $createdAt = (string) ($item->created_at ?? '');

                return $tanggal.' '.$createdAt;
            })
            ->values();

        return view('pengeluaran.index', compact('allPengeluaran'));
    }

    public function create()
    {
        $coaBeban = Coa::pengeluaranManual()
            ->orderBy('kode_akun')
            ->get();

        $coaBebanGrouped = $coaBeban->groupBy('kelompok_pengeluaran');
        $kelompokPengeluaran = config('coa.expense_groups', []);
        $asnafLabels = collect(KebijakanMustahik::ASNAF)->except('amil')->all();

        return view('pengeluaran.create', compact('coaBebanGrouped', 'kelompokPengeluaran', 'asnafLabels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'coa_debit_id' => [
                'required',
                'integer',
                Rule::exists('coa', 'id')->where(function ($query): void {
                    $query->whereIn('kode_akun', $this->manualExpenseCodes());
                }),
            ],
            'restriction_type' => ['nullable', Rule::in(['mutlaqah', 'muqayyadah'])],
            'deskripsi' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'bukti_pembayaran' => [
                'nullable',
                'file',
                'mimes:jpeg,jpg,png,pdf',
                'max:2048',
            ],
            'zakat_details' => ['nullable', 'array', 'max:7'],
            'zakat_details.*.asnaf' => ['nullable', 'string'],
            'zakat_details.*.jumlah_penerima' => ['nullable', 'integer'],
            'zakat_details.*.nominal' => ['nullable', 'integer'],
        ]);

        $coaDebit = Coa::pengeluaranManual()->findOrFail($validated['coa_debit_id']);
        $zakatDetails = [];

        if ($coaDebit->kode_akun === '5311' && empty($validated['restriction_type'])) {
            throw ValidationException::withMessages([
                'restriction_type' => 'Pilih sifat infak/sedekah yang akan disalurkan.',
            ]);
        }

        if ($coaDebit->kode_akun === '5210') {
            $zakatValidated = $request->validate([
                'zakat_details' => ['required', 'array', 'min:1', 'max:7'],
                'zakat_details.*.asnaf' => [
                    'required',
                    'string',
                    'distinct',
                    Rule::in(array_keys(collect(KebijakanMustahik::ASNAF)->except('amil')->all())),
                ],
                'zakat_details.*.jumlah_penerima' => ['required', 'integer', 'min:1'],
                'zakat_details.*.nominal' => ['required', 'integer', 'min:1'],
            ]);
            $zakatDetails = $zakatValidated['zakat_details'];

            if ((int) collect($zakatDetails)->sum('nominal') !== (int) $validated['jumlah']) {
                throw ValidationException::withMessages([
                    'zakat_details' => 'Total nominal per golongan harus sama dengan jumlah pengeluaran.',
                ]);
            }
        }

        $path = null;

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request
                ->file('bukti_pembayaran')
                ->store('bukti_pembayaran', 'public');
        }

        try {
            DB::transaction(function () use ($coaDebit, $path, $validated, $zakatDetails): void {
                $pengeluaran = new Pengeluaran;
                $pengeluaran->kategori = $coaDebit->nama_akun;
                $pengeluaran->restriction_type = $coaDebit->kode_akun === '5311'
                    ? $validated['restriction_type']
                    : null;
                $pengeluaran->deskripsi = $validated['deskripsi'];
                $pengeluaran->jumlah = (int) $validated['jumlah'];
                $pengeluaran->tanggal = $validated['tanggal'];
                $pengeluaran->bukti_pembayaran = $path;
                $pengeluaran->coa_debit_id = $coaDebit->id;

                $coaKas = Coa::where('kode_akun', '1101')->first();
                if ($coaKas) {
                    $pengeluaran->coa_kredit_id = $coaKas->id;
                }

                $pengeluaran->jenis = 'operasional';
                $pengeluaran->nominal = (int) $validated['jumlah'];
                $pengeluaran->keterangan = $validated['deskripsi'];
                $pengeluaran->status_verifikasi = 'diterima';
                $pengeluaran->save();

                foreach ($zakatDetails as $detail) {
                    $label = KebijakanMustahik::ASNAF[$detail['asnaf']];
                    $pengeluaran->zakatPenyaluran()->create([
                        'tanggal' => $validated['tanggal'],
                        'kategori_program' => 'Penyaluran Zakat - '.$label,
                        'penerima_manfaat' => $detail['jumlah_penerima'].' orang',
                        'jumlah_penerima' => $detail['jumlah_penerima'],
                        'nominal' => $detail['nominal'],
                        'jenis_ziswaf_asal' => 'zakat',
                        'bukti_penyaluran' => $path,
                        'keterangan' => $validated['deskripsi'],
                        'asnaf' => $detail['asnaf'],
                    ]);
                }

                app(Psak109PostingService::class)->postPengeluaran($pengeluaran);
            });
        } catch (\Throwable $exception) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        return redirect()
            ->route($this->indexRoute($request))
            ->with('success', 'Data pengeluaran berhasil ditambahkan dan dijurnal.');
    }

    public function destroy($id)
    {
        /*
         * Status gaji hanya boleh dikelola dari menu Penggajian.
         * Menghapus item virtual gaji dari halaman Pengeluaran tidak boleh
         * mengubah status pembayaran.
         */
        if (str_starts_with((string) $id, 'gaji_')) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran gaji tidak dapat dihapus dari menu Pengeluaran. Ubah status pembayaran melalui menu Penggajian.',
            ], 422);
        }

        $pengeluaran = Pengeluaran::findOrFail($id);
        $buktiPembayaran = $pengeluaran->bukti_pembayaran;

        if ($pengeluaran->jurnal_id) {
            app(Psak109PostingService::class)->reverseJurnal($pengeluaran->jurnal_id, 'Pengeluaran dihapus');
        }

        $pengeluaran->zakatPenyaluran()->delete();
        $pengeluaran->delete();

        if ($buktiPembayaran) {
            Storage::disk('public')->delete($buktiPembayaran);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    private function indexRoute(Request $request): string
    {
        return $request->routeIs('pegawai.keuangan.*')
            ? 'pegawai.keuangan.pengeluaran.index'
            : 'admin.pengeluaran.index';
    }

    private function manualExpenseCodes(): array
    {
        return collect(config('coa.manual_expense_accounts', []))
            ->flatMap(fn (array $accounts) => array_keys($accounts))
            ->values()
            ->all();
    }
}

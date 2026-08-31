<?php

namespace App\Http\Controllers;

use App\Models\ZiswafPenerimaan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PemasukanController extends Controller
{
    /**
     * Semua golongan / jenis ZISWAF yang tersedia.
     */
    public static function golonganLabels(): array
    {
        return [
            'zakat_maal'        => 'Zakat Maal',
            'zakat_penghasilan' => 'Zakat Penghasilan',
            'infaq'             => 'Infak',
            'shadaqah'          => 'Sedekah',
            'wakaf'             => 'Wakaf',
            'fidyah'            => 'Fidyah',
            'lainnya'           => 'Lainnya',
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
            'shadaqah'          => ['bg' => '#fdf4ff', 'text' => '#6b21a8', 'border' => '#e9d5ff', 'dot' => '#9333ea'],
            'wakaf'             => ['bg' => '#fff7ed', 'text' => '#9a3412', 'border' => '#fed7aa', 'dot' => '#ea580c'],
            'fidyah'            => ['bg' => '#fefce8', 'text' => '#854d0e', 'border' => '#fde68a', 'dot' => '#ca8a04'],
            'lainnya'           => ['bg' => '#f8fafc', 'text' => '#475569', 'border' => '#cbd5e1', 'dot' => '#64748b'],
        ];
    }

    /**
     * Label metode pembayaran.
     */
    public static function metodeLabels(): array
    {
        return [
            'manual_transfer' => 'Transfer Bank',
            'qris_manual'     => 'QRIS Manual',
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

    /**
     * Tampilkan halaman daftar pemasukan.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q'                 => ['nullable', 'string', 'max:100'],
            'golongan'          => ['nullable', 'string', 'max:50'],
            'status_verifikasi' => ['nullable', 'string', 'max:50'],
            'metode'            => ['nullable', 'string', 'max:50'],
            'tanggal_dari'      => ['nullable', 'date'],
            'tanggal_sampai'    => ['nullable', 'date'],
        ]);

        $query = ZiswafPenerimaan::with(['muzakki', 'pegawai'])
            ->latest('tanggal')
            ->latest('id');

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
            $query->where('jenis_ziswaf', $filters['golongan']);
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
            'pending'   => (int) ZiswafPenerimaan::where('status_verifikasi', 'pending')->count(),
            'bulan_ini' => (int) ZiswafPenerimaan::where('status_verifikasi', 'diterima')
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->sum('nominal'),
        ];

        /* ── Total per golongan (hanya yang diterima) ── */
        $summaryPerGolongan = [];
        foreach (self::golonganLabels() as $key => $label) {
            $summaryPerGolongan[$key] = (int) ZiswafPenerimaan::where('status_verifikasi', 'diterima')
                ->where('jenis_ziswaf', $key)
                ->sum('nominal');
        }

        $transaksi = $query->paginate(15)->withQueryString();

        return view('admin.pemasukan.index', [
            'transaksi'          => $transaksi,
            'filters'            => $filters,
            'summaryTotal'       => $summaryTotal,
            'summaryPerGolongan' => $summaryPerGolongan,
            'golonganLabels'     => self::golonganLabels(),
            'golonganColors'     => self::golonganColors(),
            'metodeLabels'       => self::metodeLabels(),
            'statusLabels'       => self::statusLabels(),
        ]);
    }

    /**
     * Simpan pemasukan manual yang diinput oleh admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_ziswaf'      => ['required', 'string', 'in:' . implode(',', array_keys(self::golonganLabels()))],
            'nominal'           => ['required', 'integer', 'min:1000'],
            'tanggal'           => ['required', 'date', 'before_or_equal:today'],
            'metode_pembayaran' => ['required', 'string', 'in:manual_transfer,qris_manual,tunai'],
            'nama_donatur'      => ['nullable', 'string', 'max:255'],
            'keterangan'        => ['nullable', 'string', 'max:1000'],
            'bukti_pembayaran'  => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:2048'],
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')
                ->store('bukti_pemasukan', 'public');
        }

        /* Sisipkan nama donatur ke keterangan jika diisi */
        $keterangan = null;
        if (!empty($validated['nama_donatur'])) {
            $keterangan = '[Donatur: ' . $validated['nama_donatur'] . '] ' . ($validated['keterangan'] ?? '');
        } else {
            $keterangan = $validated['keterangan'] ?? null;
        }

        ZiswafPenerimaan::create([
            'jenis_ziswaf'      => $validated['jenis_ziswaf'],
            'nominal'           => (int) $validated['nominal'],
            'tanggal'           => $validated['tanggal'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'muzakki_id'        => null,
            'keterangan'        => $keterangan,
            'bukti_pembayaran'  => $buktiPath,
            'status_verifikasi' => 'diterima',
            'verified_by'       => $request->user()->id,
            'verified_at'       => now(),
            'payment_status'    => 'manual_paid',
        ]);

        return redirect()
            ->route($this->indexRoute($request))
            ->with('success', 'Pemasukan berhasil ditambahkan.');
    }

    /**
     * Verifikasi pemasukan dari jamaah (terima / tolak).
     */
    public function verifikasi(Request $request, ZiswafPenerimaan $pemasukan)
    {
        $action = $request->input('action');
        $roleName = $request->user()->role === 'admin' ? 'admin' : 'petugas keuangan';

        if ($action === 'terima') {
            $pemasukan->update([
                'payment_status'     => $pemasukan->payment_status === 'manual_pending' ? 'manual_paid' : $pemasukan->payment_status,
                'status_verifikasi'  => 'diterima',
                'catatan_verifikasi' => $request->input('catatan_verifikasi', 'Diverifikasi oleh ' . $roleName . '.'),
                'verified_by'        => $request->user()->id,
                'verified_at'        => now(),
                'paid_at'            => $pemasukan->paid_at ?? now(),
            ]);

            return back()->with('success', 'Pemasukan berhasil diterima.');
        }

        if ($action === 'tolak') {
            $request->validate([
                'catatan_verifikasi' => ['required', 'string', 'max:1000'],
            ]);

            $pemasukan->update([
                'status_verifikasi'  => 'ditolak',
                'catatan_verifikasi' => $request->input('catatan_verifikasi'),
                'verified_by'        => $request->user()->id,
                'verified_at'        => now(),
            ]);

            return back()->with('success', 'Pemasukan berhasil ditolak.');
        }

        return back()->withErrors(['action' => 'Aksi tidak valid.']);
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

        if ($pemasukan->bukti_pembayaran) {
            Storage::disk('public')->delete($pemasukan->bukti_pembayaran);
        }

        $pemasukan->delete();

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

<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                    'id' => 'gaji_' . $gaji->id,
                    'kategori' => 'Biaya Honorarium',
                    'deskripsi' => 'Gaji '
                        . ($gaji->pegawai?->nama_pegawai ?? 'Pegawai')
                        . ' periode '
                        . $gaji->periode,
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

                return $tanggal . ' ' . $createdAt;
            })
            ->values();

        return view('pengeluaran.index', compact('allPengeluaran'));
    }

    public function create()
    {
        // Ambil akun beban COA, kecualikan 5104 (Biaya Honorarium) karena otomatis dari Penggajian
        $coaBeban = Coa::where('header_akun', 5)
            ->where('kode_akun', '!=', '5104')
            ->where('nama_akun', '!=', 'Biaya Honorarium')
            ->orderBy('kode_akun')
            ->get();

        // Fallback jika data akun COA beban di database belum tersedia
        if ($coaBeban->isEmpty()) {
            $fallback = [
                ['kode_akun' => '5101', 'nama_akun' => 'Biaya Bidang Idaroh'],
                ['kode_akun' => '5102', 'nama_akun' => 'Biaya Bidang Imaroh'],
                ['kode_akun' => '5103', 'nama_akun' => 'Biaya Bidang Riayah'],
                ['kode_akun' => '5105', 'nama_akun' => 'Biaya Konsumsi'],
                ['kode_akun' => '5106', 'nama_akun' => 'Biaya Administrasi Bank'],
                ['kode_akun' => '5107', 'nama_akun' => 'Biaya Pemeliharaan'],
                ['kode_akun' => '5108', 'nama_akun' => 'Biaya Kebersihan'],
                ['kode_akun' => '5109', 'nama_akun' => 'Biaya Kegiatan'],
                ['kode_akun' => '5110', 'nama_akun' => 'Biaya Pengadaan'],
                ['kode_akun' => '5111', 'nama_akun' => 'Penyaluran ZISWAF'],
            ];
            $coaBeban = collect($fallback)->map(fn($item) => (object)$item);
        }

        return view('pengeluaran.create', compact('coaBeban'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'bukti_pembayaran' => [
                'nullable',
                'file',
                'mimes:jpeg,jpg,png,pdf',
                'max:2048',
            ],
        ]);

        $path = null;

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request
                ->file('bukti_pembayaran')
                ->store('bukti_pembayaran', 'public');
        }

        try {
            $pengeluaran = new Pengeluaran();
            $pengeluaran->kategori = $validated['kategori'];
            $pengeluaran->deskripsi = $validated['deskripsi'];
            $pengeluaran->jumlah = (int) $validated['jumlah'];
            $pengeluaran->tanggal = $validated['tanggal'];
            $pengeluaran->bukti_pembayaran = $path;

            // Sambungkan otomatis ke akun COA debit jika ditemukan
            $coaDebit = Coa::where('nama_akun', $validated['kategori'])
                ->orWhere('kode_akun', explode(' - ', $validated['kategori'])[0])
                ->first();
            if ($coaDebit) {
                $pengeluaran->coa_debit_id = $coaDebit->id;
            }

            // Default coa_kredit_id ke Kas (1101) jika ada
            $coaKas = Coa::where('kode_akun', '1101')->first();
            if ($coaKas) {
                $pengeluaran->coa_kredit_id = $coaKas->id;
            }

            /*
             * Pengeluaran yang dimasukkan langsung oleh admin adalah
             * pengeluaran operasional yang sudah sah/diterima.
             * Field lama dan field keuangan baru diisi bersamaan agar
             * seluruh laporan membaca nominal yang sama.
             */
            $pengeluaran->jenis = 'operasional';
            $pengeluaran->nominal = (int) $validated['jumlah'];
            $pengeluaran->keterangan = $validated['deskripsi'];
            $pengeluaran->status_verifikasi = 'diterima';

            $pengeluaran->save();
        } catch (\Throwable $exception) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        return redirect()
            ->route($this->indexRoute($request))
            ->with('success', 'Data pengeluaran berhasil ditambahkan.');
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
}

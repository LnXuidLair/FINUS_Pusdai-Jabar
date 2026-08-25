<?php

namespace App\Http\Controllers;

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
                    'kategori' => 'Penggajian',
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
        return view('pengeluaran.create');
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

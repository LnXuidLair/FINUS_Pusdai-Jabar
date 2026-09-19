<?php

namespace App\Http\Controllers;

use App\Models\ParkirSesi;
use App\Models\ZiswafPenerimaan;
use App\Services\Accounting\Psak109PostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParkirController extends Controller
{
    /**
     * Dashboard monitor parkir hari ini.
     */
    public function index(Request $request)
    {
        $tanggal  = $request->input('tanggal', today()->toDateString());
        $status   = $request->input('status', 'all');

        $query = ParkirSesi::with('inputBy')
            ->whereDate('created_at', $tanggal)
            ->orderByDesc('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $sesiList = $query->paginate(20)->withQueryString();

        // Statistik hari ini
        $hariIni = ParkirSesi::whereDate('created_at', $tanggal);
        $stats = [
            'total_masuk'      => (clone $hariIni)->count(),
            'total_selesai'    => (clone $hariIni)->where('status', 'selesai')->count(),
            'total_motor'      => (clone $hariIni)->where('jenis_kendaraan', 'motor')->where('status', 'selesai')->count(),
            'total_mobil'      => (clone $hariIni)->where('jenis_kendaraan', 'mobil')->where('status', 'selesai')->count(),
            'total_pendapatan' => (clone $hariIni)->where('status', 'selesai')->sum('nominal'),
            'sedang_parkir'    => (clone $hariIni)->where('status', 'masuk')->count(),
        ];

        $tarif = ParkirSesi::tarifDefault();

        return view('admin.parkir.index', compact('sesiList', 'stats', 'tanggal', 'status', 'tarif'));
    }

    /**
     * Form loket: input kendaraan masuk.
     */
    public function create()
    {
        $tarif = ParkirSesi::tarifDefault();
        $labelJenis = ParkirSesi::labelJenis();

        // Kendaraan yang sedang parkir (belum selesai) hari ini
        $sedangParkir = ParkirSesi::whereDate('created_at', today())
            ->where('status', 'masuk')
            ->orderByDesc('id')
            ->get();

        return view('admin.parkir.loket', compact('tarif', 'labelJenis', 'sedangParkir'));
    }

    /**
     * Simpan kendaraan masuk (sesi dibuka).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plat_nomor'      => ['nullable', 'string', 'max:20'],
            'jenis_kendaraan' => ['required', 'in:motor,mobil,lain'],
            'metode_pembayaran' => ['required', 'in:qris,tunai'],
            'keterangan'      => ['nullable', 'string', 'max:500'],
        ]);

        $tarif = ParkirSesi::tarifDefault();
        $nominal = $tarif[$validated['jenis_kendaraan']] ?? 3000;

        ParkirSesi::create([
            'plat_nomor'       => strtoupper(trim($validated['plat_nomor'] ?? '')),
            'jenis_kendaraan'  => $validated['jenis_kendaraan'],
            'nominal'          => $nominal,
            'metode_pembayaran'=> $validated['metode_pembayaran'],
            'status'           => 'masuk',
            'waktu_masuk'      => now(),
            'keterangan'       => $validated['keterangan'] ?? null,
            'input_by'         => auth()->id() ?? auth('pegawai')->id(),
        ]);

        return redirect()->route($this->indexRoute($request))
            ->with('success', 'Kendaraan berhasil dicatat masuk parkir.');
    }

    /**
     * Konfirmasi kendaraan keluar + catat pendapatan ke pemasukan (PSAK 109).
     */
    public function selesai(Request $request, ParkirSesi $parkirSesi)
    {
        if ($parkirSesi->status !== 'masuk') {
            return back()->withErrors(['sesi' => 'Sesi parkir ini sudah selesai atau dibatalkan.']);
        }

        $penerimaan = DB::transaction(function () use ($parkirSesi, $request) {
            // 1. Buat record penerimaan ZISWAF (parkir)
            $platInfo = $parkirSesi->plat_nomor
                ? '[' . $parkirSesi->plat_nomor . '] '
                : '';
            $jenisLabel = ParkirSesi::labelJenis()[$parkirSesi->jenis_kendaraan] ?? $parkirSesi->jenis_kendaraan;

            $penerimaan = ZiswafPenerimaan::create([
                'jenis_ziswaf'      => 'parkir',
                'nominal'           => $parkirSesi->nominal,
                'tanggal'           => today()->toDateString(),
                'metode_pembayaran' => $parkirSesi->metode_pembayaran === 'qris' ? 'qris_manual' : 'tunai',
                'muzakki_id'        => null,
                'keterangan'        => 'Parkir ' . $platInfo . $jenisLabel . ' — ' . now()->format('H:i'),
                'bukti_pembayaran'  => null,
                'status_verifikasi' => 'diterima',
                'verified_by'       => auth()->id() ?? auth('pegawai')->id(),
                'verified_at'       => now(),
                'paid_at'           => now(),
                'payment_status'    => 'manual_paid',
            ]);

            // 2. Posting otomatis ke jurnal PSAK 109
            app(Psak109PostingService::class)->postPenerimaan($penerimaan);

            // 3. Tutup sesi parkir
            $parkirSesi->update([
                'status'              => 'selesai',
                'waktu_keluar'        => now(),
                'ziswaf_penerimaan_id'=> $penerimaan->id,
            ]);

            return $penerimaan;
        });

        return redirect()->route($this->loketRoute($request))
            ->with('success', 'Parkir selesai! Dana Rp ' . number_format($penerimaan->nominal, 0, ',', '.') . ' berhasil dicatat ke pemasukan.');
    }

    /**
     * Batalkan sesi parkir.
     */
    public function batal(Request $request, ParkirSesi $parkirSesi)
    {
        if ($parkirSesi->status !== 'masuk') {
            return back()->withErrors(['sesi' => 'Hanya sesi aktif yang bisa dibatalkan.']);
        }

        $parkirSesi->update([
            'status'       => 'dibatalkan',
            'waktu_keluar' => now(),
        ]);

        return redirect()->route($this->loketRoute($request))
            ->with('success', 'Sesi parkir dibatalkan.');
    }

    private function indexRoute(Request $request): string
    {
        return 'admin.parkir.index';
    }

    private function loketRoute(Request $request): string
    {
        return 'admin.parkir.loket';
    }
}

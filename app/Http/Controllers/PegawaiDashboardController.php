<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Presensi;
use App\Services\PenggajianService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PegawaiDashboardController extends Controller
{
    public function index(
        Request $request,
        PenggajianService $penggajianService,
        ?string $jabatan = null
    ) {
        $pegawai = $request->user()->pegawai;

        abort_unless(
            $pegawai,
            404,
            'Data pegawai belum terhubung dengan akun ini.'
        );

        $profile = $pegawai->dashboard_profile;

        if ($jabatan !== $profile['slug']) {
            return redirect()->route('pegawai.dashboard', [
                'jabatan' => $profile['slug'],
            ]);
        }

        $periode = now()->format('Y-m');

        // Siapkan/sinkronkan gaji bulan berjalan. Presensi pending tidak dihitung.
        $penggajianService->syncPegawai($pegawai, $periode);

        $presensiBulanIni = Presensi::where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->get();

        // Jumlah hari untuk gaji: hadir + approved + tanggal unik.
        $jumlahHadir = Presensi::where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->where('status', 'hadir')
            ->where('is_approved', true)
            ->whereNotNull('approved_at')
            ->distinct()
            ->count('tanggal');

        $presensiMenunggu = Presensi::where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->where('is_approved', false)
            ->count();

        $penggajianTerakhir = Penggajian::where('id_pegawai', $pegawai->id)
            ->orderByDesc('periode')
            ->first();

        return view('dashboard.pegawai.dynamic', [
            'pegawai' => $pegawai,
            'dashboardProfile' => $profile,
            'presensiBulanIni' => $presensiBulanIni,
            'jumlahHadir' => $jumlahHadir,
            'presensiMenunggu' => $presensiMenunggu,
            'penggajianTerakhir' => $penggajianTerakhir,
        ]);
    }

    public function laporanGaji(
        Request $request,
        PenggajianService $penggajianService
    ) {
        $pegawai = $request->user()->pegawai;

        abort_unless(
            $pegawai,
            404,
            'Data pegawai belum terhubung dengan akun ini.'
        );

        $validated = $request->validate([
            'bulan' => ['nullable', 'date_format:Y-m'],
        ]);

        $bulan = $validated['bulan'] ?? null;
        $periodeSync = $bulan ?: now()->format('Y-m');

        // Selama belum dibayar, angka mengikuti presensi yang sudah di-ACC.
        $penggajianService->syncPegawai($pegawai, $periodeSync);

        $query = Penggajian::where('id_pegawai', $pegawai->id)
            ->orderByDesc('periode');

        if ($bulan) {
            $query->where('periode', $bulan);
        }

        $laporanGaji = $query
            ->paginate(10)
            ->withQueryString();

        // Ringkasan total hanya uang yang benar-benar sudah dibayar.
        $totalGaji = Penggajian::where('id_pegawai', $pegawai->id)
            ->where('status_penggajian', 'sudah_dibayar')
            ->sum('total_gaji');

        $gajiTerakhir = Penggajian::where('id_pegawai', $pegawai->id)
            ->orderByDesc('periode')
            ->first();

        return view('pegawai.laporan-gaji.index', [
            'pegawai' => $pegawai,
            'laporanGaji' => $laporanGaji,
            'totalGaji' => $totalGaji,
            'gajiTerakhir' => $gajiTerakhir,
            'bulan' => $bulan,
        ]);
    }

    public function downloadSlip(
        Request $request,
        Penggajian $penggajian
    ) {
        $pegawai = $request->user()->pegawai;

        abort_unless(
            $pegawai,
            404,
            'Data pegawai belum terhubung dengan akun ini.'
        );

        abort_unless(
            (int) $penggajian->id_pegawai === (int) $pegawai->id,
            403,
            'Anda tidak memiliki akses ke slip gaji ini.'
        );

        abort_unless(
            $penggajian->status_penggajian === 'sudah_dibayar',
            403,
            'Slip gaji belum tersedia karena pembayaran belum dilakukan.'
        );

        $penggajian->load('pegawai');

        $pdf = Pdf::loadView('pegawai.laporan-gaji.slip', [
            'pegawai' => $pegawai,
            'penggajian' => $penggajian,
        ]);

        $nama = Str::slug($pegawai->nama_pegawai);

        return $pdf->download(
            'slip-gaji-' . $nama . '-' . $penggajian->periode . '.pdf'
        );
    }
}
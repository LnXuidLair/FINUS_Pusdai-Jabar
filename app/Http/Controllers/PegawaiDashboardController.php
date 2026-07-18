<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Presensi;
use Illuminate\Http\Request;

class PegawaiDashboardController extends Controller
{
    public function index(Request $request, ?string $jabatan = null)
    {
        $pegawai = $request->user()->pegawai;

        abort_unless($pegawai, 404, 'Data pegawai belum terhubung dengan akun ini.');

        $profile = $pegawai->dashboard_profile;

        if ($jabatan !== $profile['slug']) {
            return redirect()->route('pegawai.dashboard', [
                'jabatan' => $profile['slug'],
            ]);
        }

        $presensiBulanIni = Presensi::where('id_pegawai', $pegawai->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->get();

        $penggajianTerakhir = Penggajian::where('id_pegawai', $pegawai->id)
            ->latest('tanggal')
            ->first();

        return view('dashboard.pegawai.dynamic', [
            'pegawai' => $pegawai,
            'dashboardProfile' => $profile,
            'presensiBulanIni' => $presensiBulanIni,
            'jumlahHadir' => $presensiBulanIni->where('status', 'hadir')->count(),
            'presensiMenunggu' => $presensiBulanIni->where('is_approved', false)->count(),
            'penggajianTerakhir' => $penggajianTerakhir,
        ]);
    }

    public function laporanGaji(Request $request)
    {
        $pegawai = $request->user()->pegawai;

        abort_unless($pegawai, 404, 'Data pegawai belum terhubung dengan akun ini.');

        $bulan = $request->input('bulan');

        $query = Penggajian::where('id_pegawai', $pegawai->id)
            ->latest('tanggal');

        if ($bulan) {
            $tanggalFilter = date('Y-m', strtotime($bulan));

            $query->whereYear('tanggal', substr($tanggalFilter, 0, 4))
                ->whereMonth('tanggal', substr($tanggalFilter, 5, 2));
        }

        $laporanGaji = $query->paginate(10)->withQueryString();

        $totalGaji = Penggajian::where('id_pegawai', $pegawai->id)
            ->sum('total_gaji');

        $gajiTerakhir = Penggajian::where('id_pegawai', $pegawai->id)
            ->latest('tanggal')
            ->first();

        return view('pegawai.laporan-gaji.index', [
            'pegawai' => $pegawai,
            'laporanGaji' => $laporanGaji,
            'totalGaji' => $totalGaji,
            'gajiTerakhir' => $gajiTerakhir,
            'bulan' => $bulan,
        ]);
    }
}
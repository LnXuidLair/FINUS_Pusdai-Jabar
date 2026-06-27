<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Pegawai;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->user()->isAdmin() || abort(403, 'Anda tidak memiliki akses.');

        $bulan = now()->month;
        $tahun = now()->year;

        $pengeluaranBulanan = Pengeluaran::selectRaw('MONTH(tanggal) AS bulan, SUM(jumlah) AS total')
            ->whereYear('tanggal', $tahun)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $penggajianBulanan = Penggajian::selectRaw('MONTH(tanggal) AS bulan, SUM(total_gaji) AS total')
            ->whereYear('tanggal', $tahun)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        return view('dashboard.admin', [
            'jumlahPegawai' => Pegawai::count(),
            'jumlahJamaah' => User::where('role', User::ROLE_JAMAAH)->count(),
            'pengeluaranBulanIni' => Pengeluaran::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('jumlah'),
            'gajiBulanIni' => Penggajian::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->sum('total_gaji'),
            'jurnalBulanIni' => Jurnal::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->count(),
            'presensiHariIni' => Presensi::whereDate('tanggal', today())->count(),
            'pengeluaranBulanan' => $pengeluaranBulanan,
            'penggajianBulanan' => $penggajianBulanan,
        ]);
    }
}

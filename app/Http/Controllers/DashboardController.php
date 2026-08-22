<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Pegawai;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->user()->isAdmin()
            || abort(403, 'Anda tidak memiliki akses.');

        $bulan = now()->month;
        $tahun = now()->year;

        /*
         * Hanya pengeluaran operasional yang sudah resmi yang masuk dashboard.
         * Penggajian dihitung terpisah agar tidak terjadi double counting.
         */
        $pengeluaranResmi = Pengeluaran::query()
            ->whereNull('id_penggajian')
            ->whereNull('referensi_penggajian_id')
            ->where(function (Builder $query): void {
                $query->whereNull('jenis')
                    ->orWhere('jenis', '!=', 'gaji');
            })
            ->where(function (Builder $query): void {
                $query->where('status_verifikasi', 'diterima')
                    ->orWhereNull('status_verifikasi');
            });

        $pengeluaranBulanan = (clone $pengeluaranResmi)
            ->selectRaw('MONTH(tanggal) AS bulan')
            ->selectRaw(
                'COALESCE(SUM(COALESCE(NULLIF(nominal, 0), jumlah, 0)), 0) AS total'
            )
            ->whereYear('tanggal', $tahun)
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('total', 'bulan');

        $pengeluaranBulanIni = (int) (clone $pengeluaranResmi)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw(
                'COALESCE(SUM(COALESCE(NULLIF(nominal, 0), jumlah, 0)), 0) AS total'
            )
            ->value('total');

        /*
         * Gaji baru dianggap sebagai pengeluaran ketika status sudah dibayar.
         */
        $penggajianResmi = Penggajian::query()
            ->where('status_penggajian', 'sudah_dibayar')
            ->whereNotNull('tanggal');

        $penggajianBulanan = (clone $penggajianResmi)
            ->selectRaw(
                'MONTH(tanggal) AS bulan, COALESCE(SUM(total_gaji), 0) AS total'
            )
            ->whereYear('tanggal', $tahun)
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('total', 'bulan');

        $gajiBulanIni = (int) (clone $penggajianResmi)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->sum('total_gaji');

        return view('dashboard.admin', [
            'jumlahPegawai' => Pegawai::count(),
            'jumlahJamaah' => User::where(
                'role',
                User::ROLE_JAMAAH
            )->count(),
            'pengeluaranBulanIni' => $pengeluaranBulanIni,
            'gajiBulanIni' => $gajiBulanIni,
            'jurnalBulanIni' => Jurnal::whereMonth(
                    'tanggal',
                    $bulan
                )
                ->whereYear('tanggal', $tahun)
                ->count(),
            'presensiHariIni' => Presensi::whereDate(
                'tanggal',
                today()
            )->count(),
            'pengeluaranBulanan' => $pengeluaranBulanan,
            'penggajianBulanan' => $penggajianBulanan,
        ]);
    }
}
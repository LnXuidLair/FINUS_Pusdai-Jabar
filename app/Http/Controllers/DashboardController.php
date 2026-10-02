<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\Pegawai;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\User;
use App\Models\ZiswafPenerimaan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->user()->isAdmin()
            || abort(403, 'Anda tidak memiliki akses.');

        $bulan = now()->month;
        $tahunBerjalan = now()->year;
        $validated = $request->validate([
            'tahun' => [
                'nullable',
                'integer',
                'min:2000',
                'max:'.$tahunBerjalan,
            ],
        ]);
        $tahunGrafik = (int) ($validated['tahun'] ?? $tahunBerjalan);

        $pemasukanResmi = ZiswafPenerimaan::query()
            ->where(function (Builder $query): void {
                $query->where('status_verifikasi', 'diterima')
                    ->orWhereNull('status_verifikasi');
            });

        $pemasukanPerBulan = (clone $pemasukanResmi)
            ->selectRaw('MONTH(tanggal) AS bulan')
            ->selectRaw('COALESCE(SUM(nominal), 0) AS total')
            ->whereYear('tanggal', $tahunGrafik)
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('total', 'bulan');

        $pemasukanBulanan = collect(range(1, 12))
            ->mapWithKeys(fn (int $month): array => [
                $month => (int) ($pemasukanPerBulan[$month] ?? 0),
            ]);

        $pemasukanBulanIni = (int) (clone $pemasukanResmi)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahunBerjalan)
            ->sum('nominal');

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

        $pengeluaranOperasionalBulanan = (clone $pengeluaranResmi)
            ->selectRaw('MONTH(tanggal) AS bulan')
            ->selectRaw(
                'COALESCE(SUM(COALESCE(NULLIF(nominal, 0), jumlah, 0)), 0) AS total'
            )
            ->whereYear('tanggal', $tahunGrafik)
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('total', 'bulan');

        $pengeluaranOperasionalBulanIni = (int) (clone $pengeluaranResmi)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahunBerjalan)
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

        $penggajianDibayarBulanan = (clone $penggajianResmi)
            ->selectRaw(
                'MONTH(tanggal) AS bulan, COALESCE(SUM(total_gaji), 0) AS total'
            )
            ->whereYear('tanggal', $tahunGrafik)
            ->groupByRaw('MONTH(tanggal)')
            ->pluck('total', 'bulan');

        $gajiDibayarBulanIni = (int) (clone $penggajianResmi)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahunBerjalan)
            ->sum('total_gaji');

        $pengeluaranBulanIni = $pengeluaranOperasionalBulanIni
            + $gajiDibayarBulanIni;

        $pengeluaranBulanan = collect(range(1, 12))
            ->mapWithKeys(fn (int $month): array => [
                $month => (int) ($pengeluaranOperasionalBulanan[$month] ?? 0)
                    + (int) ($penggajianDibayarBulanan[$month] ?? 0),
            ]);

        /*
         * Grafik penggajian mengikuti periode hak gaji (YYYY-MM), bukan
         * tanggal pembayarannya. Dengan begitu gaji September tetap tampil
         * sebagai periode September walaupun dibayar pada Oktober.
         */
        $penggajianPerPeriode = Penggajian::query()
            ->selectRaw('periode, COALESCE(SUM(total_gaji), 0) AS total')
            ->where('periode', 'like', $tahunGrafik.'-%')
            ->groupBy('periode')
            ->pluck('total', 'periode');

        $penggajianBulanan = collect(range(1, 12))
            ->mapWithKeys(function (int $month) use ($penggajianPerPeriode, $tahunGrafik): array {
                $periode = sprintf('%d-%02d', $tahunGrafik, $month);

                return [$month => (int) ($penggajianPerPeriode[$periode] ?? 0)];
            });

        $gajiBulanIni = (int) Penggajian::query()
            ->where('periode', now()->format('Y-m'))
            ->sum('total_gaji');

        return view('dashboard.admin', [
            'jumlahPegawai' => Pegawai::count(),
            'jumlahJamaah' => User::where(
                'role',
                User::ROLE_JAMAAH
            )->count(),
            'pemasukanBulanIni' => $pemasukanBulanIni,
            'pengeluaranBulanIni' => $pengeluaranBulanIni,
            'gajiBulanIni' => $gajiBulanIni,
            'jurnalBulanIni' => Jurnal::whereMonth(
                'tanggal',
                $bulan
            )
                ->whereYear('tanggal', $tahunBerjalan)
                ->count(),
            'pemasukanBulanan' => $pemasukanBulanan,
            'pengeluaranBulanan' => $pengeluaranBulanan,
            'penggajianBulanan' => $penggajianBulanan,
            'tahunGrafik' => $tahunGrafik,
            'tahunGrafikTersedia' => $this->dashboardYears($tahunGrafik),
        ]);
    }

    private function dashboardYears(int $selectedYear): array
    {
        $years = collect([
            ZiswafPenerimaan::query()->min('tanggal'),
            Pengeluaran::query()->min('tanggal'),
            Penggajian::query()->min('tanggal'),
            Penggajian::query()->min('periode'),
        ])
            ->filter()
            ->map(fn ($value): int => (int) substr((string) $value, 0, 4))
            ->filter(fn (int $year): bool => $year >= 2000 && $year <= now()->year)
            ->push(now()->year)
            ->push($selectedYear);

        return range(now()->year, (int) $years->min());
    }
}

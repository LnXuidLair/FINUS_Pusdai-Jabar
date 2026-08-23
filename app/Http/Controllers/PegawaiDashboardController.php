<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pengeluaran;
use App\Models\Penggajian;
use App\Models\Presensi;
use App\Models\ZiswafPenerimaan;
use App\Services\PenggajianService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
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

        $financeDashboard = $this->financeDashboardData($pegawai, $periode);

        return view('dashboard.pegawai.dynamic', [
            'pegawai' => $pegawai,
            'dashboardProfile' => $profile,
            'presensiBulanIni' => $presensiBulanIni,
            'jumlahHadir' => $jumlahHadir,
            'presensiMenunggu' => $presensiMenunggu,
            'penggajianTerakhir' => $penggajianTerakhir,
            'financeDashboard' => $financeDashboard,
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

    private function financeDashboardData(
        Pegawai $pegawai,
        string $periode
    ): array {
        if (! $pegawai->hasAksesRole(
            Pegawai::AKSES_DKM,
            Pegawai::AKSES_KEUANGAN
        )) {
            return [];
        }

        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $pemasukanBulanIni = (int) $this->queryPenerimaanLaporan()
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('nominal');

        $pengeluaranOperasionalBulanIni = (int) $this->queryPengeluaranOperasional()
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->selectRaw('COALESCE(SUM(COALESCE(NULLIF(nominal, 0), jumlah, 0)), 0) AS total')
            ->value('total');

        $gajiDibayarBulanIni = (int) Penggajian::query()
            ->where('status_penggajian', 'sudah_dibayar')
            ->whereNotNull('tanggal')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('total_gaji');

        $pengeluaranBulanIni = $pengeluaranOperasionalBulanIni + $gajiDibayarBulanIni;
        $saldoBulanIni = $pemasukanBulanIni - $pengeluaranBulanIni;

        $penggajianPeriode = Penggajian::query()
            ->where('periode', $periode);

        $penggajianBelumDibayar = (clone $penggajianPeriode)
            ->where('status_penggajian', 'belum_dibayar')
            ->count();

        $nominalPenggajianBelumDibayar = (int) (clone $penggajianPeriode)
            ->where('status_penggajian', 'belum_dibayar')
            ->sum('total_gaji');

        $totalGajiPeriode = (int) (clone $penggajianPeriode)
            ->sum('total_gaji');

        $pengeluaranTerbaru = $this->queryPengeluaranOperasional()
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $penggajianMenunggu = Penggajian::with('pegawai')
            ->where('periode', $periode)
            ->where('status_penggajian', 'belum_dibayar')
            ->orderByDesc('total_gaji')
            ->limit(5)
            ->get();

        $monthlyTrend = $this->monthlyFinanceTrend();
        $statusPenggajian = [
            'sudah_dibayar' => (clone $penggajianPeriode)
                ->where('status_penggajian', 'sudah_dibayar')
                ->count(),
            'belum_dibayar' => $penggajianBelumDibayar,
        ];

        return [
            'pemasukan_bulan_ini' => $pemasukanBulanIni,
            'pengeluaran_operasional_bulan_ini' => $pengeluaranOperasionalBulanIni,
            'gaji_dibayar_bulan_ini' => $gajiDibayarBulanIni,
            'pengeluaran_bulan_ini' => $pengeluaranBulanIni,
            'saldo_bulan_ini' => $saldoBulanIni,
            'penggajian_belum_dibayar' => $penggajianBelumDibayar,
            'nominal_penggajian_belum_dibayar' => $nominalPenggajianBelumDibayar,
            'total_gaji_periode' => $totalGajiPeriode,
            'pengeluaran_terbaru' => $pengeluaranTerbaru,
            'penggajian_menunggu' => $penggajianMenunggu,
            'periode' => $periode,
            'chart' => [
                'labels' => $monthlyTrend['labels'],
                'pemasukan' => $monthlyTrend['pemasukan'],
                'pengeluaran' => $monthlyTrend['pengeluaran'],
                'saldo' => $monthlyTrend['saldo'],
                'status_penggajian' => [
                    $statusPenggajian['sudah_dibayar'],
                    $statusPenggajian['belum_dibayar'],
                ],
            ],
            'trend' => [
                'pemasukan' => $this->trendLabel($monthlyTrend['pemasukan']),
                'pengeluaran' => $this->trendLabel($monthlyTrend['pengeluaran']),
                'saldo' => $this->trendLabel($monthlyTrend['saldo']),
            ],
        ];
    }

    private function queryPenerimaanLaporan(): Builder
    {
        return ZiswafPenerimaan::query()
            ->where(function (Builder $query): void {
                $query->where('status_verifikasi', 'diterima')
                    ->orWhereNull('status_verifikasi');
            });
    }

    private function queryPengeluaranOperasional(): Builder
    {
        return Pengeluaran::query()
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
    }

    private function monthlyFinanceTrend(int $months = 6): array
    {
        $labels = [];
        $pemasukan = [];
        $pengeluaran = [];
        $saldo = [];

        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        for ($index = $months - 1; $index >= 0; $index--) {
            $date = now()->startOfMonth()->subMonths($index);
            $start = $date->copy()->startOfMonth()->toDateString();
            $end = $date->copy()->endOfMonth()->toDateString();

            $income = (int) $this->queryPenerimaanLaporan()
                ->whereBetween('tanggal', [$start, $end])
                ->sum('nominal');

            $operationalExpense = (int) $this->queryPengeluaranOperasional()
                ->whereBetween('tanggal', [$start, $end])
                ->selectRaw('COALESCE(SUM(COALESCE(NULLIF(nominal, 0), jumlah, 0)), 0) AS total')
                ->value('total');

            $payrollExpense = (int) Penggajian::query()
                ->where('status_penggajian', 'sudah_dibayar')
                ->whereNotNull('tanggal')
                ->whereBetween('tanggal', [$start, $end])
                ->sum('total_gaji');

            $expense = $operationalExpense + $payrollExpense;

            $labels[] = $monthNames[(int) $date->month] . ' ' . $date->format('y');
            $pemasukan[] = $income;
            $pengeluaran[] = $expense;
            $saldo[] = $income - $expense;
        }

        return [
            'labels' => $labels,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'saldo' => $saldo,
        ];
    }

    private function trendLabel(array $values): array
    {
        $current = (int) end($values);
        $previous = (int) ($values[count($values) - 2] ?? 0);
        $difference = $current - $previous;

        if ($difference === 0) {
            return [
                'label' => 'Stabil dari bulan lalu',
                'direction' => 'flat',
            ];
        }

        if ($previous === 0) {
            return [
                'label' => ($difference > 0 ? 'Naik' : 'Turun') . ' dari Rp0',
                'direction' => $difference > 0 ? 'up' : 'down',
            ];
        }

        $percentage = abs($difference / $previous) * 100;

        return [
            'label' => ($difference > 0 ? 'Naik ' : 'Turun ')
                . number_format($percentage, 1, ',', '.')
                . '% dari bulan lalu',
            'direction' => $difference > 0 ? 'up' : 'down',
        ];
    }
}

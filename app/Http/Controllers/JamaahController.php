<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\ZiswafPenerimaan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JamaahController extends Controller
{
    public function dashboard(Request $request)
    {
        $jamaah = $request->user();

        $jenisLabels = $this->jenisLabels();

        $totalTransaksiSaya = (int) ZiswafPenerimaan::where('muzakki_id', $jamaah->id)
            ->sum('nominal');

        $jumlahTransaksiSaya = ZiswafPenerimaan::where('muzakki_id', $jamaah->id)
            ->count();

        $totalPemasukanJamaah = (int) ZiswafPenerimaan::sum('nominal');

        $totalInfak = (int) ZiswafPenerimaan::where('jenis_ziswaf', 'infaq')
            ->sum('nominal');

        $totalZakat = (int) ZiswafPenerimaan::whereIn('jenis_ziswaf', ['zakat_maal', 'zakat_fitrah'])
            ->sum('nominal');

        $totalWakaf = (int) ZiswafPenerimaan::where('jenis_ziswaf', 'wakaf')
            ->sum('nominal');

        $totalPengeluaran = (int) DB::table('pengeluaran')
            ->selectRaw('COALESCE(SUM(COALESCE(nominal, jumlah, 0)), 0) as total')
            ->value('total');

        $saldoSederhana = $totalPemasukanJamaah - $totalPengeluaran;

        $riwayatSaya = ZiswafPenerimaan::where('muzakki_id', $jamaah->id)
            ->latest('tanggal')
            ->latest('id')
            ->limit(8)
            ->get();

        $transaksiTerbaruJamaah = ZiswafPenerimaan::with('muzakki')
            ->latest('tanggal')
            ->latest('id')
            ->limit(6)
            ->get();

        $pengeluaranKategori = DB::table('pengeluaran')
            ->selectRaw("
                COALESCE(kategori, jenis, 'Lainnya') as kategori_nama,
                COUNT(*) as jumlah_transaksi,
                COALESCE(SUM(COALESCE(nominal, jumlah, 0)), 0) as total
            ")
            ->groupByRaw("COALESCE(kategori, jenis, 'Lainnya')")
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $rawChart = ZiswafPenerimaan::query()
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan, SUM(nominal) as total")
            ->whereDate('tanggal', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $chartLabels = [];
        $chartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $key = $month->format('Y-m');

            $chartLabels[] = $month->translatedFormat('M Y');
            $chartData[] = (int) ($rawChart[$key] ?? 0);
        }

        $komposisiZiswaf = ZiswafPenerimaan::query()
            ->select('jenis_ziswaf')
            ->selectRaw('SUM(nominal) as total')
            ->groupBy('jenis_ziswaf')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($jenisLabels) {
                return [
                    'jenis' => $jenisLabels[$item->jenis_ziswaf] ?? ucfirst(str_replace('_', ' ', $item->jenis_ziswaf)),
                    'total' => (int) $item->total,
                ];
            });

        $komposisiLabels = $komposisiZiswaf->pluck('jenis')->values();
        $komposisiData = $komposisiZiswaf->pluck('total')->values();

        return view('dashboard.jamaah', compact(
            'jamaah',
            'jenisLabels',
            'totalTransaksiSaya',
            'jumlahTransaksiSaya',
            'totalPemasukanJamaah',
            'totalInfak',
            'totalZakat',
            'totalWakaf',
            'totalPengeluaran',
            'saldoSederhana',
            'riwayatSaya',
            'transaksiTerbaruJamaah',
            'pengeluaranKategori',
            'chartLabels',
            'chartData',
            'komposisiZiswaf',
            'komposisiLabels',
            'komposisiData'
        ));
    }

    public function storeZiswaf(Request $request)
    {
        $validated = $request->validate([
            'jenis_ziswaf' => [
                'required',
                Rule::in(['zakat_maal', 'zakat_fitrah', 'infaq', 'shadaqah', 'wakaf', 'fidyah']),
            ],
            'nominal' => ['required', 'integer', 'min:1000'],
            'metode_pembayaran' => ['required', Rule::in(['tunai', 'transfer', 'qris'])],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        ZiswafPenerimaan::create([
            'muzakki_id' => $request->user()->id,
            'tanggal' => now()->toDateString(),
            'jenis_ziswaf' => $validated['jenis_ziswaf'],
            'nominal' => $validated['nominal'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return back()->with('success', 'Transaksi ZISWAF / infak berhasil dicatat.');
    }

    private function jenisLabels(): array
    {
        return [
            'zakat_maal' => 'Zakat Maal',
            'zakat_fitrah' => 'Zakat Fitrah',
            'infaq' => 'Infak',
            'shadaqah' => 'Sedekah',
            'wakaf' => 'Wakaf',
            'fidyah' => 'Fidyah',
        ];
    }
}
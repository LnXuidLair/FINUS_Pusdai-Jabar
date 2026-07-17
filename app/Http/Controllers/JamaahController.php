<?php

namespace App\Http\Controllers;

use App\Models\ZiswafPenerimaan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $totalZakat = (int) ZiswafPenerimaan::whereIn(
            'jenis_ziswaf',
            ['zakat_maal', 'zakat_fitrah']
        )->sum('nominal');

        $totalWakaf = (int) ZiswafPenerimaan::where('jenis_ziswaf', 'wakaf')
            ->sum('nominal');

        $totalPengeluaran = (int) DB::table('pengeluaran')
            ->selectRaw('COALESCE(SUM(COALESCE(nominal, jumlah, 0)), 0) AS total')
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
                COALESCE(kategori, jenis, 'Lainnya') AS kategori_nama,
                COUNT(*) AS jumlah_transaksi,
                COALESCE(SUM(COALESCE(nominal, jumlah, 0)), 0) AS total
            ")
            ->groupByRaw("COALESCE(kategori, jenis, 'Lainnya')")
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $rawChart = ZiswafPenerimaan::query()
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') AS bulan, SUM(nominal) AS total")
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
            ->selectRaw('SUM(nominal) AS total')
            ->groupBy('jenis_ziswaf')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($jenisLabels): array {
                return [
                    'jenis' => $jenisLabels[$item->jenis_ziswaf]
                        ?? ucfirst(str_replace('_', ' ', $item->jenis_ziswaf)),
                    'total' => (int) $item->total,
                ];
            });

        $komposisiLabels = $komposisiZiswaf->pluck('jenis')->values();
        $komposisiData = $komposisiZiswaf->pluck('total')->values();

        $agendaKegiatan = collect([
            [
                'judul' => 'Kajian Rutin Subuh',
                'hari' => 'Setiap Ahad',
                'waktu' => '05.15 - 06.30 WIB',
                'lokasi' => 'Aula Masjid Pusdai',
                'kategori' => 'Kajian',
                'deskripsi' => 'Kajian pekanan untuk jamaah umum setelah salat Subuh.',
            ],
            [
                'judul' => 'Jumat Berkah',
                'hari' => 'Setiap Jumat',
                'waktu' => '11.00 - selesai',
                'lokasi' => 'Area Masjid',
                'kategori' => 'Sosial',
                'deskripsi' => 'Pembagian konsumsi dan sedekah untuk jamaah Jumat.',
            ],
            [
                'judul' => 'Kelas Tahsin Al-Qur’an',
                'hari' => 'Setiap Sabtu',
                'waktu' => '16.00 - 17.30 WIB',
                'lokasi' => 'Ruang Belajar',
                'kategori' => 'Pendidikan',
                'deskripsi' => 'Kegiatan belajar memperbaiki bacaan Al-Qur’an.',
            ],
        ]);

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
            'komposisiData',
            'agendaKegiatan'
        ));
    }

    /**
     * Menampilkan seluruh riwayat transaksi milik jamaah yang sedang login.
     */
    public function riwayat(Request $request)
    {
        $filters = $this->validatedTransactionFilters($request);
        $query = $this->jamaahTransactionQuery($request, $filters);

        $ringkasanQuery = clone $query;

        $ringkasan = [
            'jumlah' => (clone $ringkasanQuery)->count(),
            'nominal' => (int) (clone $ringkasanQuery)->sum('nominal'),
            'diterima' => (int) (clone $ringkasanQuery)
                ->where('status_verifikasi', 'diterima')
                ->sum('nominal'),
            'pending' => (int) (clone $ringkasanQuery)
                ->where(function (Builder $builder): void {
                    $builder->where('status_verifikasi', 'pending')
                        ->orWhereNull('status_verifikasi');
                })
                ->sum('nominal'),
        ];

        $transaksi = $query
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('jamaah.riwayat-transaksi', [
            'jamaah' => $request->user(),
            'transaksi' => $transaksi,
            'ringkasan' => $ringkasan,
            'filters' => $filters,
            'jenisLabels' => $this->jenisLabels(),
            'statusLabels' => $this->statusLabels(),
            'metodeLabels' => $this->metodeLabels(),
        ]);
    }

    /**
     * Menampilkan laporan transaksi pribadi berdasarkan periode.
     */
    public function laporan(Request $request)
    {
        $filters = $this->validatedTransactionFilters(
            $request,
            defaultPeriod: true
        );

        $query = $this->jamaahTransactionQuery($request, $filters);
        $summaryQuery = clone $query;

        $summary = [
            'jumlah' => (clone $summaryQuery)->count(),
            'total' => (int) (clone $summaryQuery)->sum('nominal'),
            'diterima' => (int) (clone $summaryQuery)
                ->where('status_verifikasi', 'diterima')
                ->sum('nominal'),
            'pending' => (int) (clone $summaryQuery)
                ->where(function (Builder $builder): void {
                    $builder->where('status_verifikasi', 'pending')
                        ->orWhereNull('status_verifikasi');
                })
                ->sum('nominal'),
            'ditolak' => (int) (clone $summaryQuery)
                ->where('status_verifikasi', 'ditolak')
                ->sum('nominal'),
        ];

        $perJenis = (clone $query)
            ->select('jenis_ziswaf')
            ->selectRaw('COUNT(*) AS jumlah_transaksi')
            ->selectRaw('COALESCE(SUM(nominal), 0) AS total')
            ->groupBy('jenis_ziswaf')
            ->orderByDesc('total')
            ->get();

        $monthlyRaw = (clone $query)
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') AS bulan")
            ->selectRaw('COALESCE(SUM(nominal), 0) AS total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        [$chartLabels, $chartData] = $this->buildMonthlyChart(
            Carbon::parse($filters['tanggal_mulai'])->startOfMonth(),
            Carbon::parse($filters['tanggal_selesai'])->startOfMonth(),
            $monthlyRaw->all()
        );

        $jenisChartLabels = $perJenis
            ->map(fn ($item): string => $this->jenisLabels()[$item->jenis_ziswaf]
                ?? ucfirst(str_replace('_', ' ', $item->jenis_ziswaf)))
            ->values();

        $jenisChartData = $perJenis->pluck('total')->map(fn ($value): int => (int) $value)->values();

        $transaksiLaporan = (clone $query)
            ->latest('tanggal')
            ->latest('id')
            ->get();

        return view('jamaah.laporan-transaksi', [
            'jamaah' => $request->user(),
            'filters' => $filters,
            'summary' => $summary,
            'perJenis' => $perJenis,
            'transaksiLaporan' => $transaksiLaporan,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'jenisChartLabels' => $jenisChartLabels,
            'jenisChartData' => $jenisChartData,
            'jenisLabels' => $this->jenisLabels(),
            'statusLabels' => $this->statusLabels(),
            'metodeLabels' => $this->metodeLabels(),
        ]);
    }

    /**
     * Mengunduh laporan pribadi dalam format CSV tanpa package tambahan.
     */
    public function exportLaporan(Request $request): StreamedResponse
    {
        $filters = $this->validatedTransactionFilters(
            $request,
            defaultPeriod: true
        );

        $transaksi = $this->jamaahTransactionQuery($request, $filters)
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $namaFile = sprintf(
            'laporan-transaksi-%s-%s.csv',
            $request->user()->id,
            now()->format('Ymd-His')
        );

        $jenisLabels = $this->jenisLabels();
        $statusLabels = $this->statusLabels();
        $metodeLabels = $this->metodeLabels();

        return response()->streamDownload(
            function () use (
                $transaksi,
                $jenisLabels,
                $statusLabels,
                $metodeLabels
            ): void {
                $output = fopen('php://output', 'wb');

                if ($output === false) {
                    return;
                }

                // BOM agar karakter Indonesia terbaca benar di Microsoft Excel.
                fwrite($output, "\xEF\xBB\xBF");

                fputcsv($output, [
                    'Referensi',
                    'Tanggal',
                    'Jenis ZISWAF',
                    'Metode Pembayaran',
                    'Nominal',
                    'Status',
                    'Keterangan',
                    'Catatan Verifikasi',
                ], ';');

                foreach ($transaksi as $item) {
                    $status = $item->status_verifikasi ?: 'pending';

                    fputcsv($output, [
                        'ZISWAF-' . $item->id,
                        optional($item->tanggal)->format('d/m/Y'),
                        $jenisLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf,
                        $metodeLabels[$item->metode_pembayaran]
                            ?? strtoupper(str_replace('_', ' ', $item->metode_pembayaran)),
                        (int) $item->nominal,
                        $statusLabels[$status] ?? ucfirst($status),
                        $item->keterangan ?? '-',
                        $item->catatan_verifikasi ?? '-',
                    ], ';');
                }

                fclose($output);
            },
            $namaFile,
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function createTransaksi(string $jenis)
    {
        $config = $this->transaksiConfig($jenis);

        return view('jamaah.transaksi-ziswaf', compact('jenis', 'config'));
    }

    public function storeTransaksi(Request $request, string $jenis)
    {
        $config = $this->transaksiConfig($jenis);

        $validated = $request->validate([
            'jenis_ziswaf' => [
                'required',
                Rule::in(array_keys($config['jenisOptions'])),
            ],
            'nominal' => ['required', 'integer', 'min:1000'],
            'metode_pembayaran' => [
                'required',
                Rule::in(array_keys($config['metodeOptions'])),
            ],
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

        return redirect()
            ->route('jamaah.riwayat.index')
            ->with('success', $config['successMessage']);
    }

    /**
     * Validasi filter GET yang dipakai halaman riwayat, laporan, dan ekspor.
     */
    private function validatedTransactionFilters(
        Request $request,
        bool $defaultPeriod = false
    ): array {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'jenis' => [
                'nullable',
                Rule::in(array_keys($this->jenisLabels())),
            ],
            'status' => [
                'nullable',
                Rule::in(array_keys($this->statusLabels())),
            ],
            'metode' => [
                'nullable',
                Rule::in(array_keys($this->metodeLabels())),
            ],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ]);

        if ($defaultPeriod) {
            $filters['tanggal_mulai'] = $filters['tanggal_mulai']
                ?? now()->startOfYear()->toDateString();

            $filters['tanggal_selesai'] = $filters['tanggal_selesai']
                ?? now()->toDateString();
        }

        return $filters;
    }

    /**
     * Query selalu dibatasi dengan muzakki_id user yang sedang login.
     */
    private function jamaahTransactionQuery(
        Request $request,
        array $filters
    ): Builder {
        $query = ZiswafPenerimaan::query()
            ->where('muzakki_id', $request->user()->id);

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $referenceId = null;

            if (preg_match('/(?:ZISWAF-)?(\d+)/i', $search, $matches)) {
                $referenceId = (int) $matches[1];
            }

            $query->where(function (Builder $builder) use (
                $search,
                $referenceId
            ): void {
                $builder->where('keterangan', 'like', '%' . $search . '%');

                if ($referenceId !== null && $referenceId > 0) {
                    $builder->orWhere('id', $referenceId);
                }
            });
        }

        if (!empty($filters['jenis'])) {
            $query->where('jenis_ziswaf', $filters['jenis']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'pending') {
                $query->where(function (Builder $builder): void {
                    $builder->where('status_verifikasi', 'pending')
                        ->orWhereNull('status_verifikasi');
                });
            } else {
                $query->where('status_verifikasi', $filters['status']);
            }
        }

        if (!empty($filters['metode'])) {
            $query->where('metode_pembayaran', $filters['metode']);
        }

        if (!empty($filters['tanggal_mulai'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_mulai']);
        }

        if (!empty($filters['tanggal_selesai'])) {
            $query->whereDate('tanggal', '<=', $filters['tanggal_selesai']);
        }

        return $query;
    }

    private function buildMonthlyChart(
        Carbon $start,
        Carbon $end,
        array $raw
    ): array {
        $labels = [];
        $data = [];
        $cursor = $start->copy();

        // Batasi perulangan untuk menghindari rentang yang tidak wajar.
        $maximumMonths = 120;
        $iteration = 0;

        while ($cursor->lte($end) && $iteration < $maximumMonths) {
            $key = $cursor->format('Y-m');

            $labels[] = $cursor->translatedFormat('M Y');
            $data[] = (int) ($raw[$key] ?? 0);

            $cursor->addMonth();
            $iteration++;
        }

        return [$labels, $data];
    }

    private function transaksiConfig(string $jenis): array
    {
        return match ($jenis) {
            'zakat' => [
                'title' => 'Transaksi Zakat',
                'subtitle' => 'Catat transaksi zakat maal atau zakat fitrah.',
                'jenisOptions' => [
                    'zakat_maal' => 'Zakat Maal',
                    'zakat_fitrah' => 'Zakat Fitrah',
                ],
                'metodeOptions' => [
                    'transfer' => 'Transfer Bank',
                    'qris' => 'QRIS',
                ],
                'successMessage' => 'Transaksi zakat berhasil dicatat.',
            ],

            'infak' => [
                'title' => 'Transaksi Infak',
                'subtitle' => 'Catat transaksi infak jamaah.',
                'jenisOptions' => [
                    'infaq' => 'Infak',
                ],
                'metodeOptions' => [
                    'transfer' => 'Transfer Bank',
                    'qris' => 'QRIS',
                ],
                'successMessage' => 'Transaksi infak berhasil dicatat.',
            ],

            'wakaf' => [
                'title' => 'Transaksi Wakaf',
                'subtitle' => 'Catat transaksi wakaf jamaah.',
                'jenisOptions' => [
                    'wakaf' => 'Wakaf',
                ],
                'metodeOptions' => [
                    'transfer' => 'Transfer Bank',
                    'qris' => 'QRIS',
                ],
                'successMessage' => 'Transaksi wakaf berhasil dicatat.',
            ],

            default => abort(404),
        };
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

    private function statusLabels(): array
    {
        return [
            'pending' => 'Menunggu',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
        ];
    }

    private function metodeLabels(): array
    {
        return [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            // Tetap dibaca untuk kompatibilitas data lama.
            'transfer_bank' => 'Transfer Bank',
            'virtual_account' => 'Virtual Account',
        ];
    }
}
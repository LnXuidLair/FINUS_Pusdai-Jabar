<?php

namespace App\Http\Controllers;

use App\Models\AgendaKegiatan;
use App\Models\BarangZakat;
use App\Models\HargaBarangZakat;
use App\Models\KetentuanPokokZakat;
use App\Models\MasterAsnaf;
use App\Models\Organization;
use App\Models\User;
use App\Models\ZiswafPenerimaan;
use App\Services\ZakatCalculatorService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Transaction;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class JamaahController extends Controller
{
    public function __construct(
        private readonly ZakatCalculatorService $zakatCalculator
    ) {}

    public function index()
    {
        $jamaahs = User::query()
            ->where('role', User::ROLE_JAMAAH)
            ->withCount([
                'ziswafPenerimaans as total_transaksi',
            ])
            ->withSum([
                'ziswafPenerimaans as total_nominal',
            ], 'nominal')
            ->withMax([
                'ziswafPenerimaans as transaksi_terakhir',
            ], 'created_at')
            ->orderBy('name')
            ->get();

        return view('jamaah.index', compact('jamaahs'));
    }

    public function dashboard(Request $request)
    {
        /** @var User $jamaah */
        $jamaah = $request->user();
        $jenisLabels = $this->jenisLabels();
        $transaksiBerhasilSaya = ZiswafPenerimaan::query()
            ->where('muzakki_id', $jamaah->id)
            ->where('status_verifikasi', 'diterima');
        $totalTransaksiSaya = (int) (clone $transaksiBerhasilSaya)
            ->sum('nominal');
        $jumlahTransaksiSaya = (clone $transaksiBerhasilSaya)
            ->count();
        $totalZakatSaya = (int) (clone $transaksiBerhasilSaya)
            ->whereIn('jenis_ziswaf', [
                'zakat_maal',
                'zakat_fitrah',
                'zakat_penghasilan',
                'zakat_pertanian_berbiaya',
                'zakat_pertanian_alami',
            ])
            ->sum('nominal');
        $totalInfakSaya = (int) (clone $transaksiBerhasilSaya)
            ->where('jenis_ziswaf', 'infaq')
            ->sum('nominal');
        $totalWakafSaya = (int) (clone $transaksiBerhasilSaya)
            ->where('jenis_ziswaf', 'wakaf')
            ->sum('nominal');
        $riwayatSaya = (clone $transaksiBerhasilSaya)
            ->latest('tanggal')
            ->latest('id')
            ->limit(8)
            ->get();
        $organizations = Organization::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'public_id', 'name', 'slug', 'address', 'city', 'province', 'logo_path']);

        $agendaKegiatan = AgendaKegiatan::with('organization')
            ->aktif()
            ->orderBy('urutan')
            ->orderBy('id')
            ->get()
            ->map(fn ($item) => [
                'judul' => $item->judul,
                'hari' => $item->hari,
                'waktu' => $item->waktu,
                'lokasi' => $item->lokasi,
                'kategori' => $item->kategori_label,
                'deskripsi' => $item->deskripsi,
                'organization' => $item->organization?->name ?? 'Masjid',
            ]);

        return view('dashboard.jamaah', compact(
            'jamaah',
            'jenisLabels',
            'totalTransaksiSaya',
            'jumlahTransaksiSaya',
            'totalZakatSaya',
            'totalInfakSaya',
            'totalWakafSaya',
            'riwayatSaya',
            'agendaKegiatan',
            'organizations'
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
                ->where(function ($builder): void {
                    // Nominal pending/gagal: semua transaksi yang tidak berhasil
                    // mencakup: dibatalkan, ditolak, pending, atau status kosong/NULL
                    $builder->where('status_verifikasi', 'dibatalkan')
                        ->orWhere('status_verifikasi', 'ditolak')
                        ->orWhere('status_verifikasi', 'pending')
                        ->orWhereNull('status_verifikasi');
                })
                ->sum('nominal'),
            'jumlah_gagal' => (clone $ringkasanQuery)
                ->where(function ($builder): void {
                    $builder->where('status_verifikasi', 'dibatalkan')
                        ->orWhere('status_verifikasi', 'ditolak')
                        ->orWhere('status_verifikasi', 'pending')
                        ->orWhereNull('status_verifikasi');
                })
                ->count(),
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
     * Mengembalikan data invoice dalam format JSON untuk modal AJAX.
     */
    public function invoice(Request $request, ZiswafPenerimaan $transaksi)
    {
        $jamaah = $request->user();

        abort_if(
            $transaksi->muzakki_id !== $jamaah->id || $transaksi->status_verifikasi !== 'diterima',
            403
        );

        $transaksi->load(['muzakki', 'organization']);
        $jenisLabels = $this->jenisLabels();
        $metodeLabels = $this->metodeLabels();

        return response()->json([
            'referensi' => $transaksi->order_id ?: 'ZSF-'.$transaksi->id,
            'jamaah_nama' => $jamaah->name,
            'jamaah_email' => $jamaah->email ?? '-',
            'organization' => $transaksi->organization?->name ?? 'Masjid',
            'jenis' => $jenisLabels[$transaksi->jenis_ziswaf] ?? $transaksi->jenis_ziswaf,
            'nominal' => $transaksi->nominal,
            'nominal_fmt' => 'Rp '.number_format($transaksi->nominal, 0, ',', '.'),
            'metode' => $metodeLabels[$transaksi->metode_pembayaran] ?? ($transaksi->metode_pembayaran ?? '-'),
            'tanggal' => $transaksi->tanggal?->format('d F Y'),
            'verified_at' => $transaksi->verified_at?->format('d F Y, H:i').' WIB',
            'keterangan' => $transaksi->keterangan,
            'catatan' => $transaksi->catatan_verifikasi,
            'url_cetak' => route('jamaah.riwayat.invoice.cetak', $transaksi),
        ]);
    }

    /**
     * Menampilkan halaman invoice print-friendly (buka di tab baru).
     */
    public function invoicePrint(Request $request, ZiswafPenerimaan $transaksi)
    {
        $jamaah = $request->user();

        abort_if(
            $transaksi->muzakki_id !== $jamaah->id || $transaksi->status_verifikasi !== 'diterima',
            403
        );

        $transaksi->load('organization');
        $jenisLabels = $this->jenisLabels();
        $metodeLabels = $this->metodeLabels();

        return view('jamaah.invoice-print', [
            'jamaah' => $jamaah,
            'transaksi' => $transaksi,
            'jenisLabel' => $jenisLabels[$transaksi->jenis_ziswaf] ?? $transaksi->jenis_ziswaf,
            'metodeLabel' => $metodeLabels[$transaksi->metode_pembayaran] ?? ($transaksi->metode_pembayaran ?? '-'),
            'referensi' => $transaksi->order_id ?: 'ZSF-'.$transaksi->id,
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
        $filters['status'] = 'diterima';
        $query = $this->jamaahTransactionQuery($request, $filters);
        // Query laporan: eksklusikan transaksi yang dibatalkan/ditolak
        // agar tidak mengganggu chart dan grafik
        $laporanQuery = (clone $query)
            ->whereNotIn('status_verifikasi', ['ditolak', 'dibatalkan']);
        $summaryQuery = clone $query;
        $summary = [
            'jumlah' => (clone $summaryQuery)->count(),
            'total' => (int) (clone $summaryQuery)->sum('nominal'),
            'diterima' => (int) (clone $summaryQuery)->sum('nominal'),
            'pending' => 0,
            'ditolak' => 0,
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
            ->map(fn (ZiswafPenerimaan $item): string => $this->jenisLabels()[$item->jenis_ziswaf]
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
        $filters['status'] = 'diterima';
        $transaksi = $this->jamaahTransactionQuery($request, $filters)
            ->whereNotIn('status_verifikasi', ['ditolak', 'dibatalkan'])
            ->latest('tanggal')
            ->latest('id')
            ->get();
        $namaFile = sprintf(
            'laporan-transaksi-berhasil-%s-%s.csv',
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
                    'Masjid Tujuan',
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
                        $item->order_id ?: 'ZSF-'.$item->id,
                        optional($item->tanggal)->format('d/m/Y'),
                        $item->organization?->name ?? 'Masjid',
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

    public function createTransaksi(Request $request, string $jenis)
    {
        $organizations = Organization::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'public_id', 'name', 'address', 'city', 'province']);

        abort_if(
            $organizations->isEmpty(),
            503,
            'Belum ada masjid aktif yang dapat menerima transaksi.'
        );

        $requestedOrganizationId = (int) ($request->old('organization_id') ?: $request->query('organization_id', 0));
        $selectedOrganizationId = $organizations->contains('id', $requestedOrganizationId)
            ? $requestedOrganizationId
            : (int) $organizations->first()->id;

        $config = $this->transaksiConfig($jenis);
        $paymentGatewayReady = $this->isPaymentGatewayReady();

        $zakatPolicies = [];
        $zakatCalculationCategories = [];
        $masterAsnaf = collect();
        $hargaBeras = null;
        $hargaEmas = null;
        $hargaPertanian = [];
        $zakatPenghasilanTerbayar = [
            'per_bulan' => [],
            'per_tahun' => [],
        ];

        if ($jenis === 'zakat') {
            $hargaEmas = $this->activeGoldPrice();
            $zakatPolicies = $this->zakatPoliciesForJamaah($hargaEmas);
            $zakatCalculationCategories = $this->zakatCalculationCategories();
            $masterAsnaf = MasterAsnaf::semuaAktif();
            $hargaBeras = $this->activeRicePrice();
            $hargaPertanian = [
                'padi_gabah' => $this->activeAgriculturalPrice('padi_gabah'),
                'beras' => $hargaBeras,
            ];
            $zakatPenghasilanTerbayar = $this->incomeZakatPaymentSummary((int) $request->user()->id);
        }

        return view('jamaah.transaksi-ziswaf', compact(
            'jenis',
            'config',
            'paymentGatewayReady',
            'zakatPolicies',
            'zakatCalculationCategories',
            'masterAsnaf',
            'hargaBeras',
            'hargaEmas',
            'hargaPertanian',
            'zakatPenghasilanTerbayar',
            'organizations',
            'selectedOrganizationId'
        ));
    }

    public function storeTransaksi(Request $request, string $jenis)
    {
        $organizationId = (int) $request->input('organization_id', 0);
        if (! $organizationId) {
            $activeOrganizations = Organization::query()->active()->limit(2)->get(['id']);
            if ($activeOrganizations->count() === 1) {
                $organizationId = (int) $activeOrganizations->first()->id;
                $request->merge(['organization_id' => $organizationId]);
            }
        }
        $targetOrganization = Organization::query()
            ->active()
            ->find($organizationId);

        if (! $targetOrganization) {
            throw ValidationException::withMessages([
                'organization_id' => 'Masjid tujuan tidak tersedia atau sedang tidak aktif.',
            ]);
        }

        $config = $this->transaksiConfig($jenis);
        $paymentGatewayReady = $this->isPaymentGatewayReady();
        $minimalNominal = $paymentGatewayReady ? 10000 : 1000;
        $ketentuanZakat = $this->zakatPolicyForTransaction(
            (string) $request->input('jenis_ziswaf')
        );
        $calculationCategories = $this->zakatCalculationCategories();
        $allowedCalculationCategories = array_keys(
            $calculationCategories[(string) $request->input('jenis_ziswaf')] ?? []
        );
        $rules = [
            'organization_id' => [
                'required',
                'integer',
                Rule::exists('organizations', 'id')->where(
                    fn ($query) => $query->where('is_active', true)
                ),
            ],
            'jenis_ziswaf' => [
                'required',
                Rule::in(array_keys($config['jenisOptions'])),
            ],
            'nominal' => [
                'required',
                'integer',
                'min:'.$minimalNominal,
            ],
            'metode_pembayaran' => [
                'required',
                Rule::in(array_keys($config['metodeOptions'])),
            ],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'kategori_perhitungan' => [
                Rule::requiredIf(fn (): bool => in_array(
                    $request->input('jenis_ziswaf'),
                    [
                        'zakat_maal',
                        'zakat_penghasilan',
                        'zakat_pertanian_berbiaya',
                        'zakat_pertanian_alami',
                    ],
                    true
                )),
                'nullable',
                Rule::in($allowedCalculationCategories),
            ],
            'periode_penghasilan' => [
                Rule::requiredIf(fn (): bool => $request->input('jenis_ziswaf') === 'zakat_penghasilan'),
                'nullable',
                Rule::in(['bulanan', 'tahunan']),
            ],
            'bulan_penghasilan' => [
                Rule::requiredIf(fn (): bool => $request->input('jenis_ziswaf') === 'zakat_penghasilan'
                    && $request->input('periode_penghasilan') === 'bulanan'),
                'nullable',
                'date_format:Y-m',
            ],
            'tahun_penghasilan' => [
                Rule::requiredIf(fn (): bool => $request->input('jenis_ziswaf') === 'zakat_penghasilan'),
                'nullable',
                'integer',
                'min:2000',
                'max:'.now()->year,
            ],
            'penghasilan_bersih' => [
                Rule::requiredIf(fn (): bool => $request->input('jenis_ziswaf') === 'zakat_penghasilan'),
                'nullable',
                'integer',
                'min:1',
            ],
            'tanggal_mulai_kepemilikan' => [
                Rule::requiredIf(fn (): bool => $request->input('jenis_ziswaf') === 'zakat_maal'),
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'berat_emas_gram' => [
                Rule::requiredIf(fn (): bool => $request->input('jenis_ziswaf') === 'zakat_maal'
                    && $request->input('kategori_perhitungan') === 'emas_logam_mulia'),
                'nullable',
                'numeric',
                'min:0.01',
                'max:999999999',
            ],
            'berat_panen_kg' => [
                Rule::requiredIf(fn (): bool => in_array(
                    $request->input('jenis_ziswaf'),
                    ['zakat_pertanian_berbiaya', 'zakat_pertanian_alami'],
                    true
                )),
                'nullable',
                'numeric',
                'min:0.01',
                'max:999999999',
            ],
            'wakaf_type' => [
                Rule::requiredIf(fn (): bool => $request->input('jenis_ziswaf') === 'wakaf'),
                'nullable',
                Rule::in(['permanen', 'temporer']),
            ],
            'wakaf_return_date' => [
                Rule::requiredIf(fn (): bool => $request->input('wakaf_type') === 'temporer'),
                'nullable',
                'date',
                'after:today',
            ],
        ];
        if (! $paymentGatewayReady) {
            $rules['bukti_pembayaran'] = [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
            ];
        }
        $messages = [
            'organization_id.required' => 'Masjid tujuan wajib dipilih.',
            'organization_id.exists' => 'Masjid tujuan tidak tersedia atau sedang tidak aktif.',
            'jenis_ziswaf.required' => 'Jenis transaksi wajib dipilih.',
            'jenis_ziswaf.in' => 'Jenis transaksi tidak valid.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.integer' => 'Nominal harus berupa angka.',
            'nominal.min' => $paymentGatewayReady
                ? 'Minimal nominal pembayaran melalui Midtrans adalah Rp10.000.'
                : 'Minimal nominal transaksi adalah Rp1.000.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran tidak valid.',
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diunggah untuk pembayaran manual.',
            'bukti_pembayaran.file' => 'Bukti pembayaran harus berupa file.',
            'bukti_pembayaran.mimes' => 'Bukti pembayaran harus berformat JPG, JPEG, PNG, atau PDF.',
            'bukti_pembayaran.max' => 'Ukuran bukti pembayaran maksimal 2 MB.',
            'tanggal_mulai_kepemilikan.required' => 'Tanggal mulai kepemilikan harta atau barang wajib diisi untuk menghitung haul.',
            'tanggal_mulai_kepemilikan.date' => 'Tanggal mulai kepemilikan tidak valid.',
            'tanggal_mulai_kepemilikan.before_or_equal' => 'Tanggal mulai kepemilikan tidak boleh melewati hari ini.',
            'kategori_perhitungan.required' => 'Kategori perhitungan wajib dipilih.',
            'kategori_perhitungan.in' => 'Kategori perhitungan tidak sesuai dengan jenis zakat yang dipilih.',
            'periode_penghasilan.required' => 'Metode perhitungan penghasilan wajib dipilih.',
            'periode_penghasilan.in' => 'Metode perhitungan penghasilan harus bulanan atau tahunan.',
            'bulan_penghasilan.required' => 'Bulan penghasilan wajib dipilih untuk metode bulanan.',
            'bulan_penghasilan.date_format' => 'Format bulan penghasilan tidak valid.',
            'tahun_penghasilan.required' => 'Tahun penghasilan wajib dipilih.',
            'tahun_penghasilan.integer' => 'Tahun penghasilan tidak valid.',
            'tahun_penghasilan.max' => 'Tahun penghasilan tidak boleh melewati tahun berjalan.',
            'penghasilan_bersih.required' => 'Penghasilan atau pendapatan bersih wajib diisi.',
            'penghasilan_bersih.integer' => 'Penghasilan atau pendapatan bersih harus berupa angka.',
            'penghasilan_bersih.min' => 'Penghasilan atau pendapatan bersih harus lebih dari nol.',
            'berat_emas_gram.required' => 'Berat emas wajib diisi untuk menghitung zakat emas.',
            'berat_emas_gram.numeric' => 'Berat emas harus berupa angka.',
            'berat_emas_gram.min' => 'Berat emas minimal 0,01 gram.',
            'berat_panen_kg.required' => 'Berat hasil panen wajib diisi.',
            'berat_panen_kg.numeric' => 'Berat hasil panen harus berupa angka.',
            'berat_panen_kg.min' => 'Berat hasil panen minimal 0,01 kilogram.',
        ];
        $validated = $request->validate($rules, $messages);
        $user = $request->user();

        $batasAwalHaul = $ketentuanZakat
            ? $this->haulCutoffDate($ketentuanZakat->haul)
            : null;
        $memenuhiHaul = $validated['jenis_ziswaf'] !== 'zakat_maal'
            || ($batasAwalHaul && Carbon::parse($validated['tanggal_mulai_kepemilikan'])->lte($batasAwalHaul));

        if ($validated['jenis_ziswaf'] === 'zakat_maal' && ! $batasAwalHaul) {
            throw ValidationException::withMessages([
                'tanggal_mulai_kepemilikan' => 'Format haul pada kebijakan admin belum dapat dihitung. Hubungi admin untuk memperbaiki aturan haul.',
            ]);
        }

        if (! $memenuhiHaul) {
            throw ValidationException::withMessages([
                'tanggal_mulai_kepemilikan' => sprintf(
                    'Harta atau barang belum memenuhi haul %s. Agar memenuhi haul hari ini, kepemilikan harus dimulai paling lambat %s.',
                    $ketentuanZakat->haul,
                    $batasAwalHaul->translatedFormat('d F Y')
                ),
            ]);
        }

        $rincianPerhitunganKhusus = [];
        $hargaEmas = null;
        $hargaPertanianAktif = null;
        $nisabEmasGramAcuan = 0;
        $nisabRupiahAcuan = null;
        $hargaEmasPerGramAcuan = 0;

        if ($ketentuanZakat && in_array($ketentuanZakat->jenis, ['maal', 'penghasilan'], true)) {
            $hargaEmas = $this->activeGoldPrice();
            $nisabEmasGramAcuan = $this->extractGoldNisabGrams($ketentuanZakat->nisab_pokok);
            $hargaEmasPerGramAcuan = $this->resolveGoldPricePerGram($hargaEmas);

            if ($nisabEmasGramAcuan <= 0 || $hargaEmasPerGramAcuan <= 0) {
                $field = $ketentuanZakat->jenis === 'penghasilan'
                    ? 'periode_penghasilan'
                    : 'kategori_perhitungan';

                throw ValidationException::withMessages([
                    $field => 'Harga emas aktif belum tersedia. Admin harus memperbarui harga emas pada Master Barang & Harga.',
                ]);
            }

            $nisabRupiahAcuan = (int) round($nisabEmasGramAcuan * $hargaEmasPerGramAcuan);
        }

        if ($validated['jenis_ziswaf'] === 'zakat_penghasilan') {
            $periodePenghasilan = $validated['periode_penghasilan'];
            $bulanPenghasilan = $periodePenghasilan === 'bulanan'
                ? $validated['bulan_penghasilan']
                : null;
            $tahunPenghasilan = $periodePenghasilan === 'bulanan'
                ? (int) substr($bulanPenghasilan, 0, 4)
                : (int) $validated['tahun_penghasilan'];

            if ($periodePenghasilan === 'bulanan' && $bulanPenghasilan > now()->format('Y-m')) {
                throw ValidationException::withMessages([
                    'bulan_penghasilan' => 'Bulan penghasilan tidak boleh melewati bulan berjalan.',
                ]);
            }

            $pembayaranTerverifikasi = $this->incomeZakatPaymentSummary((int) $user->id);
            $zakatSudahDibayar = $periodePenghasilan === 'bulanan'
                ? (int) ($pembayaranTerverifikasi['per_bulan'][$bulanPenghasilan] ?? 0)
                : (int) ($pembayaranTerverifikasi['per_tahun'][(string) $tahunPenghasilan] ?? 0);

            $ketentuanPerhitungan = clone $ketentuanZakat;
            $ketentuanPerhitungan->setAttribute('nisab_rupiah', $nisabRupiahAcuan);

            $perhitunganPenghasilan = $this->zakatCalculator->penghasilan([
                'periode_penghasilan' => $periodePenghasilan,
                'penghasilan_bersih' => $validated['penghasilan_bersih'],
                'zakat_sudah_dibayar' => $zakatSudahDibayar,
            ], $ketentuanPerhitungan);

            if ($perhitunganPenghasilan['penghasilan_bruto'] <= 0) {
                throw ValidationException::withMessages([
                    'penghasilan_bersih' => 'Penghasilan atau pendapatan bersih harus lebih dari nol.',
                ]);
            }

            if (! $perhitunganPenghasilan['memenuhi_nisab']) {
                throw ValidationException::withMessages([
                    'penghasilan_bersih' => sprintf(
                        'Penghasilan bersih belum mencapai nisab %s sebesar Rp%s.',
                        $periodePenghasilan,
                        number_format((int) $perhitunganPenghasilan['nisab'], 0, ',', '.')
                    ),
                ]);
            }

            if ($perhitunganPenghasilan['jumlah_zakat'] <= 0) {
                throw ValidationException::withMessages([
                    'periode_penghasilan' => 'Kewajiban zakat penghasilan pada periode ini sudah terpenuhi berdasarkan pembayaran yang telah diverifikasi.',
                ]);
            }

            $validated['tahun_penghasilan'] = $tahunPenghasilan;
            $validated['nominal'] = (int) $perhitunganPenghasilan['jumlah_zakat'];
            $rincianPerhitunganKhusus = array_merge($perhitunganPenghasilan, [
                'catatan' => $periodePenghasilan === 'bulanan'
                    ? 'Zakat penghasilan bulanan dihitung dari nisab tahunan aktif dibagi 12.'
                    : 'Zakat penghasilan tahunan direkonsiliasi dengan pembayaran terverifikasi pada tahun yang sama.',
                'periode_penghasilan' => $periodePenghasilan,
                'bulan_penghasilan' => $bulanPenghasilan,
                'tahun_penghasilan' => $tahunPenghasilan,
                'nisab_emas_gram' => $nisabEmasGramAcuan,
                'harga_emas_per_gram' => $hargaEmasPerGramAcuan,
                'sumber_harga_emas' => $hargaEmas?->sumber_harga,
            ]);
        }

        if (($validated['kategori_perhitungan'] ?? null) === 'emas_logam_mulia') {
            $nisabEmasGram = $nisabEmasGramAcuan;
            $hargaEmasPerGram = $this->resolveGoldPricePerGram($hargaEmas);
            $beratEmasGram = (float) $validated['berat_emas_gram'];

            if ($nisabEmasGram <= 0 || $hargaEmasPerGram <= 0) {
                throw ValidationException::withMessages([
                    'berat_emas_gram' => 'Harga emas atau nisab emas belum ditetapkan pada kebijakan admin.',
                ]);
            }

            if ($beratEmasGram < $nisabEmasGram) {
                throw ValidationException::withMessages([
                    'berat_emas_gram' => sprintf(
                        'Berat emas belum mencapai nisab %s gram.',
                        rtrim(rtrim(number_format($nisabEmasGram, 2, ',', '.'), '0'), ',')
                    ),
                ]);
            }

            $nilaiEmasRupiah = (int) round($beratEmasGram * $hargaEmasPerGram);
            $nisabEmasRupiah = (int) round($nisabEmasGram * $hargaEmasPerGram);
            $zakatEmas = (int) round(
                $nilaiEmasRupiah * ((float) $ketentuanZakat->kadar_persentase / 100)
            );

            $validated['nominal'] = $zakatEmas;
            $rincianPerhitunganKhusus = [
                'catatan' => 'Zakat emas dihitung otomatis dari berat emas, harga per gram, nisab, kadar zakat, dan haul aktif.',
                'berat_emas_gram' => $beratEmasGram,
                'harga_emas_per_gram' => $hargaEmasPerGram,
                'nilai_emas_rupiah' => $nilaiEmasRupiah,
                'nisab_emas_gram' => $nisabEmasGram,
                'nisab_emas_rupiah' => $nisabEmasRupiah,
                'jumlah_zakat_dihitung' => $zakatEmas,
                'sumber_harga_emas' => $hargaEmas?->sumber_harga ?? 'Nilai nisab rupiah kebijakan zakat',
            ];
        }

        if (in_array($validated['jenis_ziswaf'], ['zakat_pertanian_berbiaya', 'zakat_pertanian_alami'], true)) {
            $kategoriPertanian = $validated['kategori_perhitungan'];
            $beratPanenKg = (float) $validated['berat_panen_kg'];
            $nisabPanenKg = $this->agriculturalNisabKg($kategoriPertanian);
            $hargaPertanianAktif = $this->activeAgriculturalPrice($kategoriPertanian);
            $hargaPerKg = (int) ($hargaPertanianAktif?->harga_per_satuan ?? 0);

            if ($hargaPerKg <= 0) {
                throw ValidationException::withMessages([
                    'berat_panen_kg' => 'Harga aktif hasil pertanian belum ditetapkan pada Master Barang & Harga.',
                ]);
            }

            if ($beratPanenKg < $nisabPanenKg) {
                throw ValidationException::withMessages([
                    'berat_panen_kg' => sprintf(
                        'Hasil panen belum mencapai nisab %s kilogram.',
                        rtrim(rtrim(number_format($nisabPanenKg, 2, ',', '.'), '0'), ',')
                    ),
                ]);
            }

            $nilaiPanenRupiah = (int) round($beratPanenKg * $hargaPerKg);
            $nisabPanenRupiah = (int) round($nisabPanenKg * $hargaPerKg);
            $persentaseZakat = (float) $ketentuanZakat->kadar_persentase;
            $zakatPanenKg = $beratPanenKg * ($persentaseZakat / 100);
            $zakatPanenRupiah = (int) round($nilaiPanenRupiah * ($persentaseZakat / 100));

            $validated['nominal'] = $zakatPanenRupiah;
            $rincianPerhitunganKhusus = [
                'catatan' => 'Zakat pertanian dihitung otomatis dari berat panen, harga per kilogram, nisab, dan metode pengairan.',
                'berat_panen_kg' => $beratPanenKg,
                'harga_per_kg' => $hargaPerKg,
                'nilai_panen_rupiah' => $nilaiPanenRupiah,
                'nisab_panen_kg' => $nisabPanenKg,
                'nisab_pertanian_rupiah' => $nisabPanenRupiah,
                'zakat_panen_kg' => $zakatPanenKg,
                'jumlah_zakat_dihitung' => $zakatPanenRupiah,
                'metode_pengairan' => $validated['jenis_ziswaf'] === 'zakat_pertanian_berbiaya'
                    ? 'berbiaya'
                    : 'alami',
                'sumber_harga_pertanian' => $hargaPertanianAktif->sumber_harga,
            ];
        }

        $snapshotKebijakan = $ketentuanZakat?->toSnapshot();

        if ($snapshotKebijakan && $nisabRupiahAcuan) {
            $snapshotKebijakan['nisab_emas_gram'] = $nisabEmasGramAcuan;
            $snapshotKebijakan['nisab_rupiah'] = $nisabRupiahAcuan;
            $snapshotKebijakan['sumber_nisab'] = 'harga_emas_aktif';
        }

        if ($validated['jenis_ziswaf'] === 'zakat_penghasilan' && $snapshotKebijakan) {
            $snapshotKebijakan['nisab_tahunan'] = $rincianPerhitunganKhusus['nisab_tahunan'];
            $snapshotKebijakan['nisab_bulanan'] = $rincianPerhitunganKhusus['nisab_bulanan'];
            $snapshotKebijakan['metode_perhitungan'] = $rincianPerhitunganKhusus['periode_penghasilan'];
        }

        if ($hargaEmas && $snapshotKebijakan) {
            $snapshotKebijakan['harga_emas'] = [
                'harga_barang_zakat_id' => $hargaEmas->id,
                'barang_zakat_id' => $hargaEmas->barang_zakat_id,
                'harga_per_gram' => (int) $hargaEmas->harga_per_satuan,
                'wilayah' => $hargaEmas->wilayah,
                'berlaku_mulai' => $hargaEmas->berlaku_mulai?->toDateString(),
                'sumber_harga' => $hargaEmas->sumber_harga,
            ];
        }

        if ($hargaPertanianAktif && $snapshotKebijakan) {
            $snapshotKebijakan['harga_pertanian'] = [
                'harga_barang_zakat_id' => $hargaPertanianAktif->id,
                'barang_zakat_id' => $hargaPertanianAktif->barang_zakat_id,
                'harga_per_kg' => (int) $hargaPertanianAktif->harga_per_satuan,
                'wilayah' => $hargaPertanianAktif->wilayah,
                'berlaku_mulai' => $hargaPertanianAktif->berlaku_mulai?->toDateString(),
                'sumber_harga' => $hargaPertanianAktif->sumber_harga,
            ];
        }

        if ($validated['jenis_ziswaf'] === 'zakat_fitrah' && $snapshotKebijakan) {
            $hargaBeras = $this->activeRicePrice();
            $snapshotKebijakan['harga_beras'] = $hargaBeras ? [
                'harga_barang_zakat_id' => $hargaBeras->id,
                'barang_zakat_id' => $hargaBeras->barang_zakat_id,
                'harga_per_kg' => (int) $hargaBeras->harga_per_satuan,
                'wilayah' => $hargaBeras->wilayah,
                'berlaku_mulai' => $hargaBeras->berlaku_mulai?->toDateString(),
                'sumber_harga' => $hargaBeras->sumber_harga,
            ] : null;
        }

        // Format pendek: ZSF-{base36 dari unix timestamp}-{3 digit random}
        // Contoh: ZSF-l8n4kx-427 (~14 karakter, unik, sesuai standar Midtrans)
        $orderId = 'ZSF-'.base_convert((string) now()->timestamp, 10, 36).'-'.random_int(100, 999);
        $buktiPembayaranPath = null;
        if (! $paymentGatewayReady && $request->hasFile('bukti_pembayaran')) {
            $buktiPembayaranPath = $request->file('bukti_pembayaran')
                ->store('bukti-pembayaran-ziswaf', 'public');
        }
        $transaksi = ZiswafPenerimaan::create([
            'organization_id' => (int) $validated['organization_id'],
            'order_id' => $orderId,
            'payment_gateway' => $paymentGatewayReady ? 'midtrans' : 'manual',
            'muzakki_id' => $user->id,
            'tanggal' => now()->toDateString(),
            'jenis_ziswaf' => $validated['jenis_ziswaf'],
            'restriction_type' => null,
            'wakaf_type' => $validated['jenis_ziswaf'] === 'wakaf'
                ? $validated['wakaf_type']
                : null,
            'wakaf_return_date' => ($validated['wakaf_type'] ?? null) === 'temporer'
                ? $validated['wakaf_return_date']
                : null,
            'nominal' => $validated['nominal'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'payment_status' => $paymentGatewayReady ? 'pending' : 'manual_pending',
            'status_verifikasi' => 'pending',
            'bukti_pembayaran' => $buktiPembayaranPath,
            'keterangan' => $validated['keterangan'] ?? null,
            'rincian_perhitungan' => array_merge([
                'catatan' => $ketentuanZakat
                    ? 'Ketentuan zakat aktif disimpan saat transaksi; nominal dikonfirmasi oleh jamaah.'
                    : 'Nominal mengikuti input jamaah.',
                'tanggal_mulai_kepemilikan' => $validated['tanggal_mulai_kepemilikan'] ?? null,
                'batas_awal_haul' => $batasAwalHaul?->toDateString(),
                'memenuhi_haul' => $ketentuanZakat?->jenis === 'maal' ? $memenuhiHaul : null,
                'kategori_perhitungan' => $validated['kategori_perhitungan'] ?? null,
                'kategori_perhitungan_label' => isset($validated['kategori_perhitungan'])
                    ? ($calculationCategories[$validated['jenis_ziswaf']][$validated['kategori_perhitungan']] ?? null)
                    : null,
            ], $rincianPerhitunganKhusus),
            'nisab_digunakan' => $rincianPerhitunganKhusus['nisab_emas_rupiah']
                ?? $rincianPerhitunganKhusus['nisab_pertanian_rupiah']
                ?? $rincianPerhitunganKhusus['nisab']
                ?? $nisabRupiahAcuan,
            'persentase_zakat' => $ketentuanZakat?->kadar_persentase,
            'persentase_amil' => $ketentuanZakat?->persentase_amil ?? 0,
            'snapshot_kebijakan' => $snapshotKebijakan,
        ]);
        if (! $paymentGatewayReady) {
            return redirect()
                ->route('jamaah.riwayat.index')
                ->with('success', $config['successMessage'].' Menunggu verifikasi admin.');
        }
        $this->configureMidtrans();
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $validated['nominal'],
            ],
            'enabled_payments' => $this->enabledPaymentsFor(
                $validated['metode_pembayaran'],
                $request->input('bank_va_pilihan') ?: null,
                $request->input('ewallet_pilihan') ?: null
            ),
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => $validated['jenis_ziswaf'],
                    'price' => (int) $validated['nominal'],
                    'quantity' => 1,
                    'name' => $config['jenisOptions'][$validated['jenis_ziswaf']]
                        ?? 'Transaksi ZISWAF',
                ],
            ],
            'callbacks' => [
                'finish' => route('jamaah.riwayat.index'),
            ],
        ];
        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (Throwable $exception) {
            report($exception);
            $transaksi->update([
                'payment_status' => 'gateway_error',
                'status_verifikasi' => 'pending',
                'catatan_verifikasi' => 'Gagal membuat token pembayaran Midtrans. Silakan ulangi pembayaran atau hubungi admin.',
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'metode_pembayaran' => 'Gagal menghubungkan ke Midtrans. Periksa Server Key, Client Key, mode Sandbox/Production, dan koneksi internet.',
                ]);
        }
        $transaksi->update([
            'snap_token' => $snapToken,
        ]);

        return redirect()
            ->route('jamaah.pembayaran.show', $transaksi)
            ->with('success', 'Transaksi berhasil dibuat. Silakan lanjutkan pembayaran.');
    }

    public function showPembayaran(Request $request, ZiswafPenerimaan $transaksi)
    {
        abort_unless(
            (int) $transaksi->muzakki_id === (int) $request->user()->id,
            403
        );
        if ($transaksi->payment_gateway !== 'midtrans') {
            return redirect()
                ->route('jamaah.riwayat.index')
                ->with('warning', 'Transaksi ini menggunakan pembayaran manual dan menunggu verifikasi admin.');
        }
        if (! $this->isPaymentGatewayReady() || ! $transaksi->snap_token) {
            return redirect()
                ->route('jamaah.riwayat.index')
                ->with('warning', 'Payment gateway belum aktif atau token pembayaran belum tersedia.');
        }

        return view('jamaah.pembayaran-midtrans', [
            'transaksi' => $transaksi,
            'clientKey' => config('services.midtrans.client_key'),
            'isProduction' => (bool) config('services.midtrans.is_production'),
            'jenisLabels' => $this->jenisLabels(),
            'metodeLabels' => $this->metodeLabels(),
        ]);
    }

    public function batalPembayaran(
        Request $request,
        ZiswafPenerimaan $transaksi
    ) {
        abort_unless(
            (int) $transaksi->muzakki_id === (int) $request->user()->id,
            403
        );

        // Hanya bisa dibatalkan jika masih pending dan menggunakan Midtrans.
        $bisaBatal = $transaksi->payment_gateway === 'midtrans'
            && ! empty($transaksi->snap_token)
            && in_array(
                $transaksi->status_verifikasi,
                ['pending', null],
                true
            )
            && in_array(
                $transaksi->payment_status,
                ['pending', null, ''],
                true
            );

        if (! $bisaBatal) {
            return redirect()
                ->route('jamaah.riwayat.index')
                ->with(
                    'warning',
                    'Transaksi ini tidak dapat dibatalkan karena sudah diproses atau tidak memenuhi syarat pembatalan.'
                );
        }

        /*
         * Batalkan transaksi di Midtrans agar tidak meninggalkan
         * transaksi aktif pada dashboard Midtrans.
         */
        if ($this->isPaymentGatewayReady()) {
            try {
                $this->configureMidtrans();

                Transaction::cancel($transaksi->order_id);
            } catch (Throwable $exception) {
                /*
                 * Order mungkin sudah kedaluwarsa, sudah dibatalkan,
                 * atau belum tersedia di Midtrans. Pembatalan lokal
                 * tetap dilanjutkan.
                 */
                report($exception);
            }
        }

        $transaksi->update([
            'payment_status' => 'cancel',
            'status_verifikasi' => 'dibatalkan',
            'catatan_verifikasi' => 'Dibatalkan oleh jamaah.',
            'snap_token' => null,
        ]);

        return redirect()
            ->route('jamaah.riwayat.index')
            ->with('success', 'Transaksi berhasil dibatalkan.');
    }

    /**
     * Mengecek status terkini transaksi melalui Midtrans dan
     * menyinkronkannya dengan database lokal.
     *
     * Fitur ini menjadi fallback ketika notifikasi webhook
     * dari Midtrans tidak diterima.
     */
    public function cekStatusPembayaran(
        Request $request,
        ZiswafPenerimaan $transaksi
    ) {
        abort_unless(
            (int) $transaksi->muzakki_id === (int) $request->user()->id,
            403
        );

        if ($transaksi->payment_gateway !== 'midtrans') {
            return redirect()
                ->route('jamaah.riwayat.index')
                ->with(
                    'warning',
                    'Cek status hanya tersedia untuk transaksi melalui Midtrans.'
                );
        }

        if (! $this->isPaymentGatewayReady()) {
            return redirect()
                ->route('jamaah.riwayat.index')
                ->with('warning', 'Payment gateway belum aktif.');
        }

        try {
            $this->configureMidtrans();

            $status = Transaction::status(
                $transaksi->order_id
            );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->with(
                    'warning',
                    'Gagal menghubungi Midtrans. Periksa koneksi atau coba beberapa saat lagi.'
                );
        }

        $transactionStatus = (string) (
            $status->transaction_status ?? ''
        );

        $fraudStatus = $status->fraud_status ?? null;
        $paymentType = $status->payment_type ?? null;
        $transactionId = $status->transaction_id ?? null;

        /*
         * Jangan menurunkan status transaksi yang sudah berhasil
         * kembali menjadi pending.
         */
        if (
            in_array(
                $transaksi->payment_status,
                ['settlement', 'capture'],
                true
            )
            && $transactionStatus === 'pending'
        ) {
            return redirect()
                ->back()
                ->with(
                    'success',
                    'Status transaksi sudah terkonfirmasi sebelumnya. Tidak ada perubahan.'
                );
        }

        if (
            $transactionStatus === 'settlement'
            || (
                $transactionStatus === 'capture'
                && in_array(
                    $fraudStatus,
                    ['accept', null, ''],
                    true
                )
            )
        ) {
            $transaksi->update([
                'payment_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'fraud_status' => $fraudStatus,
                'status_verifikasi' => 'diterima',
                'catatan_verifikasi' => 'Pembayaran terkonfirmasi melalui cek status manual.',
                'verified_at' => now(),
                'paid_at' => $transaksi->paid_at ?? now(),
            ]);

            return redirect()
                ->route('jamaah.riwayat.index')
                ->with(
                    'success',
                    'Pembayaran terkonfirmasi! Transaksi berhasil diterima.'
                );
        }

        if ($transactionStatus === 'pending') {
            $transaksi->update([
                'payment_status' => 'pending',
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'fraud_status' => $fraudStatus,
            ]);

            return redirect()
                ->back()
                ->with(
                    'warning',
                    'Status pembayaran masih menunggu (pending). Silakan selesaikan pembayaran terlebih dahulu.'
                );
        }

        if (
            in_array(
                $transactionStatus,
                ['deny', 'cancel', 'expire', 'failure'],
                true
            )
        ) {
            $transaksi->update([
                'payment_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'fraud_status' => $fraudStatus,
                'status_verifikasi' => 'ditolak',
                'catatan_verifikasi' => 'Pembayaran gagal, dibatalkan, atau kedaluwarsa (sinkron dari cek status manual).',
                'verified_at' => now(),
                'snap_token' => null,
            ]);

            return redirect()
                ->route('jamaah.riwayat.index')
                ->with(
                    'warning',
                    'Pembayaran '
                        .$transactionStatus
                        .'. Transaksi telah ditandai sebagai ditolak.'
                );
        }

        return redirect()
            ->back()
            ->with(
                'warning',
                'Status Midtrans: '
                    .($transactionStatus ?: 'tidak diketahui')
                    .'. Tidak ada perubahan yang dilakukan.'
            );
    }

    /**
     * Poll status pembayaran Midtrans — mengembalikan JSON.
     * Digunakan oleh polling otomatis di halaman pembayaran
     * sehingga jamaah tidak perlu menekan tombol "Cek Status" secara manual.
     */
    public function pollStatusPembayaran(
        Request $request,
        ZiswafPenerimaan $transaksi
    ) {
        abort_unless(
            (int) $transaksi->muzakki_id === (int) $request->user()->id,
            403
        );

        if ($transaksi->payment_gateway !== 'midtrans') {
            return response()->json(['status' => 'not_midtrans'], 200);
        }

        if (! $this->isPaymentGatewayReady()) {
            return response()->json(['status' => 'gateway_disabled'], 200);
        }

        // Jika transaksi sudah selesai di sisi lokal, kembalikan langsung tanpa
        // memanggil Midtrans API lagi
        if (in_array($transaksi->payment_status, ['settlement', 'capture'], true)) {
            return response()->json([
                'status' => 'paid',
                'redirect_url' => route('jamaah.riwayat.index'),
            ]);
        }

        if (in_array($transaksi->payment_status, ['deny', 'cancel', 'expire', 'failure'], true)) {
            return response()->json([
                'status' => 'failed',
                'redirect_url' => route('jamaah.riwayat.index'),
            ]);
        }

        try {
            $this->configureMidtrans();
            $statusResponse = Transaction::status($transaksi->order_id);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['status' => 'error', 'message' => 'Gagal menghubungi Midtrans.'], 200);
        }

        $transactionStatus = (string) ($statusResponse->transaction_status ?? '');
        $fraudStatus = $statusResponse->fraud_status ?? null;
        $paymentType = $statusResponse->payment_type ?? null;
        $transactionId = $statusResponse->transaction_id ?? null;

        if (
            $transactionStatus === 'settlement'
            || (
                $transactionStatus === 'capture'
                && in_array($fraudStatus, ['accept', null, ''], true)
            )
        ) {
            $transaksi->update([
                'payment_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'fraud_status' => $fraudStatus,
                'status_verifikasi' => 'diterima',
                'catatan_verifikasi' => 'Pembayaran terkonfirmasi otomatis melalui polling status.',
                'verified_at' => now(),
                'paid_at' => $transaksi->paid_at ?? now(),
            ]);

            return response()->json([
                'status' => 'paid',
                'redirect_url' => route('jamaah.riwayat.index'),
            ]);
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true)) {
            $transaksi->update([
                'payment_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'fraud_status' => $fraudStatus,
                'status_verifikasi' => 'ditolak',
                'catatan_verifikasi' => 'Pembayaran gagal/kedaluwarsa (deteksi otomatis).',
                'verified_at' => now(),
                'snap_token' => null,
            ]);

            return response()->json([
                'status' => 'failed',
                'redirect_url' => route('jamaah.riwayat.index'),
            ]);
        }

        // masih pending
        return response()->json(['status' => 'pending']);
    }

    public function midtransNotification(Request $request)
    {
        if (! $this->isPaymentGatewayReady()) {
            return response()->json([
                'message' => 'Payment gateway is disabled.',
            ], 403);
        }
        $serverKey = config('services.midtrans.server_key');
        $orderId = (string) $request->input('order_id');
        $statusCode = (string) $request->input('status_code');
        $grossAmount = (string) $request->input('gross_amount');
        $signatureKey = (string) $request->input('signature_key');
        $validSignature = hash(
            'sha512',
            $orderId.$statusCode.$grossAmount.$serverKey
        );
        if (! hash_equals($validSignature, $signatureKey)) {
            return response()->json([
                'message' => 'Invalid signature.',
            ], 403);
        }
        $transaksi = ZiswafPenerimaan::where('order_id', $orderId)->first();
        if (! $transaksi) {
            return response()->json([
                'message' => 'Transaksi tidak ditemukan.',
            ], 404);
        }
        $transactionStatus = (string) $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');
        $paymentType = $request->input('payment_type');
        $transactionId = $request->input('transaction_id');
        if (
            in_array($transaksi->payment_status, ['settlement', 'capture'], true)
            && $transactionStatus === 'pending'
        ) {
            return response()->json([
                'message' => 'Notification ignored because transaction already paid.',
            ]);
        }
        if (
            $transactionStatus === 'settlement'
            || ($transactionStatus === 'capture' && in_array($fraudStatus, ['accept', null, ''], true))
        ) {
            $transaksi->update([
                'payment_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'fraud_status' => $fraudStatus,
                'status_verifikasi' => 'diterima',
                'catatan_verifikasi' => 'Pembayaran otomatis terkonfirmasi melalui payment gateway.',
                'verified_at' => now(),
                'paid_at' => $transaksi->paid_at ?? now(),
            ]);

            return response()->json([
                'message' => 'Payment accepted.',
            ]);
        }
        if ($transactionStatus === 'pending') {
            $transaksi->update([
                'payment_status' => 'pending',
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'fraud_status' => $fraudStatus,
                'status_verifikasi' => 'pending',
            ]);

            return response()->json([
                'message' => 'Payment pending.',
            ]);
        }
        if (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true)) {
            $transaksi->update([
                'payment_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'transaction_id' => $transactionId,
                'fraud_status' => $fraudStatus,
                'status_verifikasi' => 'ditolak',
                'catatan_verifikasi' => 'Pembayaran gagal, dibatalkan, atau kedaluwarsa melalui payment gateway.',
                'verified_at' => now(),
            ]);

            return response()->json([
                'message' => 'Payment failed.',
            ]);
        }
        $transaksi->update([
            'payment_status' => $transactionStatus ?: $transaksi->payment_status,
            'payment_type' => $paymentType,
            'transaction_id' => $transactionId,
            'fraud_status' => $fraudStatus,
        ]);

        return response()->json([
            'message' => 'Notification received.',
        ]);
    }

    private function penerimaanResmiQuery(): Builder
    {
        return ZiswafPenerimaan::query()
            ->where('status_verifikasi', 'diterima');
    }

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
            ->with('organization')
            ->where('muzakki_id', $request->user()->id);
        if (! empty($filters['q'])) {
            $search = trim($filters['q']);
            $referenceId = null;
            if (preg_match('/(?:ZSF-|ZISWAF-)?([a-z0-9]+)/i', $search, $matches)) {
                $referenceId = (int) $matches[1];
            }
            $query->where(function ($builder) use (
                $search,
                $referenceId
            ): void {
                $builder->where('keterangan', 'like', '%'.$search.'%')
                    ->orWhere('order_id', 'like', '%'.$search.'%');

                if ($referenceId !== null && $referenceId > 0) {
                    $builder->orWhere('id', $referenceId);
                }
            });
        }
        if (! empty($filters['jenis'])) {
            $query->where('jenis_ziswaf', $filters['jenis']);
        }
        if (! empty($filters['status'])) {
            if ($filters['status'] === 'pending') {
                $query->where(function ($builder): void {
                    $builder->where('status_verifikasi', 'pending')
                        ->orWhereNull('status_verifikasi');
                });
            } else {
                $query->where('status_verifikasi', $filters['status']);
            }
        }
        if (! empty($filters['metode'])) {
            $query->where('metode_pembayaran', $filters['metode']);
        }
        if (! empty($filters['tanggal_mulai'])) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_mulai']);
        }
        if (! empty($filters['tanggal_selesai'])) {
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

    private function isPaymentGatewayReady(): bool
    {
        return (bool) config('services.midtrans.enabled')
            && filled(config('services.midtrans.server_key'))
            && filled(config('services.midtrans.client_key'));
    }

    private function configureMidtrans(): void
    {
        MidtransConfig::$serverKey = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production');
        MidtransConfig::$isSanitized = (bool) config('services.midtrans.is_sanitized');
        MidtransConfig::$is3ds = (bool) config('services.midtrans.is_3ds');
    }

    private function enabledPaymentsFor(
        string $metode,
        ?string $bankVaPilihan = null,
        ?string $ewalletPilihan = null
    ): array {
        $validBankVa = ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va'];
        $validEwallet = ['gopay', 'shopeepay', 'dana'];

        return match ($metode) {
            'qris' => [
                'qris',
                'other_qris',
            ],
            'virtual_account' => in_array($bankVaPilihan, $validBankVa, true)
                ? [$bankVaPilihan]
                : ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va'],
            'e_wallet' => in_array($ewalletPilihan, $validEwallet, true)
                ? [$ewalletPilihan]
                : ['gopay', 'shopeepay', 'dana'],
            default => [
                'qris',
                'other_qris',
                'bca_va',
                'bni_va',
                'bri_va',
                'permata_va',
                'other_va',
                'gopay',
                'shopeepay',
                'dana',
            ],
        };
    }

    private function transaksiConfig(string $jenis): array
    {
        $paymentGatewayReady = $this->isPaymentGatewayReady();
        $metodeOptions = $paymentGatewayReady
            ? [
                'qris' => 'QRIS',
                'virtual_account' => 'Virtual Account',
                'e_wallet' => 'E-Wallet',
            ]
            : [
                'manual_transfer' => 'Transfer Bank Manual',
                'qris_manual' => 'QRIS Manual',
            ];

        return match ($jenis) {
            'zakat' => [
                'title' => 'Transaksi Zakat',
                'subtitle' => 'Hitung dan tunaikan zakat dengan ketentuan aktif yang sama seperti pengaturan admin.',
                'jenisOptions' => $this->activeZakatTypeOptions(),
                'metodeOptions' => $metodeOptions,
                'successMessage' => 'Transaksi zakat berhasil dibuat.',
            ],
            'infak' => [
                'title' => 'Transaksi Infak',
                'subtitle' => 'Catat transaksi infak jamaah.',
                'jenisOptions' => [
                    'infaq' => 'Infak',
                ],
                'metodeOptions' => $metodeOptions,
                'successMessage' => 'Transaksi infak berhasil dibuat.',
            ],
            'wakaf' => [
                'title' => 'Transaksi Wakaf',
                'subtitle' => 'Catat transaksi wakaf jamaah.',
                'jenisOptions' => [
                    'wakaf' => 'Wakaf',
                ],
                'metodeOptions' => $metodeOptions,
                'successMessage' => 'Transaksi wakaf berhasil dibuat.',
            ],
            default => abort(404),
        };
    }

    private function activeZakatTypeOptions(): array
    {
        $options = [];

        foreach ($this->jamaahZakatTypeMap() as $transactionType => $policyType) {
            $policy = KetentuanPokokZakat::untukJenis($policyType);

            if ($policy) {
                $options[$transactionType] = $policy->nama;
            }
        }

        return $options;
    }

    private function zakatPoliciesForJamaah(?HargaBarangZakat $hargaEmas): array
    {
        $policies = [];

        foreach ($this->jamaahZakatTypeMap() as $transactionType => $policyType) {
            $policy = KetentuanPokokZakat::untukJenis($policyType);

            if (! $policy) {
                continue;
            }

            $nisabEmasGram = in_array($policy->jenis, ['maal', 'penghasilan'], true)
                ? $this->extractGoldNisabGrams($policy->nisab_pokok)
                : 0;
            $nisabRupiah = $nisabEmasGram > 0 && $hargaEmas
                ? (int) round($nisabEmasGram * $hargaEmas->harga_per_satuan)
                : null;

            $policies[$transactionType] = array_merge($policy->toSnapshot(), [
                'nama' => $policy->nama,
                'deskripsi' => $policy->deskripsi,
                'dasar_hukum' => $policy->dasar_hukum,
                'dasar_regulasi' => $policy->dasar_regulasi,
                'nisab_rupiah' => $nisabRupiah,
                'batas_awal_haul' => $this->haulCutoffDate($policy->haul)?->toDateString(),
                'nisab_emas_gram' => $nisabEmasGram ?: null,
                'sumber_nisab' => $nisabRupiah ? 'harga_emas_aktif' : null,
            ]);
        }

        return $policies;
    }

    private function zakatPolicyForTransaction(string $transactionType): ?KetentuanPokokZakat
    {
        $policyType = $this->jamaahZakatTypeMap()[$transactionType] ?? null;

        return $policyType ? KetentuanPokokZakat::untukJenis($policyType) : null;
    }

    private function jamaahZakatTypeMap(): array
    {
        return [
            'zakat_maal' => 'maal',
            'zakat_penghasilan' => 'penghasilan',
            'zakat_fitrah' => 'fitrah',
            'zakat_pertanian_berbiaya' => 'pertanian_berbiaya',
            'zakat_pertanian_alami' => 'pertanian_alami',
        ];
    }

    private function zakatCalculationCategories(): array
    {
        return [
            'zakat_maal' => [
                'simpanan_uang_tunai' => 'Simpanan dan Uang Tunai',
                'emas_logam_mulia' => 'Emas dan Logam Mulia',
            ],
            'zakat_penghasilan' => [
                'gaji_upah' => 'Gaji atau Upah',
                'honorarium_jasa' => 'Honorarium atau Jasa Profesional',
                'bonus_tunjangan' => 'Bonus atau Tunjangan',
                'pendapatan_jasa_lainnya' => 'Pendapatan Jasa Lainnya',
            ],
            'zakat_pertanian_berbiaya' => [
                'padi_gabah' => 'Padi / Gabah',
                'beras' => 'Beras',
            ],
            'zakat_pertanian_alami' => [
                'padi_gabah' => 'Padi / Gabah',
                'beras' => 'Beras',
            ],
        ];
    }

    private function incomeZakatPaymentSummary(int $muzakkiId): array
    {
        $summary = [
            'per_bulan' => [],
            'per_tahun' => [],
        ];

        $payments = ZiswafPenerimaan::query()
            ->where('muzakki_id', $muzakkiId)
            ->where('jenis_ziswaf', 'zakat_penghasilan')
            ->where('status_verifikasi', 'diterima')
            ->get(['nominal', 'tanggal', 'rincian_perhitungan']);

        foreach ($payments as $payment) {
            $detail = $payment->rincian_perhitungan ?? [];
            $periode = $detail['periode_penghasilan'] ?? null;
            $bulan = $detail['bulan_penghasilan'] ?? $payment->tanggal?->format('Y-m');
            $tahun = (string) ($detail['tahun_penghasilan'] ?? $payment->tanggal?->year ?? '');

            if ($tahun !== '') {
                $summary['per_tahun'][$tahun] = ($summary['per_tahun'][$tahun] ?? 0)
                    + (int) $payment->nominal;
            }

            if ($periode === 'bulanan' && $bulan) {
                $summary['per_bulan'][$bulan] = ($summary['per_bulan'][$bulan] ?? 0)
                    + (int) $payment->nominal;
            }
        }

        return $summary;
    }

    private function agriculturalNisabKg(string $category): float
    {
        return $category === 'beras' ? 520 : 653;
    }

    private function activeAgriculturalPrice(string $category): ?HargaBarangZakat
    {
        if ($category === 'beras') {
            return $this->activeRicePrice();
        }

        if ($category !== 'padi_gabah') {
            return null;
        }

        $commodity = BarangZakat::query()
            ->where('aktif', true)
            ->where('kategori', 'hasil_pertanian')
            ->where('satuan_dasar', 'kg')
            ->where(function (Builder $query): void {
                $query->whereRaw('LOWER(nama) LIKE ?', ['%gabah%'])
                    ->orWhereRaw('LOWER(nama) LIKE ?', ['%padi%'])
                    ->orWhereRaw('LOWER(kode) LIKE ?', ['%gabah%'])
                    ->orWhereRaw('LOWER(kode) LIKE ?', ['%padi%']);
            })
            ->first();

        return $commodity?->harga()
            ->berlakuPada()
            ->latest('berlaku_mulai')
            ->latest('id')
            ->first();
    }

    private function activeRicePrice(): ?HargaBarangZakat
    {
        $beras = BarangZakat::query()
            ->where('aktif', true)
            ->where(function (Builder $query): void {
                $query->whereRaw('LOWER(nama) = ?', ['beras'])
                    ->orWhereRaw('LOWER(kode) = ?', ['beras']);
            })
            ->first();

        return $beras?->harga()
            ->berlakuPada()
            ->latest('berlaku_mulai')
            ->latest('id')
            ->first();
    }

    private function activeGoldPrice(): ?HargaBarangZakat
    {
        $emas = BarangZakat::query()
            ->where('aktif', true)
            ->where('kategori', 'logam_mulia')
            ->where('satuan_dasar', 'gram')
            ->where(function (Builder $query): void {
                $query->whereRaw('LOWER(nama) LIKE ?', ['%emas%'])
                    ->orWhereRaw('LOWER(kode) LIKE ?', ['%emas%']);
            })
            ->first();

        return $emas?->harga()
            ->berlakuPada()
            ->latest('berlaku_mulai')
            ->latest('id')
            ->first();
    }

    private function resolveGoldPricePerGram(?HargaBarangZakat $goldPrice): int
    {
        return $goldPrice ? (int) $goldPrice->harga_per_satuan : 0;
    }

    private function extractGoldNisabGrams(?string $nisab): float
    {
        if (! preg_match('/([\d.,]+)\s*gram/i', (string) $nisab, $matches)) {
            return 0;
        }

        $normalized = str_replace(',', '.', $matches[1]);

        return max((float) $normalized, 0);
    }

    private function haulCutoffDate(?string $haul): ?Carbon
    {
        $normalized = strtolower(trim((string) $haul));

        if (! preg_match('/(\d+)\s*(tahun|bulan|hari)/', $normalized, $matches)) {
            return null;
        }

        $amount = max((int) $matches[1], 1);
        $today = now()->startOfDay();

        return match ($matches[2]) {
            'tahun' => $today->subYearsNoOverflow($amount),
            'bulan' => $today->subMonthsNoOverflow($amount),
            'hari' => $today->subDays($amount),
        };
    }

    private function jenisLabels(): array
    {
        return [
            'zakat_maal' => 'Zakat Maal',
            'zakat_fitrah' => 'Zakat Fitrah',
            'zakat_penghasilan' => 'Zakat Penghasilan',
            'zakat_pertanian_berbiaya' => 'Zakat Pertanian (Berbiaya)',
            'zakat_pertanian_alami' => 'Zakat Pertanian (Alami)',
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
            'dibatalkan' => 'Dibatalkan',
        ];
    }

    private function metodeLabels(): array
    {
        return [
            'manual_transfer' => 'Transfer Bank Manual',
            'qris_manual' => 'QRIS Manual',
            'qris' => 'QRIS',
            'other_qris' => 'QRIS',
            'virtual_account' => 'Virtual Account',
            'e_wallet' => 'E-Wallet',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'other_va' => 'Virtual Account Lainnya',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'dana' => 'DANA',
        ];
    }

}

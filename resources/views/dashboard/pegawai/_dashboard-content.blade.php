<style>
    .role-hero {
        border-radius: 8px;
        color: #fff;
        padding: 24px;
        background: linear-gradient(135deg, #065f22 0%, #16a34a 100%) !important;
        box-shadow: 0 18px 38px rgba(22, 163, 74, .20);
    }

    .role-hero * {
        color: #ffffff !important;
    }

    .role-hero small,
    .role-hero .text-muted {
        color: rgba(255, 255, 255, .85) !important;
    }

    .metric-card {
        border: 1px solid #dcfce7;
        border-radius: 8px;
        height: 100%;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .metric-card .card-body {
        background: #ffffff;
        border-radius: 8px;
    }

    .metric-card .text-muted {
        color: #166534 !important;
        font-weight: 600;
    }

    .metric-card h2,
    .metric-card h4 {
        color: #065f22 !important;
        font-weight: 800;
    }

    .metric-card small {
        color: #64748b !important;
    }

    [style*="--role-color"] {
        --role-color: #16a34a !important;
    }

    .focus-item {
        border-left: 4px solid var(--role-color);
        background: #ecfdf5;
        color: #14532d;
        padding: 12px 14px;
        border-radius: 0 8px 8px 0;
        font-weight: 600;
    }

    .card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .card-body h4 {
        color: #065f22;
        font-weight: 700;
    }

    .btn-primary {
        background: linear-gradient(135deg, #047857, #16a34a) !important;
        border-color: #16a34a !important;
        color: #ffffff !important;
        font-weight: 700;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #065f46, #15803d) !important;
        border-color: #15803d !important;
        color: #ffffff !important;
    }

    .btn-outline-secondary {
        border-color: #16a34a !important;
        color: #16a34a !important;
        font-weight: 700;
    }

    .btn-outline-secondary:hover {
        background: #16a34a !important;
        border-color: #16a34a !important;
        color: #ffffff !important;
    }

    .finance-list {
        display: grid;
        gap: 10px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .finance-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid #dcfce7;
        border-radius: 8px;
        background: #f7fdf9;
    }

    .finance-list-title {
        display: block;
        color: #065f22;
        font-weight: 800;
        line-height: 1.35;
    }

    .finance-list-meta {
        display: block;
        margin-top: 3px;
        color: #64748b;
        font-size: 12px;
    }

    .finance-list-value {
        color: #047857;
        font-weight: 900;
        white-space: nowrap;
    }

    .finance-chart-card {
        height: 100%;
    }

    .finance-chart-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .finance-chart-head h4 {
        margin: 0;
    }

    .trend-badge {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .trend-badge.is-down {
        background: #fff7ed;
        color: #c2410c;
    }

    .trend-badge.is-flat {
        background: #f1f5f9;
        color: #475569;
    }

    .finance-chart-wrap {
        position: relative;
        height: 290px;
        min-height: 290px;
    }

    .finance-chart-wrap.is-compact {
        height: 250px;
        min-height: 250px;
    }

    .personal-strip {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 14px;
    }

    .personal-strip-item {
        padding: 12px;
        border: 1px solid #dcfce7;
        border-radius: 8px;
        background: #ffffff;
    }

    .personal-strip-item small {
        display: block;
        color: #64748b;
        font-weight: 700;
    }

    .personal-strip-item strong {
        display: block;
        margin-top: 4px;
        color: #065f22;
        font-size: 18px;
        font-weight: 900;
    }

    @media (max-width: 767.98px) {
        .personal-strip {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $isKeuangan = $pegawai->hasAksesRole(\App\Models\Pegawai::AKSES_KEUANGAN);
    $isDkm = $pegawai->hasAksesRole(\App\Models\Pegawai::AKSES_DKM);
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
    $financeDashboard = $financeDashboard ?? [];
    $trendClass = fn ($direction) => match ($direction ?? 'flat') {
        'up' => 'is-up',
        'down' => 'is-down',
        default => 'is-flat',
    };
@endphp

<div class="role-hero mb-4" style="background: linear-gradient(135deg, #065f22 0%, #16a34a 100%);">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <div class="small text-uppercase" style="opacity:.8">
                Dashboard {{ $dashboardProfile['jabatan'] }}
            </div>
            <h2 class="mb-1">{{ $pegawai->nama_pegawai }}</h2>
            <p class="mb-0">{{ $dashboardProfile['subtitle'] }}</p>
        </div>

        <div class="mt-3 mt-md-0 text-md-right">
            <div>NIP {{ $pegawai->nip }}</div>
            <small>{{ $pegawai->email }}</small>
        </div>
    </div>
</div>

@if($isKeuangan)
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="text-muted">Pengeluaran Bulan Ini</div>
                    <h4 class="mb-0">{{ $rupiah($financeDashboard['pengeluaran_bulan_ini'] ?? 0) }}</h4>
                    <small>Operasional dan gaji dibayar</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="text-muted">Gaji Belum Dibayar</div>
                    <h2 class="mb-0">{{ number_format($financeDashboard['penggajian_belum_dibayar'] ?? 0) }}</h2>
                    <small>{{ $rupiah($financeDashboard['nominal_penggajian_belum_dibayar'] ?? 0) }}</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="text-muted">Total Gaji Periode</div>
                    <h4 class="mb-0">{{ $rupiah($financeDashboard['total_gaji_periode'] ?? 0) }}</h4>
                    <small>Periode {{ $financeDashboard['periode'] ?? now()->format('Y-m') }}</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="text-muted">Saldo Bulan Ini</div>
                    <h4 class="mb-0">{{ $rupiah($financeDashboard['saldo_bulan_ini'] ?? 0) }}</h4>
                    <small>Pemasukan dikurangi pengeluaran</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-8 mb-3">
            <div class="card finance-chart-card">
                <div class="card-body">
                    <div class="finance-chart-head">
                        <div>
                            <h4>Pemasukan vs Pengeluaran</h4>
                            <small class="text-muted">Perbandingan arus masuk dan keluar selama 6 bulan terakhir.</small>
                        </div>
                        <span class="trend-badge {{ $trendClass($financeDashboard['trend']['saldo']['direction'] ?? 'flat') }}">
                            {{ $financeDashboard['trend']['saldo']['label'] ?? 'Stabil dari bulan lalu' }}
                        </span>
                    </div>
                    <div class="finance-chart-wrap">
                        <canvas id="keuanganExpenseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-3">
            <div class="card finance-chart-card">
                <div class="card-body">
                    <div class="finance-chart-head">
                        <div>
                            <h4>Status Penggajian</h4>
                            <small class="text-muted">Periode {{ $financeDashboard['periode'] ?? now()->format('Y-m') }}.</small>
                        </div>
                    </div>
                    <div class="finance-chart-wrap is-compact">
                        <canvas id="keuanganPayrollChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Pengeluaran Terbaru</h4>
                    <ul class="finance-list">
                        @forelse($financeDashboard['pengeluaran_terbaru'] ?? [] as $pengeluaran)
                            <li class="finance-list-item">
                                <span>
                                    <span class="finance-list-title">{{ $pengeluaran->kategori ?? 'Pengeluaran' }}</span>
                                    <span class="finance-list-meta">{{ $pengeluaran->tanggal }} · {{ $pengeluaran->deskripsi ?? $pengeluaran->keterangan }}</span>
                                </span>
                                <span class="finance-list-value">{{ $rupiah($pengeluaran->nominal ?: $pengeluaran->jumlah) }}</span>
                            </li>
                        @empty
                            <li class="finance-list-item">
                                <span class="finance-list-title">Belum ada pengeluaran operasional.</span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Penggajian Menunggu</h4>
                    <ul class="finance-list">
                        @forelse($financeDashboard['penggajian_menunggu'] ?? [] as $gaji)
                            <li class="finance-list-item">
                                <span>
                                    <span class="finance-list-title">{{ $gaji->pegawai?->nama_pegawai ?? 'Pegawai' }}</span>
                                    <span class="finance-list-meta">{{ $gaji->jumlah_kehadiran }} hari hadir · {{ $gaji->periode }}</span>
                                </span>
                                <span class="finance-list-value">{{ $rupiah($gaji->total_gaji) }}</span>
                            </li>
                        @empty
                            <li class="finance-list-item">
                                <span class="finance-list-title">Tidak ada gaji yang menunggu pembayaran.</span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Aksi Cepat</h4>
                    <a href="{{ route('pegawai.keuangan.pengeluaran.create') }}" class="btn btn-primary btn-block mb-2">
                        Tambah Pengeluaran
                    </a>
                    <a href="{{ route('pegawai.keuangan.penggajian.index') }}" class="btn btn-outline-secondary btn-block mb-2">
                        Kelola Penggajian
                    </a>
                    <a href="{{ route('pegawai.laporan-keuangan.arus-kas') }}" class="btn btn-outline-secondary btn-block mb-2">
                        Lihat Arus Kas
                    </a>
                    <a href="{{ route('pegawai.laporan-keuangan.jurnal-umum') }}" class="btn btn-outline-secondary btn-block">
                        Jurnal Umum
                    </a>
                    <div class="personal-strip">
                        <div class="personal-strip-item">
                            <small>Hadir</small>
                            <strong>{{ $jumlahHadir }}</strong>
                        </div>
                        <div class="personal-strip-item">
                            <small>Pending</small>
                            <strong>{{ $presensiMenunggu }}</strong>
                        </div>
                        <div class="personal-strip-item">
                            <small>Gaji Saya</small>
                            <strong>{{ $rupiah($penggajianTerakhir->total_gaji ?? 0) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@elseif($isDkm)
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="text-muted">Pemasukan Bulan Ini</div>
                    <h4 class="mb-0">{{ $rupiah($financeDashboard['pemasukan_bulan_ini'] ?? 0) }}</h4>
                    <small>Penerimaan ZISWAF diterima</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="text-muted">Pengeluaran Bulan Ini</div>
                    <h4 class="mb-0">{{ $rupiah($financeDashboard['pengeluaran_bulan_ini'] ?? 0) }}</h4>
                    <small>Operasional dan gaji dibayar</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="text-muted">Saldo Bulan Ini</div>
                    <h4 class="mb-0">{{ $rupiah($financeDashboard['saldo_bulan_ini'] ?? 0) }}</h4>
                    <small>Ringkasan arus kas bersih</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-7 mb-3">
            <div class="card finance-chart-card">
                <div class="card-body">
                    <div class="finance-chart-head">
                        <div>
                            <h4>Pemasukan vs Pengeluaran</h4>
                            <small class="text-muted">Perbandingan arus masuk dan keluar selama 6 bulan terakhir.</small>
                        </div>
                        <span class="trend-badge {{ $trendClass($financeDashboard['trend']['pemasukan']['direction'] ?? 'flat') }}">
                            {{ $financeDashboard['trend']['pemasukan']['label'] ?? 'Stabil dari bulan lalu' }}
                        </span>
                    </div>
                    <div class="finance-chart-wrap">
                        <canvas id="dkmCashCompareChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5 mb-3">
            <div class="card finance-chart-card">
                <div class="card-body">
                    <div class="finance-chart-head">
                        <div>
                            <h4>Saldo Bersih</h4>
                            <small class="text-muted">Perkembangan surplus atau defisit bulanan.</small>
                        </div>
                        <span class="trend-badge {{ $trendClass($financeDashboard['trend']['saldo']['direction'] ?? 'flat') }}">
                            {{ $financeDashboard['trend']['saldo']['label'] ?? 'Stabil dari bulan lalu' }}
                        </span>
                    </div>
                    <div class="finance-chart-wrap">
                        <canvas id="dkmBalanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-3">
            <div class="card">
                <div class="card-body" style="--role-color:#16a34a">
                    <h4 class="mb-3">Fokus DKM</h4>
                    @foreach($dashboardProfile['focus'] as $focus)
                        <div class="focus-item mb-2">{{ $focus }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Laporan</h4>
                    <a href="{{ route('pegawai.laporan-keuangan.jurnal-umum') }}" class="btn btn-outline-secondary btn-block mb-2">
                        Jurnal Umum
                    </a>
                    <a href="{{ route('pegawai.laporan-keuangan.arus-kas') }}" class="btn btn-outline-secondary btn-block mb-2">
                        Arus Kas
                    </a>
                    <a href="{{ route('pegawai.laporan-keuangan.arus-kas-psak') }}" class="btn btn-outline-secondary btn-block mb-2">
                        Arus Kas PSAK
                    </a>
                    <a href="{{ route('pegawai.laporan-gaji.index') }}" class="btn btn-primary btn-block">
                        Laporan Gaji Pribadi
                    </a>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="text-muted">Kehadiran Bulan Ini</div>
                    <h2 class="mb-0">{{ $jumlahHadir }}</h2>
                    <small>presensi hadir</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="text-muted">Menunggu Persetujuan</div>
                    <h2 class="mb-0">{{ $presensiMenunggu }}</h2>
                    <small>pengajuan presensi</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="text-muted">Penggajian Terakhir</div>
                    <h4 class="mb-0">
                        {{ $rupiah($penggajianTerakhir->total_gaji ?? 0) }}
                    </h4>
                    <small>{{ $penggajianTerakhir->status_penggajian ?? 'Belum tersedia' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-3">
        <div class="card">
            <div class="card-body" style="--role-color:#16a34a">
                <h4 class="mb-3">Fokus {{ $dashboardProfile['jabatan'] }}</h4>

                @foreach($dashboardProfile['focus'] as $focus)
                    <div class="focus-item mb-2">{{ $focus }}</div>
                @endforeach
            </div>
        </div>
        </div>

        <div class="col-lg-5 mb-3">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">Aksi Cepat</h4>
                <a href="{{ route('pegawai.presensi.create') }}" class="btn btn-primary btn-block mb-2">
                    Isi Presensi
                </a>

                <a href="{{ route('pegawai.presensi.index') }}" class="btn btn-outline-secondary btn-block">
                    Riwayat Presensi
                </a>
            </div>
        </div>
        </div>
    </div>
@endif

@if($isKeuangan || $isDkm)
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (() => {
                if (typeof Chart === 'undefined') {
                    return;
                }

                const chartData = @json($financeDashboard['chart'] ?? []);
                const labels = chartData.labels || [];
                const rupiah = value => new Intl.NumberFormat(
                    'id-ID',
                    {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0
                    }
                ).format(Number(value || 0));
                const tooltipLabel = context => {
                    const label = context.dataset.label || context.label || '';
                    const value = context.parsed?.y ?? context.parsed ?? 0;
                    return `${label}: ${rupiah(value)}`;
                };
                const baseOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                color: '#14532d',
                                font: {
                                    weight: '700'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#172033',
                            titleColor: '#ffffff',
                            bodyColor: '#e2e8f0',
                            padding: 12,
                            cornerRadius: 10,
                            callbacks: {
                                label: tooltipLabel
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    weight: '700'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(20, 83, 45, .08)'
                            },
                            ticks: {
                                color: '#64748b',
                                callback: value => {
                                    const number = Number(value || 0);
                                    if (Math.abs(number) >= 1000000) {
                                        return `${number / 1000000} jt`;
                                    }
                                    if (Math.abs(number) >= 1000) {
                                        return `${number / 1000} rb`;
                                    }
                                    return number;
                                }
                            }
                        }
                    }
                };
                const createBarGradient = (context, colorStart, colorEnd) => {
                    const gradient = context.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, colorStart);
                    gradient.addColorStop(1, colorEnd);
                    return gradient;
                };

                const cashControlCanvas = document.getElementById('keuanganExpenseChart');
                if (cashControlCanvas) {
                    const context = cashControlCanvas.getContext('2d');
                    new Chart(context, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'Pemasukan',
                                    data: chartData.pemasukan || [],
                                    backgroundColor: createBarGradient(
                                        context,
                                        'rgba(22, 163, 74, .90)',
                                        'rgba(22, 163, 74, .36)'
                                    ),
                                    borderColor: '#16a34a',
                                    borderWidth: 1,
                                    borderRadius: 8,
                                    borderSkipped: false,
                                    maxBarThickness: 32
                                },
                                {
                                    label: 'Pengeluaran',
                                    data: chartData.pengeluaran || [],
                                    backgroundColor: createBarGradient(
                                        context,
                                        'rgba(249, 115, 22, .86)',
                                        'rgba(249, 115, 22, .34)'
                                    ),
                                    borderColor: '#f97316',
                                    borderWidth: 1,
                                    borderRadius: 8,
                                    borderSkipped: false,
                                    maxBarThickness: 32
                                }
                            ]
                        },
                        options: baseOptions
                    });
                }

                const payrollCanvas = document.getElementById('keuanganPayrollChart');
                if (payrollCanvas) {
                    new Chart(payrollCanvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Sudah Dibayar', 'Belum Dibayar'],
                            datasets: [
                                {
                                    data: chartData.status_penggajian || [0, 0],
                                    backgroundColor: ['#16a34a', '#f97316'],
                                    borderColor: '#ffffff',
                                    borderWidth: 4,
                                    hoverOffset: 5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '68%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        color: '#14532d',
                                        font: {
                                            weight: '700'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#172033',
                                    titleColor: '#ffffff',
                                    bodyColor: '#e2e8f0',
                                    padding: 12,
                                    cornerRadius: 10
                                }
                            }
                        }
                    });
                }

                const cashCompareCanvas = document.getElementById('dkmCashCompareChart');
                if (cashCompareCanvas) {
                    const context = cashCompareCanvas.getContext('2d');
                    new Chart(context, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'Pemasukan',
                                    data: chartData.pemasukan || [],
                                    backgroundColor: createBarGradient(
                                        context,
                                        'rgba(22, 163, 74, .90)',
                                        'rgba(22, 163, 74, .36)'
                                    ),
                                    borderColor: '#16a34a',
                                    borderWidth: 1,
                                    borderRadius: 8,
                                    borderSkipped: false,
                                    maxBarThickness: 32
                                },
                                {
                                    label: 'Pengeluaran',
                                    data: chartData.pengeluaran || [],
                                    backgroundColor: createBarGradient(
                                        context,
                                        'rgba(249, 115, 22, .86)',
                                        'rgba(249, 115, 22, .34)'
                                    ),
                                    borderColor: '#f97316',
                                    borderWidth: 1,
                                    borderRadius: 8,
                                    borderSkipped: false,
                                    maxBarThickness: 32
                                }
                            ]
                        },
                        options: baseOptions
                    });
                }

                const balanceCanvas = document.getElementById('dkmBalanceChart');
                if (balanceCanvas) {
                    new Chart(balanceCanvas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'Saldo Bersih',
                                    data: chartData.saldo || [],
                                    borderColor: '#047857',
                                    backgroundColor: 'rgba(22, 163, 74, .12)',
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#047857',
                                    pointBorderWidth: 2,
                                    fill: true,
                                    tension: .35
                                }
                            ]
                        },
                        options: baseOptions
                    });
                }
            })();
        </script>
    @endpush
@endif

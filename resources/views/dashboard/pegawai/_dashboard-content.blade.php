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

    .finance-dashboard-layout {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(300px, .85fr);
        align-items: start;
        gap: 18px;
        margin-bottom: 24px;
    }

    .finance-chart-stack,
    .finance-side-stack {
        display: grid;
        gap: 18px;
        min-width: 0;
    }

    .finance-activity-card {
        overflow: hidden;
    }

    .finance-activity-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 22px 0;
    }

    .finance-activity-head h4 {
        margin: 0;
    }

    .finance-activity-head small {
        display: block;
        margin-top: 4px;
    }

    .finance-activity-tabs {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px;
        border: 1px solid #DDE8E0;
        border-radius: 8px;
        background: #F3F8F5;
    }

    .finance-activity-tab {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 34px;
        padding: 0 11px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
    }

    .finance-activity-tab.is-active {
        background: #ffffff;
        color: #047857;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .08);
    }

    .finance-activity-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 999px;
        background: #DCFCE7;
        color: #166534;
        font-size: 10px;
        font-weight: 900;
    }

    .finance-activity-panel[hidden] {
        display: none;
    }

    .finance-table-scroll {
        overflow-x: auto;
        margin-top: 18px;
        border-top: 1px solid #E4ECE6;
    }

    .finance-activity-table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .finance-activity-table th,
    .finance-activity-table td {
        padding: 13px 16px;
        border-bottom: 1px solid #E8EFEB;
        text-align: left;
        vertical-align: middle;
    }

    .finance-activity-table th {
        background: #F7FAF8;
        color: #52645A;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .finance-activity-table td {
        color: #334155;
        font-size: 12px;
        line-height: 1.45;
    }

    .finance-activity-table tbody tr:hover {
        background: #FAFDFC;
    }

    .finance-activity-table .is-strong {
        color: #14532d;
        font-weight: 800;
    }

    .finance-activity-table .is-amount {
        color: #047857;
        font-weight: 900;
        text-align: right;
        white-space: nowrap;
    }

    .finance-activity-table th.is-amount {
        color: #52645A;
    }

    .finance-status {
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        padding: 0 8px;
        border-radius: 999px;
        background: #DCFCE7;
        color: #166534;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .finance-status.is-waiting {
        background: #FFF7ED;
        color: #C2410C;
    }

    .finance-table-empty {
        padding: 28px 16px !important;
        color: #64748b !important;
        text-align: center !important;
    }

    .finance-table-footer {
        display: flex;
        justify-content: flex-end;
        padding: 13px 16px 16px;
    }

    .finance-table-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #047857;
        font-size: 12px;
        font-weight: 800;
    }

    .finance-table-link:hover {
        color: #065f46;
        text-decoration: none;
    }

    .finance-chart-toolbar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 14px;
    }

    .finance-year-filter {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 44px;
        padding: 7px 9px 7px 13px;
        border: 1px solid #DDE8E0;
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
    }

    .finance-year-filter label {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
    }

    .finance-year-filter select {
        min-width: 96px;
        height: 32px;
        padding: 0 30px 0 10px;
        border: 1px solid #D8E3DC;
        border-radius: 6px;
        background-color: #F8FBF9;
        color: #14532d;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
    }

    .finance-side-stack .personal-strip {
        grid-template-columns: 1fr;
    }

    @media (max-width: 991.98px) {
        .finance-dashboard-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .finance-activity-head {
            align-items: stretch;
            flex-direction: column;
        }

        .finance-activity-tabs {
            width: 100%;
        }

        .finance-activity-tab {
            flex: 1;
            justify-content: center;
        }
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
    $tahunGrafik = $tahunGrafik ?? now()->year;
    $tahunGrafikTersedia = $tahunGrafikTersedia ?? [$tahunGrafik];
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

    <div class="finance-chart-toolbar">
        <form
            method="GET"
            action="{{ route('pegawai.dashboard', ['jabatan' => $dashboardProfile['slug']]) }}"
            class="finance-year-filter"
        >
            <label for="keuangan-chart-year">
                <i class="far fa-calendar-alt"></i>
                Tahun Grafik
            </label>
            <select id="keuangan-chart-year" name="tahun" onchange="this.form.submit()">
                @foreach($tahunGrafikTersedia as $tahun)
                    <option value="{{ $tahun }}" @selected($tahun === $tahunGrafik)>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>
            <noscript>
                <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
            </noscript>
        </form>
    </div>

    <div class="finance-dashboard-layout">
        <div class="finance-chart-stack">
            <div class="card finance-chart-card">
                <div class="card-body">
                    <div class="finance-chart-head">
                        <div>
                            <h4>Pemasukan vs Pengeluaran</h4>
                            <small class="text-muted">Perbandingan arus masuk dan keluar Januari-Desember {{ $tahunGrafik }}.</small>
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

            <div class="card finance-chart-card">
                <div class="card-body">
                    <div class="finance-chart-head">
                        <div>
                            <h4>Penggajian per Bulan</h4>
                            <small class="text-muted">Total gaji berdasarkan periode Januari-Desember {{ $tahunGrafik }}.</small>
                        </div>
                        <span class="trend-badge {{ $trendClass($financeDashboard['trend']['penggajian']['direction'] ?? 'flat') }}">
                            {{ $financeDashboard['trend']['penggajian']['label'] ?? 'Stabil dari bulan lalu' }}
                        </span>
                    </div>
                    <div class="finance-chart-wrap">
                        <canvas id="keuanganPayrollChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card finance-activity-card">
                <div class="finance-activity-head">
                    <div>
                        <h4>Aktivitas Keuangan Terbaru</h4>
                        <small class="text-muted">Ringkasan lima data terbaru untuk pemeriksaan cepat.</small>
                    </div>

                    <div class="finance-activity-tabs" role="tablist" aria-label="Aktivitas keuangan terbaru">
                        <button
                            type="button"
                            id="finance-tab-expense"
                            class="finance-activity-tab is-active"
                            role="tab"
                            aria-selected="true"
                            aria-controls="finance-panel-expense"
                            data-finance-tab="expense"
                        >
                            Riwayat Pengeluaran
                            <span class="finance-activity-count">{{ count($financeDashboard['pengeluaran_terbaru'] ?? []) }}</span>
                        </button>
                        <button
                            type="button"
                            id="finance-tab-payroll"
                            class="finance-activity-tab"
                            role="tab"
                            aria-selected="false"
                            aria-controls="finance-panel-payroll"
                            data-finance-tab="payroll"
                        >
                            Gaji Menunggu
                            <span class="finance-activity-count">{{ count($financeDashboard['penggajian_menunggu'] ?? []) }}</span>
                        </button>
                    </div>
                </div>

                <div
                    id="finance-panel-expense"
                    class="finance-activity-panel"
                    role="tabpanel"
                    aria-labelledby="finance-tab-expense"
                    data-finance-panel="expense"
                >
                    <div class="finance-table-scroll">
                        <table class="finance-activity-table">
                            <colgroup>
                                <col style="width: 15%">
                                <col style="width: 22%">
                                <col style="width: 33%">
                                <col style="width: 17%">
                                <col style="width: 13%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                    <th class="is-amount">Nominal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($financeDashboard['pengeluaran_terbaru'] ?? [] as $pengeluaran)
                                    <tr>
                                        <td>{{ $pengeluaran->tanggal ? \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d/m/Y') : '-' }}</td>
                                        <td class="is-strong">{{ $pengeluaran->kategori ?? 'Pengeluaran' }}</td>
                                        <td>{{ $pengeluaran->deskripsi ?? $pengeluaran->keterangan ?? '-' }}</td>
                                        <td class="is-amount">{{ $rupiah($pengeluaran->nominal ?: $pengeluaran->jumlah) }}</td>
                                        <td>
                                            <span class="finance-status">
                                                {{ ucfirst(str_replace('_', ' ', $pengeluaran->status_verifikasi ?: 'tercatat')) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="finance-table-empty">Belum ada pengeluaran operasional.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="finance-table-footer">
                        <a href="{{ route('pegawai.keuangan.pengeluaran.index') }}" class="finance-table-link">
                            Lihat Semua Pengeluaran
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div
                    id="finance-panel-payroll"
                    class="finance-activity-panel"
                    role="tabpanel"
                    aria-labelledby="finance-tab-payroll"
                    data-finance-panel="payroll"
                    hidden
                >
                    <div class="finance-table-scroll">
                        <table class="finance-activity-table">
                            <colgroup>
                                <col style="width: 17%">
                                <col style="width: 29%">
                                <col style="width: 17%">
                                <col style="width: 22%">
                                <col style="width: 15%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Pegawai</th>
                                    <th>Kehadiran</th>
                                    <th class="is-amount">Total Gaji</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($financeDashboard['penggajian_menunggu'] ?? [] as $gaji)
                                    <tr>
                                        <td>{{ $gaji->periode }}</td>
                                        <td class="is-strong">{{ $gaji->pegawai?->nama_pegawai ?? 'Pegawai' }}</td>
                                        <td>{{ number_format((int) $gaji->jumlah_kehadiran) }} hari</td>
                                        <td class="is-amount">{{ $rupiah($gaji->total_gaji) }}</td>
                                        <td><span class="finance-status is-waiting">Menunggu</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="finance-table-empty">Tidak ada gaji yang menunggu pembayaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="finance-table-footer">
                        <a href="{{ route('pegawai.keuangan.penggajian.index') }}" class="finance-table-link">
                            Kelola Semua Penggajian
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="finance-side-stack">
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

    <div class="finance-chart-toolbar">
        <form
            method="GET"
            action="{{ route('pegawai.dashboard', ['jabatan' => $dashboardProfile['slug']]) }}"
            class="finance-year-filter"
        >
            <label for="dkm-chart-year">
                <i class="far fa-calendar-alt"></i>
                Tahun Grafik
            </label>
            <select id="dkm-chart-year" name="tahun" onchange="this.form.submit()">
                @foreach($tahunGrafikTersedia as $tahun)
                    <option value="{{ $tahun }}" @selected($tahun === $tahunGrafik)>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>
            <noscript>
                <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
            </noscript>
        </form>
    </div>

    <div class="finance-dashboard-layout">
        <div class="finance-chart-stack">
            <div class="card finance-chart-card">
                <div class="card-body">
                    <div class="finance-chart-head">
                        <div>
                            <h4>Pemasukan vs Pengeluaran</h4>
                            <small class="text-muted">Perbandingan arus masuk dan keluar Januari-Desember {{ $tahunGrafik }}.</small>
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

            <div class="card finance-chart-card">
                <div class="card-body">
                    <div class="finance-chart-head">
                        <div>
                            <h4>Penggajian per Bulan</h4>
                            <small class="text-muted">Total gaji berdasarkan periode Januari-Desember {{ $tahunGrafik }}.</small>
                        </div>
                        <span class="trend-badge {{ $trendClass($financeDashboard['trend']['penggajian']['direction'] ?? 'flat') }}">
                            {{ $financeDashboard['trend']['penggajian']['label'] ?? 'Stabil dari bulan lalu' }}
                        </span>
                    </div>
                    <div class="finance-chart-wrap">
                        <canvas id="dkmPayrollChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="finance-side-stack">
            <div class="card">
                <div class="card-body" style="--role-color:#16a34a">
                    <h4 class="mb-3">Fokus DKM</h4>
                    @foreach($dashboardProfile['focus'] as $focus)
                        <div class="focus-item mb-2">{{ $focus }}</div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Akses Cepat</h4>
                    <a href="{{ route('pegawai.laporan-keuangan.jurnal-umum') }}" class="btn btn-outline-secondary btn-block mb-2">
                        Jurnal Umum
                    </a>
                    <a href="{{ route('pegawai.laporan-keuangan.arus-kas') }}" class="btn btn-outline-secondary btn-block mb-2">
                        Laporan Keuangan
                    </a>
                    <a href="{{ route('pegawai.laporan-gaji.index') }}" class="btn btn-primary btn-block">
                        Slip Gaji Saya
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
                const activityTabs = document.querySelectorAll('[data-finance-tab]');
                const activityPanels = document.querySelectorAll('[data-finance-panel]');

                activityTabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        const selectedTab = tab.dataset.financeTab;

                        activityTabs.forEach(item => {
                            const isActive = item.dataset.financeTab === selectedTab;
                            item.classList.toggle('is-active', isActive);
                            item.setAttribute('aria-selected', String(isActive));
                        });

                        activityPanels.forEach(panel => {
                            panel.hidden = panel.dataset.financePanel !== selectedTab;
                        });
                    });

                    tab.addEventListener('keydown', event => {
                        if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) {
                            return;
                        }

                        event.preventDefault();
                        const tabs = Array.from(activityTabs);
                        const direction = event.key === 'ArrowRight' ? 1 : -1;
                        const targetIndex = (tabs.indexOf(tab) + direction + tabs.length) % tabs.length;

                        tabs[targetIndex].focus();
                        tabs[targetIndex].click();
                    });
                });

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
                const createAreaGradient = (context, colorStart, colorEnd) => {
                    const gradient = context.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, colorStart);
                    gradient.addColorStop(1, colorEnd);
                    return gradient;
                };

                const cashControlCanvas = document.getElementById('keuanganExpenseChart');
                if (cashControlCanvas) {
                    const context = cashControlCanvas.getContext('2d');
                    new Chart(context, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'Pemasukan',
                                    data: chartData.pemasukan || [],
                                    backgroundColor: createAreaGradient(
                                        context,
                                        'rgba(22, 163, 74, .30)',
                                        'rgba(22, 163, 74, .03)'
                                    ),
                                    borderColor: '#16a34a',
                                    borderWidth: 3,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#16a34a',
                                    pointBorderWidth: 2,
                                    fill: true,
                                    tension: .42
                                },
                                {
                                    label: 'Pengeluaran',
                                    data: chartData.pengeluaran || [],
                                    backgroundColor: createAreaGradient(
                                        context,
                                        'rgba(249, 115, 22, .24)',
                                        'rgba(249, 115, 22, .02)'
                                    ),
                                    borderColor: '#f97316',
                                    borderWidth: 3,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#f97316',
                                    pointBorderWidth: 2,
                                    fill: true,
                                    tension: .42
                                }
                            ]
                        },
                        options: baseOptions
                    });
                }

                const payrollCanvas = document.getElementById('keuanganPayrollChart');
                if (payrollCanvas) {
                    const context = payrollCanvas.getContext('2d');
                    new Chart(context, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'Penggajian',
                                    data: chartData.penggajian || [],
                                    backgroundColor: createAreaGradient(
                                        context,
                                        'rgba(37, 99, 235, .28)',
                                        'rgba(96, 165, 250, .03)'
                                    ),
                                    borderColor: '#2563eb',
                                    borderWidth: 3,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#2563eb',
                                    pointBorderWidth: 2,
                                    fill: true,
                                    tension: .42
                                }
                            ]
                        },
                        options: baseOptions
                    });
                }

                const cashCompareCanvas = document.getElementById('dkmCashCompareChart');
                if (cashCompareCanvas) {
                    const context = cashCompareCanvas.getContext('2d');
                    new Chart(context, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'Pemasukan',
                                    data: chartData.pemasukan || [],
                                    backgroundColor: createAreaGradient(
                                        context,
                                        'rgba(22, 163, 74, .30)',
                                        'rgba(22, 163, 74, .03)'
                                    ),
                                    borderColor: '#16a34a',
                                    borderWidth: 3,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#16a34a',
                                    pointBorderWidth: 2,
                                    fill: true,
                                    tension: .42
                                },
                                {
                                    label: 'Pengeluaran',
                                    data: chartData.pengeluaran || [],
                                    backgroundColor: createAreaGradient(
                                        context,
                                        'rgba(249, 115, 22, .24)',
                                        'rgba(249, 115, 22, .02)'
                                    ),
                                    borderColor: '#f97316',
                                    borderWidth: 3,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#f97316',
                                    pointBorderWidth: 2,
                                    fill: true,
                                    tension: .42
                                }
                            ]
                        },
                        options: baseOptions
                    });
                }

                const dkmPayrollCanvas = document.getElementById('dkmPayrollChart');
                if (dkmPayrollCanvas) {
                    const context = dkmPayrollCanvas.getContext('2d');
                    new Chart(context, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'Penggajian',
                                    data: chartData.penggajian || [],
                                    backgroundColor: createAreaGradient(
                                        context,
                                        'rgba(37, 99, 235, .28)',
                                        'rgba(96, 165, 250, .03)'
                                    ),
                                    borderColor: '#2563eb',
                                    borderWidth: 3,
                                    pointRadius: 3,
                                    pointHoverRadius: 6,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#2563eb',
                                    pointBorderWidth: 2,
                                    fill: true,
                                    tension: .42
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

{{-- FINUS DARK MODE LOCAL: dashboard/pegawai/_dashboard-content.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="dashboard/pegawai/_dashboard-content.blade.php">
html[data-finus-theme="dark"] body .metric-card { border-color:#293D31 !important; background:#111A15 !important; box-shadow:0 12px 28px rgba(0,0,0,.18) !important; }
html[data-finus-theme="dark"] body .metric-card .card-body { background:#111A15 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.finance-chart-card,.finance-activity-card,.focus-item,.personal-strip-item) { border-color:#293D31 !important; background:#111B15 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .text-muted { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .trend-badge { border-color:#2D6140 !important; background:#15331E !important; color:#A9F0B9 !important; }
html[data-finus-theme="dark"] body .trend-badge.is-down { border-color:#704044 !important; background:#371E22 !important; color:#F5B1B5 !important; }
html[data-finus-theme="dark"] body .trend-badge.is-flat { border-color:#4B554E !important; background:#1B241E !important; color:#B8C4BB !important; }
html[data-finus-theme="dark"] body .finance-year-filter { border-color:#293D31 !important; background:#111B15 !important; }
html[data-finus-theme="dark"] body .finance-year-filter label { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .finance-year-filter select { border-color:#365141 !important; background:#16241B !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finance-activity-tabs { border-color:#365141 !important; background:#16241B !important; }
html[data-finus-theme="dark"] body .finance-activity-tab { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .finance-activity-tab.is-active { background:#223329 !important; color:#A9F0B9 !important; }
html[data-finus-theme="dark"] body .finance-table-scroll { border-color:#293D31 !important; }
html[data-finus-theme="dark"] body .finance-activity-table th { background:#16241B !important; color:#AFC0B5 !important; }
html[data-finus-theme="dark"] body .finance-activity-table td { border-color:#293D31 !important; color:#DCE7E0 !important; }
html[data-finus-theme="dark"] body .finance-activity-table tbody tr:hover { background:#15231B !important; }
html[data-finus-theme="dark"] body .finance-activity-table :where(.is-strong,.is-amount) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finance-table-empty { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .finance-table-link { color:#A9F0B9 !important; }
</style>
@endpush


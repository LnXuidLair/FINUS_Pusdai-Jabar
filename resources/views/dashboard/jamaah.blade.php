@extends('layouts.app')

@section('title', 'Dashboard Jamaah')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');

    $greeting = match (true) {
        now()->hour >= 5 && now()->hour < 11 => 'Selamat pagi',
        now()->hour >= 11 && now()->hour < 15 => 'Selamat siang',
        now()->hour >= 15 && now()->hour < 18 => 'Selamat sore',
        default => 'Selamat malam',
    };

    $summaryCards = [
        [
            'label' => 'Transaksi Saya',
            'value' => $totalTransaksiSaya ?? 0,
            'note' => ($jumlahTransaksiSaya ?? 0) . ' transaksi telah tercatat.',
            'tag' => 'Pribadi',
            'icon' => 'fa-hand-holding-heart',
            'color' => '#179B40',
            'soft' => '#EAF8EE',
        ],
        [
            'label' => 'Pemasukan Jamaah',
            'value' => $totalPemasukanJamaah ?? 0,
            'note' => 'Akumulasi seluruh transaksi jamaah.',
            'tag' => 'Keseluruhan',
            'icon' => 'fa-chart-line',
            'color' => '#2563EB',
            'soft' => '#EDF4FF',
        ],
        [
            'label' => 'Infak Terkumpul',
            'value' => $totalInfak ?? 0,
            'note' => 'Total dana pada kategori infak.',
            'tag' => 'Infak',
            'icon' => 'fa-kaaba',
            'color' => '#0891B2',
            'soft' => '#EAFBFE',
        ],
        [
            'label' => 'Pengeluaran Masjid',
            'value' => $totalPengeluaran ?? 0,
            'note' => 'Pengeluaran yang telah dicatat admin.',
            'tag' => 'Transparansi',
            'icon' => 'fa-wallet',
            'color' => '#E5484D',
            'soft' => '#FFF0F0',
        ],
    ];

    $fundCards = [
        [
            'label' => 'Total Transaksi',
            'value' => $totalZakat ?? 0,
            'icon' => 'fa-hand-holding-heart',
            'color' => '#179B40',
            'soft' => '#EAF8EE',
        ],
        [
            'label' => 'Total Wakaf',
            'value' => $totalWakaf ?? 0,
            'icon' => 'fa-mosque',
            'color' => '#2563EB',
            'soft' => '#EDF4FF',
        ],
    ];
@endphp

<style>
    :root {
        --j-green: #179B40;
        --j-dark: #0E5423;
        --j-text: #172033;
        --j-muted: #64748B;
        --j-border: #E2EAE5;
        --j-soft: #EAF8EE;
        --j-surface: #FFFFFF;
        --j-shadow: 0 12px 30px rgba(15, 23, 42, .07);
    }

    .j-dashboard {
        padding: 8px 0 36px;
        color: var(--j-text);
    }

    .j-heading,
    .j-card {
        border: 1px solid var(--j-border);
        border-radius: 18px;
        background: var(--j-surface);
        box-shadow: var(--j-shadow);
    }

    .j-heading {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
        padding: 21px 23px;
    }

    .j-heading::after {
        content: "";
        position: absolute;
        top: -65px;
        right: -30px;
        width: 155px;
        height: 155px;
        border-radius: 50%;
        background: var(--j-soft);
    }

    .j-heading-main,
    .j-breadcrumb {
        position: relative;
        z-index: 1;
    }

    .j-heading-main,
    .j-title-wrap,
    .j-panel-title {
        display: flex;
        align-items: center;
    }

    .j-heading-main {
        gap: 14px;
    }

    .j-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        min-width: 46px;
        height: 46px;
        border-radius: 13px;
        background: var(--soft, var(--j-soft));
        color: var(--tone, var(--j-green));
        font-size: 17px;
    }

    .j-eyebrow {
        color: var(--j-green);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .j-heading h1,
    .j-panel-head h2,
    .j-stat h2,
    .j-agenda h3 {
        margin: 0;
    }

    .j-heading h1 {
        margin-top: 2px;
        font-size: 22px;
        font-weight: 800;
    }

    .j-heading p,
    .j-panel-head p,
    .j-stat p,
    .j-agenda p,
    .j-note,
    .j-meta {
        color: var(--j-muted);
    }

    .j-heading p,
    .j-panel-head p {
        margin: 4px 0 0;
        font-size: 12.5px;
    }

    .j-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 700;
        color: var(--j-muted);
    }

    .j-breadcrumb strong {
        color: var(--j-dark);
    }

    .j-grid {
        display: grid;
        gap: 18px;
        margin-bottom: 18px;
    }

    .j-grid-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .j-grid-2 {
        grid-template-columns: minmax(0, 1.65fr) minmax(300px, .85fr);
    }

    .j-grid-equal {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .j-stat {
        --tone: var(--j-green);
        --soft: var(--j-soft);
        position: relative;
        overflow: hidden;
        min-height: 137px;
        padding: 18px;
        transition: .2s ease;
    }

    .j-stat:hover {
        transform: translateY(-3px);
    }

    .j-stat::after {
        content: "";
        position: absolute;
        right: -35px;
        bottom: -45px;
        width: 105px;
        height: 105px;
        border-radius: 50%;
        background: var(--soft);
    }

    .j-stat-top,
    .j-panel-head,
    .j-agenda-top,
    .j-expense-head,
    .j-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .j-stat-top,
    .j-stat-body {
        position: relative;
        z-index: 1;
    }

    .j-tag,
    .j-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 9px;
        border-radius: 9px;
        background: #F5F8F6;
        color: var(--j-muted);
        font-size: 9.5px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .j-stat-body {
        margin-top: 13px;
    }

    .j-stat p {
        margin: 0 0 4px;
        font-size: 12px;
    }

    .j-stat h2 {
        font-size: 21px;
        font-weight: 800;
    }

    .j-stat small {
        display: block;
        margin-top: 4px;
        color: var(--j-muted);
        font-size: 10.5px;
    }

    .j-panel-head {
        padding: 17px 19px;
        border-bottom: 1px solid var(--j-border);
    }

    .j-title-wrap {
        gap: 11px;
    }

    .j-panel-head h2 {
        font-size: 16px;
        font-weight: 800;
    }

    .j-panel-body {
        padding: 18px 19px;
    }

    .j-chart {
        height: 310px;
    }

    .j-chart-small {
        height: 285px;
    }

    .j-list {
        display: grid;
        gap: 12px;
    }

    .j-agenda {
        padding: 14px;
        border: 1px solid var(--j-border);
        border-radius: 14px;
        background: #FBFDFB;
    }

    .j-agenda h3 {
        font-size: 14px;
        font-weight: 800;
    }

    .j-agenda p {
        margin: 9px 0 0;
        font-size: 11px;
        line-height: 1.6;
    }

    .j-meta {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 7px;
        font-size: 10.5px;
    }

    .j-note {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin-top: 14px;
        padding: 12px;
        border-radius: 12px;
        background: var(--j-soft);
        font-size: 11px;
    }

    .j-table-wrap {
        overflow-x: auto;
    }

    .j-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .j-table th,
    .j-table td {
        padding: 13px 15px;
        border-bottom: 1px solid #EDF1EE;
        font-size: 11px;
        vertical-align: middle;
    }

    .j-table th {
        background: #F7FAF8;
        color: var(--j-muted);
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .j-table tbody tr:hover {
        background: #FBFDFB;
    }

    .j-money {
        color: var(--j-dark);
        font-weight: 800;
        white-space: nowrap;
    }

    .j-type {
        display: inline-block;
        padding: 5px 8px;
        border-radius: 8px;
        background: var(--j-soft);
        color: var(--j-dark);
        font-size: 9.5px;
        font-weight: 800;
    }

    .j-empty {
        padding: 28px !important;
        text-align: center;
        color: var(--j-muted);
    }

    .j-expense {
        margin-bottom: 15px;
    }

    .j-expense h3 {
        margin: 0;
        font-size: 12px;
        font-weight: 800;
    }

    .j-expense small {
        color: var(--j-muted);
    }

    .j-expense-value {
        text-align: right;
        font-size: 12px;
        font-weight: 800;
    }

    .j-expense-value span {
        display: block;
        color: var(--j-muted);
        font-size: 9px;
    }

    .j-progress {
        height: 7px;
        margin-top: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: #EDF3EF;
    }

    .j-progress span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--j-dark), var(--j-green));
    }

    .j-total {
        margin-top: 18px;
        padding: 13px;
        border-radius: 12px;
        background: var(--j-soft);
        color: var(--j-dark);
        font-size: 12px;
    }

    .j-funds {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .j-fund {
        --tone: var(--j-green);
        --soft: var(--j-soft);
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 12px;
        align-items: center;
        padding: 15px;
        border: 1px solid var(--j-border);
        border-radius: 14px;
        background: #FBFDFB;
    }

    .j-fund strong {
        display: block;
        margin-top: 2px;
        color: var(--tone);
        font-size: 16px;
    }

    .j-footnote {
        margin: 14px 0 0;
        color: var(--j-muted);
        font-size: 10.5px;
        line-height: 1.6;
    }

    @media (max-width: 1199px) {
        .j-grid-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .j-grid-2,
        .j-grid-equal {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .j-heading {
            align-items: flex-start;
            padding: 18px;
        }

        .j-breadcrumb {
            display: none;
        }

        .j-grid-4,
        .j-funds {
            grid-template-columns: 1fr;
        }

        .j-panel-head {
            align-items: flex-start;
        }

        .j-chart,
        .j-chart-small {
            height: 260px;
        }
    }
</style>

<div class="j-dashboard">
    <section class="j-heading">
        <div class="j-heading-main">
            <span class="j-icon"><i class="fa-solid fa-mosque"></i></span>
            <div>
                <div class="j-eyebrow">{{ $greeting }}, {{ $jamaah->name }}</div>
                <h1>Dashboard Jamaah</h1>
                <p>Pantau transaksi ZISWAF, agenda masjid, dan transparansi keuangan melalui FINUS.</p>
            </div>
        </div>

        <div class="j-breadcrumb">
            <span>Dashboard Jamaah</span>
            <i class="fa-solid fa-chevron-right"></i>
            <strong>Beranda</strong>
        </div>
    </section>

    <section class="j-grid j-grid-4">
        @foreach($summaryCards as $card)
            <article
                class="j-card j-stat"
                style="--tone: {{ $card['color'] }}; --soft: {{ $card['soft'] }};"
            >
                <div class="j-stat-top">
                    <span class="j-icon"><i class="fa-solid {{ $card['icon'] }}"></i></span>
                    <span class="j-tag">{{ $card['tag'] }}</span>
                </div>

                <div class="j-stat-body">
                    <p>{{ $card['label'] }}</p>
                    <h2>{{ $rupiah($card['value']) }}</h2>
                    <small>{{ $card['note'] }}</small>
                </div>
            </article>
        @endforeach
    </section>

    <section class="j-grid j-grid-equal">
        <article class="j-card">
            <header class="j-panel-head">
                <div class="j-title-wrap">
                    <span class="j-icon"><i class="fa-solid fa-calendar-days"></i></span>
                    <div>
                        <h2>Agenda & Kegiatan</h2>
                        <p>Informasi kegiatan terbaru masjid.</p>
                    </div>
                </div>
            </header>

            <div class="j-panel-body">
                <div class="j-list">
                    @forelse(($agendaKegiatan ?? []) as $agenda)
                        <article class="j-agenda">
                            <div class="j-agenda-top">
                                <h3>{{ $agenda['judul'] }}</h3>
                                <span class="j-badge">{{ $agenda['kategori'] }}</span>
                            </div>
                            <div class="j-meta"><i class="fa-solid fa-calendar-day"></i>{{ $agenda['hari'] }}</div>
                            <div class="j-meta"><i class="fa-regular fa-clock"></i>{{ $agenda['waktu'] }}</div>
                            <div class="j-meta"><i class="fa-solid fa-location-dot"></i>{{ $agenda['lokasi'] }}</div>
                            <p>{{ $agenda['deskripsi'] }}</p>
                        </article>
                    @empty
                        <div class="j-empty">Belum ada agenda kegiatan.</div>
                    @endforelse
                </div>

                <div class="j-note">
                    <i class="fa-solid fa-circle-info"></i>
                    Gunakan menu transaksi pada sidebar untuk melakukan pembayaran ZISWAF.
                </div>
            </div>
        </article>

        <article class="j-card">
            <header class="j-panel-head">
                <div class="j-title-wrap">
                    <span class="j-icon" style="--tone:#2563EB;--soft:#EDF4FF">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    <div>
                        <h2>Riwayat Transaksi Saya</h2>
                        <p>Daftar transaksi ZISWAF yang pernah kamu input.</p>
                    </div>
                </div>
                <div class="j-heading-actions">
                    <a class="j-btn" href="{{ route('jamaah.laporan.index') }}">
                        <i class="fa-solid fa-file-lines"></i>
                        Buka Laporan
                    </a>
                    <a class="j-btn jt-btn-soft" href="{{ route('jamaah.laporan.export', request()->query()) }}">
                        <i class="fa-solid fa-file-csv"></i>
                        Export CSV
                    </a>
                    <button type="button" class="j-btn jt-btn-primary" onclick="window.print()">
                        <i class="fa-solid fa-print"></i>
                        Cetak
                    </button>
                </div>
            </header>

            <div class="j-table-wrap">
                <table class="j-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Metode</th>
                            <th>Keterangan</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($riwayatSaya ?? []) as $transaksi)
                            <tr>
                                <td>{{ $transaksi->tanggal?->format('d/m/Y') }}</td>
                                <td>
                                    <span class="j-type">
                                        {{ $jenisLabels[$transaksi->jenis_ziswaf] ?? $transaksi->jenis_ziswaf }}
                                    </span>
                                </td>
                                <td>{{ strtoupper(str_replace('_', ' ', $transaksi->metode_pembayaran)) }}</td>
                                <td>{{ $transaksi->keterangan ?? '-' }}</td>
                                <td class="text-right j-money">{{ $rupiah($transaksi->nominal) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="j-empty">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="j-grid j-grid-equal">
        <article class="j-card">
            <header class="j-panel-head">
                <div class="j-title-wrap">
                    <span class="j-icon"><i class="fa-solid fa-users-viewfinder"></i></span>
                    <div>
                        <h2>Transaksi Jamaah Terbaru</h2>
                        <p>Aktivitas transaksi terbaru yang tercatat pada sistem.</p>
                    </div>
                </div>
            </header>

            <div class="j-table-wrap">
                <table class="j-table">
                    <thead>
                        <tr>
                            <th>Jamaah</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($transaksiTerbaruJamaah ?? []) as $item)
                            <tr>
                                <td>{{ $item->muzakki?->name ?? 'Jamaah' }}</td>
                                <td>{{ $item->tanggal?->format('d/m/Y') }}</td>
                                <td>{{ $jenisLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf }}</td>
                                <td class="text-right j-money">{{ $rupiah($item->nominal) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="j-empty">Belum ada transaksi jamaah.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="j-card">
            <header class="j-panel-head">
                <div class="j-title-wrap">
                    <span class="j-icon" style="--tone:#E5484D;--soft:#FFF0F0">
                        <i class="fa-solid fa-receipt"></i>
                    </span>
                    <div>
                        <h2>Ringkasan Pengeluaran Masjid</h2>
                        <p>Transparansi pengeluaran berdasarkan kategori.</p>
                    </div>
                </div>
            </header>

            <div class="j-panel-body">
                @forelse(($pengeluaranKategori ?? []) as $item)
                    @php
                        $persen = ($totalPengeluaran ?? 0) > 0
                            ? round(($item->total / $totalPengeluaran) * 100)
                            : 0;
                    @endphp

                    <div class="j-expense">
                        <div class="j-expense-head">
                            <div>
                                <h3>{{ $item->kategori_nama ?: 'Lainnya' }}</h3>
                                <small>{{ $item->jumlah_transaksi }} transaksi</small>
                            </div>
                            <div class="j-expense-value">
                                {{ $rupiah($item->total) }}
                                <span>{{ $persen }}%</span>
                            </div>
                        </div>
                        <div class="j-progress">
                            <span style="width: {{ min(100, $persen) }}%"></span>
                        </div>
                    </div>
                @empty
                    <div class="j-empty">Belum ada data pengeluaran.</div>
                @endforelse

                @if(($totalPengeluaran ?? 0) > 0)
                    <div class="j-total">
                        <span>Total Pengeluaran</span>
                        <strong>{{ $rupiah($totalPengeluaran) }}</strong>
                    </div>
                @endif
            </div>
        </article>
    </section>

    <section class="j-card">
        <header class="j-panel-head">
            <div class="j-title-wrap">
                <span class="j-icon"><i class="fa-solid fa-scale-balanced"></i></span>
                <div>
                    <h2>Ringkasan Dana ZISWAF</h2>
                    <p>Ringkasan dana yang telah tercatat pada sistem FINUS.</p>
                </div>
            </div>
        </header>

        <div class="j-panel-body">
            <div class="j-funds">
                @foreach($fundCards as $fund)
                    <div
                        class="j-fund"
                        style="--tone:{{ $fund['color'] }};--soft:{{ $fund['soft'] }}"
                    >
                        <span class="j-icon"><i class="fa-solid {{ $fund['icon'] }}"></i></span>
                        <div>
                            <small>{{ $fund['label'] }}</small>
                            <strong>{{ $rupiah($fund['value']) }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="j-footnote">
                Informasi ini merupakan ringkasan transaksi ZISWAF yang tercatat pada sistem.
                Laporan resmi dan rekonsiliasi tetap dikelola oleh admin FINUS.
            </p>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const money = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    const common = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#64748B',
                    usePointStyle: true,
                    boxWidth: 7,
                    font: { size: 10, weight: '600' }
                }
            },
            tooltip: {
                callbacks: {
                    label: context => `${context.dataset.label || context.label}: ${money(context.raw)}`
                }
            }
        }
    };

    const line = document.getElementById('grafikPemasukanJamaah');
    if (line) {
        const gradient = line.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(23,155,64,.25)');
        gradient.addColorStop(1, 'rgba(23,155,64,.01)');

        new Chart(line, {
            type: 'line',
            data: {
                labels: @json($chartLabels ?? []),
                datasets: [{
                    label: 'Pemasukan Jamaah',
                    data: @json($chartData ?? []),
                    borderColor: '#179B40',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    tension: .38,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#179B40',
                    pointBorderWidth: 2
                }]
            },
            options: {
                ...common,
                interaction: { intersect: false, mode: 'index' },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748B', font: { size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(100,116,139,.14)' },
                        ticks: {
                            color: '#64748B',
                            font: { size: 10 },
                            callback: money
                        }
                    }
                }
            }
        });
    }

    const doughnut = document.getElementById('grafikKomposisiZiswaf');
    if (doughnut) {
        new Chart(doughnut, {
            type: 'doughnut',
            data: {
                labels: @json($komposisiLabels ?? []),
                datasets: [{
                    label: 'Nominal',
                    data: @json($komposisiData ?? []),
                    backgroundColor: ['#179B40', '#2563EB', '#0891B2', '#7C3AED', '#EA8B22', '#E5484D'],
                    borderColor: '#FFFFFF',
                    borderWidth: 4,
                    hoverOffset: 5
                }]
            },
            options: {
                ...common,
                cutout: '68%',
                plugins: {
                    ...common.plugins,
                    legend: {
                        ...common.plugins.legend,
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endpush
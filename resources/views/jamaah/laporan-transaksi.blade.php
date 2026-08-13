@extends('layouts.app')

@section('title', 'Laporan Transaksi Jamaah')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');

    $laporanJenisFilterLabels = collect($jenisLabels)
        ->except(['shadaqah', 'fidyah', 'zakat_fitrah'])
        ->all();

    $jenisChartColors = ['#179B40', '#2563EB', '#0891B2', '#7C3AED', '#EA8B22', '#E5484D'];
    $jenisCompositionTotal = (int) $perJenis->sum('total');

    $summaryCards = [
        [
            'label' => 'Total Pengajuan',
            'value' => $rupiah($summary['total']),
            'icon' => 'fa-wallet',
            'color' => '#2563EB',
            'soft' => '#EDF4FF',
        ],
        [
            'label' => 'Dana Diterima',
            'value' => $rupiah($summary['diterima']),
            'icon' => 'fa-circle-check',
            'color' => '#117A32',
            'soft' => '#EAF8EE',
        ],
        [
            'label' => 'Menunggu Verifikasi',
            'value' => $rupiah($summary['pending']),
            'icon' => 'fa-clock',
            'color' => '#B86B00',
            'soft' => '#FFF7E8',
        ],
        [
            'label' => 'Jumlah Transaksi',
            'value' => number_format($summary['jumlah'], 0, ',', '.'),
            'icon' => 'fa-receipt',
            'color' => '#7C3AED',
            'soft' => '#F4EEFF',
        ],
    ];
@endphp

@include('layouts.partials.finus-ui')

<div class="jt-page">
    <!-- HEADER KHUSUS CETAK PDF -->
    <div class="jt-print-header">
        <div class="jt-print-header-top">
            <div class="jt-print-logo-container">
                <img src="{{ asset('assets/images/pusdai_icon.png') }}" alt="Logo Pusdai" class="jt-print-logo">
            </div>
            <div class="jt-print-title-container">
                <h1 class="jt-print-org">MASJID PUSDAI JAWA BARAT</h1>
                <p class="jt-print-tagline">Layanan Integrasi Keuangan Umat - FINUS</p>
                <p class="jt-print-address">Jl. Diponegoro No.63, Kota Bandung, Jawa Barat 40122</p>
            </div>
        </div>
        <div class="jt-print-divider"></div>
        <div class="jt-print-meta">
            <h2 class="jt-print-doc-title">LAPORAN TRANSAKSI ZISWAF JAMAAH</h2>
            <table class="jt-print-meta-table">
                <tr>
                    <td style="width: 15%"><strong>Nama Jamaah</strong></td>
                    <td style="width: 35%">: {{ $jamaah->name }}</td>
                    <td style="width: 20%"><strong>Periode Laporan</strong></td>
                    <td style="width: 30%">: {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($filters['tanggal_selesai'])->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>: {{ $jamaah->email }}</td>
                    <td><strong>Tanggal Cetak</strong></td>
                    <td>: {{ now()->translatedFormat('d F Y H:i') }} WIB</td>
                </tr>
            </table>
        </div>
    </div>

    <section class="jt-card jt-filter-card">
        <header class="jt-card-head">
            <div class="jt-title-row">
                <span class="jt-icon"><i class="fa-solid fa-calendar-days"></i></span>
                <div>
                    <h2>Periode Laporan</h2>
                    <p>Pilih rentang tanggal, jenis transaksi, dan status verifikasi.</p>
                </div>
            </div>

            <div class="jt-filter-head-actions">
                <a class="jt-btn" href="{{ route('jamaah.riwayat.index') }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Riwayat
                </a>

                <button
                    type="button"
                    class="jt-btn jt-btn-primary"
                    onclick="window.print()"
                >
                    <i class="fa-solid fa-file-pdf"></i>
                    Cetak PDF
                </button>
            </div>
        </header>

        <div class="jt-card-body">
            <form method="GET" action="{{ route('jamaah.laporan.index') }}" class="jt-filter" id="filterForm">
                <div class="jt-field">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        class="jt-control"
                        value="{{ $filters['tanggal_mulai'] }}"
                        required
                    >
                </div>

                <div class="jt-field">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input
                        type="date"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        class="jt-control"
                        value="{{ $filters['tanggal_selesai'] }}"
                        required
                    >
                </div>

                <div class="jt-field">
                    <label for="jenis">Jenis ZISWAF</label>
                    <select id="jenis" name="jenis" class="jt-control">
                        <option value="">Semua jenis</option>
                        @foreach($laporanJenisFilterLabels as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['jenis'] ?? '') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="jt-field">
                    <label for="status">Status Verifikasi</label>
                    <select id="status" name="status" class="jt-control">
                        <option value="">Semua status</option>

                        @foreach($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="jt-filter-actions">
                    <button type="submit" class="jt-btn jt-btn-primary">
                        <i class="fa-solid fa-chart-line"></i>
                        Tampilkan
                    </button>

                    <a href="{{ route('jamaah.laporan.index') }}" class="jt-btn">
                        <i class="fa-solid fa-rotate-left"></i>
                        Tahun Ini
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="jt-grid jt-grid-4 jt-print-summary-cards">
        @foreach($summaryCards as $card)
            <article
                class="jt-card jt-stat"
                style="--stat-color:{{ $card['color'] }};--stat-soft:{{ $card['soft'] }}"
            >
                <div class="jt-stat-top">
                    <span class="jt-icon">
                        <i class="fa-solid {{ $card['icon'] }}"></i>
                    </span>
                </div>

                <div class="jt-stat-body">
                    <span>{{ $card['label'] }}</span>
                    <strong>{{ $card['value'] }}</strong>
                </div>
            </article>
        @endforeach
    </section>

    <section class="jt-grid jt-grid-2 jt-print-chart-section">
        <article class="jt-card">
            <header class="jt-card-head">
                <div class="jt-title-row">
                    <span class="jt-icon"><i class="fa-solid fa-chart-area"></i></span>
                    <div>
                        <h2>Perkembangan Bulanan</h2>
                        <p>Nominal transaksi sesuai periode laporan.</p>
                    </div>
                </div>
            </header>

            <div class="jt-card-body">
                <div class="jt-monthly-chart-legend" aria-hidden="true">
                    <span></span>
                    <strong>Nominal Transaksi</strong>
                </div>

                <div class="jt-chart">
                    <canvas id="jtMonthlyChart"></canvas>
                </div>
            </div>
        </article>

        <article class="jt-card">
            <header class="jt-card-head">
                <div class="jt-title-row">
                    <span class="jt-icon"><i class="fa-solid fa-chart-pie"></i></span>
                    <div>
                        <h2>Komposisi Jenis</h2>
                        <p>Perbandingan nominal berdasarkan jenis ZISWAF.</p>
                    </div>
                </div>
            </header>

            <div class="jt-card-body">
                <div class="jt-type-composition">
                    <div class="jt-chart-small">
                        <canvas id="jtTypeChart"></canvas>
                    </div>

                    <div class="jt-type-detail">
                        <div class="jt-type-detail-summary">
                            <span>Total komposisi</span>
                            <strong>{{ $rupiah($jenisCompositionTotal) }}</strong>
                            <small>{{ number_format($perJenis->count(), 0, ',', '.') }} kategori tercatat</small>
                        </div>

                        <div class="jt-type-detail-list">
                            @forelse($perJenis as $item)
                                @php
                                    $percentage = $jenisCompositionTotal > 0
                                        ? round(((int) $item->total / $jenisCompositionTotal) * 100, 1)
                                        : 0;
                                    $color = $jenisChartColors[$loop->index % count($jenisChartColors)];
                                @endphp

                                <div class="jt-type-detail-item" style="--type-color: {{ $color }}">
                                    <div class="jt-type-detail-main">
                                        <span class="jt-type-dot"></span>
                                        <div>
                                            <strong>{{ $jenisLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf }}</strong>
                                            <small>{{ number_format($item->jumlah_transaksi, 0, ',', '.') }} transaksi</small>
                                        </div>
                                    </div>

                                    <div class="jt-type-detail-value">
                                        <strong>{{ number_format($percentage, 1, ',', '.') }}%</strong>
                                        <span>{{ $rupiah($item->total) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="jt-type-detail-empty">
                                    Belum ada transaksi untuk ditampilkan pada komposisi jenis.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </section>

    <section class="jt-card jt-report-breakdown-section">
        <header class="jt-card-head">
            <div class="jt-title-row">
                <span class="jt-icon"><i class="fa-solid fa-layer-group"></i></span>
                <div>
                    <h2>Ringkasan per Jenis</h2>
                    <p>Jumlah transaksi dan nominal pada setiap kategori.</p>
                </div>
            </div>
        </header>

        <div class="jt-card-body">
            <div class="jt-breakdown">
                @forelse($perJenis as $item)
                    <div class="jt-breakdown-item">
                        <div>
                            <strong>
                                {{ $jenisLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf }}
                            </strong>
                            <small>{{ number_format($item->jumlah_transaksi, 0, ',', '.') }} transaksi</small>
                        </div>

                        <div class="jt-money">{{ $rupiah($item->total) }}</div>
                    </div>
                @empty
                    <div class="jt-empty">Belum ada data pada periode ini.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="jt-card jt-report-detail-section">
        <header class="jt-card-head">
            <div class="jt-title-row">
                <span class="jt-icon"><i class="fa-solid fa-table-list"></i></span>
                <div>
                    <h2>Rincian Laporan</h2>
                    <p>Daftar lengkap transaksi yang masuk ke laporan ini.</p>
                </div>
            </div>
        </header>

        <div class="jt-table-wrap">
            <table class="jt-table">
                <thead>
                    <tr>
                        <th>Referensi</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th class="text-right">Nominal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transaksiLaporan as $item)
                        @php
                            $status = $item->status_verifikasi ?: 'pending';
                        @endphp

                        <tr>
                            <td class="jt-reference">{{ $item->order_id ?: 'ZSF-' . $item->id }}</td>

                            <td>{{ $item->tanggal?->format('d/m/Y') }}</td>

                            <td>
                                <span class="jt-type">
                                    {{ $jenisLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf }}
                                </span>
                            </td>

                            <td>
                                {{ $metodeLabels[$item->metode_pembayaran]
                                    ?? strtoupper(str_replace('_', ' ', $item->metode_pembayaran)) }}
                            </td>

                            <td>
                                <span class="jt-badge jt-badge-{{ $status }}">
                                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                                </span>
                            </td>

                            <td class="jt-note">{{ $item->keterangan ?? '-' }}</td>

                            <td class="jt-money text-right">{{ $rupiah($item->nominal) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="jt-empty">
                                Tidak ada transaksi dalam periode laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($transaksiLaporan->isNotEmpty())
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">Total</th>
                            <th class="text-right jt-money">{{ $rupiah($summary['total']) }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </section>
    <!-- FOOTER KHUSUS CETAK PDF -->
    <div class="jt-print-footer">
        <div class="jt-print-footer-top">
            <p class="jt-print-thankyou">
                <strong>Terima kasih atas ZISWAF yang Anda tunaikan.</strong><br>
                Semoga menjadi amal jariyah yang dilipatgandakan pahalanya dan diberkahi Allah SWT. Aamiin Yaa Rabbal 'Aalamiin.
            </p>
        </div>
        <div class="jt-print-verification">
            <p class="jt-print-date">Bandung, {{ now()->translatedFormat('d F Y') }}</p>
            <div class="jt-print-verify-badge">
                <i class="fa-solid fa-circle-check"></i>
                Telah Diverifikasi oleh Administrasi Masjid Pusdai Jabar
            </div>
        </div>
        <div class="jt-print-footer-bottom">
            <p>Laporan ini sah dan diterbitkan secara elektronik oleh sistem FINUS (Financial Integration and Information System) Masjid Pusdai Jawa Barat.</p>
        </div>
    </div>
</div>

@push('styles')
<style>
    .jt-print-header,
    .jt-print-footer,
    .jt-print-verification {
        display: none;
    }

    /* Enlarge summary card icons on screen */
    .jt-stat .jt-icon {
        width: 52px !important;
        min-width: 52px !important;
        height: 52px !important;
        border-radius: 14px !important;
        font-size: 22px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .jt-stat-body {
        margin-top: 14px !important;
    }

    .jt-stat strong {
        font-size: 22px !important;
        margin-top: 5px !important;
    }

    .jt-filter-head-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 9px;
    }

    .jt-monthly-chart-legend {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        margin: 4px 0 8px;
        color: #64748B;
        font-size: 14px;
        font-weight: 800;
        line-height: 1;
    }

    .jt-monthly-chart-legend span {
        width: 13px;
        min-width: 13px;
        height: 13px;
        border: 2px solid #0E5423;
        border-radius: 999px;
        background: rgba(23, 155, 64, .72);
    }

    .jt-type-composition {
        display: grid;
        grid-template-columns: minmax(220px, .9fr) minmax(240px, 1fr);
        gap: 18px;
        align-items: center;
    }

    .jt-type-composition .jt-chart-small {
        min-height: 300px;
    }

    .jt-type-detail {
        display: grid;
        gap: 12px;
        align-self: stretch;
        align-content: center;
    }

    .jt-type-detail-summary {
        padding-bottom: 12px;
        border-bottom: 1px solid #E2E8F0;
    }

    .jt-type-detail-summary span,
    .jt-type-detail-summary small,
    .jt-type-detail-value span,
    .jt-type-detail-main small {
        color: #64748B;
        font-size: 12px;
    }

    .jt-type-detail-summary strong {
        display: block;
        margin-top: 3px;
        color: #0E5423;
        font-size: 22px;
        font-weight: 850;
    }

    .jt-type-detail-list {
        display: grid;
        gap: 0;
    }

    .jt-type-detail-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 11px 0;
        border-bottom: 1px solid #E2E8F0;
    }

    .jt-type-detail-item:last-child {
        border-bottom: 0;
    }

    .jt-type-detail-main {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .jt-type-dot {
        width: 10px;
        min-width: 10px;
        height: 10px;
        border-radius: 999px;
        background: var(--type-color);
    }

    .jt-type-detail-main strong,
    .jt-type-detail-value strong {
        display: block;
        color: #1E293B;
        font-size: 13px;
        font-weight: 800;
    }

    .jt-type-detail-value {
        text-align: right;
        white-space: nowrap;
    }

    .jt-type-detail-value strong {
        color: #0E5423;
    }

    .jt-type-detail-empty {
        padding: 14px 0;
        color: #64748B;
        font-size: 12px;
        line-height: 1.5;
    }

    .jt-report-breakdown-section .jt-card-head h2,
    .jt-report-detail-section .jt-card-head h2 {
        font-size: 17px;
    }

    .jt-report-breakdown-section .jt-card-head p,
    .jt-report-detail-section .jt-card-head p {
        font-size: 13px;
    }

    .jt-report-breakdown-section .jt-breakdown-item strong {
        font-size: 14px;
    }

    .jt-report-breakdown-section .jt-breakdown-item small {
        font-size: 12px;
    }

    .jt-report-breakdown-section .jt-money {
        font-size: 15px;
    }

    .jt-report-detail-section .jt-table th {
        font-size: 12px;
    }

    .jt-report-detail-section .jt-table td {
        font-size: 14px;
    }

    .jt-report-detail-section .jt-reference,
    .jt-report-detail-section .jt-money {
        font-size: 14.5px;
    }

    .jt-report-detail-section .jt-type,
    .jt-report-detail-section .jt-badge {
        font-size: 12px;
    }

    @media (max-width: 767px) {
        .jt-filter-head-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .jt-type-composition {
            grid-template-columns: 1fr;
        }
    }

    @media print {
        /* Hide layout wrappers and unwanted blocks */
        .sidebar,
        .header,
        .header.finus-topbar,
        .finus-page-heading,
        .jt-heading,
        .jt-heading-actions,
        .jt-filter-card,
        .jt-no-print,
        .jt-pagination,
        footer,
        .finus-footer {
            display: none !important;
        }

        /* Adjust main layout */
        body {
            background: #ffffff !important;
            color: #1e293b !important;
            font-family: 'Nunito', sans-serif !important;
            font-size: 11px !important;
            line-height: 1.4 !important;
        }

        .content-wrap {
            margin-left: 0 !important;
            padding-top: 0 !important;
            width: 100% !important;
            background: #ffffff !important;
        }

        .content-wrap .main {
            padding: 0 !important;
            background: transparent !important;
        }

        .jt-page {
            padding: 0 !important;
            max-width: 100% !important;
            margin: 0 !important;
            background: #ffffff !important;
        }

        /* Print Header styling */
        .jt-print-header {
            display: block !important;
            margin-bottom: 25px;
        }

        .jt-print-header-top {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            margin-bottom: 15px;
        }

        .jt-print-logo-container {
            margin-bottom: 12px !important;
            display: flex !important;
            justify-content: center !important;
            width: 100% !important;
        }

        .jt-print-logo {
            height: 80px !important; /* Enlarged print header logo */
            width: auto;
            /* Filter to turn white logo into green (#179B40) */
            filter: brightness(0) saturate(100%) invert(46%) sepia(35%) saturate(1518%) hue-rotate(92deg) brightness(97%) contrast(84%) !important;
            -webkit-filter: brightness(0) saturate(100%) invert(46%) sepia(35%) saturate(1518%) hue-rotate(92deg) brightness(97%) contrast(84%) !important;
        }

        .jt-print-title-container {
            text-align: center !important;
            width: 100% !important;
        }

        .jt-print-org {
            font-size: 24px;
            font-weight: 900;
            color: #0E5423;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }

        .jt-print-tagline {
            font-size: 11px;
            font-weight: 700;
            color: #179B40;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .jt-print-address {
            font-size: 9.5px;
            color: #64748B;
            margin: 0;
        }

        .jt-print-divider {
            height: 4px;
            background: linear-gradient(to right, #0E5423, #179B40);
            margin-bottom: 20px;
            border-radius: 2px;
        }

        .jt-print-doc-title {
            font-size: 16px;
            font-weight: 850;
            text-align: center;
            margin: 0 0 15px 0;
            color: #0E5423;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .jt-print-meta-table {
            width: 100%;
            margin-bottom: 20px;
            border: 2px solid #64748B;
            border-radius: 8px;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            background: #F8FAFC;
        }

        .jt-print-meta-table td {
            padding: 10px 14px;
            font-size: 11px;
            vertical-align: middle;
            border-bottom: 1.5px solid #94A3B8;
        }

        .jt-print-meta-table tr:last-child td {
            border-bottom: none;
        }

        section.jt-print-summary-cards {
            display: none !important;
        }

        /* 4-grid stats cards style for printing when reused outside PDF export */
        .jt-grid-4 {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 12px !important;
            margin-bottom: 25px !important;
            page-break-inside: avoid;
        }

        .jt-grid-4.jt-print-summary-cards {
            display: none !important;
        }

        .jt-stat {
            border: 2px solid #94A3B8 !important;
            background: #FFFFFF !important;
            border-radius: 12px !important;
            padding: 14px !important;
            box-shadow: none !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            margin-bottom: 0 !important;
            min-height: 120px !important;
            height: auto !important;
            overflow: visible !important;
        }

        .jt-stat-top {
            display: flex !important; /* Keep icons visible in print */
            margin-bottom: 10px !important;
        }

        .jt-stat .jt-icon {
            width: 44px !important; /* Enlarged icon for print */
            min-width: 44px !important;
            height: 44px !important;
            border-radius: 12px !important;
            font-size: 18px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: var(--stat-soft, var(--jt-green-soft)) !important;
            color: var(--stat-color, var(--jt-green)) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .jt-stat-body {
            margin-top: 0 !important;
            display: block !important;
        }

        .jt-stat span {
            font-size: 10px !important;
            color: #475569 !important; /* Explicit slate gray text for labels in print */
            text-transform: uppercase;
            font-weight: 800;
            display: block !important;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .jt-stat strong {
            font-size: 16px !important;
            color: #0E5423 !important; /* Explicit solid color for value in print */
            font-weight: 800 !important;
            display: block !important;
        }

        /* Charts side-by-side print layout */
        .jt-grid-2 {
            display: grid !important;
            grid-template-columns: 1.15fr 0.85fr !important;
            gap: 15px !important;
            margin-bottom: 25px !important;
            page-break-inside: avoid;
        }

        .jt-print-chart-section {
            display: none !important;
        }

        .jt-card {
            border: 1.25px solid #8FA3B8 !important;
            box-shadow: none !important;
            border-radius: 8px !important;
            page-break-inside: avoid;
            margin-bottom: 0 !important;
            background: #FFFFFF !important;
            overflow: hidden !important;
        }

        .jt-card-head {
            padding: 10px 14px !important;
            border-bottom: 1.25px solid #B7C4D2 !important;
            background: #F8FAFC !important;
        }

        .jt-card-head h2 {
            font-size: 12px !important;
            font-weight: 800 !important;
            color: #0E5423 !important;
            margin: 0 !important;
        }

        .jt-card-head p {
            font-size: 9px !important;
            color: #64748B !important;
            margin: 3px 0 0 0 !important;
        }

        .jt-card-body {
            padding: 12px !important;
        }

        .jt-report-detail-section {
            margin-top: 18px !important;
        }

        .jt-report-breakdown-section,
        .jt-report-detail-section {
            border-color: #8FA3B8 !important;
        }

        .jt-report-detail-section .jt-card-head {
            border-bottom-color: #8FA3B8 !important;
        }

        .jt-chart,
        .jt-chart-small {
            height: 220px !important; /* Increased height to avoid clipping chart elements */
            position: relative;
            overflow: visible !important;
        }

        .jt-chart canvas {
            height: 100% !important; /* Force bar chart canvas to stretch width & height */
            width: 100% !important;
        }

        .jt-chart-small canvas {
            display: block !important;
            margin: 0 auto !important;
            max-height: 100% !important;
            max-width: 100% !important;
            height: auto !important; /* Allow doughnut to maintain aspect ratio naturally */
            width: auto !important;
        }

        /* Breakdown list */
        .jt-breakdown {
            margin: 0 !important;
            gap: 0 !important;
        }

        .jt-breakdown-item {
            padding: 10px 0 !important;
            border: 0 !important;
            border-bottom: 1px solid #AEBCCC !important;
            border-radius: 0 !important;
            background: #FFFFFF !important;
        }

        .jt-breakdown-item:last-child {
            border-bottom: none !important;
        }

        .jt-breakdown-item strong {
            font-size: 11px !important;
            color: #1e293b !important;
        }

        .jt-breakdown-item small {
            font-size: 9px !important;
            color: #64748B !important;
        }

        .jt-money {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #0E5423 !important;
        }

        /* Details table print styling */
        .jt-table-wrap {
            border: 0 !important;
            border-radius: 0 !important;
            overflow: hidden;
            margin-bottom: 0 !important;
        }

        .jt-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        .jt-table th {
            background: #F1F5F9 !important;
            color: #1E293B !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            padding: 8px 10px !important;
            border-bottom: 1.25px solid #8FA3B8 !important;
            text-transform: uppercase;
        }

        .jt-table td {
            padding: 8px 10px !important;
            font-size: 9.5px !important;
            border-bottom: 1px solid #B7C4D2 !important;
            color: #334155 !important;
        }

        .jt-table tr:last-child td {
            border-bottom: none !important;
        }

        .jt-badge {
            font-size: 9px !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            border: 1px solid #CBD5E1 !important;
            background: #FFFFFF !important;
            font-weight: 700 !important;
            display: inline-block;
        }

        .jt-badge-diterima {
            border-color: #86EFAC !important;
            color: #166534 !important;
            background: #F0FDF4 !important;
        }

        .jt-badge-pending {
            border-color: #FDE047 !important;
            color: #854D0E !important;
            background: #FEFCE8 !important;
        }

        .jt-badge-gagal,
        .jt-badge-ditolak,
        .jt-badge-dibatalkan {
            border-color: #FCA5A5 !important;
            color: #991B1B !important;
            background: #FEF2F2 !important;
        }

        .jt-table tfoot th,
        .jt-table tfoot td {
            background: #F8FAFC !important;
            border-top: 1.25px solid #8FA3B8 !important;
            padding: 8px 10px !important;
            font-size: 11px !important;
            font-weight: 800 !important;
        }

        /* Print Footer styling */
        .jt-print-footer {
            display: block !important;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .jt-print-footer-top {
            border-top: 1px solid #E2EAE5;
            padding-top: 12px;
            margin-bottom: 20px;
        }

        .jt-print-thankyou {
            font-size: 10px;
            color: #334155;
            line-height: 1.5;
            margin: 0;
            text-align: center;
        }

        .jt-print-verification {
            display: block !important;
            text-align: right !important;
            margin-top: 25px;
            margin-bottom: 25px;
            padding-right: 40px;
            page-break-inside: avoid;
        }

        .jt-print-date {
            font-size: 11px;
            color: #475569;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .jt-print-verify-badge {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            background: #F0FDF4 !important;
            border: 1.5px solid #86EFAC !important;
            color: #166534 !important;
            padding: 10px 18px !important;
            border-radius: 8px !important;
            font-size: 11px !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .jt-print-verify-badge i {
            font-size: 14px !important;
        }

        .jt-print-footer-bottom {
            text-align: center;
            font-size: 8.5px;
            color: #94A3B8;
            border-top: 1px dashed #E2E8F0;
            padding-top: 10px;
        }
    }
</style>
@endpush
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const money = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    const jenisChartData = @json($jenisChartData).map(value => Number(value || 0));
    const jenisChartTotal = jenisChartData.reduce((total, value) => total + value, 0);

    const monthlyCanvas = document.getElementById('jtMonthlyChart');

    if (monthlyCanvas) {
        new Chart(monthlyCanvas, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Nominal Transaksi',
                    data: @json($chartData),
                    backgroundColor: 'rgba(23, 155, 64, .72)',
                    borderColor: '#0E5423',
                    borderWidth: 1,
                    borderRadius: 7,
                    maxBarThickness: 45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: context => money(context.raw)
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748B', font: { size: 12 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(100,116,139,.14)' },
                        ticks: {
                            color: '#64748B',
                            font: { size: 12 },
                            callback: money
                        }
                    }
                }
            }
        });
    }

    const typeCanvas = document.getElementById('jtTypeChart');

    if (typeCanvas) {
        const centerLabelPlugin = {
            id: 'jtDoughnutCenterLabel',
            afterDraw(chart) {
                const { ctx, chartArea } = chart;

                if (!chartArea) {
                    return;
                }

                const firstArc = chart.getDatasetMeta(0).data[0];
                const centerX = firstArc?.x ?? (chartArea.left + chartArea.right) / 2;
                const centerY = firstArc?.y ?? (chartArea.top + chartArea.bottom) / 2;

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                if (jenisChartTotal > 0) {
                    ctx.fillStyle = '#64748B';
                    ctx.font = '600 12px Nunito, sans-serif';
                    ctx.fillText('Total', centerX, centerY - 10);

                    ctx.fillStyle = '#0E5423';
                    ctx.font = '800 14px Nunito, sans-serif';
                    ctx.fillText(money(jenisChartTotal), centerX, centerY + 10);
                } else {
                    ctx.fillStyle = '#64748B';
                    ctx.font = '700 13px Nunito, sans-serif';
                    ctx.fillText('Belum ada data', centerX, centerY);
                }

                ctx.restore();
            }
        };

        new Chart(typeCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($jenisChartLabels),
                datasets: [{
                    data: jenisChartData,
                    backgroundColor: @json($jenisChartColors),
                    borderColor: '#FFFFFF',
                    borderWidth: 4,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '67%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#64748B',
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 14,
                            boxHeight: 8,
                            padding: 14,
                            font: { size: 11, weight: '600' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: context => `${context.label}: ${money(context.raw)}`
                        }
                    }
                }
            },
            plugins: [centerLabelPlugin]
        });
    }

});
</script>
@endpush

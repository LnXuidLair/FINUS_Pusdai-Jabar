@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
@include('layouts.partials.finus-ui')
<style>
    :root {
        --finus-green-dark: #0E5423;
        --finus-green: #179B40;
        --finus-green-light: #22BA51;
        --finus-green-soft: #EAF8EE;
        --finus-green-soft-2: #F5FBF7;

        --finus-text: #172033;
        --finus-muted: #64748B;
        --finus-border: #E1EAE4;
        --finus-bg: #F5F9F6;

        --finus-red: #E5484D;
        --finus-red-soft: #FFF0F0;

        --finus-blue: #2563EB;
        --finus-blue-soft: #EDF4FF;

        --finus-orange: #EA8B22;
        --finus-orange-soft: #FFF7E8;

        --finus-purple: #7C3AED;
        --finus-purple-soft: #F4EEFF;

        --finus-cyan: #0891B2;
        --finus-cyan-soft: #EAFBFE;
    }

    .finus-dashboard {
        position: relative;
        padding: 8px 1px 35px;
    }

    /* =====================================================
       HEADER DASHBOARD
    ===================================================== */
    .finus-dashboard-header {
        position: relative;
        overflow: hidden;
        margin-bottom: 22px;
        padding: 28px 30px;
        border-radius: 22px;

        background:
            linear-gradient(
                135deg,
                #0E5423 0%,
                #179B40 48%,
                #22BA51 100%
            );

        box-shadow:
            0 18px 38px rgba(14, 84, 35, .17),
            inset 0 1px 0 rgba(255, 255, 255, .12);
    }

    .finus-dashboard-header::before {
        content: "";
        position: absolute;
        top: -110px;
        right: -50px;
        width: 270px;
        height: 270px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .10);
    }

    .finus-dashboard-header::after {
        content: "";
        position: absolute;
        right: 190px;
        bottom: -145px;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: rgba(126, 255, 135, .13);
    }

    .finus-dashboard-header-content {
        position: relative;
        z-index: 2;

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 22px;
    }

    .finus-dashboard-heading {
        display: flex;
        align-items: center;
        gap: 17px;
    }

    .finus-dashboard-heading-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 58px;
        min-width: 58px;
        height: 58px;

        border: 1px solid rgba(255, 255, 255, .20);
        border-radius: 17px;

        background: rgba(255, 255, 255, .14);
        color: #ffffff;

        font-size: 23px;

        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .14);
        backdrop-filter: blur(8px);
    }

    .finus-dashboard-title {
        margin: 0;
        color: #ffffff;
        font-size: 23px;
        font-weight: 800;
        line-height: 1.3;
        letter-spacing: -.02em;
    }

    .finus-dashboard-subtitle {
        margin: 6px 0 0;
        color: rgba(255, 255, 255, .81);
        font-size: 13.5px;
        line-height: 1.6;
    }

    .finus-dashboard-period {
        display: inline-flex;
        align-items: center;
        gap: 9px;

        min-height: 43px;
        padding: 0 15px;

        border: 1px solid rgba(255, 255, 255, .19);
        border-radius: 12px;

        background: rgba(7, 78, 31, .22);
        color: #ffffff;

        font-size: 13px;
        font-weight: 700;

        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .10);
        backdrop-filter: blur(8px);
    }

    .finus-dashboard-period i {
        color: #B8FFBF;
    }

    /* =====================================================
       SUMMARY CARDS
    ===================================================== */
    .finus-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 17px;
        margin-bottom: 22px;
    }

    .finus-summary-card {
        position: relative;
        overflow: hidden;

        min-height: 145px;
        padding: 20px;

        border: 1px solid var(--finus-border);
        border-radius: 18px;

        background: #ffffff;

        box-shadow:
            0 12px 28px rgba(15, 23, 42, .055),
            0 3px 10px rgba(14, 84, 35, .025);

        transition:
            transform .22s ease,
            box-shadow .22s ease,
            border-color .22s ease;
    }

    .finus-summary-card::after {
        content: "";
        position: absolute;
        right: -35px;
        bottom: -45px;

        width: 115px;
        height: 115px;

        border-radius: 50%;

        background: var(--card-soft);
        opacity: .75;
        pointer-events: none;
    }

    .finus-summary-card:hover {
        border-color: rgba(23, 155, 64, .22);
        transform: translateY(-3px);

        box-shadow:
            0 17px 35px rgba(15, 23, 42, .08),
            0 6px 15px rgba(14, 84, 35, .04);
    }

    .finus-summary-top {
        position: relative;
        z-index: 2;

        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
    }

    .finus-summary-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 45px;
        min-width: 45px;
        height: 45px;

        border-radius: 13px;

        background: var(--card-soft);
        color: var(--card-color);

        font-size: 18px;
    }

    .finus-summary-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 6px 9px;

        border-radius: 9px;

        background: #F5F8F6;
        color: var(--finus-muted);

        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .finus-summary-indicator::before {
        content: "";

        width: 6px;
        height: 6px;

        border-radius: 50%;

        background: var(--card-color);
        box-shadow: 0 0 0 3px var(--card-soft);
    }

    .finus-summary-content {
        position: relative;
        z-index: 2;
        margin-top: 17px;
    }

    .finus-summary-label {
        margin: 0 0 6px;

        color: var(--finus-muted);

        font-size: 12.5px;
        font-weight: 600;
        line-height: 1.4;
    }

    .finus-summary-value {
        margin: 0;

        color: var(--finus-text);

        font-size: 22px;
        font-weight: 800;
        line-height: 1.25;
        letter-spacing: -.025em;

        word-break: break-word;
    }

    .finus-summary-note {
        margin-top: 7px;

        color: #94A3B8;

        font-size: 11.5px;
        line-height: 1.45;
    }

    .finus-card-red {
        --card-color: var(--finus-red);
        --card-soft: var(--finus-red-soft);
    }

    .finus-card-green {
        --card-color: var(--finus-green);
        --card-soft: var(--finus-green-soft);
    }

    .finus-card-blue {
        --card-color: var(--finus-blue);
        --card-soft: var(--finus-blue-soft);
    }

    .finus-card-orange {
        --card-color: var(--finus-orange);
        --card-soft: var(--finus-orange-soft);
    }

    .finus-card-purple {
        --card-color: var(--finus-purple);
        --card-soft: var(--finus-purple-soft);
    }

    .finus-card-cyan {
        --card-color: var(--finus-cyan);
        --card-soft: var(--finus-cyan-soft);
    }

    /* =====================================================
       CONTENT CARDS
    ===================================================== */
    .finus-content-row {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, .8fr);
        gap: 18px;
    }

    .finus-content-card {
        overflow: hidden;

        border: 1px solid var(--finus-border);
        border-radius: 19px;

        background: #ffffff;

        box-shadow:
            0 14px 32px rgba(15, 23, 42, .06),
            0 4px 12px rgba(14, 84, 35, .025);
    }

    .finus-content-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;

        padding: 20px 22px;

        border-bottom: 1px solid #E9EFEB;

        background:
            linear-gradient(
                180deg,
                #FFFFFF 0%,
                #F9FCFA 100%
            );
    }

    .finus-content-heading {
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .finus-content-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 42px;
        min-width: 42px;
        height: 42px;

        border-radius: 12px;

        background: var(--finus-green-soft);
        color: var(--finus-green);

        font-size: 16px;
    }

    .finus-content-title {
        margin: 0;

        color: var(--finus-text);

        font-size: 16px;
        font-weight: 800;
        line-height: 1.35;
    }

    .finus-content-description {
        margin: 4px 0 0;

        color: var(--finus-muted);

        font-size: 12px;
        line-height: 1.45;
    }

    .finus-chart-legend {
        display: flex;
        align-items: center;
        gap: 13px;

        color: var(--finus-muted);

        font-size: 11.5px;
        font-weight: 600;
    }

    .finus-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .finus-legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .finus-legend-expense {
        background: #E5484D;
    }

    .finus-legend-payroll {
        background: #179B40;
    }

    .finus-chart-body {
        padding: 21px 20px 19px;
    }

    .finus-chart {
        position: relative;
        height: 350px;
    }

    /* =====================================================
       QUICK ACCESS
    ===================================================== */
    .finus-quick-body {
        padding: 15px;
    }

    .finus-quick-link {
        display: flex;
        align-items: center;
        gap: 12px;

        min-height: 59px;
        margin-bottom: 10px;
        padding: 10px 12px;

        border: 1px solid #E5ECE7;
        border-radius: 13px;

        background: #FBFDFC;
        color: var(--finus-text) !important;

        text-decoration: none !important;

        transition:
            background .2s ease,
            border-color .2s ease,
            box-shadow .2s ease,
            transform .2s ease;
    }

    .finus-quick-link:last-child {
        margin-bottom: 0;
    }

    .finus-quick-link:hover {
        border-color: #BEDCC7;
        background: var(--finus-green-soft-2);

        box-shadow: 0 8px 17px rgba(14, 84, 35, .07);

        transform: translateX(3px);
    }

    .finus-quick-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 38px;
        min-width: 38px;
        height: 38px;

        border-radius: 11px;

        background: var(--finus-green-soft);
        color: var(--finus-green);

        font-size: 14px;
    }

    .finus-quick-text {
        min-width: 0;
        flex: 1;
    }

    .finus-quick-title {
        color: var(--finus-text);

        font-size: 13px;
        font-weight: 800;
        line-height: 1.35;
    }

    .finus-quick-description {
        margin-top: 2px;

        color: var(--finus-muted);

        font-size: 10.5px;
        line-height: 1.4;
    }

    .finus-quick-arrow {
        color: #94A3B8;
        font-size: 11px;

        transition:
            color .2s ease,
            transform .2s ease;
    }

    .finus-quick-link:hover .finus-quick-arrow {
        color: var(--finus-green);
        transform: translateX(2px);
    }

    /* =====================================================
       TOTAL OPERASIONAL
    ===================================================== */
    .finus-operation-summary {
        position: relative;
        overflow: hidden;

        margin-top: 14px;
        padding: 17px;

        border-radius: 14px;

        background:
            linear-gradient(
                135deg,
                #0E5423 0%,
                #179B40 100%
            );

        color: #ffffff;
    }

    .finus-operation-summary::after {
        content: "";

        position: absolute;
        right: -35px;
        bottom: -45px;

        width: 120px;
        height: 120px;

        border-radius: 50%;

        background: rgba(255, 255, 255, .09);
    }

    .finus-operation-summary-label {
        position: relative;
        z-index: 2;

        color: rgba(255, 255, 255, .75);

        font-size: 11px;
        font-weight: 600;
    }

    .finus-operation-summary-value {
        position: relative;
        z-index: 2;

        margin-top: 6px;

        color: #ffffff;

        font-size: 20px;
        font-weight: 800;
        line-height: 1.3;
    }

    .finus-operation-summary-note {
        position: relative;
        z-index: 2;

        margin-top: 5px;

        color: rgba(255, 255, 255, .70);

        font-size: 10.5px;
    }

    /* =====================================================
       RESPONSIVE
    ===================================================== */
    @media (max-width: 1199px) {
        .finus-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .finus-content-row {
            grid-template-columns: minmax(0, 1.6fr) minmax(260px, .8fr);
        }
    }

    @media (max-width: 991px) {
        .finus-content-row {
            grid-template-columns: 1fr;
        }

        .finus-quick-body {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .finus-quick-link {
            margin-bottom: 0;
        }

        .finus-operation-summary {
            grid-column: 1 / -1;
            margin-top: 2px;
        }
    }

    @media (max-width: 767px) {
        .finus-dashboard-header {
            padding: 23px 20px;
            border-radius: 18px;
        }

        .finus-dashboard-header-content {
            align-items: flex-start;
            flex-direction: column;
        }

        .finus-dashboard-period {
            min-height: 39px;
        }

        .finus-dashboard-heading {
            align-items: flex-start;
        }

        .finus-dashboard-heading-icon {
            width: 50px;
            min-width: 50px;
            height: 50px;
            border-radius: 14px;
        }

        .finus-dashboard-title {
            font-size: 20px;
        }

        .finus-summary-card {
            min-height: 137px;
        }

        .finus-content-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .finus-chart {
            height: 320px;
        }
    }

    @media (max-width: 575px) {
        .finus-summary {
            grid-template-columns: 1fr;
            gap: 13px;
        }

        .finus-summary-card {
            min-height: auto;
            padding: 18px;
        }

        .finus-quick-body {
            grid-template-columns: 1fr;
        }

        .finus-operation-summary {
            grid-column: auto;
        }

        .finus-chart-body {
            padding: 18px 10px 16px;
        }

        .finus-chart {
            height: 290px;
        }

        .finus-chart-legend {
            flex-wrap: wrap;
        }
    }
</style>

@php
    $totalOperasionalBulanIni =
        (float) $pengeluaranBulanIni +
        (float) $gajiBulanIni;
@endphp

<div class="finus-dashboard">

    {{-- Header Dashboard --}}
    <div class="finus-dashboard-header">
        <div class="finus-dashboard-header-content">

            <div class="finus-dashboard-heading">
                <div class="finus-dashboard-heading-icon">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>

                <div>
                    <h3 class="finus-dashboard-title">
                        Dashboard Admin FINUS
                    </h3>

                    <p class="finus-dashboard-subtitle">
                        Ringkasan aktivitas keuangan dan operasional masjid.
                    </p>
                </div>
            </div>

            <div class="finus-dashboard-period">
                <i class="fa-solid fa-calendar-days"></i>
                <span>{{ now()->format('d/m/Y') }}</span>
            </div>

        </div>
    </div>

    {{-- Kartu Ringkasan --}}
    <div class="finus-summary">

        <div class="finus-summary-card finus-card-red">
            <div class="finus-summary-top">
                <div class="finus-summary-icon">
                    <i class="fa-solid fa-receipt"></i>
                </div>

                <div class="finus-summary-indicator">
                    Bulan ini
                </div>
            </div>

            <div class="finus-summary-content">
                <p class="finus-summary-label">
                    Total Pengeluaran
                </p>

                <h4 class="finus-summary-value">
                    Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}
                </h4>

                <div class="finus-summary-note">
                    Pengeluaran operasional yang telah dicatat.
                </div>
            </div>
        </div>

        <div class="finus-summary-card finus-card-green">
            <div class="finus-summary-top">
                <div class="finus-summary-icon">
                    <i class="fa-solid fa-wallet"></i>
                </div>

                <div class="finus-summary-indicator">
                    Bulan ini
                </div>
            </div>

            <div class="finus-summary-content">
                <p class="finus-summary-label">
                    Total Penggajian
                </p>

                <h4 class="finus-summary-value">
                    Rp {{ number_format($gajiBulanIni, 0, ',', '.') }}
                </h4>

                <div class="finus-summary-note">
                    Gaji pegawai yang tercatat pada periode ini.
                </div>
            </div>
        </div>

        <div class="finus-summary-card finus-card-blue">
            <div class="finus-summary-top">
                <div class="finus-summary-icon">
                    <i class="fa-solid fa-book-open"></i>
                </div>

                <div class="finus-summary-indicator">
                    Bulan ini
                </div>
            </div>

            <div class="finus-summary-content">
                <p class="finus-summary-label">
                    Jurnal Keuangan
                </p>

                <h4 class="finus-summary-value">
                    {{ number_format($jurnalBulanIni, 0, ',', '.') }} transaksi
                </h4>

                <div class="finus-summary-note">
                    Jumlah transaksi jurnal yang telah dicatat.
                </div>
            </div>
        </div>

        <div class="finus-summary-card finus-card-orange">
            <div class="finus-summary-top">
                <div class="finus-summary-icon">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div class="finus-summary-indicator">
                    Aktif
                </div>
            </div>

            <div class="finus-summary-content">
                <p class="finus-summary-label">
                    Pegawai Masjid
                </p>

                <h4 class="finus-summary-value">
                    {{ number_format($jumlahPegawai, 0, ',', '.') }} orang
                </h4>

                <div class="finus-summary-note">
                    Total pegawai yang terdaftar di dalam sistem.
                </div>
            </div>
        </div>

        <div class="finus-summary-card finus-card-purple">
            <div class="finus-summary-top">
                <div class="finus-summary-icon">
                    <i class="fa-solid fa-user-group"></i>
                </div>

                <div class="finus-summary-indicator">
                    Terdaftar
                </div>
            </div>

            <div class="finus-summary-content">
                <p class="finus-summary-label">
                    Akun Jamaah
                </p>

                <h4 class="finus-summary-value">
                    {{ number_format($jumlahJamaah, 0, ',', '.') }} akun
                </h4>

                <div class="finus-summary-note">
                    Jumlah akun jamaah yang telah terdaftar.
                </div>
            </div>
        </div>

        <div class="finus-summary-card finus-card-cyan">
            <div class="finus-summary-top">
                <div class="finus-summary-icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>

                <div class="finus-summary-indicator">
                    Hari ini
                </div>
            </div>

            <div class="finus-summary-content">
                <p class="finus-summary-label">
                    Presensi Pegawai
                </p>

                <h4 class="finus-summary-value">
                    {{ number_format($presensiHariIni, 0, ',', '.') }} catatan
                </h4>

                <div class="finus-summary-note">
                    Presensi pegawai yang tercatat hari ini.
                </div>
            </div>
        </div>

    </div>

    {{-- Grafik dan Menu Cepat --}}
    <div class="finus-content-row">

        {{-- Grafik --}}
        <div class="finus-content-card">
            <div class="finus-content-header">
                <div class="finus-content-heading">
                    <div class="finus-content-icon">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>

                    <div>
                        <h5 class="finus-content-title">
                            Operasional Tahunan
                        </h5>

                        <p class="finus-content-description">
                            Perbandingan pengeluaran dan penggajian tahun
                            {{ now()->year }}.
                        </p>
                    </div>
                </div>

                <div class="finus-chart-legend">
                    <div class="finus-legend-item">
                        <span class="finus-legend-dot finus-legend-expense"></span>
                        Pengeluaran
                    </div>

                    <div class="finus-legend-item">
                        <span class="finus-legend-dot finus-legend-payroll"></span>
                        Penggajian
                    </div>
                </div>
            </div>

            <div class="finus-chart-body">
                <div class="finus-chart">
                    <canvas id="operasionalChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Akses Cepat --}}
        <div class="finus-content-card">
            <div class="finus-content-header">
                <div class="finus-content-heading">
                    <div class="finus-content-icon">
                        <i class="fa-solid fa-bolt"></i>
                    </div>

                    <div>
                        <h5 class="finus-content-title">
                            Akses Cepat
                        </h5>

                        <p class="finus-content-description">
                            Buka menu pengelolaan utama.
                        </p>
                    </div>
                </div>
            </div>

            <div class="finus-quick-body">

                <a
                    href="{{ route('admin.pengeluaran.index') }}"
                    class="finus-quick-link"
                >
                    <div class="finus-quick-icon">
                        <i class="fa-solid fa-receipt"></i>
                    </div>

                    <div class="finus-quick-text">
                        <div class="finus-quick-title">
                            Pengeluaran
                        </div>

                        <div class="finus-quick-description">
                            Kelola transaksi pengeluaran.
                        </div>
                    </div>

                    <i class="fa-solid fa-chevron-right finus-quick-arrow"></i>
                </a>

                <a
                    href="{{ route('admin.penggajian.index') }}"
                    class="finus-quick-link"
                >
                    <div class="finus-quick-icon">
                        <i class="fa-solid fa-wallet"></i>
                    </div>

                    <div class="finus-quick-text">
                        <div class="finus-quick-title">
                            Penggajian
                        </div>

                        <div class="finus-quick-description">
                            Kelola pembayaran gaji pegawai.
                        </div>
                    </div>

                    <i class="fa-solid fa-chevron-right finus-quick-arrow"></i>
                </a>

                <a
                    href="{{ route('admin.pegawai.index') }}"
                    class="finus-quick-link"
                >
                    <div class="finus-quick-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div class="finus-quick-text">
                        <div class="finus-quick-title">
                            Data Pegawai
                        </div>

                        <div class="finus-quick-description">
                            Lihat dan kelola pegawai masjid.
                        </div>
                    </div>

                    <i class="fa-solid fa-chevron-right finus-quick-arrow"></i>
                </a>

                <a
                    href="{{ route('admin.laporan.jurnal-umum') }}"
                    class="finus-quick-link"
                >
                    <div class="finus-quick-icon">
                        <i class="fa-solid fa-book-open"></i>
                    </div>

                    <div class="finus-quick-text">
                        <div class="finus-quick-title">
                            Jurnal Umum
                        </div>

                        <div class="finus-quick-description">
                            Lihat pencatatan jurnal keuangan.
                        </div>
                    </div>

                    <i class="fa-solid fa-chevron-right finus-quick-arrow"></i>
                </a>

                <div class="finus-operation-summary">
                    <div class="finus-operation-summary-label">
                        Total operasional bulan ini
                    </div>

                    <div class="finus-operation-summary-value">
                        Rp {{ number_format($totalOperasionalBulanIni, 0, ',', '.') }}
                    </div>

                    <div class="finus-operation-summary-note">
                        Pengeluaran dan penggajian bulan berjalan.
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartCanvas = document.getElementById('operasionalChart');

    if (!chartCanvas) {
        return;
    }

    const months = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agu',
        'Sep',
        'Okt',
        'Nov',
        'Des'
    ];

    const fillMonths = function (data) {
        return Array.from(
            { length: 12 },
            function (_, index) {
                return Number(data[index + 1] || 0);
            }
        );
    };

    const pengeluaranData = fillMonths(
        @json($pengeluaranBulanan)
    );

    const penggajianData = fillMonths(
        @json($penggajianBulanan)
    );

    const context = chartCanvas.getContext('2d');

    const expenseGradient = context.createLinearGradient(
        0,
        0,
        0,
        350
    );

    expenseGradient.addColorStop(
        0,
        'rgba(229, 72, 77, 0.88)'
    );

    expenseGradient.addColorStop(
        1,
        'rgba(229, 72, 77, 0.42)'
    );

    const payrollGradient = context.createLinearGradient(
        0,
        0,
        0,
        350
    );

    payrollGradient.addColorStop(
        0,
        'rgba(23, 155, 64, 0.92)'
    );

    payrollGradient.addColorStop(
        1,
        'rgba(34, 186, 81, 0.43)'
    );

    new Chart(context, {
        type: 'bar',

        data: {
            labels: months,

            datasets: [
                {
                    label: 'Pengeluaran',
                    data: pengeluaranData,
                    backgroundColor: expenseGradient,
                    borderColor: '#E5484D',
                    borderWidth: 1,
                    borderRadius: 7,
                    borderSkipped: false,
                    maxBarThickness: 30,
                    categoryPercentage: 0.72,
                    barPercentage: 0.78
                },
                {
                    label: 'Penggajian',
                    data: penggajianData,
                    backgroundColor: payrollGradient,
                    borderColor: '#179B40',
                    borderWidth: 1,
                    borderRadius: 7,
                    borderSkipped: false,
                    maxBarThickness: 30,
                    categoryPercentage: 0.72,
                    barPercentage: 0.78
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
                mode: 'index',
                intersect: false
            },

            animation: {
                duration: 750
            },

            plugins: {
                legend: {
                    display: false
                },

                tooltip: {
                    backgroundColor: '#172033',
                    titleColor: '#FFFFFF',
                    bodyColor: '#E2E8F0',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: true,

                    callbacks: {
                        label: function (context) {
                            const value = Number(
                                context.parsed.y || 0
                            );

                            return context.dataset.label +
                                ': Rp ' +
                                value.toLocaleString('id-ID');
                        }
                    }
                }
            },

            scales: {
                x: {
                    grid: {
                        display: false
                    },

                    border: {
                        display: false
                    },

                    ticks: {
                        color: '#64748B',
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    }
                },

                y: {
                    beginAtZero: true,

                    grid: {
                        color: 'rgba(148, 163, 184, 0.15)',
                        drawTicks: false
                    },

                    border: {
                        display: false
                    },

                    ticks: {
                        color: '#64748B',
                        padding: 10,
                        font: {
                            size: 10
                        },

                        callback: function (value) {
                            const number = Number(value);

                            if (number >= 1000000000) {
                                return 'Rp ' +
                                    (number / 1000000000)
                                        .toLocaleString('id-ID') +
                                    ' M';
                            }

                            if (number >= 1000000) {
                                return 'Rp ' +
                                    (number / 1000000)
                                        .toLocaleString('id-ID') +
                                    ' jt';
                            }

                            if (number >= 1000) {
                                return 'Rp ' +
                                    (number / 1000)
                                        .toLocaleString('id-ID') +
                                    ' rb';
                            }

                            return 'Rp ' +
                                number.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: dashboard/admin.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="dashboard/admin.blade.php">
html[data-finus-theme="dark"] body .finus-dashboard { --finus-text:#F1F6F3; --finus-muted:#9EAEA4; --finus-border:#293D31; }
html[data-finus-theme="dark"] body .finus-dashboard .finus-summary-card { border-color:#293D31 !important; background:linear-gradient(155deg,#15231B,#101914) !important; color:#F1F6F3 !important; box-shadow:0 13px 30px rgba(0,0,0,.20) !important; }
html[data-finus-theme="dark"] body .finus-dashboard .finus-summary-value { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-dashboard :where(.finus-summary-label,.finus-summary-note,.finus-content-description,.finus-quick-description,.finus-operation-summary-note) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .finus-dashboard .finus-content-card { border-color:#293D31 !important; background:linear-gradient(155deg,#15231B,#101914) !important; box-shadow:0 14px 32px rgba(0,0,0,.21) !important; }
html[data-finus-theme="dark"] body .finus-dashboard .finus-content-header { border-color:#293D31 !important; background:linear-gradient(180deg,#17251D,#121D17) !important; }
html[data-finus-theme="dark"] body .finus-dashboard :where(.finus-content-title,.finus-quick-title,.finus-operation-summary-value) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-dashboard .finus-quick-link { border-color:#293D31 !important; background:#121F18 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-dashboard .finus-quick-link:hover { border-color:#365141 !important; background:#192A20 !important; }
</style>
@endpush


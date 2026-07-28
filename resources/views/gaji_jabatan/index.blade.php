@extends('layouts.app')

@section('title', 'Gaji & Jabatan')
@section('hide-page-header', '1')

@php
    $gajiJabatanCollection = $gajiJabatans instanceof \Illuminate\Contracts\Pagination\Paginator
        ? collect($gajiJabatans->items())
        : collect($gajiJabatans);

    $totalJabatan = $gajiJabatanCollection->count();
    $totalGajiHarian = (float) $gajiJabatanCollection->sum('gaji_perhari');
    $rataRataGaji = $totalJabatan > 0
        ? $totalGajiHarian / $totalJabatan
        : 0;

    $gajiTertinggi = (float) ($gajiJabatanCollection->max('gaji_perhari') ?? 0);
@endphp

@section('content')
@include('layouts.partials.finus-ui')
<style>
    :root {
        --gj-dark: #0E5423;
        --gj-green: #179B40;
        --gj-light: #22BA51;
        --gj-soft: #EAF8EE;
        --gj-soft-2: #F5FBF7;

        --gj-text: #172033;
        --gj-muted: #64748B;
        --gj-border: #E1EAE4;

        --gj-blue: #2563EB;
        --gj-blue-soft: #EEF4FF;

        --gj-amber: #D97706;
        --gj-amber-soft: #FFF7E6;

        --gj-purple: #7C3AED;
        --gj-purple-soft: #F5F0FF;
    }

    .gj-page {
        position: relative;
        padding-bottom: 32px;
    }

    .gj-page::before {
        content: "";
        position: absolute;
        top: -55px;
        right: 2%;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(34, 186, 81, .055);
        pointer-events: none;
    }

    /* =====================================================
       HERO
    ===================================================== */
    .gj-hero {
        position: relative;
        z-index: 1;
        overflow: hidden;

        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 22px;

        margin-bottom: 17px;
        padding: 27px 29px;

        border-radius: 22px;

        background:
            radial-gradient(
                circle at 91% 7%,
                rgba(255, 255, 255, .16),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #0E5423 0%,
                #179B40 48%,
                #22BA51 100%
            );

        box-shadow:
            0 18px 38px rgba(14, 84, 35, .17),
            inset 0 1px 0 rgba(255, 255, 255, .13);
    }

    .gj-hero::after {
        content: "";
        position: absolute;
        right: 170px;
        bottom: -145px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(126, 255, 135, .12);
        pointer-events: none;
    }

    .gj-hero-left,
    .gj-hero-action {
        position: relative;
        z-index: 2;
    }

    .gj-hero-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .gj-hero-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 58px;
        min-width: 58px;
        height: 58px;

        border: 1px solid rgba(255, 255, 255, .21);
        border-radius: 17px;

        background: rgba(255, 255, 255, .14);
        color: #FFFFFF;

        font-size: 22px;

        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, .15);

        backdrop-filter: blur(8px);
    }

    .gj-hero-title {
        margin: 0;

        color: #FFFFFF;

        font-size: 23px;
        font-weight: 800;
        line-height: 1.3;
        letter-spacing: -.02em;
    }

    .gj-hero-subtitle {
        display: flex;
        align-items: center;
        gap: 8px;

        margin: 6px 0 0;

        color: rgba(255, 255, 255, .82);

        font-size: 13px;
        line-height: 1.5;
    }

    .gj-hero-subtitle::before {
        content: "";

        width: 7px;
        height: 7px;

        border-radius: 50%;

        background: #A5FFAE;

        box-shadow:
            0 0 0 4px rgba(165, 255, 174, .13);
    }

    .gj-add-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;

        min-height: 45px;
        padding: 0 17px;

        border: 1px solid rgba(255, 255, 255, .70);
        border-radius: 12px;

        background: #FFFFFF;
        color: var(--gj-dark) !important;

        font-size: 13px;
        font-weight: 800;

        text-decoration: none !important;
        white-space: nowrap;

        box-shadow:
            0 9px 20px rgba(0, 70, 27, .16);

        transition:
            transform .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .gj-add-button i {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 24px;
        height: 24px;

        border-radius: 7px;

        background: var(--gj-soft);
        color: var(--gj-green);

        font-size: 11px;
    }

    .gj-add-button:hover {
        background: #F3FFF6;

        transform: translateY(-2px);

        box-shadow:
            0 13px 25px rgba(0, 70, 27, .22);
    }

    /* =====================================================
       SUMMARY
    ===================================================== */
    .gj-summary {
        position: relative;
        z-index: 1;

        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;

        margin-bottom: 17px;
    }

    .gj-stat-card {
        position: relative;
        overflow: hidden;

        display: flex;
        align-items: center;
        gap: 13px;

        min-height: 96px;
        padding: 17px 18px;

        border: 1px solid var(--gj-border);
        border-radius: 17px;

        background: #FFFFFF;

        box-shadow:
            0 10px 25px rgba(15, 23, 42, .055);

        transition:
            transform .2s ease,
            border-color .2s ease,
            box-shadow .2s ease;
    }

    .gj-stat-card::after {
        content: "";

        position: absolute;
        right: -35px;
        bottom: -44px;

        width: 105px;
        height: 105px;

        border-radius: 50%;

        background: var(--stat-soft);
        opacity: .8;
    }

    .gj-stat-card:hover {
        border-color: rgba(23, 155, 64, .23);

        transform: translateY(-2px);

        box-shadow:
            0 15px 31px rgba(15, 23, 42, .075);
    }

    .gj-stat-icon,
    .gj-stat-content {
        position: relative;
        z-index: 1;
    }

    .gj-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 45px;
        min-width: 45px;
        height: 45px;

        border-radius: 13px;

        background: var(--stat-soft);
        color: var(--stat-color);

        font-size: 17px;
    }

    .gj-stat-label {
        display: block;

        color: var(--gj-muted);

        font-size: 11.5px;
        font-weight: 700;
        line-height: 1.4;
    }

    .gj-stat-value {
        display: block;

        margin-top: 4px;

        color: var(--gj-text);

        font-size: 18px;
        font-weight: 800;
        line-height: 1.25;

        word-break: break-word;
    }

    .gj-stat-total {
        --stat-color: var(--gj-blue);
        --stat-soft: var(--gj-blue-soft);
    }

    .gj-stat-sum {
        --stat-color: var(--gj-green);
        --stat-soft: var(--gj-soft);
    }

    .gj-stat-average {
        --stat-color: var(--gj-amber);
        --stat-soft: var(--gj-amber-soft);
    }

    .gj-stat-highest {
        --stat-color: var(--gj-purple);
        --stat-soft: var(--gj-purple-soft);
    }

    /* =====================================================
       MAIN CARD
    ===================================================== */
    .gj-card {
        position: relative;
        z-index: 1;
        overflow: hidden;

        border: 1px solid rgba(14, 84, 35, .09);
        border-radius: 20px;

        background: #FFFFFF;

        box-shadow:
            0 18px 42px rgba(15, 23, 42, .075);
    }

    .gj-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;

        padding: 20px 22px;

        border-bottom: 1px solid #E7EEE9;

        background:
            linear-gradient(
                180deg,
                #FFFFFF,
                #F9FCFA
            );
    }

    .gj-card-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .gj-card-heading-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 41px;
        min-width: 41px;
        height: 41px;

        border-radius: 12px;

        background: var(--gj-soft);
        color: var(--gj-green);
    }

    .gj-card-title {
        margin: 0;

        color: var(--gj-text);

        font-size: 16px;
        font-weight: 800;
        line-height: 1.35;
    }

    .gj-card-description {
        margin: 4px 0 0;

        color: var(--gj-muted);

        font-size: 11.5px;
        line-height: 1.45;
    }

    .gj-visible-count {
        display: inline-flex;
        align-items: center;
        gap: 7px;

        padding: 7px 11px;

        border: 1px solid #D6E9DC;
        border-radius: 10px;

        background: var(--gj-soft);
        color: var(--gj-dark);

        font-size: 11px;
        font-weight: 800;

        white-space: nowrap;
    }

    /* =====================================================
       SEARCH
    ===================================================== */
    .gj-toolbar {
        padding: 17px 22px;

        border-bottom: 1px solid #E9EFEB;

        background: #FFFFFF;
    }

    .gj-search-wrapper {
        position: relative;

        width: min(100%, 460px);
    }

    .gj-search-icon {
        position: absolute;
        top: 50%;
        left: 15px;
        z-index: 2;

        color: #789084;

        font-size: 13px;

        transform: translateY(-50%);
        pointer-events: none;
    }

    .gj-search {
        width: 100%;
        height: 44px;

        padding: 0 42px 0 41px;

        border: 1px solid #D8E4DC;
        border-radius: 12px;

        background: #FBFDFC;
        color: var(--gj-text);

        font-size: 13px;

        outline: none;

        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .gj-search::placeholder {
        color: #94A3B8;
    }

    .gj-search:hover {
        border-color: #BFD4C6;
        background: #FFFFFF;
    }

    .gj-search:focus {
        border-color: var(--gj-green);
        background: #FFFFFF;

        box-shadow:
            0 0 0 4px rgba(23, 155, 64, .11);
    }

    .gj-clear-search {
        position: absolute;
        top: 50%;
        right: 9px;

        display: none;
        align-items: center;
        justify-content: center;

        width: 28px;
        height: 28px;
        padding: 0;

        border: 0;
        border-radius: 8px;

        background: #EDF5EF;
        color: #64748B;

        cursor: pointer;

        transform: translateY(-50%);
    }

    .gj-clear-search:hover {
        background: #DFF1E4;
        color: var(--gj-green);
    }

    .gj-search-help {
        display: flex;
        align-items: center;
        gap: 6px;

        margin: 7px 0 0;

        color: #7A8B82;

        font-size: 10.5px;
    }

    .gj-search-help i {
        color: var(--gj-green);
    }

    /* =====================================================
       TABLE
    ===================================================== */
    .gj-table-area {
        padding: 20px 22px 24px;

        background:
            linear-gradient(
                180deg,
                rgba(234, 248, 238, .32),
                #FFFFFF 86px
            );
    }

    .gj-table-wrapper {
        overflow: hidden;

        border: 1px solid var(--gj-border);
        border-radius: 15px;

        background: #FFFFFF;
    }

    .gj-table {
        width: 100%;
        margin: 0;

        border-collapse: separate;
        border-spacing: 0;
    }

    .gj-table thead th {
        padding: 14px 15px;

        border: 0;
        border-bottom: 1px solid var(--gj-border);

        background: #F3F8F4;
        color: #4B6253;

        font-size: 10.5px;
        font-weight: 800;
        line-height: 1.4;
        letter-spacing: .07em;

        text-align: center !important;
        text-transform: uppercase;
        vertical-align: middle;
    }

    .gj-table tbody td {
        padding: 14px 15px;

        border: 0;
        border-bottom: 1px solid #ECF1ED;

        background: #FFFFFF;
        color: #334155;

        font-size: 13px;

        text-align: center !important;
        vertical-align: middle;

        transition:
            background .2s ease;
    }

    .gj-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .gj-table tbody tr[data-search-row]:hover td {
        background: #F7FCF8;
    }

    .gj-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        width: 31px;
        height: 31px;

        border: 1px solid #D6E9DC;
        border-radius: 9px;

        background: var(--gj-soft-2);
        color: var(--gj-dark);

        font-size: 11.5px;
        font-weight: 800;
    }

    .gj-position {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;

        max-width: 100%;
    }

    .gj-position-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        width: 38px;
        min-width: 38px;
        height: 38px;

        border-radius: 11px;

        background: var(--gj-soft);
        color: var(--gj-green);

        font-size: 14px;
    }

    .gj-position-name {
        color: var(--gj-text);

        font-size: 13.5px;
        font-weight: 800;
        line-height: 1.35;
    }

    .gj-position-note {
        margin-top: 3px;

        color: var(--gj-muted);

        font-size: 10.5px;
        line-height: 1.4;
    }

    .gj-salary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        min-height: 34px;
        padding: 0 12px;

        border: 1px solid #CDE8D5;
        border-radius: 10px;

        background: #EFFAF2;
        color: #137035;

        font-size: 12.5px;
        font-weight: 800;

        white-space: nowrap;
    }

    .gj-edit-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;

        min-height: 36px;
        padding: 0 12px;

        border: 1px solid #F1D89F;
        border-radius: 10px;

        background: #FFF9EA;
        color: #A16207 !important;

        font-size: 12px;
        font-weight: 800;

        text-decoration: none !important;

        transition:
            background .2s ease,
            border-color .2s ease,
            transform .2s ease,
            box-shadow .2s ease;
    }

    .gj-edit-button:hover {
        border-color: #E7C66F;

        background: #FFF3CC;
        color: #854D0E !important;

        transform: translateY(-1px);

        box-shadow:
            0 6px 13px rgba(161, 98, 7, .13);
    }

    /* =====================================================
       EMPTY STATE
    ===================================================== */
    .gj-empty-state {
        padding: 54px 20px !important;

        text-align: center !important;
    }

    .gj-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 62px;
        height: 62px;

        margin: 0 auto 14px;

        border-radius: 18px;

        background: var(--gj-soft);
        color: var(--gj-green);

        font-size: 23px;
    }

    .gj-empty-title {
        color: var(--gj-text);

        font-size: 14px;
        font-weight: 800;
    }

    .gj-empty-description {
        margin: 5px 0 0;

        color: var(--gj-muted);

        font-size: 12px;
    }

    /* =====================================================
       RESPONSIVE
    ===================================================== */
    @media (max-width: 1199.98px) {
        .gj-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .gj-card-header {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (max-width: 767.98px) {
        .gj-hero {
            align-items: flex-start;
            flex-direction: column;

            padding: 23px 20px;

            border-radius: 18px;
        }

        .gj-hero-icon {
            width: 51px;
            min-width: 51px;
            height: 51px;

            border-radius: 15px;
        }

        .gj-hero-title {
            font-size: 20px;
        }

        .gj-hero-action,
        .gj-add-button {
            width: 100%;
        }

        .gj-summary {
            grid-template-columns: 1fr;
            gap: 11px;
        }

        .gj-stat-card {
            min-height: 82px;
        }

        .gj-card {
            border-radius: 17px;
        }

        .gj-card-header,
        .gj-toolbar {
            padding: 17px;
        }

        .gj-search-wrapper {
            width: 100%;
        }

        .gj-table-area {
            padding: 15px;
        }

        .gj-table-wrapper {
            overflow: visible;

            border: 0;

            background: transparent;
        }

        .gj-table,
        .gj-table tbody,
        .gj-table tr,
        .gj-table td {
            display: block;

            width: 100%;
        }

        .gj-table thead {
            display: none;
        }

        .gj-table tbody tr[data-search-row] {
            overflow: hidden;

            margin-bottom: 13px;

            border: 1px solid var(--gj-border);
            border-radius: 15px;

            background: #FFFFFF;

            box-shadow:
                0 8px 20px rgba(15, 23, 42, .045);
        }

        .gj-table tbody tr[data-search-row] td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;

            min-height: 52px;
            padding: 11px 14px;

            border-bottom: 1px solid #ECF1ED;

            text-align: right !important;
        }

        .gj-table tbody tr[data-search-row] td:last-child {
            border-bottom: 0;
        }

        .gj-table tbody tr[data-search-row] td::before {
            content: attr(data-label);

            flex-shrink: 0;

            color: #718078;

            font-size: 9.5px;
            font-weight: 800;
            letter-spacing: .06em;

            text-align: left;
            text-transform: uppercase;
        }

        .gj-position {
            justify-content: flex-end;
        }

        .gj-position-icon {
            display: none;
        }

        .gj-empty-state {
            display: block !important;

            padding: 44px 18px !important;

            border: 1px solid var(--gj-border) !important;
            border-radius: 15px;

            background: #FFFFFF !important;
        }

        .gj-empty-state::before {
            display: none;
        }
    }
</style>

<div class="gj-page">
    <section class="gj-hero">
        <div class="gj-hero-left">
            <div class="gj-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-briefcase"></i>
            </div>

            <div>
                <h1 class="gj-hero-title">
                    Gaji & Jabatan
                </h1>

                <p
                    class="gj-hero-subtitle"
                    id="recordCount"
                    data-label="data jabatan"
                >
                    Kelola {{ number_format($totalJabatan) }}
                    jabatan dan nominal gaji harian pegawai.
                </p>
            </div>
        </div>

        <div class="gj-hero-action">
            <a
                href="{{ route('admin.gaji-jabatan.create') }}"
                class="gj-add-button"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Jabatan
            </a>
        </div>
    </section>

    <section class="gj-summary" aria-label="Ringkasan gaji dan jabatan">
        <article class="gj-stat-card gj-stat-total">
            <div class="gj-stat-icon" aria-hidden="true">
                <i class="fa-solid fa-briefcase"></i>
            </div>

            <div class="gj-stat-content">
                <span class="gj-stat-label">
                    Total Jabatan
                </span>

                <strong class="gj-stat-value">
                    {{ number_format($totalJabatan) }}
                </strong>
            </div>
        </article>

        <article class="gj-stat-card gj-stat-sum">
            <div class="gj-stat-icon" aria-hidden="true">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>

            <div class="gj-stat-content">
                <span class="gj-stat-label">
                    Total Gaji Harian
                </span>

                <strong class="gj-stat-value">
                    Rp {{ number_format($totalGajiHarian, 0, ',', '.') }}
                </strong>
            </div>
        </article>

        <article class="gj-stat-card gj-stat-average">
            <div class="gj-stat-icon" aria-hidden="true">
                <i class="fa-solid fa-chart-line"></i>
            </div>

            <div class="gj-stat-content">
                <span class="gj-stat-label">
                    Rata-rata Gaji
                </span>

                <strong class="gj-stat-value">
                    Rp {{ number_format($rataRataGaji, 0, ',', '.') }}
                </strong>
            </div>
        </article>

        <article class="gj-stat-card gj-stat-highest">
            <div class="gj-stat-icon" aria-hidden="true">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>

            <div class="gj-stat-content">
                <span class="gj-stat-label">
                    Gaji Tertinggi
                </span>

                <strong class="gj-stat-value">
                    Rp {{ number_format($gajiTertinggi, 0, ',', '.') }}
                </strong>
            </div>
        </article>
    </section>

    <section class="gj-card">
        <header class="gj-card-header">
            <div class="gj-card-heading">
                <div class="gj-card-heading-icon" aria-hidden="true">
                    <i class="fa-solid fa-list-check"></i>
                </div>

                <div>
                    <h2 class="gj-card-title">
                        Daftar Gaji Jabatan
                    </h2>

                    <p class="gj-card-description">
                        Kelola jabatan dan nominal gaji harian pegawai masjid.
                    </p>
                </div>
            </div>

            <div class="gj-visible-count">
                <i class="fa-solid fa-database"></i>

                <span id="visibleCount">
                    {{ number_format($totalJabatan) }}
                </span>

                data ditampilkan
            </div>
        </header>

        <div class="gj-toolbar">
            <div class="gj-search-wrapper">
                <i class="fa-solid fa-magnifying-glass gj-search-icon"></i>

                <input
                    id="searchInput"
                    type="search"
                    class="gj-search"
                    placeholder="Ketik dari awal nama jabatan atau nominal gaji..."
                    autocomplete="off"
                    aria-label="Cari gaji dan jabatan"
                >

                <button
                    type="button"
                    id="clearSearch"
                    class="gj-clear-search"
                    aria-label="Hapus pencarian"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <p class="gj-search-help">
                <i class="fa-solid fa-circle-info"></i>
                Pencarian dimulai dari karakter pertama setiap data.
            </p>
        </div>

        <div class="gj-table-area">
            <div class="gj-table-wrapper">
                <table id="searchTable" class="gj-table">
                    <thead>
                        <tr>
                            <th width="80">No.</th>
                            <th>Nama Jabatan</th>
                            <th width="250">Gaji per Hari</th>
                            <th width="140">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($gajiJabatans as $item)
                            @php
                                $gajiFormatted = 'Rp ' . number_format(
                                    $item->gaji_perhari,
                                    0,
                                    ',',
                                    '.'
                                );

                                $gajiRaw = preg_replace(
                                    '/\D+/',
                                    '',
                                    (string) $item->gaji_perhari
                                );
                            @endphp

                            <tr
                                data-search-row
                                data-search-start="{{ $item->jabatan }}|{{ $gajiFormatted }}|{{ $gajiRaw }}"
                            >
                                <td data-label="Nomor">
                                    <span
                                        class="gj-number"
                                        data-row-number
                                    >
                                        {{ $loop->iteration }}
                                    </span>
                                </td>

                                <td data-label="Jabatan">
                                    <div class="gj-position">
                                        <div class="gj-position-icon">
                                            <i class="fa-solid fa-user-tie"></i>
                                        </div>

                                        <div>
                                            <div class="gj-position-name">
                                                {{ $item->jabatan }}
                                            </div>

                                            <div class="gj-position-note">
                                                Jabatan pegawai masjid
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Gaji per Hari">
                                    <span class="gj-salary">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                        {{ $gajiFormatted }}
                                    </span>
                                </td>

                                <td data-label="Aksi">
                                    <a
                                        href="{{ route('admin.gaji-jabatan.edit', $item) }}"
                                        class="gj-edit-button"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="4"
                                    class="gj-empty-state"
                                >
                                    <div class="gj-empty-icon">
                                        <i class="fa-solid fa-briefcase"></i>
                                    </div>

                                    <div class="gj-empty-title">
                                        Belum ada data gaji jabatan
                                    </div>

                                    <p class="gj-empty-description">
                                        Tekan tombol Tambah Jabatan untuk memasukkan data pertama.
                                    </p>
                                </td>
                            </tr>
                        @endforelse

                        <tr
                            id="emptySearchRow"
                            style="display: none;"
                        >
                            <td
                                colspan="4"
                                class="gj-empty-state"
                            >
                                <div class="gj-empty-icon">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </div>

                                <div class="gj-empty-title">
                                    Data tidak ditemukan
                                </div>

                                <p class="gj-empty-description">
                                    Coba ketik dari karakter pertama nama jabatan atau nominal gaji.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script>
(() => {
    const input = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearSearch');
    const rows = Array.from(
        document.querySelectorAll('[data-search-row]')
    );
    const emptyRow = document.getElementById('emptySearchRow');
    const recordCount = document.getElementById('recordCount');
    const visibleCount = document.getElementById('visibleCount');

    const normalize = value => {
        return (value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    };

    const onlyDigits = value => {
        return (value || '').replace(/\D/g, '');
    };

    const filterRows = () => {
        if (!input) {
            return;
        }

        const keyword = normalize(input.value);
        const keywordDigits = onlyDigits(keyword);

        let visible = 0;

        rows.forEach(row => {
            const values = (row.dataset.searchStart || '')
                .split('|')
                .map(normalize)
                .filter(Boolean);

            /*
             * Pencarian teks dimulai dari karakter pertama.
             */
            const matchesText =
                keyword === ''
                || values.some(value => {
                    return value.startsWith(keyword);
                });

            /*
             * Pencarian nominal dimulai dari angka pertama.
             */
            const matchesNumber =
                keywordDigits !== ''
                && values.some(value => {
                    return onlyDigits(value)
                        .startsWith(keywordDigits);
                });

            const matches =
                keyword === ''
                || matchesText
                || matchesNumber;

            row.style.display = matches ? '' : 'none';

            if (matches) {
                visible++;

                const numberElement = row.querySelector(
                    '[data-row-number]'
                );

                if (numberElement) {
                    numberElement.textContent = visible;
                }
            }
        });

        if (emptyRow) {
            emptyRow.style.display =
                visible === 0 && rows.length > 0
                    ? ''
                    : 'none';
        }

        if (visibleCount) {
            visibleCount.textContent = visible;
        }

        if (recordCount) {
            recordCount.textContent =
                `Menampilkan ${visible} data jabatan pada daftar saat ini.`;
        }

        if (clearButton) {
            clearButton.style.display =
                input.value.trim() !== ''
                    ? 'flex'
                    : 'none';
        }
    };

    input?.addEventListener(
        'input',
        filterRows
    );

    clearButton?.addEventListener(
        'click',
        () => {
            input.value = '';
            input.focus();
            filterRows();
        }
    );

    filterRows();
})();
</script>
@endsection
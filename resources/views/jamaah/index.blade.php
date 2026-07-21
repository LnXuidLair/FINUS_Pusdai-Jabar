@extends('layouts.app')

@section('title', 'Data Jamaah')
@section('hide-page-header', '1')

@php
    $jamaahList = $jamaahs ?? collect();

    $jamaahCollection = $jamaahList instanceof \Illuminate\Contracts\Pagination\Paginator
        ? collect($jamaahList->items())
        : collect($jamaahList);

    $totalJamaah = $jamaahCollection->count();
    $jamaahTerverifikasi = $jamaahCollection
        ->filter(fn ($jamaah) => ! empty($jamaah->email_verified_at))
        ->count();
    $jamaahBelumVerifikasi = max(0, $totalJamaah - $jamaahTerverifikasi);
    $totalSeluruhTransaksi = (int) $jamaahCollection
        ->sum(fn ($jamaah) => (int) ($jamaah->total_transaksi ?? 0));
    $totalSeluruhNominal = (float) $jamaahCollection
        ->sum(fn ($jamaah) => (float) ($jamaah->total_nominal ?? 0));
@endphp

@push('styles')
<style>
    :root {
        --jm-dark:#0E5423;
        --jm-green:#179B40;
        --jm-light:#22BA51;
        --jm-soft:#EAF8EE;
        --jm-soft-2:#F5FBF7;
        --jm-text:#172033;
        --jm-muted:#64748B;
        --jm-border:#E1EAE4;
        --jm-blue:#2563EB;
        --jm-blue-soft:#EEF4FF;
        --jm-amber:#D97706;
        --jm-amber-soft:#FFF7E6;
        --jm-purple:#7C3AED;
        --jm-purple-soft:#F5F0FF;
    }

    .jm-page {
        position:relative;
        padding-bottom:32px;
    }

    .jm-page::before {
        content:"";
        position:absolute;
        top:-55px;
        right:2%;
        width:260px;
        height:260px;
        border-radius:50%;
        background:rgba(34,186,81,.055);
        pointer-events:none;
    }

    .jm-hero {
        position:relative;
        z-index:1;
        overflow:hidden;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:22px;
        margin-bottom:17px;
        padding:27px 29px;
        border-radius:22px;
        background:
            radial-gradient(circle at 91% 7%,rgba(255,255,255,.16),transparent 30%),
            linear-gradient(135deg,#0E5423 0%,#179B40 48%,#22BA51 100%);
        box-shadow:0 18px 38px rgba(14,84,35,.17);
    }

    .jm-hero::after {
        content:"";
        position:absolute;
        right:170px;
        bottom:-145px;
        width:220px;
        height:220px;
        border-radius:50%;
        background:rgba(126,255,135,.12);
    }

    .jm-hero-left,
    .jm-hero-badge {
        position:relative;
        z-index:2;
    }

    .jm-hero-left {
        display:flex;
        align-items:center;
        gap:16px;
        min-width:0;
    }

    .jm-hero-icon {
        display:flex;
        align-items:center;
        justify-content:center;
        width:58px;
        min-width:58px;
        height:58px;
        border:1px solid rgba(255,255,255,.21);
        border-radius:17px;
        background:rgba(255,255,255,.14);
        color:#fff;
        font-size:22px;
        backdrop-filter:blur(8px);
    }

    .jm-hero-title {
        margin:0;
        color:#fff;
        font-size:23px;
        font-weight:800;
        line-height:1.3;
        letter-spacing:-.02em;
    }

    .jm-hero-subtitle {
        display:flex;
        align-items:center;
        gap:8px;
        margin:6px 0 0;
        color:rgba(255,255,255,.82);
        font-size:13px;
    }

    .jm-hero-subtitle::before {
        content:"";
        width:7px;
        height:7px;
        border-radius:50%;
        background:#A5FFAE;
        box-shadow:0 0 0 4px rgba(165,255,174,.13);
    }

    .jm-hero-badge {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        min-height:42px;
        padding:0 14px;
        border:1px solid rgba(255,255,255,.22);
        border-radius:12px;
        background:rgba(0,80,29,.20);
        color:#fff;
        font-size:12px;
        font-weight:800;
        white-space:nowrap;
        backdrop-filter:blur(8px);
    }

    .jm-hero-badge i {
        color:#B7FFBE;
    }

    .jm-summary {
        position:relative;
        z-index:1;
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:14px;
        margin-bottom:17px;
    }

    .jm-stat {
        position:relative;
        overflow:hidden;
        display:flex;
        align-items:center;
        gap:13px;
        min-height:96px;
        padding:17px 18px;
        border:1px solid var(--jm-border);
        border-radius:17px;
        background:#fff;
        box-shadow:0 10px 25px rgba(15,23,42,.055);
        transition:.2s;
    }

    .jm-stat::after {
        content:"";
        position:absolute;
        right:-35px;
        bottom:-44px;
        width:105px;
        height:105px;
        border-radius:50%;
        background:var(--stat-soft);
        opacity:.8;
    }

    .jm-stat:hover {
        border-color:rgba(23,155,64,.23);
        transform:translateY(-2px);
        box-shadow:0 15px 31px rgba(15,23,42,.075);
    }

    .jm-stat-icon,
    .jm-stat-content {
        position:relative;
        z-index:1;
    }

    .jm-stat-icon {
        display:flex;
        align-items:center;
        justify-content:center;
        width:45px;
        min-width:45px;
        height:45px;
        border-radius:13px;
        background:var(--stat-soft);
        color:var(--stat-color);
        font-size:17px;
    }

    .jm-stat small {
        display:block;
        color:var(--jm-muted);
        font-size:11.5px;
        font-weight:700;
    }

    .jm-stat strong {
        display:block;
        margin-top:4px;
        color:var(--jm-text);
        font-size:18px;
        font-weight:800;
        line-height:1.25;
        word-break:break-word;
    }

    .jm-stat-total { --stat-color:var(--jm-blue); --stat-soft:var(--jm-blue-soft); }
    .jm-stat-verified { --stat-color:var(--jm-green); --stat-soft:var(--jm-soft); }
    .jm-stat-unverified { --stat-color:var(--jm-amber); --stat-soft:var(--jm-amber-soft); }
    .jm-stat-nominal { --stat-color:var(--jm-purple); --stat-soft:var(--jm-purple-soft); }

    .jm-card {
        position:relative;
        z-index:1;
        overflow:hidden;
        border:1px solid rgba(14,84,35,.09);
        border-radius:20px;
        background:#fff;
        box-shadow:0 18px 42px rgba(15,23,42,.075);
    }

    .jm-card-head {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        padding:20px 22px;
        border-bottom:1px solid #E7EEE9;
        background:linear-gradient(180deg,#fff,#F9FCFA);
    }

    .jm-card-title {
        display:flex;
        align-items:center;
        gap:12px;
    }

    .jm-card-title-icon {
        display:flex;
        align-items:center;
        justify-content:center;
        width:41px;
        min-width:41px;
        height:41px;
        border-radius:12px;
        background:var(--jm-soft);
        color:var(--jm-green);
    }

    .jm-card-title h2 {
        margin:0;
        color:var(--jm-text);
        font-size:16px;
        font-weight:800;
    }

    .jm-card-title p {
        margin:4px 0 0;
        color:var(--jm-muted);
        font-size:11.5px;
    }

    .jm-visible {
        display:inline-flex;
        align-items:center;
        gap:7px;
        padding:7px 11px;
        border:1px solid #D6E9DC;
        border-radius:10px;
        background:var(--jm-soft);
        color:var(--jm-dark);
        font-size:11px;
        font-weight:800;
        white-space:nowrap;
    }

    .jm-toolbar {
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:15px;
        padding:17px 22px;
        border-bottom:1px solid #E9EFEB;
    }

    .jm-search-area {
        flex:1;
        min-width:0;
    }

    .jm-search-wrap {
        position:relative;
        width:min(100%,470px);
    }

    .jm-search-icon {
        position:absolute;
        top:50%;
        left:15px;
        z-index:2;
        color:#789084;
        font-size:13px;
        transform:translateY(-50%);
        pointer-events:none;
    }

    .jm-search {
        width:100%;
        height:44px;
        padding:0 42px 0 41px;
        border:1px solid #D8E4DC;
        border-radius:12px;
        background:#FBFDFC;
        color:var(--jm-text);
        font-size:13px;
        outline:none;
        transition:.2s;
    }

    .jm-search::placeholder {
        color:#94A3B8;
    }

    .jm-search:focus {
        border-color:var(--jm-green);
        background:#fff;
        box-shadow:0 0 0 4px rgba(23,155,64,.11);
    }

    .jm-clear {
        position:absolute;
        top:50%;
        right:9px;
        display:none;
        align-items:center;
        justify-content:center;
        width:28px;
        height:28px;
        padding:0;
        border:0;
        border-radius:8px;
        background:#EDF5EF;
        color:#64748B;
        transform:translateY(-50%);
        cursor:pointer;
    }

    .jm-search-help {
        display:flex;
        align-items:center;
        gap:6px;
        margin:7px 0 0;
        color:#7A8B82;
        font-size:10.5px;
    }

    .jm-search-help i {
        color:var(--jm-green);
    }

    .jm-filters {
        display:inline-flex;
        gap:5px;
        padding:4px;
        border:1px solid #DCE7DF;
        border-radius:12px;
        background:#F7FAF8;
    }

    .jm-filter {
        min-height:34px;
        padding:0 12px;
        border:0;
        border-radius:9px;
        background:transparent;
        color:#64748B;
        font-size:11px;
        font-weight:800;
        white-space:nowrap;
        cursor:pointer;
        transition:.2s;
    }

    .jm-filter:hover {
        background:#EDF7F0;
        color:var(--jm-dark);
    }

    .jm-filter.active {
        background:#fff;
        color:var(--jm-dark);
        box-shadow:0 4px 10px rgba(15,23,42,.07);
    }

    .jm-table-area {
        padding:20px 22px 24px;
        background:linear-gradient(180deg,rgba(234,248,238,.32),#fff 86px);
    }

    .jm-table-wrap {
        overflow:hidden;
        border:1px solid var(--jm-border);
        border-radius:15px;
        background:#fff;
    }

    .jm-table {
        width:100%;
        margin:0;
        border-collapse:separate;
        border-spacing:0;
    }

    .jm-table thead th {
        padding:14px 11px;
        border:0;
        border-bottom:1px solid var(--jm-border);
        background:#F3F8F4;
        color:#4B6253;
        font-size:9.5px;
        font-weight:800;
        letter-spacing:.06em;
        text-align:center!important;
        text-transform:uppercase;
        vertical-align:middle;
        white-space:nowrap;
    }

    .jm-table tbody td {
        padding:14px 11px;
        border:0;
        border-bottom:1px solid #ECF1ED;
        background:#fff;
        color:#334155;
        font-size:12px;
        text-align:center!important;
        vertical-align:middle;
        transition:.2s;
    }

    .jm-table tbody tr:last-child td {
        border-bottom:0;
    }

    .jm-table tbody tr[data-search-row]:hover td {
        background:#F7FCF8;
    }

    .jm-number {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:31px;
        height:31px;
        border:1px solid #D6E9DC;
        border-radius:9px;
        background:var(--jm-soft-2);
        color:var(--jm-dark);
        font-size:11.5px;
        font-weight:800;
    }

    .jm-identity {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:10px;
        min-width:180px;
    }

    .jm-avatar {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:38px;
        min-width:38px;
        height:38px;
        border:1px solid #CFE6D6;
        border-radius:11px;
        background:linear-gradient(135deg,#EAF8EE,#D8F2DF);
        color:var(--jm-dark);
        font-size:12px;
        font-weight:900;
    }

    .jm-name {
        color:var(--jm-text);
        font-size:13px;
        font-weight:800;
    }

    .jm-email {
        display:flex;
        align-items:center;
        justify-content:center;
        gap:5px;
        margin-top:3px;
        color:var(--jm-muted);
        font-size:10px;
        word-break:break-word;
    }

    .jm-status,
    .jm-chip,
    .jm-money {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:7px;
        min-height:31px;
        padding:0 10px;
        border:1px solid;
        border-radius:10px;
        font-size:10.5px;
        font-weight:800;
        white-space:nowrap;
    }

    .jm-status {
        border-radius:999px;
    }

    .jm-status::before {
        content:"";
        width:6px;
        height:6px;
        border-radius:50%;
    }

    .jm-status.verified {
        border-color:#BDE5C7;
        background:#EFFAF2;
        color:#166534;
    }

    .jm-status.verified::before {
        background:#22C55E;
        box-shadow:0 0 0 3px rgba(34,197,94,.12);
    }

    .jm-status.unverified {
        border-color:#F1D59A;
        background:#FFF8E8;
        color:#92400E;
    }

    .jm-status.unverified::before {
        background:#F59E0B;
        box-shadow:0 0 0 3px rgba(245,158,11,.12);
    }

    .jm-chip {
        border-color:#C9DBFA;
        background:var(--jm-blue-soft);
        color:#1D4ED8;
    }

    .jm-money {
        border-color:#CDE8D5;
        background:#EFFAF2;
        color:#137035;
    }

    .jm-date {
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:6px;
        color:#475569;
        font-size:10.5px;
    }

    .jm-date i {
        color:var(--jm-green);
    }

    .jm-empty {
        padding:54px 20px!important;
        text-align:center!important;
    }

    .jm-empty-icon {
        display:flex;
        align-items:center;
        justify-content:center;
        width:62px;
        height:62px;
        margin:0 auto 14px;
        border-radius:18px;
        background:var(--jm-soft);
        color:var(--jm-green);
        font-size:23px;
    }

    .jm-empty strong {
        display:block;
        color:var(--jm-text);
        font-size:14px;
    }

    .jm-empty span {
        display:block;
        margin-top:5px;
        color:var(--jm-muted);
        font-size:12px;
    }

    @media (max-width:1199.98px) {
        .jm-summary {
            grid-template-columns:repeat(2,minmax(0,1fr));
        }
    }

    @media (max-width:991.98px) {
        .jm-card-head,
        .jm-toolbar {
            align-items:flex-start;
            flex-direction:column;
        }

        .jm-toolbar,
        .jm-search-wrap,
        .jm-filters {
            width:100%;
        }

        .jm-filter {
            flex:1;
        }
    }

    @media (max-width:767.98px) {
        .jm-hero {
            align-items:flex-start;
            flex-direction:column;
            padding:23px 20px;
            border-radius:18px;
        }

        .jm-hero-icon {
            width:51px;
            min-width:51px;
            height:51px;
        }

        .jm-hero-title {
            font-size:20px;
        }

        .jm-hero-badge {
            width:100%;
        }

        .jm-summary {
            grid-template-columns:1fr;
            gap:11px;
        }

        .jm-card {
            border-radius:17px;
        }

        .jm-card-head,
        .jm-toolbar {
            padding:17px;
        }

        .jm-table-area {
            padding:15px;
        }

        .jm-table-wrap {
            overflow:visible;
            border:0;
            background:transparent;
        }

        .jm-table,
        .jm-table tbody,
        .jm-table tr,
        .jm-table td {
            display:block;
            width:100%;
        }

        .jm-table thead {
            display:none;
        }

        .jm-table tbody tr[data-search-row] {
            overflow:hidden;
            margin-bottom:13px;
            border:1px solid var(--jm-border);
            border-radius:15px;
            background:#fff;
            box-shadow:0 8px 20px rgba(15,23,42,.045);
        }

        .jm-table tbody tr[data-search-row] td {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:14px;
            min-height:52px;
            padding:11px 14px;
            border-bottom:1px solid #ECF1ED;
            text-align:right!important;
        }

        .jm-table tbody tr[data-search-row] td:last-child {
            border-bottom:0;
        }

        .jm-table tbody tr[data-search-row] td::before {
            content:attr(data-label);
            flex-shrink:0;
            color:#718078;
            font-size:9.5px;
            font-weight:800;
            letter-spacing:.06em;
            text-align:left;
            text-transform:uppercase;
        }

        .jm-identity {
            justify-content:flex-end;
            min-width:0;
        }

        .jm-avatar {
            display:none;
        }

        .jm-empty {
            display:block!important;
            padding:44px 18px!important;
            border:1px solid var(--jm-border)!important;
            border-radius:15px;
            background:#fff!important;
        }

        .jm-empty::before {
            display:none;
        }
    }
</style>
@endpush

@section('content')
<div class="jm-page">
    <section class="jm-hero">
        <div class="jm-hero-left">
            <div class="jm-hero-icon">
                <i class="fa-solid fa-users"></i>
            </div>

            <div>
                <h1 class="jm-hero-title">Data Jamaah</h1>
                <p class="jm-hero-subtitle" id="recordCount">
                    Kelola {{ number_format($totalJamaah) }} akun jamaah yang terdaftar di FINUS.
                </p>
            </div>
        </div>

        <div class="jm-hero-badge">
            <i class="fa-solid fa-mosque"></i>
            Jamaah PUSDAI Jawa Barat
        </div>
    </section>

    <section class="jm-summary">
        <article class="jm-stat jm-stat-total">
            <div class="jm-stat-icon"><i class="fa-solid fa-users"></i></div>
            <div class="jm-stat-content">
                <small>Total Jamaah</small>
                <strong>{{ number_format($totalJamaah) }}</strong>
            </div>
        </article>

        <article class="jm-stat jm-stat-verified">
            <div class="jm-stat-icon"><i class="fa-solid fa-user-check"></i></div>
            <div class="jm-stat-content">
                <small>Terverifikasi</small>
                <strong>{{ number_format($jamaahTerverifikasi) }}</strong>
            </div>
        </article>

        <article class="jm-stat jm-stat-unverified">
            <div class="jm-stat-icon"><i class="fa-solid fa-user-clock"></i></div>
            <div class="jm-stat-content">
                <small>Belum Verifikasi</small>
                <strong>{{ number_format($jamaahBelumVerifikasi) }}</strong>
            </div>
        </article>

        <article class="jm-stat jm-stat-nominal">
            <div class="jm-stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div class="jm-stat-content">
                <small>Total Nominal</small>
                <strong>Rp {{ number_format($totalSeluruhNominal, 0, ',', '.') }}</strong>
            </div>
        </article>
    </section>

    <section class="jm-card">
        <header class="jm-card-head">
            <div class="jm-card-title">
                <div class="jm-card-title-icon">
                    <i class="fa-solid fa-address-book"></i>
                </div>
                <div>
                    <h2>Daftar Jamaah</h2>
                    <p>Informasi akun, status verifikasi, dan aktivitas transaksi jamaah.</p>
                </div>
            </div>

            <div class="jm-visible">
                <i class="fa-solid fa-database"></i>
                <span id="visibleCount">{{ $totalJamaah }}</span>
                data ditampilkan
            </div>
        </header>

        <div class="jm-toolbar">
            <div class="jm-search-area">
                <div class="jm-search-wrap">
                    <i class="fa-solid fa-magnifying-glass jm-search-icon"></i>
                    <input
                        id="searchInput"
                        type="search"
                        class="jm-search"
                        placeholder="Ketik dari awal nama, email, transaksi, atau nominal..."
                        autocomplete="off"
                    >
                    <button
                        type="button"
                        id="clearSearch"
                        class="jm-clear"
                        aria-label="Hapus pencarian"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <p class="jm-search-help">
                    <i class="fa-solid fa-circle-info"></i>
                    Pencarian dimulai dari karakter pertama setiap data.
                </p>
            </div>

            <div class="jm-filters" role="group" aria-label="Filter status jamaah">
                <button type="button" class="jm-filter active" data-status-filter="all">
                    Semua ({{ $totalJamaah }})
                </button>
                <button type="button" class="jm-filter" data-status-filter="verified">
                    Terverifikasi ({{ $jamaahTerverifikasi }})
                </button>
                <button type="button" class="jm-filter" data-status-filter="unverified">
                    Belum ({{ $jamaahBelumVerifikasi }})
                </button>
            </div>
        </div>

        <div class="jm-table-area">
            <div class="jm-table-wrap">
                <table id="jamaahTable" class="jm-table">
                    <thead>
                        <tr>
                            <th width="65">No.</th>
                            <th>Identitas Jamaah</th>
                            <th width="150">Status</th>
                            <th width="145">Total Transaksi</th>
                            <th width="180">Total Nominal</th>
                            <th width="165">Transaksi Terakhir</th>
                            <th width="165">Tanggal Daftar</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($jamaahList as $jamaah)
                            @php
                                $totalTransaksi = (int) ($jamaah->total_transaksi ?? 0);
                                $totalNominal = (float) ($jamaah->total_nominal ?? 0);
                                $totalNominalFormatted = 'Rp ' . number_format($totalNominal, 0, ',', '.');
                                $totalNominalRaw = preg_replace('/\D+/', '', (string) $totalNominal);

                                $statusVerifikasi = $jamaah->email_verified_at
                                    ? 'Terverifikasi'
                                    : 'Belum Verifikasi';

                                $statusKey = $jamaah->email_verified_at
                                    ? 'verified'
                                    : 'unverified';

                                $tanggalDaftar = $jamaah->created_at
                                    ? $jamaah->created_at->format('d/m/Y H:i')
                                    : '-';

                                $transaksiTerakhir = ! empty($jamaah->transaksi_terakhir)
                                    ? \Carbon\Carbon::parse($jamaah->transaksi_terakhir)->format('d/m/Y H:i')
                                    : '-';

                                $nameParts = preg_split('/\s+/', trim((string) $jamaah->name));

                                $initials = mb_strtoupper(
                                    mb_substr($nameParts[0] ?? 'J', 0, 1)
                                    . (
                                        count($nameParts) > 1
                                            ? mb_substr($nameParts[count($nameParts) - 1], 0, 1)
                                            : ''
                                    )
                                );
                            @endphp

                            <tr
                                data-search-row
                                data-status="{{ $statusKey }}"
                                data-search-start="{{ $jamaah->name }}|{{ $jamaah->email }}|{{ $statusVerifikasi }}|{{ $totalTransaksi }}|{{ $totalNominalFormatted }}|{{ $totalNominalRaw }}|{{ $transaksiTerakhir }}|{{ $tanggalDaftar }}"
                            >
                                <td data-label="Nomor">
                                    <span class="jm-number" data-row-number>{{ $loop->iteration }}</span>
                                </td>

                                <td data-label="Jamaah">
                                    <div class="jm-identity">
                                        <div class="jm-avatar">{{ $initials }}</div>
                                        <div>
                                            <div class="jm-name">{{ $jamaah->name }}</div>
                                            <div class="jm-email">
                                                <i class="fa-solid fa-envelope"></i>
                                                {{ $jamaah->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Status">
                                    <span class="jm-status {{ $statusKey }}">
                                        {{ $statusVerifikasi }}
                                    </span>
                                </td>

                                <td data-label="Total Transaksi">
                                    <span class="jm-chip">
                                        <i class="fa-solid fa-receipt"></i>
                                        {{ number_format($totalTransaksi, 0, ',', '.') }} transaksi
                                    </span>
                                </td>

                                <td data-label="Total Nominal">
                                    <span class="jm-money">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                        {{ $totalNominalFormatted }}
                                    </span>
                                </td>

                                <td data-label="Transaksi Terakhir">
                                    <span class="jm-date">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                        {{ $transaksiTerakhir }}
                                    </span>
                                </td>

                                <td data-label="Tanggal Daftar">
                                    <span class="jm-date">
                                        <i class="fa-solid fa-calendar-plus"></i>
                                        {{ $tanggalDaftar }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="jm-empty">
                                    <div class="jm-empty-icon">
                                        <i class="fa-solid fa-users-slash"></i>
                                    </div>
                                    <strong>Belum ada data jamaah</strong>
                                    <span>Data akan tampil setelah jamaah melakukan pendaftaran.</span>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="emptySearchRow" style="display:none;">
                            <td colspan="7" class="jm-empty">
                                <div class="jm-empty-icon">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </div>
                                <strong>Data jamaah tidak ditemukan</strong>
                                <span>Coba ketik dari karakter pertama atau ubah filter status.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const input = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearSearch');
    const rows = Array.from(document.querySelectorAll('[data-search-row]'));
    const emptyRow = document.getElementById('emptySearchRow');
    const recordCount = document.getElementById('recordCount');
    const visibleCount = document.getElementById('visibleCount');
    const filters = Array.from(document.querySelectorAll('[data-status-filter]'));

    let activeStatus = 'all';

    const normalize = value => (value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();

    const onlyDigits = value => (value || '').replace(/\D/g, '');

    const filterRows = () => {
        if (!input) return;

        const keyword = normalize(input.value);
        const keywordDigits = onlyDigits(keyword);
        let visible = 0;

        rows.forEach(row => {
            const values = (row.dataset.searchStart || '')
                .split('|')
                .map(normalize)
                .filter(Boolean);

            const matchText = keyword === ''
                || values.some(value => value.startsWith(keyword));

            const matchNumber = keywordDigits !== ''
                && values.some(value => onlyDigits(value).startsWith(keywordDigits));

            const matchSearch = keyword === ''
                || matchText
                || matchNumber;

            const matchStatus = activeStatus === 'all'
                || row.dataset.status === activeStatus;

            const match = matchSearch && matchStatus;

            row.style.display = match ? '' : 'none';

            if (match) {
                visible++;
                const number = row.querySelector('[data-row-number]');
                if (number) number.textContent = visible;
            }
        });

        if (emptyRow) {
            emptyRow.style.display =
                visible === 0 && rows.length > 0 ? '' : 'none';
        }

        if (visibleCount) visibleCount.textContent = visible;

        if (recordCount) {
            recordCount.textContent =
                `Menampilkan ${visible} jamaah pada daftar saat ini.`;
        }

        if (clearButton) {
            clearButton.style.display =
                input.value.trim() ? 'flex' : 'none';
        }
    };

    input?.addEventListener('input', filterRows);

    clearButton?.addEventListener('click', () => {
        input.value = '';
        input.focus();
        filterRows();
    });

    filters.forEach(button => {
        button.addEventListener('click', () => {
            activeStatus = button.dataset.statusFilter || 'all';

            filters.forEach(item => {
                const active = item === button;
                item.classList.toggle('active', active);
                item.setAttribute('aria-pressed', active ? 'true' : 'false');
            });

            filterRows();
        });
    });

    filterRows();
})();
</script>
@endpush
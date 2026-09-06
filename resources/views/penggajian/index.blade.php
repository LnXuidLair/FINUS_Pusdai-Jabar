@extends('layouts.app')

@section('title', 'Penggajian')
@section('hide-page-header', '1')

@php
    $penggajianItems = $penggajians instanceof \Illuminate\Contracts\Pagination\Paginator
        ? collect($penggajians->items())
        : collect($penggajians);

    $totalData = $penggajianItems->count();
    $totalGaji = (float) $penggajianItems->sum('total_gaji');
    $totalStatus = $penggajianItems->pluck('status_penggajian')->filter()->unique()->count();
    $penggajianIndexRoute = request()->routeIs('pegawai.keuangan.*')
        ? 'pegawai.keuangan.penggajian.index'
        : 'admin.penggajian.index';
    $penggajianStatusRoute = request()->routeIs('pegawai.keuangan.*')
        ? 'pegawai.keuangan.penggajian.status'
        : 'admin.penggajian.status';
@endphp

@push('styles')
<style>
:root {
    --fd-dark: #0E5423;
    --fd-green: #179B40;
    --fd-light: #22BA51;
    --fd-soft: #EAF8EE;
    --fd-soft-2: #F5FBF7;
    --fd-text: #172033;
    --fd-muted: #64748B;
    --fd-border: #E1EAE4;
    --fd-blue: #2563EB;
    --fd-blue-soft: #EEF4FF;
    --fd-amber: #D97706;
    --fd-amber-soft: #FFF7E6;
    --fd-red: #DC2626;
    --fd-red-soft: #FFF1F2;
    --fd-purple: #7C3AED;
    --fd-purple-soft: #F5F0FF;
}

.finus-data-page {
    position: relative;
    padding-bottom: 32px;
}

.finus-data-page::before {
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

/* HERO */
.finus-data-hero {
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
        radial-gradient(circle at 91% 7%, rgba(255,255,255,.16), transparent 30%),
        linear-gradient(135deg, #0E5423 0%, #179B40 48%, #22BA51 100%);
    box-shadow:
        0 18px 38px rgba(14,84,35,.17),
        inset 0 1px 0 rgba(255,255,255,.13);
}

.finus-data-hero::after {
    content: "";
    position: absolute;
    right: 170px;
    bottom: -145px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(126,255,135,.12);
    pointer-events: none;
}

.finus-data-hero-left,
.finus-data-hero-actions {
    position: relative;
    z-index: 2;
}

.finus-data-hero-left {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
}

.finus-data-hero-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 58px;
    min-width: 58px;
    height: 58px;
    border: 1px solid rgba(255,255,255,.21);
    border-radius: 17px;
    background: rgba(255,255,255,.14);
    color: #fff;
    font-size: 22px;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.15);
    backdrop-filter: blur(8px);
}

.finus-data-hero-title {
    margin: 0;
    color: #fff;
    font-size: 23px;
    font-weight: 800;
    line-height: 1.3;
    letter-spacing: -.02em;
}

.finus-data-hero-subtitle {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 6px 0 0;
    color: rgba(255,255,255,.82);
    font-size: 13px;
    line-height: 1.5;
}

.finus-data-hero-subtitle::before {
    content: "";
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #A5FFAE;
    box-shadow: 0 0 0 4px rgba(165,255,174,.13);
}

.finus-data-add {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 45px;
    padding: 0 17px;
    border: 1px solid rgba(255,255,255,.70);
    border-radius: 12px;
    background: #fff;
    color: var(--fd-dark) !important;
    font-size: 13px;
    font-weight: 800;
    text-decoration: none !important;
    white-space: nowrap;
    box-shadow: 0 9px 20px rgba(0,70,27,.16);
    transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
}

.finus-data-add i {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 7px;
    background: var(--fd-soft);
    color: var(--fd-green);
    font-size: 11px;
}

.finus-data-add:hover {
    background: #F3FFF6;
    transform: translateY(-2px);
    box-shadow: 0 13px 25px rgba(0,70,27,.22);
}

/* SUMMARY */
.finus-data-summary {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 17px;
}

.finus-data-stat {
    display: flex;
    align-items: center;
    gap: 13px;
    min-height: 92px;
    padding: 17px 18px;
    border: 1px solid var(--fd-border);
    border-radius: 17px;
    background: #fff;
    box-shadow: 0 10px 25px rgba(15,23,42,.055);
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
}

.finus-data-stat:hover {
    border-color: rgba(23,155,64,.23);
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(15,23,42,.075);
}

.finus-data-stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    min-width: 45px;
    height: 45px;
    border-radius: 13px;
    background: var(--stat-soft, var(--fd-soft));
    color: var(--stat-color, var(--fd-green));
    font-size: 17px;
}

.finus-data-stat-label {
    display: block;
    color: var(--fd-muted);
    font-size: 11.5px;
    font-weight: 700;
    line-height: 1.4;
}

.finus-data-stat-value {
    display: block;
    margin-top: 4px;
    color: var(--fd-text);
    font-size: 20px;
    font-weight: 800;
    line-height: 1.25;
    word-break: break-word;
}

.finus-stat-green { --stat-color: var(--fd-green); --stat-soft: var(--fd-soft); }
.finus-stat-blue { --stat-color: var(--fd-blue); --stat-soft: var(--fd-blue-soft); }
.finus-stat-amber { --stat-color: var(--fd-amber); --stat-soft: var(--fd-amber-soft); }
.finus-stat-red { --stat-color: var(--fd-red); --stat-soft: var(--fd-red-soft); }
.finus-stat-purple { --stat-color: var(--fd-purple); --stat-soft: var(--fd-purple-soft); }

/* CARD */
.finus-data-card {
    position: relative;
    z-index: 1;
    overflow: hidden;
    border: 1px solid rgba(14,84,35,.09);
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 18px 42px rgba(15,23,42,.075);
}

.finus-data-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 20px 22px;
    border-bottom: 1px solid #E7EEE9;
    background: linear-gradient(180deg, #fff, #F9FCFA);
}

.finus-data-card-title-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.finus-data-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 41px;
    min-width: 41px;
    height: 41px;
    border-radius: 12px;
    background: var(--fd-soft);
    color: var(--fd-green);
}

.finus-data-card-title {
    margin: 0;
    color: var(--fd-text);
    font-size: 16px;
    font-weight: 800;
    line-height: 1.35;
}

.finus-data-card-description {
    margin: 4px 0 0;
    color: var(--fd-muted);
    font-size: 11.5px;
    line-height: 1.45;
}

.finus-data-visible {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 11px;
    border: 1px solid #D6E9DC;
    border-radius: 10px;
    background: var(--fd-soft);
    color: var(--fd-dark);
    font-size: 11px;
    font-weight: 800;
    white-space: nowrap;
}

/* TOOLBAR */
.finus-data-toolbar {
    padding: 17px 22px;
    border-bottom: 1px solid #E9EFEB;
    background: #fff;
}

.finus-data-search-wrap {
    position: relative;
    width: min(100%, 460px);
}

.finus-data-search-icon {
    position: absolute;
    top: 50%;
    left: 15px;
    z-index: 2;
    color: #789084;
    font-size: 13px;
    transform: translateY(-50%);
    pointer-events: none;
}

.finus-data-search {
    width: 100%;
    height: 44px;
    padding: 0 42px 0 41px;
    border: 1px solid #D8E4DC;
    border-radius: 12px;
    background: #FBFDFC;
    color: var(--fd-text);
    font-size: 13px;
    outline: none;
    transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
}

.finus-data-search::placeholder { color: #94A3B8; }

.finus-data-search:hover {
    border-color: #BFD4C6;
    background: #fff;
}

.finus-data-search:focus {
    border-color: var(--fd-green);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(23,155,64,.11);
}

.finus-data-clear {
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

.finus-data-clear:hover {
    background: #DFF1E4;
    color: var(--fd-green);
}

.finus-data-search-help {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 7px 0 0;
    color: #7A8B82;
    font-size: 10.5px;
}

.finus-data-search-help i { color: var(--fd-green); }

/* TABLE */
.finus-data-table-area {
    padding: 20px 22px 24px;
    background: linear-gradient(180deg, rgba(234,248,238,.32), #fff 86px);
}

.finus-data-table-wrap {
    overflow: hidden;
    border: 1px solid var(--fd-border);
    border-radius: 15px;
    background: #fff;
}

.finus-data-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.finus-data-table thead th {
    padding: 14px 15px;
    border: 0;
    border-bottom: 1px solid var(--fd-border);
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

.finus-data-table tbody td {
    padding: 14px 15px;
    border: 0;
    border-bottom: 1px solid #ECF1ED;
    background: #fff;
    color: #334155;
    font-size: 13px;
    text-align: center !important;
    vertical-align: middle;
    transition: background .2s ease;
}

.finus-data-table tbody tr:last-child td { border-bottom: 0; }
.finus-data-table tbody tr[data-search-row]:hover td { background: #F7FCF8; }

.finus-data-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 31px;
    height: 31px;
    border: 1px solid #D6E9DC;
    border-radius: 9px;
    background: var(--fd-soft-2);
    color: var(--fd-dark);
    font-size: 11.5px;
    font-weight: 800;
}

.finus-data-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    max-width: 100%;
    color: var(--fd-text);
    font-weight: 800;
}

.finus-data-primary-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    min-width: 35px;
    height: 35px;
    border-radius: 10px;
    background: var(--fd-soft);
    color: var(--fd-green);
}

.finus-data-chip,
.finus-data-money,
.finus-data-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 32px;
    padding: 0 11px;
    border: 1px solid;
    border-radius: 10px;
    font-size: 11.5px;
    font-weight: 800;
    white-space: nowrap;
}

.finus-data-chip {
    border-color: #DCE6DF;
    background: #F8FAF9;
    color: #3E5447;
}

.finus-data-chip.green {
    border-color: #D5E9DB;
    background: var(--fd-soft);
    color: var(--fd-dark);
}

.finus-data-chip.blue {
    border-color: #C9DBFA;
    background: var(--fd-blue-soft);
    color: #1D4ED8;
}

.finus-data-money {
    border-color: #CDE8D5;
    background: #EFFAF2;
    color: #137035;
}

.finus-data-status {
    border-radius: 999px;
}

.finus-data-status::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.finus-status-success {
    border-color: #BDE5C7;
    background: #EFFAF2;
    color: #166534;
}
.finus-status-success::before {
    background: #22C55E;
    box-shadow: 0 0 0 3px rgba(34,197,94,.12);
}

.finus-status-warning {
    border-color: #F1D59A;
    background: #FFF8E8;
    color: #92400E;
}
.finus-status-warning::before {
    background: #F59E0B;
    box-shadow: 0 0 0 3px rgba(245,158,11,.12);
}

.finus-status-danger {
    border-color: #F1C6CB;
    background: #FFF3F4;
    color: #B91C1C;
}
.finus-status-danger::before {
    background: #EF4444;
    box-shadow: 0 0 0 3px rgba(239,68,68,.12);
}

.finus-status-info {
    border-color: #C9DBFA;
    background: #F0F5FF;
    color: #1D4ED8;
}
.finus-status-info::before {
    background: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}

.finus-status-neutral {
    border-color: #DCE6DF;
    background: #F8FAF9;
    color: #475569;
}
.finus-status-neutral::before {
    background: #94A3B8;
    box-shadow: 0 0 0 3px rgba(148,163,184,.12);
}

.finus-data-edit {
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
    transition: background .2s ease, border-color .2s ease, transform .2s ease, box-shadow .2s ease;
}

.finus-data-edit:hover {
    border-color: #E7C66F;
    background: #FFF3CC;
    color: #854D0E !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 13px rgba(161,98,7,.13);
}

/* EMPTY */
.finus-data-empty {
    padding: 54px 20px !important;
    text-align: center !important;
}

.finus-data-empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 62px;
    height: 62px;
    margin: 0 auto 14px;
    border-radius: 18px;
    background: var(--fd-soft);
    color: var(--fd-green);
    font-size: 23px;
}

.finus-data-empty-title {
    color: var(--fd-text);
    font-size: 14px;
    font-weight: 800;
}

.finus-data-empty-text {
    margin: 5px 0 0;
    color: var(--fd-muted);
    font-size: 12px;
}

/* RESPONSIVE */
@media (max-width: 991.98px) {
    .finus-data-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .finus-data-card-head { align-items: flex-start; flex-direction: column; }
}

@media (max-width: 767.98px) {
    .finus-data-hero {
        align-items: flex-start;
        flex-direction: column;
        padding: 23px 20px;
        border-radius: 18px;
    }

    .finus-data-hero-icon {
        width: 51px;
        min-width: 51px;
        height: 51px;
        border-radius: 15px;
    }

    .finus-data-hero-title { font-size: 20px; }
    .finus-data-hero-actions,
    .finus-data-add { width: 100%; }

    .finus-data-summary {
        grid-template-columns: 1fr;
        gap: 11px;
    }

    .finus-data-stat { min-height: 82px; }
    .finus-data-card { border-radius: 17px; }
    .finus-data-card-head,
    .finus-data-toolbar { padding: 17px; }
    .finus-data-search-wrap { width: 100%; }
    .finus-data-table-area { padding: 15px; }

    .finus-data-table-wrap {
        overflow: visible;
        border: 0;
        background: transparent;
    }

    .finus-data-table,
    .finus-data-table tbody,
    .finus-data-table tr,
    .finus-data-table td {
        display: block;
        width: 100%;
    }

    .finus-data-table thead { display: none; }

    .finus-data-table tbody tr[data-search-row] {
        overflow: hidden;
        margin-bottom: 13px;
        border: 1px solid var(--fd-border);
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(15,23,42,.045);
    }

    .finus-data-table tbody tr[data-search-row] td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        min-height: 52px;
        padding: 11px 14px;
        border-bottom: 1px solid #ECF1ED;
        text-align: right !important;
    }

    .finus-data-table tbody tr[data-search-row] td:last-child { border-bottom: 0; }

    .finus-data-table tbody tr[data-search-row] td::before {
        content: attr(data-label);
        flex-shrink: 0;
        color: #718078;
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: .06em;
        text-align: left;
        text-transform: uppercase;
    }

    .finus-data-primary { justify-content: flex-end; }

    .finus-data-empty {
        display: block !important;
        padding: 44px 18px !important;
        border: 1px solid var(--fd-border) !important;
        border-radius: 15px;
        background: #fff !important;
    }

    .finus-data-empty::before { display: none; }
}

</style>
@endpush

@section('content')
@include('layouts.partials.finus-ui')
@php
    $sudahDibayar = $penggajians->where('status_penggajian', 'sudah_dibayar')->count();
    $belumDibayar = $penggajians->where('status_penggajian', 'belum_dibayar')->count();
@endphp
<div class="finus-data-page" data-finus-data-page>
    <section class="finus-data-hero">
        <div class="finus-data-hero-left">
            <div class="finus-data-hero-icon"><i class="fa-solid fa-wallet"></i></div>
            <div>
                <h1 class="finus-data-hero-title">Penggajian</h1>
                <p class="finus-data-hero-subtitle" data-record-subtitle data-label="data penggajian">
                    Gaji dihitung otomatis dari presensi hadir yang telah di-ACC admin.
                </p>
            </div>
        </div>
        <div class="finus-data-hero-actions">
            <span class="finus-data-add" style="cursor:default"><i class="fa-solid fa-calculator"></i>Otomatis dari Presensi</span>
        </div>
    </section>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <section class="finus-data-summary">
        <article class="finus-data-stat finus-stat-blue">
            <div class="finus-data-stat-icon"><i class="fa-solid fa-users"></i></div>
            <div><span class="finus-data-stat-label">Total Pegawai</span><strong class="finus-data-stat-value">{{ number_format($penggajians->count()) }}</strong></div>
        </article>
        <article class="finus-data-stat finus-stat-green">
            <div class="finus-data-stat-icon"><i class="fa-solid fa-money-check-dollar"></i></div>
            <div><span class="finus-data-stat-label">Total Gaji Periode</span><strong class="finus-data-stat-value">Rp {{ number_format($penggajians->sum('total_gaji'), 0, ',', '.') }}</strong></div>
        </article>
        <article class="finus-data-stat finus-stat-purple">
            <div class="finus-data-stat-icon"><i class="fa-solid fa-list-check"></i></div>
            <div><span class="finus-data-stat-label">Dibayar / Belum</span><strong class="finus-data-stat-value">{{ $sudahDibayar }} / {{ $belumDibayar }}</strong></div>
        </article>
    </section>

    <section class="finus-data-card">
        <header class="finus-data-card-head">
            <div class="finus-data-card-title-wrap">
                <div class="finus-data-card-icon"><i class="fa-solid fa-users-gear"></i></div>
                <div>
                    <h2 class="finus-data-card-title">Daftar Penggajian</h2>
                    <p class="finus-data-card-description">Admin hanya menentukan status pembayaran. Nominal dan kehadiran dihitung sistem.</p>
                </div>
            </div>
            <div class="finus-data-visible"><i class="fa-solid fa-database"></i><span data-visible-count>{{ $penggajians->count() }}</span> data ditampilkan</div>
        </header>

        <div class="finus-data-toolbar" style="display:flex;gap:14px;justify-content:space-between;align-items:flex-end;flex-wrap:wrap">
            <form method="GET" action="{{ route($penggajianIndexRoute) }}" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap">
                <div>
                    <label for="periode" style="display:block;font-size:11px;font-weight:800;margin-bottom:6px;color:#64748B">PERIODE</label>
                    <input type="month" id="periode" name="periode" value="{{ $periode }}" class="finus-data-search" style="width:190px;padding-left:12px;padding-right:12px" required>
                </div>
                <button type="submit" class="finus-data-add" style="border-color:#D6E9DC"><i class="fa-solid fa-filter"></i>Tampilkan</button>
            </form>

            <div class="finus-data-search-wrap">
                <i class="fa-solid fa-magnifying-glass finus-data-search-icon"></i>
                <input type="search" class="finus-data-search" data-finus-search placeholder="Ketik dari awal pegawai, jabatan, periode, total, atau status..." autocomplete="off">
                <button type="button" class="finus-data-clear" data-finus-clear aria-label="Hapus pencarian"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>

        <div class="finus-data-table-area">
            <div class="finus-data-table-wrap">
                <table class="finus-data-table">
                    <thead>
                        <tr>
                            <th width="60">No.</th>
                            <th>Pegawai</th>
                            <th width="145">Jabatan</th>
                            <th width="145">Gaji/Hari</th>
                            <th width="125">Kehadiran ACC</th>
                            <th width="165">Total Gaji</th>
                            <th width="150">Status</th>
                            <th width="130">Tanggal Bayar</th>
                            <th width="165">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penggajians as $item)
                            @php
                                $pegawaiNama = $item->pegawai?->nama_pegawai ?? '-';
                                $jabatan = $item->pegawai?->jabatan ?? '-';
                                $sudahBayar = $item->status_penggajian === 'sudah_dibayar';
                                $gajiTersedia = $item->pegawai?->gajiJabatan !== null;
                                $totalFormatted = 'Rp ' . number_format($item->total_gaji, 0, ',', '.');
                                $gajiHarianFormatted = 'Rp ' . number_format($item->gaji_perhari, 0, ',', '.');
                            @endphp
                            <tr data-search-row data-search-start="{{ $pegawaiNama }}|{{ $jabatan }}|{{ $item->periode }}|{{ $item->jumlah_kehadiran }}|{{ $totalFormatted }}|{{ $item->status_penggajian }}">
                                <td data-label="Nomor"><span class="finus-data-number" data-row-number>{{ $loop->iteration }}</span></td>
                                <td data-label="Pegawai"><span class="finus-data-primary"><span class="finus-data-primary-icon"><i class="fa-solid fa-user-tie"></i></span>{{ $pegawaiNama }}</span></td>
                                <td data-label="Jabatan"><span class="finus-data-chip">{{ $jabatan }}</span></td>
                                <td data-label="Gaji/Hari">
                                    @if($gajiTersedia)<span class="finus-data-money">{{ $gajiHarianFormatted }}</span>@else<span class="text-danger">Belum diatur</span>@endif
                                </td>
                                <td data-label="Kehadiran ACC"><span class="finus-data-chip green"><i class="fa-solid fa-calendar-check"></i>{{ $item->jumlah_kehadiran }} hari</span></td>
                                <td data-label="Total Gaji"><span class="finus-data-money">{{ $totalFormatted }}</span></td>
                                <td data-label="Status"><span class="finus-data-status {{ $sudahBayar ? 'finus-status-success' : 'finus-status-warning' }}">{{ $sudahBayar ? 'Sudah Dibayar' : 'Belum Dibayar' }}</span></td>
                                <td data-label="Tanggal Bayar">{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') : '-' }}</td>
                                <td data-label="Aksi">
                                    @if($gajiTersedia)
                                        <form method="POST" action="{{ route($penggajianStatusRoute, $item) }}" onsubmit="return confirm('{{ $sudahBayar ? 'Ubah status menjadi Belum Dibayar?' : 'Pastikan gaji sudah dikirim. Tandai sebagai Sudah Dibayar?' }}')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_penggajian" value="{{ $sudahBayar ? 'belum_dibayar' : 'sudah_dibayar' }}">
                                            <button type="submit" class="finus-data-edit" style="{{ $sudahBayar ? '' : 'border-color:#BDE5C7;background:#EFFAF2;color:#166534!important' }}">
                                                <i class="fa-solid {{ $sudahBayar ? 'fa-rotate-left' : 'fa-check' }}"></i>{{ $sudahBayar ? 'Belum Dibayar' : 'Tandai Dibayar' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted" style="font-size:11px">Atur Gaji Jabatan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="finus-data-empty"><div class="finus-data-empty-icon"><i class="fa-solid fa-wallet"></i></div><div class="finus-data-empty-title">Belum ada pegawai</div></td></tr>
                        @endforelse
                        <tr data-empty-search-row style="display:none;"><td colspan="9" class="finus-data-empty"><div class="finus-data-empty-icon"><i class="fa-solid fa-magnifying-glass"></i></div><div class="finus-data-empty-title">Penggajian tidak ditemukan</div></td></tr>
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
    const normalize = value => (value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    document.querySelectorAll('[data-finus-data-page]').forEach(page => {
        const input = page.querySelector('[data-finus-search]');
        const clearButton = page.querySelector('[data-finus-clear]');
        const rows = Array.from(page.querySelectorAll('[data-search-row]'));
        const emptyRow = page.querySelector('[data-empty-search-row]');
        const visibleElements = page.querySelectorAll('[data-visible-count]');
        if (!input) return;
        const filterRows = () => {
            const keyword = normalize(input.value);
            let visible = 0;
            rows.forEach(row => {
                const values = (row.dataset.searchStart || '').split('|').map(normalize).filter(Boolean);
                const matches = keyword === '' || values.some(value => value.startsWith(keyword));
                row.style.display = matches ? '' : 'none';
                if (matches) {
                    visible++;
                    const number = row.querySelector('[data-row-number]');
                    if (number) number.textContent = visible;
                }
            });
            if (emptyRow) emptyRow.style.display = visible === 0 && rows.length > 0 ? '' : 'none';
            visibleElements.forEach(el => el.textContent = visible);
            if (clearButton) clearButton.style.display = input.value.trim() ? 'flex' : 'none';
        };
        input.addEventListener('input', filterRows);
        clearButton?.addEventListener('click', () => { input.value = ''; input.focus(); filterRows(); });
        filterRows();
    });
})();
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: penggajian/index.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="penggajian/index.blade.php">
html[data-finus-theme="dark"] body .finus-data-page {
    --fd-dark:#B9F4C7; --fd-green:#64DD81; --fd-light:#7BE795;
    --fd-soft:#173620; --fd-soft-2:#101D16; --fd-text:#F1F6F3; --fd-muted:#9EAEA4; --fd-border:#293D31;
    --fd-blue:#82AEFF; --fd-blue-soft:#192C47; --fd-amber:#F4B65A; --fd-amber-soft:#382A18;
    --fd-red:#FF7D84; --fd-red-soft:#3C2024; --fd-purple:#B295FF; --fd-purple-soft:#302442;
}
html[data-finus-theme="dark"] body .finus-data-page::before { background:rgba(38,184,79,.035) !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-hero {
    border:1px solid rgba(126,255,135,.10) !important;
    background:radial-gradient(circle at 91% 7%,rgba(255,255,255,.10),transparent 30%),linear-gradient(135deg,#0B5A27 0%,#14863A 50%,#1DB04B 100%) !important;
    box-shadow:0 18px 38px rgba(0,0,0,.24),inset 0 1px 0 rgba(255,255,255,.08) !important;
}
html[data-finus-theme="dark"] body .finus-data-page .finus-data-add {
    border-color:rgba(255,255,255,.24) !important; background:rgba(5,31,14,.48) !important; color:#F7FFF9 !important; box-shadow:0 10px 22px rgba(0,0,0,.18) !important;
}
html[data-finus-theme="dark"] body .finus-data-page .finus-data-add i { background:rgba(100,221,129,.16) !important; color:#9CF3AF !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-add:hover { background:rgba(7,42,18,.68) !important; border-color:rgba(255,255,255,.34) !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-stat {
    border-color:#293D31 !important; background:linear-gradient(150deg,#14211A,#101813) !important; color:#F1F6F3 !important; box-shadow:0 12px 28px rgba(0,0,0,.18) !important;
}
html[data-finus-theme="dark"] body .finus-data-page .finus-data-stat:hover { border-color:#365141 !important; background:linear-gradient(150deg,#18271E,#121C16) !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-stat::after { opacity:.30 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-stat-label { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-stat-value { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-data-page :where(.finus-data-card,.finus-import-card) {
    border-color:#293D31 !important; background:#111A15 !important; color:#F1F6F3 !important; box-shadow:0 18px 42px rgba(0,0,0,.22) !important;
}
html[data-finus-theme="dark"] body .finus-data-page :where(.finus-data-card-head,.finus-import-head) {
    border-bottom-color:#293D31 !important; background:linear-gradient(180deg,#17251D 0%,#121D17 100%) !important; color:#F1F6F3 !important;
}
html[data-finus-theme="dark"] body .finus-data-page :where(.finus-data-card-title,.finus-import-title,.finus-import-label) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-data-page :where(.finus-data-card-description,.finus-import-desc,.finus-import-help,.finus-data-search-help,.finus-file-picker-help) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-visible { border-color:#31503C !important; background:#173620 !important; color:#BFF4CA !important; }
html[data-finus-theme="dark"] body .finus-data-page :where(.finus-data-toolbar,.finus-import-body) { border-color:#293D31 !important; background:#111A15 !important; }
html[data-finus-theme="dark"] body .finus-data-page :where(.finus-data-search,.finus-import-input) {
    border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important;
}
html[data-finus-theme="dark"] body .finus-data-page :where(.finus-data-search,.finus-import-input):focus { border-color:#64DD81 !important; background:#0F1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.12) !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-search::placeholder { color:#71847A !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-clear { background:#18271E !important; color:#A1B2A7 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-table-area { background:linear-gradient(180deg,#0F1913 0%,#0D1611 100%) !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-table-wrap { border-color:#293D31 !important; background:#111A15 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-table thead th { border-color:#293D31 !important; background:#17261D !important; color:#D2E1D6 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-table tbody td { border-color:#24372B !important; background:#111A15 !important; color:#DCE7E0 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-table tbody tr[data-search-row]:hover td { background:#17251D !important; }
html[data-finus-theme="dark"] body .finus-data-page :where(.finus-data-number,.finus-data-chip) { border-color:#30493A !important; background:#14241B !important; color:#CFE3D4 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-money { border-color:#2F5C3C !important; background:#15331E !important; color:#A9F0B9 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-status-success { border-color:#2D6140 !important; background:#15331E !important; color:#A9F0B9 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-status-warning { border-color:#6A552D !important; background:#342817 !important; color:#F4D38B !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-status-danger { border-color:#704044 !important; background:#371E22 !important; color:#F5B1B5 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-status-info { border-color:#35557A !important; background:#182B43 !important; color:#B9D0FF !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-status-neutral { border-color:#3B4C42 !important; background:#18211C !important; color:#B6C4BA !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-edit { border-color:#66542B !important; background:#332817 !important; color:#F2CE7E !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-delete { border-color:#693B40 !important; background:#341C20 !important; color:#F39DA3 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-primary { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-empty { border-color:#293D31 !important; background:#111B15 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-data-page :where(.finus-data-empty-title,.finus-file-picker-name) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-data-page .finus-data-empty-text { color:#9EAEA4 !important; }
</style>
@endpush


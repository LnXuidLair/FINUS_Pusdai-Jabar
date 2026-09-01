@extends('layouts.app')

@php
    $isPegawai = request()->routeIs('pegawai.keuangan.*');
    $roleTitle = $isPegawai ? 'Keuangan' : 'Admin';
    $pemasukanIndexRoute = $isPegawai ? 'pegawai.keuangan.pemasukan.index' : 'admin.pemasukan.index';
    $pemasukanStoreRoute = $isPegawai ? 'pegawai.keuangan.pemasukan.store' : 'admin.pemasukan.store';
    $pemasukanVerifikasiRoute = $isPegawai ? 'pegawai.keuangan.pemasukan.verifikasi' : 'admin.pemasukan.verifikasi';
    $pemasukanDestroyRoute = $isPegawai ? 'pegawai.keuangan.pemasukan.destroy' : 'admin.pemasukan.destroy';
    $pemasukanBasePath = $isPegawai ? url('/pegawai/keuangan/pemasukan') : url('/admin/pemasukan');
@endphp

@section('title', 'Pemasukan — ' . $roleTitle . ' FINUS')

@section('content')
@include('layouts.partials.finus-ui')

<style>
    /* ============================================================
       PAGE
    ============================================================ */
    .pm-page { padding: 6px 0 52px; }

    /* ============================================================
       HERO
    ============================================================ */
    .pm-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
        padding: 26px 30px;
        border-radius: 22px;
        background:
            radial-gradient(circle at 88% 10%, rgba(255,255,255,.15), transparent 32%),
            linear-gradient(135deg, #0a3d1e 0%, #0e5423 42%, #179b40 100%);
        box-shadow: 0 18px 38px rgba(14,84,35,.18), inset 0 1px 0 rgba(255,255,255,.12);
    }

    .pm-hero::before {
        content: "";
        position: absolute;
        top: -80px; right: -60px;
        width: 230px; height: 230px;
        border-radius: 50%;
        background: rgba(126,255,135,.12);
        pointer-events: none;
    }

    .pm-hero-left { position: relative; z-index: 1; }

    .pm-hero h1 {
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        margin: 0 0 5px;
        letter-spacing: -.3px;
    }

    .pm-hero p {
        font-size: 13px;
        color: rgba(255,255,255,.72);
        margin: 0;
    }

    .pm-btn-add {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 22px;
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 13px;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        backdrop-filter: blur(6px);
        transition: background .2s, transform .2s, box-shadow .2s;
        text-decoration: none;
    }

    .pm-btn-add:hover {
        background: rgba(255,255,255,.26);
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(0,0,0,.18);
        color: #fff;
        text-decoration: none;
    }

    /* ============================================================
       SUMMARY CARDS — 3 kolom
    ============================================================ */
    .pm-stat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }

    @media (max-width: 480px) { .pm-stat-grid { grid-template-columns: 1fr; } }

    .pm-stat {
        background: #fff;
        border: 1px solid #e4edf2;
        border-radius: 18px;
        padding: 20px 22px;
        box-shadow: 0 3px 16px rgba(15,23,42,.055);
        transition: transform .18s, box-shadow .18s;
    }

    .pm-stat:hover { transform: translateY(-3px); box-shadow: 0 10px 26px rgba(15,23,42,.09); }

    .pm-stat-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pm-stat-value {
        font-size: 22px;
        font-weight: 800;
        color: #0e5423;
        line-height: 1.2;
        letter-spacing: -.5px;
    }

    .pm-stat-value.blue   { color: #1d4ed8; }
    .pm-stat-value.orange { color: #c2540a; }
    .pm-stat-sub { font-size: 11px; color: #94a3b8; margin-top: 3px; }

    /* ============================================================
       GOLONGAN CARDS — 4 kolom
    ============================================================ */
    .pm-gol-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 22px;
    }

    @media (max-width: 991px) { .pm-gol-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px)  { .pm-gol-grid { grid-template-columns: 1fr; } }

    .pm-gol-card {
        background: #fff;
        border: 1px solid #e4edf2;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 2px 10px rgba(15,23,42,.045);
        transition: transform .15s;
    }

    .pm-gol-card:hover { transform: translateY(-2px); }

    .pm-gol-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .pm-gol-name {
        font-size: 11px;
        font-weight: 700;
        color: #374151;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pm-gol-amount {
        font-size: 13px;
        font-weight: 800;
        color: #111827;
        margin-top: 2px;
    }

    /* ============================================================
       FILTER CARD
    ============================================================ */
    .pm-filter {
        background: #fff;
        border: 1px solid #e4edf2;
        border-radius: 18px;
        padding: 18px 20px;
        margin-bottom: 18px;
        box-shadow: 0 2px 12px rgba(15,23,42,.045);
    }

    .pm-filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
    }

    .pm-fg { display: flex; flex-direction: column; gap: 4px; min-width: 130px; flex: 1; }
    .pm-fg.wide { min-width: 190px; flex: 2; }

    .pm-fl {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #64748b;
    }

    .pm-fi, .pm-fs {
        padding: 9px 12px;
        border: 1px solid #dde5ea;
        border-radius: 10px;
        font-size: 13px;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        width: 100%;
        transition: border-color .18s, box-shadow .18s;
        font-family: inherit;
    }

    .pm-fi:focus, .pm-fs:focus {
        border-color: #179b40;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(23,155,64,.12);
    }

    .pm-filter-actions { display: flex; gap: 8px; }

    .pm-fbtn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        transition: all .17s;
        text-decoration: none;
    }

    .pm-fbtn-primary {
        background: linear-gradient(135deg, #0e5423, #179b40);
        color: #fff;
        box-shadow: 0 5px 14px rgba(14,84,35,.22);
    }

    .pm-fbtn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(14,84,35,.32); color: #fff; text-decoration: none; }

    .pm-fbtn-reset {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #dde5ea;
    }

    .pm-fbtn-reset:hover { background: #e2e8f0; color: #374151; text-decoration: none; }

    /* ============================================================
       TABLE CARD
    ============================================================ */
    .pm-card {
        background: #fff;
        border: 1px solid #e4edf2;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15,23,42,.06);
    }

    .pm-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 22px 14px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
    }

    .pm-card-title { font-size: 15px; font-weight: 800; color: #111827; }
    .pm-card-count { font-size: 11px; color: #64748b; margin-top: 2px; }

    .pm-table-wrap { overflow-x: auto; }

    .pm-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 820px;
    }

    .pm-table thead th {
        background: #f8fafc;
        padding: 11px 16px;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #64748b;
        border-bottom: 1px solid #e4edf2;
        text-align: left;
        white-space: nowrap;
    }

    .pm-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background .12s;
    }

    .pm-table tbody tr:last-child { border-bottom: none; }
    .pm-table tbody tr:hover { background: #f8fafc; }

    .pm-table td {
        padding: 13px 16px;
        font-size: 13px;
        color: #374151;
        vertical-align: middle;
    }

    /* ============================================================
       BADGES
    ============================================================ */
    .pm-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid;
        white-space: nowrap;
    }

    .pm-bdot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

    .pm-s-diterima { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
    .pm-s-pending  { background: #fff7ed; color: #9a3412; border-color: #fed7aa; }
    .pm-s-ditolak  { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
    .pm-src-jamaah { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; font-size: 10px; }
    .pm-src-admin  { background: #f0fdf4; color: #065f46; border-color: #a7f3d0; font-size: 10px; }

    /* ============================================================
       ACTION BUTTONS
    ============================================================ */
    .pm-actions { display: flex; gap: 6px; align-items: center; flex-wrap: nowrap; }

    .pm-abtn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 11px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 600;
        border: 1px solid;
        cursor: pointer;
        white-space: nowrap;
        background: none;
        transition: all .14s;
        text-decoration: none;
        font-family: inherit;
    }

    .pm-abtn-terima { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    .pm-abtn-terima:hover { background: #dcfce7; color: #15803d; }
    .pm-abtn-tolak  { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .pm-abtn-tolak:hover  { background: #fee2e2; color: #b91c1c; }
    .pm-abtn-bukti  { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .pm-abtn-bukti:hover  { background: #f1f5f9; }
    .pm-abtn-hapus  { background: #fff5f5; color: #dc2626; border-color: #fca5a5; }
    .pm-abtn-hapus:hover  { background: #fee2e2; }

    /* ============================================================
       EMPTY STATE
    ============================================================ */
    .pm-empty {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }

    .pm-empty i    { font-size: 48px; margin-bottom: 14px; display: block; opacity: .5; }
    .pm-empty-h    { font-size: 16px; font-weight: 700; color: #475569; margin-bottom: 6px; }
    .pm-empty-sub  { font-size: 13px; }

    /* ============================================================
       PAGINATION
    ============================================================ */
    .pm-pagination {
        padding: 16px 22px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
    }

    /* ============================================================
       MODAL — Tambah Pemasukan
    ============================================================ */
    .pm-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(5,25,12,.48);
        backdrop-filter: blur(4px);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .pm-overlay.open { display: flex; }

    .pm-modal {
        background: #fff;
        border-radius: 24px;
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 28px 60px rgba(0,0,0,.22);
        animation: pm-in .22s cubic-bezier(.22,.61,.36,1);
    }

    @keyframes pm-in {
        from { opacity: 0; transform: translateY(18px) scale(.97); }
        to   { opacity: 1; transform: translateY(0)   scale(1); }
    }

    .pm-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 22px 24px 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .pm-modal-head h2 { font-size: 17px; font-weight: 800; color: #111827; margin: 0; }

    .pm-modal-x {
        width: 34px; height: 34px;
        background: #f1f5f9; border: none; border-radius: 9px;
        font-size: 20px; cursor: pointer; color: #64748b;
        display: inline-flex; align-items: center; justify-content: center;
        transition: background .14s;
    }

    .pm-modal-x:hover { background: #e2e8f0; color: #374151; }

    .pm-modal-body { padding: 22px 24px; }

    .pm-fgroup { margin-bottom: 16px; }

    .pm-flabel {
        display: block;
        font-size: 11.5px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .pm-flabel span { color: #dc2626; }

    .pm-finput, .pm-fselect, .pm-ftarea {
        width: 100%;
        padding: 10px 13px;
        border: 1px solid #dde5ea;
        border-radius: 11px;
        font-size: 13.5px;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        transition: border-color .18s, box-shadow .18s;
        font-family: inherit;
    }

    .pm-finput:focus, .pm-fselect:focus, .pm-ftarea:focus {
        border-color: #179b40;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(23,155,64,.12);
    }

    .pm-ftarea { min-height: 82px; resize: vertical; }
    .pm-fhint  { font-size: 11px; color: #94a3b8; margin-top: 4px; }
    .pm-frow   { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 480px) { .pm-frow { grid-template-columns: 1fr; } }

    .pm-modal-foot {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 24px;
        border-top: 1px solid #f1f5f9;
    }

    .pm-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: linear-gradient(135deg, #0e5423, #179b40);
        color: #fff;
        border: none;
        border-radius: 11px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 6px 16px rgba(14,84,35,.24);
        transition: transform .18s, box-shadow .18s;
        font-family: inherit;
    }

    .pm-submit:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(14,84,35,.32); }

    .pm-cancel {
        padding: 10px 18px;
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #dde5ea;
        border-radius: 11px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s;
        font-family: inherit;
    }

    .pm-cancel:hover { background: #e2e8f0; color: #374151; }

    /* ============================================================
       MODAL — Tolak
    ============================================================ */
    .pm-tolak-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(5,25,12,.46);
        backdrop-filter: blur(4px);
        z-index: 2100;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .pm-tolak-overlay.open { display: flex; }

    .pm-tolak-box {
        background: #fff;
        border-radius: 20px;
        width: 100%;
        max-width: 440px;
        padding: 28px 26px;
        box-shadow: 0 24px 56px rgba(0,0,0,.2);
        animation: pm-in .22s cubic-bezier(.22,.61,.36,1);
    }

    /* ============================================================
       ALERTS
    ============================================================ */
    .pm-alert-ok {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 14px;
        color: #166534;
        font-size: 13.5px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .pm-alert-err {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        background: #fef2f2;
        border: 1px solid #fca5a5;
        border-radius: 14px;
        color: #991b1b;
        font-size: 13.5px;
        font-weight: 600;
        margin-bottom: 20px;
    }
</style>

<div class="fr-page pm-page">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="pm-alert-ok">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="pm-alert-err">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- ====== HERO ====== --}}
    <div class="pm-hero">
        <div class="pm-hero-left">
            <h1><i class="fa-solid fa-hand-holding-dollar" style="margin-right:10px;opacity:.85;"></i>Pemasukan</h1>
            <p>Kelola seluruh pemasukan ZISWAF — dari jamaah maupun input manual admin.</p>
        </div>
        <button type="button" class="pm-btn-add" onclick="pmOpenTambah()">
            <i class="fa-solid fa-plus"></i> Tambah Pemasukan
        </button>
    </div>

    {{-- ====== 2 SUMMARY CARDS ====== --}}
    <div class="pm-stat-grid">
        <div class="pm-stat">
            <div class="pm-stat-label">
                <i class="fa-solid fa-circle-check" style="color:#16a34a;"></i> Total Diterima
            </div>
            <div class="pm-stat-value">Rp {{ number_format($summaryTotal['total'], 0, ',', '.') }}</div>
            <div class="pm-stat-sub">semua periode</div>
        </div>
        <div class="pm-stat">
            <div class="pm-stat-label">
                <i class="fa-solid fa-calendar-day" style="color:#2563eb;"></i> Bulan Ini
            </div>
            <div class="pm-stat-value blue">Rp {{ number_format($summaryTotal['bulan_ini'], 0, ',', '.') }}</div>
            <div class="pm-stat-sub">{{ now()->translatedFormat('F Y') }}</div>
        </div>
    </div>

    {{-- ====== GOLONGAN CARDS (8 item) ====== --}}
    <div class="pm-gol-grid">
        @foreach($golonganLabels as $key => $label)
            @php $colors = $golonganColors[$key] ?? ['bg'=>'#f8fafc','text'=>'#475569','border'=>'#e2e8f0','dot'=>'#64748b']; @endphp
            <div class="pm-gol-card" style="border-color:{{ $colors['border'] }};">
                <span class="pm-gol-dot" style="background:{{ $colors['dot'] }};"></span>
                <div style="min-width:0;">
                    <div class="pm-gol-name">{{ $label }}</div>
                    <div class="pm-gol-amount">Rp {{ number_format($summaryPerGolongan[$key] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ====== FILTER ====== --}}
    <div class="pm-filter">
        <form method="GET" action="{{ route($pemasukanIndexRoute) }}">
            <div class="pm-filter-row">

                <div class="pm-fg wide">
                    <label class="pm-fl">Cari</label>
                    <input type="text" name="q" class="pm-fi" placeholder="Nama muzakki, keterangan…" value="{{ $filters['q'] ?? '' }}">
                </div>

                <div class="pm-fg">
                    <label class="pm-fl">Golongan</label>
                    <select name="golongan" class="pm-fs">
                        <option value="">Semua Golongan</option>
                        @foreach($golonganLabels as $val => $lbl)
                            <option value="{{ $val }}" {{ ($filters['golongan'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="pm-fg" style="flex:0;min-width:auto;">
                    <label class="pm-fl" style="opacity:0">.</label>
                    <div class="pm-filter-actions">
                        <button type="submit" class="pm-fbtn pm-fbtn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i> Filter
                        </button>
                        <a href="{{ route($pemasukanIndexRoute) }}" class="pm-fbtn pm-fbtn-reset">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- ====== TABLE CARD ====== --}}
    <div class="pm-card">
        <div class="pm-card-head">
            <div>
                <div class="pm-card-title">Daftar Pemasukan</div>
                <div class="pm-card-count">
                    {{ $transaksi->firstItem() ?? 0 }}–{{ $transaksi->lastItem() ?? 0 }}
                    dari {{ $transaksi->total() }} data
                    @if(array_filter($filters))
                        <span style="color:#179b40;font-weight:700;">(difilter)</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="pm-table-wrap">
            <table class="pm-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Sumber / Muzakki</th>
                        <th>Golongan</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $item)
                        @php
                            $isJamaah = $item->muzakki_id !== null;
                            $colors   = $golonganColors[$item->jenis_ziswaf] ?? ['bg'=>'#f8fafc','text'=>'#475569','border'=>'#e2e8f0','dot'=>'#64748b'];
                            $golLabel = $golonganLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf;
                            $metLabel = $metodeLabels[$item->metode_pembayaran] ?? ($item->metode_pembayaran ?? '-');

                            // Nama donatur dari keterangan manual
                            $donatur = null;
                            if (!$isJamaah && $item->keterangan && str_contains($item->keterangan, '[Donatur:')) {
                                preg_match('/\[Donatur:\s*([^\]]+)\]/', $item->keterangan, $m);
                                $donatur = $m[1] ?? null;
                            }
                        @endphp
                        <tr>
                            {{-- No --}}
                            <td style="color:#94a3b8;font-size:12px;font-weight:600;">
                                {{ $transaksi->firstItem() + $loop->index }}
                            </td>

                            {{-- Tanggal --}}
                            <td>
                                <div style="font-weight:700;">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:1px;">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}
                                </div>
                            </td>

                            {{-- Sumber --}}
                            <td>
                                @if($isJamaah)
                                    <div style="font-weight:700;">{{ $item->muzakki?->name ?? 'Jamaah' }}</div>
                                    <span class="pm-badge pm-src-jamaah" style="margin-top:3px;">
                                        <i class="fa-solid fa-user" style="font-size:9px;"></i> Jamaah
                                    </span>
                                @else
                                    <div style="font-weight:700;">{{ $donatur ?? 'Input Admin' }}</div>
                                    <span class="pm-badge pm-src-admin" style="margin-top:3px;">
                                        <i class="fa-solid fa-user-shield" style="font-size:9px;"></i> Admin
                                    </span>
                                @endif
                            </td>

                            {{-- Golongan --}}
                            <td>
                                <span class="pm-badge" style="background:{{ $colors['bg'] }};color:{{ $colors['text'] }};border-color:{{ $colors['border'] }};">
                                    <span class="pm-bdot" style="background:{{ $colors['dot'] }};"></span>
                                    {{ $golLabel }}
                                </span>
                            </td>

                            {{-- Nominal --}}
                            <td style="font-weight:800;color:#0e5423;font-size:14px;">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>

                            {{-- Metode --}}
                            <td style="font-size:12px;color:#475569;">{{ $metLabel }}</td>

                            {{-- Status --}}
                            <td>
                                @if($item->status_verifikasi === 'diterima')
                                    <span class="pm-badge pm-s-diterima">
                                        <span class="pm-bdot" style="background:#16a34a;"></span> Diterima
                                    </span>
                                @elseif($item->status_verifikasi === 'ditolak')
                                    <span class="pm-badge pm-s-ditolak">
                                        <span class="pm-bdot" style="background:#dc2626;"></span> Ditolak
                                    </span>
                                @else
                                    <span class="pm-badge pm-s-pending">
                                        <span class="pm-bdot" style="background:#f97316;"></span> Menunggu
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td>
                                <div class="pm-actions">

                                    @if($item->status_verifikasi === 'pending')
                                        {{-- Terima --}}
                                        <form method="POST" action="{{ route($pemasukanVerifikasiRoute, $item) }}" style="margin:0;" onsubmit="return confirm('Terima pemasukan ini?')">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="action" value="terima">
                                            <button type="submit" class="pm-abtn pm-abtn-terima">
                                                <i class="fa-solid fa-check"></i> Terima
                                            </button>
                                        </form>
                                        {{-- Tolak --}}
                                        <button type="button" class="pm-abtn pm-abtn-tolak" onclick="pmOpenTolak({{ $item->id }})">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    @endif

                                    @if($item->bukti_pembayaran)
                                        <a href="{{ Storage::url($item->bukti_pembayaran) }}" target="_blank" class="pm-abtn pm-abtn-bukti" title="Lihat bukti">
                                            <i class="fa-solid fa-file-image"></i>
                                        </a>
                                    @endif

                                    @if(!$isJamaah)
                                        <form method="POST" action="{{ route($pemasukanDestroyRoute, $item) }}" style="margin:0;" onsubmit="return confirm('Hapus pemasukan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="pm-abtn pm-abtn-hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="pm-empty">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                    <div class="pm-empty-h">Belum ada data pemasukan</div>
                                    <div class="pm-empty-sub">
                                        @if(array_filter($filters))
                                            Tidak ada pemasukan yang cocok dengan filter yang dipilih.
                                        @else
                                            Tambahkan pemasukan manual atau tunggu transaksi dari jamaah.
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksi->hasPages())
            <div class="pm-pagination">{{ $transaksi->links() }}</div>
        @endif
    </div>

</div>

{{-- ================================================================
     MODAL — Tambah Pemasukan Manual
================================================================ --}}
<div class="pm-overlay" id="pmTambahOverlay">
    <div class="pm-modal" role="dialog" aria-modal="true">
        <div class="pm-modal-head">
            <h2><i class="fa-solid fa-plus-circle" style="color:#179b40;margin-right:8px;"></i>Tambah Pemasukan Manual</h2>
            <button type="button" class="pm-modal-x" onclick="pmCloseTambah()" aria-label="Tutup">&times;</button>
        </div>

        <form method="POST" action="{{ route($pemasukanStoreRoute) }}" enctype="multipart/form-data" id="pmTambahForm">
            @csrf
            <div class="pm-modal-body">

                {{-- Golongan --}}
                <div class="pm-fgroup">
                    <label class="pm-flabel" for="pm_golongan">Golongan <span>*</span></label>
                    <select name="jenis_ziswaf" id="pm_golongan" class="pm-fselect" required>
                        <option value="">-- Pilih Golongan --</option>
                        @foreach($golonganLabels as $val => $lbl)
                            <option value="{{ $val }}" {{ old('jenis_ziswaf') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pm-frow">
                    {{-- Nominal --}}
                    <div class="pm-fgroup">
                        <label class="pm-flabel" for="pm_nominal">Nominal (Rp) <span>*</span></label>
                        <input type="number" name="nominal" id="pm_nominal" class="pm-finput"
                            min="1000" step="1000" placeholder="Contoh: 500000"
                            value="{{ old('nominal') }}" required>
                    </div>
                    {{-- Tanggal --}}
                    <div class="pm-fgroup">
                        <label class="pm-flabel" for="pm_tanggal">Tanggal <span>*</span></label>
                        <input type="date" name="tanggal" id="pm_tanggal" class="pm-finput"
                            value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                            max="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>

                {{-- Metode --}}
                <div class="pm-fgroup">
                    <label class="pm-flabel" for="pm_metode">Metode Pembayaran <span>*</span></label>
                    <select name="metode_pembayaran" id="pm_metode" class="pm-fselect" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="manual_transfer" {{ old('metode_pembayaran') === 'manual_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="qris_manual"     {{ old('metode_pembayaran') === 'qris_manual'     ? 'selected' : '' }}>QRIS Manual</option>
                        <option value="tunai"           {{ old('metode_pembayaran') === 'tunai'           ? 'selected' : '' }}>Tunai / Cash</option>
                    </select>
                </div>

                {{-- Nama Donatur --}}
                <div class="pm-fgroup">
                    <label class="pm-flabel" for="pm_donatur">Nama Donatur / Muzakki</label>
                    <input type="text" name="nama_donatur" id="pm_donatur" class="pm-finput"
                        placeholder="Opsional — kosongkan jika anonim"
                        value="{{ old('nama_donatur') }}" maxlength="255">
                    <div class="pm-fhint">Nama dicatat di keterangan, tidak membuat akun baru.</div>
                </div>

                {{-- Keterangan --}}
                <div class="pm-fgroup">
                    <label class="pm-flabel" for="pm_ket">Keterangan</label>
                    <textarea name="keterangan" id="pm_ket" class="pm-ftarea"
                        placeholder="Keterangan tambahan (opsional)…" maxlength="1000">{{ old('keterangan') }}</textarea>
                </div>

                {{-- Bukti --}}
                <div class="pm-fgroup">
                    <label class="pm-flabel" for="pm_bukti">Bukti Pembayaran</label>
                    <input type="file" name="bukti_pembayaran" id="pm_bukti" class="pm-finput"
                        accept=".jpg,.jpeg,.png,.pdf" style="padding:7px 12px;">
                    <div class="pm-fhint">JPG, PNG atau PDF. Maks. 2 MB.</div>
                </div>

            </div>
            <div class="pm-modal-foot">
                <button type="button" class="pm-cancel" onclick="pmCloseTambah()">Batal</button>
                <button type="submit" class="pm-submit" id="pmSubmitBtn">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pemasukan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================
     MODAL — Tolak Verifikasi
================================================================ --}}
<div class="pm-tolak-overlay" id="pmTolakOverlay">
    <div class="pm-tolak-box" role="dialog" aria-modal="true">
        <h2 style="font-size:17px;font-weight:800;color:#111827;margin:0 0 6px;">
            <i class="fa-solid fa-xmark-circle" style="color:#dc2626;margin-right:8px;"></i>Tolak Pemasukan
        </h2>
        <p style="font-size:13px;color:#64748b;margin:0 0 18px;">
            Berikan alasan penolakan yang jelas.
        </p>
        <form method="POST" id="pmTolakForm">
            @csrf @method('PATCH')
            <input type="hidden" name="action" value="tolak">
            <div class="pm-fgroup">
                <label class="pm-flabel" for="pm_catatan_tolak">Catatan Penolakan <span style="color:#dc2626;">*</span></label>
                <textarea name="catatan_verifikasi" id="pm_catatan_tolak" class="pm-ftarea"
                    placeholder="Contoh: Bukti pembayaran tidak jelas atau tidak valid…"
                    required minlength="5"></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:10px;">
                <button type="button" class="pm-cancel" onclick="pmCloseTolak()">Batal</button>
                <button type="submit" style="padding:10px 20px;background:#dc2626;color:#fff;border:none;border-radius:11px;font-size:13.5px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:7px;font-family:inherit;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fa-solid fa-xmark"></i> Tolak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    /* ── Modal Tambah ── */
    function pmOpenTambah() {
        document.getElementById('pmTambahOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function pmCloseTambah() {
        document.getElementById('pmTambahOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }
    document.getElementById('pmTambahOverlay').addEventListener('click', function(e) {
        if (e.target === this) pmCloseTambah();
    });

    /* Buka modal otomatis jika ada validasi error dari server */
    @if($errors->any() && old('jenis_ziswaf'))
        document.addEventListener('DOMContentLoaded', () => pmOpenTambah());
    @endif

    /* ── Modal Tolak ── */
    function pmOpenTolak(id) {
        const url = '{{ $pemasukanBasePath }}/' + id + '/verifikasi';
        document.getElementById('pmTolakForm').setAttribute('action', url);
        document.getElementById('pm_catatan_tolak').value = '';
        document.getElementById('pmTolakOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('pm_catatan_tolak').focus(), 100);
    }
    function pmCloseTolak() {
        document.getElementById('pmTolakOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }
    document.getElementById('pmTolakOverlay').addEventListener('click', function(e) {
        if (e.target === this) pmCloseTolak();
    });

    /* ── ESC untuk tutup modal ── */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { pmCloseTambah(); pmCloseTolak(); }
    });

    /* ── Cegah double submit ── */
    const pmSubmitBtn = document.getElementById('pmSubmitBtn');
    if (pmSubmitBtn) {
        pmSubmitBtn.closest('form').addEventListener('submit', function() {
            pmSubmitBtn.disabled = true;
            pmSubmitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan…';
        });
    }
</script>
@endsection

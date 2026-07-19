@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('hide-page-header', '1')

@php
    $pegawaiCollection = $pegawais instanceof \Illuminate\Contracts\Pagination\Paginator
        ? collect($pegawais->items())
        : collect($pegawais);

    $totalPegawai = $pegawaiCollection->count();

    $verifiedPegawai = $pegawaiCollection
        ->filter(fn ($item) => (bool) $item->is_verified)
        ->count();

    $unverifiedPegawai = max(
        0,
        $totalPegawai - $verifiedPegawai
    );

    $jumlahJabatan = $pegawaiCollection
        ->pluck('jabatan')
        ->filter()
        ->unique()
        ->count();
@endphp

@push('styles')
<style>
    :root {
        --ep-dark:#0E5423;
        --ep-green:#179B40;
        --ep-light:#22BA51;
        --ep-soft:#EAF8EE;
        --ep-bg:#F5FBF7;
        --ep-text:#172033;
        --ep-muted:#64748B;
        --ep-border:#E1EAE4;
        --ep-warning:#D97706;
        --ep-danger:#DC2626;
        --ep-blue:#2563EB;
    }

    .employee-page { position:relative; padding-bottom:32px; }
    .employee-page::before {
        content:""; position:absolute; top:-50px; right:2%; width:250px; height:250px;
        border-radius:50%; background:rgba(34,186,81,.055); pointer-events:none;
    }

    .employee-hero {
        position:relative; z-index:1; overflow:hidden;
        display:flex; align-items:center; justify-content:space-between; gap:20px;
        margin-bottom:17px; padding:27px 29px; border-radius:22px;
        background:
            radial-gradient(circle at 90% 5%,rgba(255,255,255,.16),transparent 29%),
            linear-gradient(135deg,#0E5423 0%,#179B40 50%,#22BA51 100%);
        box-shadow:0 18px 38px rgba(14,84,35,.17);
    }
    .employee-hero::after {
        content:""; position:absolute; right:170px; bottom:-145px; width:220px; height:220px;
        border-radius:50%; background:rgba(126,255,135,.12);
    }
    .employee-hero-left,.employee-hero-action { position:relative; z-index:2; }
    .employee-hero-left { display:flex; align-items:center; gap:16px; min-width:0; }
    .employee-hero-icon {
        display:flex; align-items:center; justify-content:center;
        width:58px; min-width:58px; height:58px; border-radius:17px;
        border:1px solid rgba(255,255,255,.21); background:rgba(255,255,255,.14);
        color:#fff; font-size:22px; backdrop-filter:blur(8px);
    }
    .employee-hero h1 { margin:0; color:#fff; font-size:23px; font-weight:800; letter-spacing:-.02em; }
    .employee-hero p { margin:6px 0 0; color:rgba(255,255,255,.82); font-size:13px; }
    .employee-add {
        display:inline-flex; align-items:center; justify-content:center; gap:9px;
        min-height:45px; padding:0 17px; border:1px solid rgba(255,255,255,.7);
        border-radius:12px; background:#fff; color:var(--ep-dark)!important;
        font-size:13px; font-weight:800; text-decoration:none!important;
        box-shadow:0 9px 20px rgba(0,70,27,.16); transition:.2s;
    }
    .employee-add i {
        display:flex; align-items:center; justify-content:center; width:24px; height:24px;
        border-radius:7px; background:var(--ep-soft); color:var(--ep-green); font-size:11px;
    }
    .employee-add:hover { transform:translateY(-2px); background:#F3FFF6; box-shadow:0 13px 25px rgba(0,70,27,.22); }

    .employee-summary {
        position:relative; z-index:1; display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:17px;
    }
    .employee-stat {
        display:flex; align-items:center; gap:13px; min-height:92px; padding:17px 18px;
        border:1px solid var(--ep-border); border-radius:17px; background:#fff;
        box-shadow:0 10px 25px rgba(15,23,42,.055); transition:.2s;
    }
    .employee-stat:hover { transform:translateY(-2px); border-color:rgba(23,155,64,.23); }
    .employee-stat-icon {
        display:flex; align-items:center; justify-content:center;
        width:45px; min-width:45px; height:45px; border-radius:13px;
        background:var(--stat-soft); color:var(--stat-color); font-size:17px;
    }
    .employee-stat small { display:block; color:var(--ep-muted); font-size:11.5px; font-weight:700; }
    .employee-stat strong { display:block; margin-top:4px; color:var(--ep-text); font-size:21px; line-height:1.2; }
    .employee-stat-total { --stat-color:var(--ep-blue); --stat-soft:#EEF4FF; }
    .employee-stat-verified { --stat-color:var(--ep-green); --stat-soft:var(--ep-soft); }
    .employee-stat-unverified { --stat-color:var(--ep-warning); --stat-soft:#FFF7E6; }
    .employee-stat-position { --stat-color:#7C3AED; --stat-soft:#F5F0FF; }

    .employee-card {
        position:relative; z-index:1; overflow:hidden; border:1px solid rgba(14,84,35,.09);
        border-radius:20px; background:#fff;
        box-shadow:0 18px 42px rgba(15,23,42,.075);
    }
    .employee-card-head {
        display:flex; align-items:center; justify-content:space-between; gap:16px;
        padding:20px 22px; border-bottom:1px solid #E7EEE9;
        background:linear-gradient(180deg,#fff,#F9FCFA);
    }
    .employee-card-title { display:flex; align-items:center; gap:12px; }
    .employee-card-title-icon {
        display:flex; align-items:center; justify-content:center;
        width:41px; min-width:41px; height:41px; border-radius:12px;
        background:var(--ep-soft); color:var(--ep-green);
    }
    .employee-card-title h2 { margin:0; color:var(--ep-text); font-size:16px; font-weight:800; }
    .employee-card-title p { margin:4px 0 0; color:var(--ep-muted); font-size:11.5px; }
    .employee-visible {
        display:inline-flex; align-items:center; gap:7px; padding:7px 11px;
        border:1px solid #D6E9DC; border-radius:10px; background:var(--ep-soft);
        color:var(--ep-dark); font-size:11px; font-weight:800; white-space:nowrap;
    }

    .employee-toolbar {
        display:flex; align-items:flex-start; justify-content:space-between; gap:15px;
        padding:17px 22px; border-bottom:1px solid #E9EFEB;
    }
    .employee-search-area { flex:1; min-width:0; }
    .employee-search-wrap { position:relative; width:min(100%,430px); }
    .employee-search-wrap > i {
        position:absolute; top:50%; left:15px; z-index:2;
        color:#789084; font-size:13px; transform:translateY(-50%);
    }
    .employee-search {
        width:100%; height:44px; padding:0 42px 0 41px;
        border:1px solid #D8E4DC; border-radius:12px; background:#FBFDFC;
        color:var(--ep-text); font-size:13px; outline:none; transition:.2s;
    }
    .employee-search:focus {
        border-color:var(--ep-green); background:#fff;
        box-shadow:0 0 0 4px rgba(23,155,64,.11);
    }
    .employee-search::placeholder { color:#94A3B8; }
    .employee-clear {
        position:absolute; top:50%; right:9px; display:none;
        align-items:center; justify-content:center; width:28px; height:28px;
        padding:0; border:0; border-radius:8px; background:#EDF5EF; color:#64748B;
        transform:translateY(-50%); cursor:pointer;
    }
    .employee-search-help {
        display:flex; align-items:center; gap:6px; margin:7px 0 0;
        color:#7A8B82; font-size:10.5px;
    }
    .employee-search-help i { color:var(--ep-green); }

    .employee-filters {
        display:inline-flex; gap:5px; padding:4px;
        border:1px solid #DCE7DF; border-radius:12px; background:#F7FAF8;
    }
    .employee-filter {
        min-height:34px; padding:0 12px; border:0; border-radius:9px;
        background:transparent; color:#64748B; font-size:11px; font-weight:800;
        cursor:pointer; transition:.2s; white-space:nowrap;
    }
    .employee-filter:hover { background:#EDF7F0; color:var(--ep-dark); }
    .employee-filter.active {
        background:#fff; color:var(--ep-dark); box-shadow:0 4px 10px rgba(15,23,42,.07);
    }

    .employee-table-area {
        padding:20px 22px 24px;
        background:linear-gradient(180deg,rgba(234,248,238,.32),#fff 86px);
    }
    .employee-table-wrap {
        overflow:hidden; border:1px solid var(--ep-border);
        border-radius:15px; background:#fff;
    }
    .employee-table { width:100%; margin:0; border-collapse:separate; border-spacing:0; }
    .employee-table thead th {
        padding:14px 15px; border:0; border-bottom:1px solid var(--ep-border);
        background:#F3F8F4; color:#4B6253; font-size:10.5px; font-weight:800;
        letter-spacing:.07em; text-align:center!important;
        text-transform:uppercase; vertical-align:middle;
    }
    .employee-table tbody td {
        padding:14px 15px; border:0; border-bottom:1px solid #ECF1ED;
        background:#fff; color:#334155; font-size:13px;
        text-align:center!important; vertical-align:middle;
        transition:.2s;
    }
    .employee-table tbody tr:last-child td { border-bottom:0; }
    .employee-table tbody tr[data-search-row]:hover td { background:#F7FCF8; }

    .employee-no {
        display:inline-flex; align-items:center; justify-content:center;
        width:31px; height:31px; border:1px solid #D6E9DC; border-radius:9px;
        background:var(--ep-bg); color:var(--ep-dark); font-size:11.5px; font-weight:800;
    }
    .employee-identity {
        display:flex; align-items:center; justify-content:center;
        gap:11px; min-width:190px; text-align:center;
    }
    .employee-avatar {
        display:flex; align-items:center; justify-content:center;
        width:39px; min-width:39px; height:39px; border:1px solid #CFE6D6;
        border-radius:12px; background:linear-gradient(135deg,#EAF8EE,#D8F2DF);
        color:var(--ep-dark); font-size:12px; font-weight:900;
    }
    .employee-name { color:var(--ep-text); font-size:13.5px; font-weight:800; }
    .employee-email {
        display:flex; align-items:center; justify-content:center;
        gap:4px; margin-top:3px; color:var(--ep-muted);
        font-size:10.5px; word-break:break-word;
    }
    .employee-chip {
        display:inline-flex; align-items:center; gap:6px; padding:7px 10px;
        border:1px solid #DCE6DF; border-radius:9px; background:#F8FAF9;
        color:#3E5447; font-size:11.5px; font-weight:800; white-space:nowrap;
    }
    .employee-chip.position {
        border-color:#D5E9DB; background:var(--ep-soft); color:var(--ep-dark);
    }
    .employee-chip i { color:var(--ep-green); font-size:10px; }

    .employee-status {
        display:inline-flex; align-items:center; gap:7px; min-height:30px;
        padding:0 10px; border:1px solid; border-radius:999px;
        font-size:10.5px; font-weight:800; white-space:nowrap;
    }
    .employee-status::before { content:""; width:6px; height:6px; border-radius:50%; }
    .employee-status.verified { border-color:#BDE5C7; background:#EFFAF2; color:#166534; }
    .employee-status.verified::before { background:#22C55E; box-shadow:0 0 0 3px rgba(34,197,94,.12); }
    .employee-status.unverified { border-color:#F1D59A; background:#FFF8E8; color:#92400E; }
    .employee-status.unverified::before { background:#F59E0B; box-shadow:0 0 0 3px rgba(245,158,11,.12); }

    .employee-actions {
        display:inline-flex; align-items:center; justify-content:center; gap:6px;
    }
    .employee-action {
        display:inline-flex; align-items:center; justify-content:center; gap:6px;
        width:35px; height:35px; padding:0; border:1px solid; border-radius:10px;
        font-size:12px; text-decoration:none!important; cursor:pointer; transition:.2s;
    }
    .employee-action:hover { transform:translateY(-1px); }
    .employee-action.detail { border-color:#C9DBFA; background:#F0F5FF; color:#2563EB!important; }
    .employee-action.edit { border-color:#EED69B; background:#FFF8E9; color:#B77900!important; }
    .employee-action.delete { border-color:#F1C6CB; background:#FFF3F4; color:#DC2626!important; }
    .employee-action-label { display:none; }

    .employee-empty { padding:54px 20px!important; text-align:center; }
    .employee-empty i {
        display:flex; align-items:center; justify-content:center;
        width:62px; height:62px; margin:0 auto 14px; border-radius:18px;
        background:var(--ep-soft); color:var(--ep-green); font-size:23px;
    }
    .employee-empty strong { display:block; color:var(--ep-text); font-size:14px; }
    .employee-empty span { display:block; margin-top:5px; color:var(--ep-muted); font-size:12px; }

    .employee-delete-modal .modal-content {
        overflow:hidden; border:0; border-radius:19px;
        box-shadow:0 26px 65px rgba(15,23,42,.23);
    }
    .employee-delete-modal .modal-body { padding:28px 27px 20px; text-align:center; }
    .employee-delete-icon {
        display:flex; align-items:center; justify-content:center;
        width:64px; height:64px; margin:0 auto 16px; border-radius:19px;
        background:#FFF1F2; color:var(--ep-danger); font-size:23px;
    }
    .employee-delete-modal h3 { margin:0; color:var(--ep-text); font-size:19px; font-weight:800; }
    .employee-delete-modal p { margin:9px 0 0; color:var(--ep-muted); font-size:13px; line-height:1.6; }
    .employee-delete-modal .modal-footer { justify-content:center; gap:9px; padding:0 27px 25px; border:0; }
    .employee-modal-btn {
        display:inline-flex; align-items:center; justify-content:center; gap:8px;
        min-height:43px; padding:0 17px; border-radius:11px;
        font-size:12.5px; font-weight:800;
    }
    .employee-modal-cancel { border:1px solid #D7E1DA; background:#fff; color:#475569; }
    .employee-modal-delete {
        border:1px solid var(--ep-danger); background:var(--ep-danger); color:#fff;
        box-shadow:0 8px 17px rgba(220,38,38,.18);
    }

    @media (max-width:1199.98px) {
        .employee-summary { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }

    @media (max-width:991.98px) {
        .employee-card-head,.employee-toolbar { align-items:flex-start; flex-direction:column; }
        .employee-toolbar,.employee-search-wrap,.employee-filters { width:100%; }
        .employee-filter { flex:1; }
    }

    @media (max-width:767.98px) {
        .employee-hero { align-items:flex-start; flex-direction:column; padding:23px 20px; border-radius:18px; }
        .employee-hero-icon { width:51px; min-width:51px; height:51px; }
        .employee-hero h1 { font-size:20px; }
        .employee-hero-action,.employee-add { width:100%; }
        .employee-summary { grid-template-columns:1fr; gap:11px; }
        .employee-stat { min-height:82px; }
        .employee-card { border-radius:17px; }
        .employee-card-head,.employee-toolbar { padding:17px; }
        .employee-table-area { padding:15px; }
        .employee-table-wrap { overflow:visible; border:0; background:transparent; }
        .employee-table,.employee-table tbody,.employee-table tr,.employee-table td { display:block; width:100%; }
        .employee-table thead { display:none; }
        .employee-table tbody tr[data-search-row] {
            overflow:hidden; margin-bottom:13px; border:1px solid var(--ep-border);
            border-radius:15px; background:#fff; box-shadow:0 8px 20px rgba(15,23,42,.045);
        }
        .employee-table tbody tr[data-search-row] td {
            display:flex; align-items:center; justify-content:space-between; gap:14px;
            min-height:52px; padding:11px 14px; border-bottom:1px solid #ECF1ED;
            text-align:right!important;
        }
        .employee-table tbody tr[data-search-row] td:last-child { border-bottom:0; }
        .employee-table tbody tr[data-search-row] td::before {
            content:attr(data-label); flex-shrink:0; color:#718078;
            font-size:9.5px; font-weight:800; letter-spacing:.06em; text-transform:uppercase;
        }
        .employee-identity { min-width:0; justify-content:flex-end; }
        .employee-avatar { display:none; }
        .employee-actions { flex-wrap:wrap; justify-content:flex-end; }
        .employee-action { width:auto; padding:0 10px; }
        .employee-action-label { display:inline; font-size:10px; font-weight:800; }
        .employee-empty {
            display:block!important; padding:44px 18px!important;
            border:1px solid var(--ep-border)!important; border-radius:15px; background:#fff!important;
        }
        .employee-empty::before { display:none; }
    }
</style>
@endpush

@section('content')
<div class="employee-page">
    <section class="employee-hero">
        <div class="employee-hero-left">
            <div class="employee-hero-icon"><i class="fa-solid fa-users"></i></div>
            <div>
                <h1>Data Pegawai</h1>
                <p id="recordCount" data-label="pegawai">
                    Kelola {{ number_format($totalPegawai, 0, ',', '.') }} pegawai masjid yang terdaftar di FINUS.
                </p>
            </div>
        </div>

        <div class="employee-hero-action">
            <a href="{{ route('admin.pegawai.create') }}" class="employee-add">
                <i class="fa-solid fa-plus"></i>
                Tambah Pegawai
            </a>
        </div>
    </section>

    <section class="employee-summary">
        <article class="employee-stat employee-stat-total">
            <div class="employee-stat-icon"><i class="fa-solid fa-users"></i></div>
            <div><small>Total Pegawai</small><strong>{{ number_format($totalPegawai) }}</strong></div>
        </article>

        <article class="employee-stat employee-stat-verified">
            <div class="employee-stat-icon"><i class="fa-solid fa-user-check"></i></div>
            <div><small>Sudah Terverifikasi</small><strong>{{ number_format($verifiedPegawai) }}</strong></div>
        </article>

        <article class="employee-stat employee-stat-unverified">
            <div class="employee-stat-icon"><i class="fa-solid fa-user-clock"></i></div>
            <div><small>Belum Terverifikasi</small><strong>{{ number_format($unverifiedPegawai) }}</strong></div>
        </article>

        <article class="employee-stat employee-stat-position">
            <div class="employee-stat-icon"><i class="fa-solid fa-briefcase"></i></div>
            <div><small>Jumlah Jabatan</small><strong>{{ number_format($jumlahJabatan) }}</strong></div>
        </article>
    </section>

    <section class="employee-card">
        <header class="employee-card-head">
            <div class="employee-card-title">
                <div class="employee-card-title-icon"><i class="fa-solid fa-address-book"></i></div>
                <div>
                    <h2>Daftar Pegawai</h2>
                    <p>Cari, lihat detail, ubah, atau hapus data pegawai.</p>
                </div>
            </div>

            <div class="employee-visible">
                <i class="fa-solid fa-database"></i>
                <span id="visibleCount">{{ $totalPegawai }}</span>
                data ditampilkan
            </div>
        </header>

        <div class="employee-toolbar">
            <div class="employee-search-area">
                <div class="employee-search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input
                        id="searchInput"
                        type="search"
                        class="employee-search"
                        placeholder="Ketik dari awal NIP, nama, email, atau jabatan..."
                        autocomplete="off"
                    >
                    <button type="button" id="clearSearch" class="employee-clear" aria-label="Hapus pencarian">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <p class="employee-search-help">
                    <i class="fa-solid fa-circle-info"></i>
                    Pencarian dimulai dari karakter pertama setiap data.
                </p>
            </div>

            <div class="employee-filters" role="group" aria-label="Filter status">
                <button type="button" class="employee-filter active" data-status-filter="all" aria-pressed="true">
                    Semua ({{ $totalPegawai }})
                </button>
                <button type="button" class="employee-filter" data-status-filter="verified" aria-pressed="false">
                    Terverifikasi ({{ $verifiedPegawai }})
                </button>
                <button type="button" class="employee-filter" data-status-filter="unverified" aria-pressed="false">
                    Belum ({{ $unverifiedPegawai }})
                </button>
            </div>
        </div>

        <div class="employee-table-area">
            <div class="employee-table-wrap">
                <table id="pegawaiTable" class="employee-table">
                    <thead>
                        <tr>
                            <th width="66" class="text-center">No.</th>
                            <th>Identitas Pegawai</th>
                            <th width="155">NIP</th>
                            <th width="180">Jabatan</th>
                            <th width="145" class="text-center">Status</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pegawais as $pegawai)
                            @php
                                $parts = preg_split('/\s+/', trim((string) $pegawai->nama_pegawai));
                                $initials = mb_strtoupper(
                                    mb_substr($parts[0] ?? 'P', 0, 1)
                                    . (count($parts) > 1 ? mb_substr($parts[count($parts)-1], 0, 1) : '')
                                );
                                $status = $pegawai->is_verified ? 'verified' : 'unverified';
                                $statusLabel = $pegawai->is_verified ? 'Terverifikasi' : 'Belum Terverifikasi';
                            @endphp

                            <tr
                                data-search-row
                                data-status="{{ $status }}"
                                data-search-start="{{ $pegawai->nip }}|{{ $pegawai->nama_pegawai }}|{{ $pegawai->email }}|{{ $pegawai->jabatan }}|{{ $statusLabel }}"
                            >
                                <td class="text-center" data-label="Nomor">
                                    <span class="employee-no" data-row-number>{{ $loop->iteration }}</span>
                                </td>

                                <td data-label="Pegawai">
                                    <div class="employee-identity">
                                        <div class="employee-avatar">{{ $initials }}</div>
                                        <div>
                                            <div class="employee-name">{{ $pegawai->nama_pegawai }}</div>
                                            <div class="employee-email">
                                                <i class="fa-solid fa-envelope mr-1"></i>
                                                {{ $pegawai->email ?: 'Email belum tersedia' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="NIP">
                                    <span class="employee-chip">
                                        <i class="fa-solid fa-id-card"></i>
                                        {{ $pegawai->nip }}
                                    </span>
                                </td>

                                <td data-label="Jabatan">
                                    <span class="employee-chip position">
                                        <i class="fa-solid fa-briefcase"></i>
                                        {{ $pegawai->jabatan }}
                                    </span>
                                </td>

                                <td class="text-center" data-label="Status">
                                    <span class="employee-status {{ $status }}">
                                        {{ $pegawai->is_verified ? 'Terverifikasi' : 'Belum' }}
                                    </span>
                                </td>

                                <td class="text-center" data-label="Aksi">
                                    <div class="employee-actions">
                                        <a
                                            href="{{ route('admin.pegawai.show', $pegawai->id) }}"
                                            class="employee-action detail"
                                            title="Detail {{ $pegawai->nama_pegawai }}"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                            <span class="employee-action-label">Detail</span>
                                        </a>

                                        <a
                                            href="{{ route('admin.pegawai.edit', $pegawai->id) }}"
                                            class="employee-action edit"
                                            title="Edit {{ $pegawai->nama_pegawai }}"
                                        >
                                            <i class="fa-solid fa-pen"></i>
                                            <span class="employee-action-label">Edit</span>
                                        </a>

                                        <button
                                            type="button"
                                            class="employee-action delete"
                                            title="Hapus {{ $pegawai->nama_pegawai }}"
                                            data-toggle="modal"
                                            data-target="#deletePegawaiModal"
                                            data-delete-url="{{ route('admin.pegawai.destroy', $pegawai->id) }}"
                                            data-employee-name="{{ $pegawai->nama_pegawai }}"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                            <span class="employee-action-label">Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="employee-empty">
                                    <i class="fa-solid fa-users-slash"></i>
                                    <strong>Belum ada data pegawai</strong>
                                    <span>Tekan tombol Tambah Pegawai untuk memasukkan data pertama.</span>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="emptySearchRow" style="display:none;">
                            <td colspan="6" class="employee-empty">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <strong>Data pegawai tidak ditemukan</strong>
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

@push('modals')
<div class="modal fade employee-delete-modal" id="deletePegawaiModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="employee-delete-icon"><i class="fa-solid fa-trash-can"></i></div>
                <h3>Hapus Data Pegawai?</h3>
                <p>
                    Data <strong id="deletePegawaiName">pegawai</strong> akan dihapus.
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="employee-modal-btn employee-modal-cancel" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>

                <form id="deletePegawaiForm" method="POST" action="" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="employee-modal-btn employee-modal-delete">
                        <i class="fa-solid fa-trash"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

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

    const filterRows = () => {
        const keyword = normalize(input?.value);
        let visible = 0;

        rows.forEach(row => {
            const values = (row.dataset.searchStart || '')
                .split('|')
                .map(normalize)
                .filter(Boolean);

            const matchSearch = keyword === ''
                || values.some(value => value.startsWith(keyword));

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
            emptyRow.style.display = visible === 0 && rows.length > 0 ? '' : 'none';
        }

        if (visibleCount) visibleCount.textContent = visible;
        if (recordCount) recordCount.textContent = `Menampilkan ${visible} pegawai pada daftar saat ini.`;

        if (clearButton) {
            clearButton.style.display = input.value.trim() ? 'flex' : 'none';
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

(() => {
    const modal = document.getElementById('deletePegawaiModal');
    const form = document.getElementById('deletePegawaiForm');
    const name = document.getElementById('deletePegawaiName');

    if (!modal || !form || !name || !window.jQuery) return;

    window.jQuery(modal).on('show.bs.modal', event => {
        const trigger = event.relatedTarget;
        if (!trigger) return;

        form.action = trigger.dataset.deleteUrl || '';
        name.textContent = trigger.dataset.employeeName || 'pegawai';
    });
})();
</script>
@endpush
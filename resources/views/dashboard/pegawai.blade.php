@extends('layouts.app')
@section('title', 'Dashboard Staff')
@section('content')
@include('layouts.partials.finus-ui')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');

    $presensiItems = collect($presensiBulanIni ?? []);
    $hadirBulanIni = $presensiItems->where('status', 'hadir')->count();
    $menungguPersetujuan = $presensiItems
        ->filter(fn ($item) => isset($item->is_approved) && ! (bool) $item->is_approved)
        ->count();

    $totalGajiTerakhir = $penggajianTerakhir->total_gaji ?? 0;
    $tanggalHariIni = now()->format('d/m/Y');
@endphp

<style>
    :root {
        --finus-green-dark: #065f22;
        --finus-green: #16a34a;
        --finus-green-soft: #ecfdf5;
        --finus-text-dark: #0f172a;
        --finus-text-muted: #64748b;
        --finus-border: #e2e8f0;
        --finus-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .finus-dashboard-header {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background: linear-gradient(135deg, var(--finus-green-dark) 0%, var(--finus-green) 100%);
        color: #ffffff;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 18px 38px rgba(22, 163, 74, .20);
    }

    .finus-dashboard-header::before {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        right: 110px;
        bottom: -150px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .10);
    }

    .finus-dashboard-header::after {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        right: -85px;
        top: -95px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .16);
    }

    .finus-dashboard-header-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .finus-dashboard-heading {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .finus-dashboard-heading-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: rgba(255, 255, 255, .17);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 27px;
        box-shadow: inset 0 0 0 1px rgba(79, 249, 64, 0.83);
    }

    .finus-dashboard-title {
        margin: 0;
        color: #ffffff;
        font-size: 28px;
        font-weight: 800;
    }

    .finus-dashboard-subtitle {
        margin: 8px 0 0;
        color: rgba(255, 255, 255, .82);
    }

    .finus-dashboard-period {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border-radius: 14px;
        background: rgba(255, 255, 255, .16);
        color: #ffffff;
        font-weight: 800;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .16);
        white-space: nowrap;
    }

    .finus-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .finus-summary-card {
        position: relative;
        min-height: 160px;
        padding: 24px;
        border: 0;
        border-radius: 20px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: var(--finus-shadow);
    }

    .finus-summary-card::after {
        content: "";
        position: absolute;
        right: -54px;
        bottom: -66px;
        width: 150px;
        height: 150px;
        border-radius: 999px;
        background: #dcfce7;
        opacity: .55;
    }

    .finus-summary-card:nth-child(2)::after {
        background: #dbeafe;
    }

    .finus-summary-card:nth-child(3)::after {
        background: #ffedd5;
    }

    .finus-summary-card h5 {
        position: relative;
        z-index: 2;
        margin-bottom: 14px;
        color: #475569;
        font-size: 14px;
        font-weight: 700;
    }

    .finus-summary-card p {
        position: relative;
        z-index: 2;
        margin-bottom: 0;
        color: var(--finus-text-dark);
        font-size: 26px;
        font-weight: 900;
    }

    .pegawai-card {
        border: 0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: var(--finus-shadow);
        overflow: hidden;
    }

    .pegawai-card-header {
        padding: 24px 26px 12px;
        border-bottom: 0;
        background: #ffffff;
    }

    .pegawai-card-title {
        margin: 0;
        color: var(--finus-text-dark);
        font-size: 20px;
        font-weight: 800;
    }

    .pegawai-card-subtitle {
        margin: 6px 0 0;
        color: var(--finus-text-muted);
        font-size: 14px;
    }

    .pegawai-card-body {
        padding: 18px 26px 26px;
    }

    .focus-list {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .focus-list li {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 18px;
        margin-bottom: 10px;
        border-left: 4px solid var(--finus-green);
        border-radius: 0 12px 12px 0;
        background: #f8fafc;
        color: #1e293b;
        font-weight: 600;
    }

    .quick-action-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px;
        margin-bottom: 12px;
        border: 1px solid var(--finus-border);
        border-radius: 16px;
        background: #ffffff;
        color: #1e293b;
        text-decoration: none;
        transition: .2s;
    }

    .quick-action-link:hover {
        transform: translateY(-2px);
        color: #047857;
        text-decoration: none;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
    }

    .quick-action-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: #dcfce7;
        color: var(--finus-green);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .quick-action-text strong {
        display: block;
        color: var(--finus-text-dark);
        font-weight: 800;
    }

    .quick-action-text small {
        color: var(--finus-text-muted);
    }

    .gap-3 {
        gap: 1rem;
    }

    @media (max-width: 991.98px) {
        .finus-dashboard-header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .finus-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="finus-dashboard-header">
    <div class="finus-dashboard-header-content">
        <div class="finus-dashboard-heading">
            <div class="finus-dashboard-heading-icon">
                <i class="fa fa-user-check"></i>
            </div>

            <div>
                <h2 class="finus-dashboard-title">
                    Dashboard {{ $pegawai->jabatan ?? 'Pegawai' }}
                </h2>
                <p class="finus-dashboard-subtitle">
                    Informasi presensi, aktivitas kerja, dan penggajian terbaru.
                </p>
            </div>
        </div>

        <div class="finus-dashboard-period">
            <i class="ti-calendar"></i>
            {{ $tanggalHariIni }}
        </div>
    </div>
</div>

<div class="finus-summary">
    <div class="finus-summary-card">
        <h5>Presensi Bulan Ini</h5>
        <p>{{ $hadirBulanIni }} hadir</p>
    </div>

    <div class="finus-summary-card">
        <h5>Penggajian Terakhir</h5>
        <p>{{ $rupiah($totalGajiTerakhir) }}</p>
    </div>

    <div class="finus-summary-card">
        <h5>Menunggu Persetujuan</h5>
        <p>{{ $menungguPersetujuan }}</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="pegawai-card mb-4">
            <div class="pegawai-card-header">
                <h4 class="pegawai-card-title">
                    Fokus {{ $pegawai->jabatan ?? 'Pegawai' }}
                </h4>
                <p class="pegawai-card-subtitle">
                    Aktivitas utama sesuai tugas pegawai.
                </p>
            </div>

            <div class="pegawai-card-body">
                <ul class="focus-list">
                    <li>
                        <i class="fa fa-check-circle text-success"></i>
                        Pelaksanaan tugas {{ $pegawai->jabatan ?? 'pegawai' }}
                    </li>

                    <li>
                        <i class="fa fa-check-circle text-success"></i>
                        Presensi dan aktivitas pribadi
                    </li>

                    <li>
                        <i class="fa fa-check-circle text-success"></i>
                        Koordinasi dengan admin dan pengurus
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="pegawai-card mb-4">
            <div class="pegawai-card-header">
                <h4 class="pegawai-card-title">Akses Cepat</h4>
                <p class="pegawai-card-subtitle">
                    Buka menu pegawai utama.
                </p>
            </div>

            <div class="pegawai-card-body">
                <a href="{{ route('pegawai.presensi.create') }}" class="quick-action-link">
                    <div class="d-flex align-items-center gap-3">
                        <span class="quick-action-icon">
                            <i class="fa fa-calendar-plus"></i>
                        </span>

                        <span class="quick-action-text">
                            <strong>Isi Presensi</strong>
                            <small>Kirim presensi harian pegawai.</small>
                        </span>
                    </div>

                    <i class="fa fa-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('pegawai.presensi.index') }}" class="quick-action-link">
                    <div class="d-flex align-items-center gap-3">
                        <span class="quick-action-icon">
                            <i class="fa fa-list-check"></i>
                        </span>

                        <span class="quick-action-text">
                            <strong>Riwayat Presensi</strong>
                            <small>Lihat data presensi yang sudah dikirim.</small>
                        </span>
                    </div>

                    <i class="fa fa-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- FINUS DARK MODE LOCAL: dashboard/pegawai.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="dashboard/pegawai.blade.php">
html[data-finus-theme="dark"] body .finus-summary-card,
html[data-finus-theme="dark"] body .pegawai-card { border-color:#293D31 !important; background:linear-gradient(155deg,#15211A,#111A15) !important; color:#F1F6F3 !important; box-shadow:0 13px 30px rgba(0,0,0,.19) !important; }
html[data-finus-theme="dark"] body .pegawai-card-header { border-color:#293D31 !important; background:linear-gradient(180deg,#17251D,#121D17) !important; }
html[data-finus-theme="dark"] body :where(.pegawai-card-title,.quick-action-text strong,.finus-summary-card strong) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.pegawai-card-subtitle,.quick-action-text small,.finus-summary-card small) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .focus-list li { border-color:#31503C !important; background:#14241B !important; color:#CBDDD0 !important; }
html[data-finus-theme="dark"] body .quick-action-link { border-color:#293D31 !important; background:#121F18 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .quick-action-link:hover { border-color:#365141 !important; background:#192A20 !important; }
html[data-finus-theme="dark"] body .quick-action-icon { background:#173620 !important; color:#64DD81 !important; }
</style>
@endpush


@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('hide-page-header', '1')

@section('content')
@include('layouts.partials.finus-ui')

@php
    $initial = mb_strtoupper(
        mb_substr(
            trim($pegawai->nama_pegawai ?? 'P'),
            0,
            1
        )
    );

    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');

    $gajiPerhari = $pegawai->gajiJabatan?->gaji_perhari ?? 0;

    $gajiSudahDibayar = $penggajianTerakhir
        && $penggajianTerakhir->status_penggajian === 'sudah_dibayar';

    $recoveryCode = $pegawai->user?->recovery_code;
@endphp

<div class="fmu-page employee-detail-page">

    <section class="fmu-hero employee-detail-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon">
                <i class="fa-solid fa-address-card"></i>
            </span>

            <div>
                <h1>Detail Pegawai</h1>
                <p>Informasi identitas, jabatan, presensi, dan penggajian pegawai.</p>
            </div>
        </div>

        <div class="fmu-hero-actions">
            <a
                href="{{ route('admin.pegawai.edit', $pegawai->id) }}"
                class="fmu-btn employee-hero-edit"
            >
                <i class="fa-solid fa-pen"></i>
                Edit Pegawai
            </a>
        </div>
    </section>

    <section class="fmu-card employee-detail-main-card mb-3">
        <div class="fmu-card-body employee-detail-main-body">
            <div class="employee-detail-layout">

                <aside class="employee-profile-card">
                    <div class="employee-profile-top">
                        <span class="employee-avatar" aria-hidden="true">
                            {{ $initial }}
                        </span>

                        <h2 class="employee-profile-name">
                            {{ $pegawai->nama_pegawai }}
                        </h2>

                        <span class="employee-role-pill">
                            <i class="fa-solid fa-briefcase"></i>
                            {{ $pegawai->jabatan ?: 'Jabatan belum ditentukan' }}
                        </span>
                    </div>

                    <div class="employee-recovery-card">
                        <div class="employee-recovery-head">
                            <div>
                                <span class="employee-recovery-label">Recovery Code</span>
                                <small>Kode pemulihan akun pegawai</small>
                            </div>

                            <span class="employee-recovery-icon" aria-hidden="true">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                        </div>

                        @if($recoveryCode)
                            <div class="employee-recovery-control">
                                <div
                                    id="pegawaiRecoveryCode"
                                    class="employee-recovery-value is-hidden"
                                    data-recovery-code="{{ $recoveryCode }}"
                                    role="textbox"
                                    aria-readonly="true"
                                    aria-label="Recovery Code Pegawai disembunyikan"
                                    aria-live="polite"
                                >
                                    ••••••••••••••••
                                </div>

                                <button
                                    type="button"
                                    id="togglePegawaiRecoveryCode"
                                    aria-label="Tampilkan Recovery Code"
                                    aria-pressed="false"
                                    title="Tampilkan Recovery Code"
                                >
                                    <i
                                        id="pegawaiRecoveryCodeIcon"
                                        class="fa-solid fa-eye"
                                        aria-hidden="true"
                                    ></i>
                                </button>
                            </div>
                        @else
                            <div class="employee-recovery-empty">
                                <i class="fa-solid fa-circle-info"></i>
                                Belum tersedia
                            </div>
                        @endif
                    </div>

                    <div class="employee-status-block">
                        <span class="employee-status-label">Status Akun Pegawai</span>

                        <span
                            class="fmu-badge employee-status-badge"
                            style="
                                --badge-color:{{ $pegawai->is_verified ? '#179B40' : '#D97706' }};
                                --badge-soft:{{ $pegawai->is_verified ? '#EAF8EE' : '#FFF7E6' }}
                            "
                        >
                            {{ $pegawai->is_verified ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                        </span>
                    </div>
                </aside>

                <div class="employee-information-area">
                    <div class="employee-information-grid">
                        @foreach([
                            ['NIP', $pegawai->nip ?: '-', 'fa-id-card', 'green'],
                            ['Jabatan', $pegawai->jabatan ?: '-', 'fa-briefcase', 'green'],
                            ['Email', strtolower((string) ($pegawai->email ?: '-')), 'fa-envelope', 'blue'],
                            ['Telepon', $pegawai->no_telp ?: '-', 'fa-phone', 'blue'],
                        ] as [$label, $value, $icon, $tone])
                            <article class="employee-info-card employee-info-{{ $tone }}">
                                <span class="employee-info-icon" aria-hidden="true">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </span>

                                <div class="employee-info-copy">
                                    <small>{{ $label }}</small>
                                    <strong @class(['employee-email-value' => $label === 'Email'])>
                                        {{ $value }}
                                    </strong>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <article class="employee-address-card">
                        <div class="employee-address-head">
                            <span class="employee-address-icon" aria-hidden="true">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>

                            <div>
                                <h3>Alamat</h3>
                                <p>Alamat domisili pegawai.</p>
                            </div>
                        </div>

                        <div class="employee-address-value">
                            {{ $pegawai->alamat ?: 'Alamat belum tersedia.' }}
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="employee-summary-grid mb-3">
        <article class="employee-summary-card summary-green">
            <span class="employee-summary-icon" aria-hidden="true">
                <i class="fa-solid fa-money-bill-wave"></i>
            </span>

            <div class="employee-summary-copy">
                <small>Gaji per Hari</small>
                <strong>{{ $rupiah($gajiPerhari) }}</strong>
            </div>
        </article>

        <article class="employee-summary-card summary-blue">
            <span class="employee-summary-icon" aria-hidden="true">
                <i class="fa-solid fa-calendar-check"></i>
            </span>

            <div class="employee-summary-copy">
                <small>Hadir Disetujui Bulan Ini</small>
                <strong>{{ number_format($jumlahHadirDisetujui) }} hari</strong>
            </div>
        </article>

        <article class="employee-summary-card summary-amber">
            <span class="employee-summary-icon" aria-hidden="true">
                <i class="fa-solid fa-clock"></i>
            </span>

            <div class="employee-summary-copy">
                <small>Presensi Menunggu</small>
                <strong>{{ number_format($presensiMenunggu) }}</strong>
            </div>
        </article>

        <article class="employee-summary-card summary-purple">
            <span class="employee-summary-icon" aria-hidden="true">
                <i class="fa-solid fa-wallet"></i>
            </span>

            <div class="employee-summary-copy">
                <small>Penggajian Terakhir</small>

                @if($penggajianTerakhir)
                    <strong>
                        {{
                            $gajiSudahDibayar
                                ? $rupiah($penggajianTerakhir->total_gaji)
                                : 'Belum Dibayar'
                        }}
                    </strong>
                @else
                    <strong>Belum tersedia</strong>
                @endif
            </div>
        </article>
    </section>

    <section class="fmu-card employee-detail-actions-card">
        <div class="employee-detail-actions">
            <a
                href="{{ route('admin.pegawai.index') }}"
                class="fmu-btn"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Daftar
            </a>

            <a
                href="{{ route('admin.pegawai.edit', $pegawai->id) }}"
                class="fmu-btn fmu-btn-primary"
            >
                <i class="fa-solid fa-user-pen"></i>
                Edit Pegawai
            </a>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('pegawaiRecoveryCode');
        const button = document.getElementById('togglePegawaiRecoveryCode');
        const icon = document.getElementById('pegawaiRecoveryCodeIcon');

        if (!input || !button || !icon) {
            return;
        }

        const recoveryCode = input.dataset.recoveryCode || '';
        const maskedCode = '••••••••••••••••';

        button.addEventListener('click', function () {
            const isHidden = input.classList.contains('is-hidden');

            input.classList.toggle('is-hidden', !isHidden);
            input.textContent = isHidden ? recoveryCode : maskedCode;
            input.setAttribute(
                'aria-label',
                isHidden
                    ? 'Recovery Code Pegawai: ' + recoveryCode
                    : 'Recovery Code Pegawai disembunyikan'
            );

            button.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
            button.setAttribute(
                'aria-label',
                isHidden ? 'Sembunyikan Recovery Code' : 'Tampilkan Recovery Code'
            );
            button.setAttribute(
                'title',
                isHidden ? 'Sembunyikan Recovery Code' : 'Tampilkan Recovery Code'
            );

            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
        });
    });
</script>

@endsection

@push('styles')
<style data-finus-detail-pegawai="1">
    .employee-detail-page {
        --employee-surface: #FFFFFF;
        --employee-surface-soft: #F7FBF8;
        --employee-border: #DCE9E0;
        --employee-text: #18251D;
        --employee-muted: #66786D;
        --employee-green: #169844;
        --employee-green-soft: #E9F8EE;
        --employee-blue: #2563EB;
        --employee-blue-soft: #EDF4FF;
        --employee-amber: #D97706;
        --employee-amber-soft: #FFF5E6;
        --employee-purple: #7C3AED;
        --employee-purple-soft: #F4EFFF;
    }

    .employee-detail-hero {
        margin-bottom: 20px;
    }

    .employee-hero-edit {
        border-color: rgba(255,255,255,.28) !important;
        background: rgba(255,255,255,.94) !important;
        color: #0E5423 !important;
        box-shadow: 0 10px 24px rgba(0,0,0,.10) !important;
    }

    .employee-detail-main-card {
        overflow: visible;
    }

    .employee-detail-main-body {
        padding: 24px !important;
    }

    .employee-detail-layout {
        display: grid;
        grid-template-columns: minmax(260px, 300px) minmax(0, 1fr);
        gap: 20px;
        align-items: stretch;
    }

    .employee-profile-card {
        display: flex;
        flex-direction: column;
        min-width: 0;
        padding: 22px;
        border: 1px solid var(--employee-border);
        border-radius: 18px;
        background: linear-gradient(180deg, #FBFDFC 0%, #F5FAF7 100%);
    }

    .employee-profile-top {
        display: flex;
        align-items: center;
        flex-direction: column;
        text-align: center;
    }

    .employee-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 88px;
        height: 88px;
        border-radius: 25px;
        background: linear-gradient(145deg, #0F7C31, #1CB84E);
        color: #FFFFFF;
        font-size: 31px;
        font-weight: 900;
        box-shadow: 0 14px 30px rgba(14, 84, 35, .18);
    }

    .employee-profile-name {
        margin: 17px 0 0;
        color: var(--employee-text);
        font-size: 18px;
        font-weight: 900;
        line-height: 1.35;
        word-break: break-word;
    }

    .employee-role-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        margin-top: 9px;
        padding: 7px 11px;
        border-radius: 999px;
        background: var(--employee-green-soft);
        color: #176E36;
        font-size: 11.5px;
        font-weight: 800;
    }

    .employee-recovery-card {
        margin-top: 20px;
        padding: 15px;
        border: 1px solid var(--employee-border);
        border-radius: 15px;
        background: var(--employee-surface);
    }

    .employee-recovery-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .employee-recovery-label {
        display: block;
        color: var(--employee-text);
        font-size: 12px;
        font-weight: 900;
    }

    .employee-recovery-head small {
        display: block;
        margin-top: 3px;
        color: var(--employee-muted);
        font-size: 10px;
        line-height: 1.35;
    }

    .employee-recovery-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        border-radius: 10px;
        background: var(--employee-green-soft);
        color: var(--employee-green);
        font-size: 13px;
    }

    .employee-recovery-control {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 42px;
        gap: 8px;
        margin-top: 12px;
    }

    .employee-recovery-value,
    .employee-recovery-control button {
        min-height: 42px;
        border: 1px solid #CFE0D4;
        border-radius: 11px;
        outline: none;
        box-sizing: border-box;
    }

    .employee-recovery-value {
        display: flex;
        align-items: center;
        min-width: 0;
        width: 100%;
        padding: 9px 12px;
        background: #F7FBF8;
        color: #174C29;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .025em;
        line-height: 1.45;
        overflow-wrap: anywhere;
        word-break: break-word;
        white-space: normal;
        user-select: all;
    }

    .employee-recovery-value.is-hidden {
        letter-spacing: .12em;
        user-select: none;
    }

    .employee-recovery-control button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #FFFFFF;
        color: #0E5423;
        cursor: pointer;
        transition: background .18s ease, border-color .18s ease, transform .18s ease;
    }

    .employee-recovery-control button:hover,
    .employee-recovery-control button:focus-visible {
        border-color: #79C98F;
        background: #ECF8F0;
        transform: translateY(-1px);
    }

    .employee-recovery-empty {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
        min-height: 42px;
        padding: 0 12px;
        border-radius: 11px;
        background: #F7FBF8;
        color: var(--employee-muted);
        font-size: 11.5px;
        font-weight: 700;
    }

    .employee-status-block {
        margin-top: auto;
        padding-top: 20px;
        text-align: center;
    }

    .employee-status-label {
        display: block;
        margin-bottom: 8px;
        color: var(--employee-muted);
        font-size: 11.5px;
        font-weight: 700;
    }

    .employee-status-badge {
        font-size: 10.5px;
    }

    .employee-information-area {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
    }

    .employee-information-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .employee-info-card,
    .employee-address-card,
    .employee-summary-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--employee-border);
        background: var(--employee-surface);
        box-shadow: 0 9px 22px rgba(15, 23, 42, .045);
    }

    .employee-info-card {
        display: flex;
        align-items: center;
        gap: 14px;
        min-height: 104px;
        padding: 18px 19px;
        border-radius: 17px;
    }

    .employee-info-card::after,
    .employee-summary-card::after {
        content: "";
        position: absolute;
        right: -42px;
        bottom: -64px;
        width: 122px;
        height: 122px;
        border-radius: 999px;
        background: var(--employee-card-soft, var(--employee-green-soft));
        opacity: .62;
        pointer-events: none;
    }

    .employee-info-icon,
    .employee-address-icon,
    .employee-summary-icon {
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border-radius: 13px;
        background: var(--employee-card-soft, var(--employee-green-soft));
        color: var(--employee-card-accent, var(--employee-green));
    }

    .employee-info-icon {
        width: 48px;
        height: 48px;
        font-size: 17px;
    }

    .employee-info-green {
        --employee-card-accent: var(--employee-green);
        --employee-card-soft: var(--employee-green-soft);
    }

    .employee-info-blue {
        --employee-card-accent: var(--employee-blue);
        --employee-card-soft: var(--employee-blue-soft);
    }

    .employee-info-copy {
        position: relative;
        z-index: 2;
        min-width: 0;
    }

    .employee-info-copy small,
    .employee-summary-copy small {
        display: block;
        color: var(--employee-muted);
        font-size: 11.5px;
        font-weight: 750;
    }

    .employee-info-copy strong {
        display: block;
        margin-top: 5px;
        color: var(--employee-text);
        font-size: 15.5px;
        font-weight: 900;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .employee-email-value {
        font-size: 14px !important;
        letter-spacing: -.015em;
    }

    .employee-address-card {
        border-radius: 17px;
    }

    .employee-address-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--employee-border);
        background: linear-gradient(180deg, #FFFFFF, #FBFDFC);
    }

    .employee-address-icon {
        width: 46px;
        height: 46px;
        background: var(--employee-green-soft);
        color: var(--employee-green);
        font-size: 16px;
    }

    .employee-address-head h3 {
        margin: 0;
        color: var(--employee-text);
        font-size: 17px;
        font-weight: 900;
    }

    .employee-address-head p {
        margin: 4px 0 0;
        color: var(--employee-muted);
        font-size: 11.5px;
    }

    .employee-address-value {
        padding: 20px;
        color: var(--employee-text);
        font-size: 14px;
        font-weight: 700;
        line-height: 1.7;
    }

    .employee-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .employee-summary-card {
        display: flex;
        align-items: center;
        gap: 14px;
        min-height: 104px;
        padding: 17px 18px;
        border-radius: 17px;
    }

    .employee-summary-icon {
        width: 48px;
        height: 48px;
        font-size: 17px;
    }

    .employee-summary-copy {
        position: relative;
        z-index: 2;
        min-width: 0;
    }

    .employee-summary-copy strong {
        display: block;
        margin-top: 5px;
        color: var(--employee-text);
        font-size: clamp(18px, 1.8vw, 22px);
        font-weight: 900;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .summary-green {
        --employee-card-accent: var(--employee-green);
        --employee-card-soft: var(--employee-green-soft);
    }

    .summary-blue {
        --employee-card-accent: var(--employee-blue);
        --employee-card-soft: var(--employee-blue-soft);
    }

    .summary-amber {
        --employee-card-accent: var(--employee-amber);
        --employee-card-soft: var(--employee-amber-soft);
    }

    .summary-purple {
        --employee-card-accent: var(--employee-purple);
        --employee-card-soft: var(--employee-purple-soft);
    }

    .employee-detail-actions-card {
        overflow: hidden;
    }

    .employee-detail-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 22px;
        background: #FBFDFC;
    }

    @media (max-width: 1199.98px) {
        .employee-detail-layout {
            grid-template-columns: 260px minmax(0, 1fr);
        }

        .employee-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .employee-detail-layout {
            grid-template-columns: 1fr;
        }

        .employee-profile-card {
            display: grid;
            grid-template-columns: minmax(210px, .8fr) minmax(0, 1.2fr);
            gap: 18px;
            align-items: center;
        }

        .employee-profile-top {
            grid-row: 1 / span 2;
        }

        .employee-recovery-card {
            margin-top: 0;
        }

        .employee-status-block {
            margin-top: 0;
            padding-top: 0;
            text-align: left;
        }
    }

    @media (max-width: 767.98px) {
        .employee-detail-main-body {
            padding: 17px !important;
        }

        .employee-profile-card {
            display: flex;
            padding: 18px;
        }

        .employee-recovery-card {
            width: 100%;
            margin-top: 18px;
        }

        .employee-status-block {
            padding-top: 18px;
            text-align: center;
        }

        .employee-information-grid,
        .employee-summary-grid {
            grid-template-columns: 1fr;
        }

        .employee-detail-actions {
            align-items: stretch;
            flex-direction: column-reverse;
            padding: 16px 17px;
        }

        .employee-detail-actions .fmu-btn {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .employee-info-card,
        .employee-summary-card {
            min-height: 92px;
            padding: 15px;
        }

        .employee-address-head,
        .employee-address-value {
            padding-left: 16px;
            padding-right: 16px;
        }
    }
</style>
@endpush

@push('dark-styles')
<style data-finus-dark-local="pegawai/show.blade.php">
    html[data-finus-theme="dark"] .employee-detail-page {
        --employee-surface: #111B15;
        --employee-surface-soft: #0D1711;
        --employee-border: #294033;
        --employee-text: #F1F6F3;
        --employee-muted: #9EAEA4;
        --employee-green: #64DD81;
        --employee-green-soft: #173620;
        --employee-blue: #82AEFF;
        --employee-blue-soft: #192C47;
        --employee-amber: #F4B65A;
        --employee-amber-soft: #382A18;
        --employee-purple: #B295FF;
        --employee-purple-soft: #302442;
    }

    html[data-finus-theme="dark"] .employee-hero-edit {
        border-color: #31543D !important;
        background: #102018 !important;
        color: #C7F7D2 !important;
        box-shadow: 0 10px 24px rgba(0,0,0,.20) !important;
    }

    html[data-finus-theme="dark"] .employee-hero-edit:hover,
    html[data-finus-theme="dark"] .employee-hero-edit:focus-visible {
        border-color: #4F785D !important;
        background: #172A1F !important;
        color: #E0FFE7 !important;
    }

    html[data-finus-theme="dark"] .employee-detail-main-card,
    html[data-finus-theme="dark"] .employee-detail-actions-card {
        border-color: var(--employee-border) !important;
        background: #101A14 !important;
        box-shadow: 0 14px 34px rgba(0,0,0,.20) !important;
    }

    html[data-finus-theme="dark"] .employee-profile-card,
    html[data-finus-theme="dark"] .employee-info-card,
    html[data-finus-theme="dark"] .employee-address-card,
    html[data-finus-theme="dark"] .employee-summary-card,
    html[data-finus-theme="dark"] .employee-recovery-card {
        border-color: var(--employee-border) !important;
        background: linear-gradient(155deg, #15211A 0%, #111A15 100%) !important;
        color: var(--employee-text) !important;
        box-shadow: 0 12px 28px rgba(0,0,0,.16) !important;
    }

    html[data-finus-theme="dark"] .employee-avatar {
        background: linear-gradient(145deg, #127136, #16A648) !important;
        box-shadow: 0 14px 30px rgba(0,0,0,.24) !important;
    }

    html[data-finus-theme="dark"] .employee-role-pill {
        background: #173620 !important;
        color: #BFF4CA !important;
    }

    html[data-finus-theme="dark"] .employee-recovery-value,
    html[data-finus-theme="dark"] .employee-recovery-control button,
    html[data-finus-theme="dark"] .employee-recovery-empty {
        border-color: #31493A !important;
        background: #0C1610 !important;
        color: #E4EFE8 !important;
    }

    html[data-finus-theme="dark"] .employee-recovery-control button {
        color: #7BE694 !important;
    }

    html[data-finus-theme="dark"] .employee-recovery-control button:hover,
    html[data-finus-theme="dark"] .employee-recovery-control button:focus-visible {
        border-color: #4E745A !important;
        background: #16271C !important;
    }

    html[data-finus-theme="dark"] .employee-address-head {
        border-color: var(--employee-border) !important;
        background: linear-gradient(180deg, #17251D 0%, #121D17 100%) !important;
    }

    html[data-finus-theme="dark"] .employee-detail-actions {
        background: linear-gradient(180deg, #17251D 0%, #121D17 100%) !important;
    }

    html[data-finus-theme="dark"] .employee-profile-name,
    html[data-finus-theme="dark"] .employee-recovery-label,
    html[data-finus-theme="dark"] .employee-info-copy strong,
    html[data-finus-theme="dark"] .employee-address-head h3,
    html[data-finus-theme="dark"] .employee-address-value,
    html[data-finus-theme="dark"] .employee-summary-copy strong {
        color: var(--employee-text) !important;
    }

    html[data-finus-theme="dark"] .employee-recovery-head small,
    html[data-finus-theme="dark"] .employee-status-label,
    html[data-finus-theme="dark"] .employee-info-copy small,
    html[data-finus-theme="dark"] .employee-address-head p,
    html[data-finus-theme="dark"] .employee-summary-copy small {
        color: var(--employee-muted) !important;
    }

    html[data-finus-theme="dark"] .employee-status-badge {
        --badge-soft: #17281D !important;
    }

    html[data-finus-theme="dark"] .summary-green,
    html[data-finus-theme="dark"] .employee-info-green {
        --employee-card-accent: #64DD81;
        --employee-card-soft: #173620;
    }

    html[data-finus-theme="dark"] .summary-blue,
    html[data-finus-theme="dark"] .employee-info-blue {
        --employee-card-accent: #82AEFF;
        --employee-card-soft: #192C47;
    }

    html[data-finus-theme="dark"] .summary-amber {
        --employee-card-accent: #F4B65A;
        --employee-card-soft: #382A18;
    }

    html[data-finus-theme="dark"] .summary-purple {
        --employee-card-accent: #B295FF;
        --employee-card-soft: #302442;
    }

    html[data-finus-theme="dark"] .employee-info-card::after,
    html[data-finus-theme="dark"] .employee-summary-card::after {
        opacity: .5;
    }
</style>
@endpush

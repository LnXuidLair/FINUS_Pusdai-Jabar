@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('hide-page-header', '1')

@section('content')
@include('layouts.partials.finus-ui')

@php
    $initial =
        mb_strtoupper(
            mb_substr(
                trim(
                    $pegawai->nama_pegawai
                    ?? 'P'
                ),
                0,
                1
            )
        );

    $rupiah = fn ($value) =>
        'Rp ' . number_format(
            (int) $value,
            0,
            ',',
            '.'
        );

    $gajiPerhari =
        $pegawai->gajiJabatan
            ?->gaji_perhari
        ?? 0;

    $gajiSudahDibayar =
        $penggajianTerakhir
        && $penggajianTerakhir
            ->status_penggajian
            === 'sudah_dibayar';
@endphp

<div class="fmu-page">

    <section class="fmu-hero">

        <div class="fmu-hero-main">

            <span class="fmu-hero-icon">
                <i class="fa-solid fa-address-card"></i>
            </span>

            <div>
                <h1>Detail Pegawai</h1>

                <p>
                    Informasi identitas, jabatan,
                    presensi, dan penggajian pegawai.
                </p>
            </div>
        </div>

        <div class="fmu-hero-actions">

            <a
                href="{{
                    route(
                        'admin.pegawai.edit',
                        $pegawai->id
                    )
                }}"
                class="fmu-btn"
                style="
                    background:#fff;
                    color:#0E5423!important
                "
            >
                <i class="fa-solid fa-pen"></i>
                Edit
            </a>
        </div>
    </section>

    <section class="fmu-card mb-3">

        <div class="fmu-card-body">

            <div
                class="fmu-grid"
                style="
                    grid-template-columns:
                    minmax(230px,.55fr)
                    minmax(0,1.45fr);
                    align-items:start
                "
            >

                <aside class="fmu-side-note text-center">

                    <span
                        style="
                            display:inline-flex;
                            align-items:center;
                            justify-content:center;
                            width:82px;
                            height:82px;
                            border-radius:24px;
                            background:
                                linear-gradient(
                                    135deg,
                                    #0E5423,
                                    #22BA51
                                );
                            color:#fff;
                            font-size:29px;
                            font-weight:900;
                        "
                    >
                        {{ $initial }}
                    </span>

                    <h3 class="mt-3">
                        {{ $pegawai->nama_pegawai }}
                    </h3>

                    <p>
                        {{
                            $pegawai->jabatan
                            ?: 'Jabatan belum ditentukan'
                        }}
                    </p>

                    <div
                        style="
                            margin:14px 0 16px;
                            padding:12px 10px;
                            border:1px solid #CFE6D6;
                            border-radius:12px;
                            background:#F2FBF5;
                        "
                    >
                        <small
                            class="d-block"
                            style="color:#64748B;font-weight:700"
                        >
                            Recovery Code
                        </small>

                        @php
                            $recoveryCode = $pegawai->user?->recovery_code;
                        @endphp

                        @if($recoveryCode)
                            <div
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:8px;
                                    margin-top:7px;
                                "
                            >
                                <input
                                    id="pegawaiRecoveryCode"
                                    type="password"
                                    value="{{ $recoveryCode }}"
                                    readonly
                                    autocomplete="off"
                                    aria-label="Recovery Code Pegawai"
                                    style="
                                        width:100%;
                                        min-width:0;
                                        padding:8px 10px;
                                        border:1px solid #BFDAC7;
                                        border-radius:9px;
                                        background:#FFFFFF;
                                        color:#0E5423;
                                        font-size:13px;
                                        font-weight:800;
                                        letter-spacing:.04em;
                                        outline:none;
                                    "
                                >

                                <button
                                    type="button"
                                    id="togglePegawaiRecoveryCode"
                                    aria-label="Tampilkan Recovery Code"
                                    aria-pressed="false"
                                    title="Tampilkan Recovery Code"
                                    style="
                                        width:38px;
                                        height:38px;
                                        flex:0 0 38px;
                                        display:inline-flex;
                                        align-items:center;
                                        justify-content:center;
                                        border:1px solid #BFDAC7;
                                        border-radius:9px;
                                        background:#FFFFFF;
                                        color:#0E5423;
                                        cursor:pointer;
                                    "
                                >
                                    <i
                                        id="pegawaiRecoveryCodeIcon"
                                        class="fa-solid fa-eye"
                                        aria-hidden="true"
                                    ></i>
                                </button>
                            </div>
                        @else
                            <strong
                                style="
                                    display:block;
                                    margin-top:5px;
                                    color:#64748B;
                                    font-size:13px;
                                "
                            >
                                Belum tersedia
                            </strong>
                        @endif
                    </div>

                    <small class="d-block mb-1">
                        Status Akun Pegawai
                    </small>

                    <span
                        class="fmu-badge"
                        style="
                            --badge-color:{{
                                $pegawai->is_verified
                                    ? '#179B40'
                                    : '#D97706'
                            }};
                            --badge-soft:{{
                                $pegawai->is_verified
                                    ? '#EAF8EE'
                                    : '#FFF7E6'
                            }}
                        "
                    >
                        {{
                            $pegawai->is_verified
                                ? 'Terverifikasi'
                                : 'Belum Terverifikasi'
                        }}
                    </span>
                </aside>

                <div class="fmu-grid fmu-grid-2">

                    @foreach([
                        [
                            'NIP',
                            $pegawai->nip,
                            'fa-id-card'
                        ],
                        [
                            'Jabatan',
                            $pegawai->jabatan,
                            'fa-briefcase'
                        ],
                        [
                            'Email',
                            strtolower((string) ($pegawai->email ?: '-')),
                            'fa-envelope'
                        ],
                        [
                            'Telepon',
                            $pegawai->no_telp ?: '-',
                            'fa-phone'
                        ],
                    ] as [$label, $value, $icon])

                        <article class="fmu-stat">

                            <span class="fmu-stat-icon">
                                <i
                                    class="
                                        fa-solid
                                        {{ $icon }}
                                    "
                                ></i>
                            </span>

                            <div class="fmu-stat-copy">

                                <small>
                                    {{ $label }}
                                </small>

                                <strong
                                    style="
                                        font-size:15px;
                                        word-break:break-word
                                    "
                                >
                                    {{ $value }}
                                </strong>
                            </div>
                        </article>

                    @endforeach

                    <article
                        class="
                            fmu-card
                            fmu-field-full
                        "
                    >
                        <div class="fmu-card-head">

                            <div class="fmu-card-head-main">

                                <span class="fmu-card-icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </span>

                                <div>
                                    <h3>Alamat</h3>
                                    <p>
                                        Alamat domisili pegawai.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="fmu-card-body">
                            {{
                                $pegawai->alamat
                                ?: 'Alamat belum tersedia.'
                            }}
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="fmu-grid fmu-grid-4 mb-3">

        <article
            class="fmu-stat"
            style="
                --fmu-stat-color:#179B40;
                --fmu-stat-soft:#EAF8EE
            "
        >
            <span class="fmu-stat-icon">
                <i class="fa-solid fa-money-bill-wave"></i>
            </span>

            <div class="fmu-stat-copy">
                <small>Gaji per Hari</small>

                <strong>
                    {{ $rupiah($gajiPerhari) }}
                </strong>
            </div>
        </article>

        <article
            class="fmu-stat"
            style="
                --fmu-stat-color:#2563EB;
                --fmu-stat-soft:#EEF4FF
            "
        >
            <span class="fmu-stat-icon">
                <i class="fa-solid fa-calendar-check"></i>
            </span>

            <div class="fmu-stat-copy">
                <small>Hadir Di-ACC Bulan Ini</small>

                <strong>
                    {{
                        number_format(
                            $jumlahHadirDisetujui
                        )
                    }}
                    hari
                </strong>
            </div>
        </article>

        <article
            class="fmu-stat"
            style="
                --fmu-stat-color:#D97706;
                --fmu-stat-soft:#FFF7E6
            "
        >
            <span class="fmu-stat-icon">
                <i class="fa-solid fa-clock"></i>
            </span>

            <div class="fmu-stat-copy">
                <small>Presensi Menunggu</small>

                <strong>
                    {{
                        number_format(
                            $presensiMenunggu
                        )
                    }}
                </strong>
            </div>
        </article>

        <article
            class="fmu-stat"
            style="
                --fmu-stat-color:#7C3AED;
                --fmu-stat-soft:#F5F0FF
            "
        >
            <span class="fmu-stat-icon">
                <i class="fa-solid fa-wallet"></i>
            </span>

            <div class="fmu-stat-copy">
                <small>Penggajian Terakhir</small>

                @if($penggajianTerakhir)

                    <strong>
                        {{
                            $gajiSudahDibayar
                                ? $rupiah(
                                    $penggajianTerakhir
                                        ->total_gaji
                                )
                                : 'Belum Dibayar'
                        }}
                    </strong>

                @else

                    <strong>
                        Belum tersedia
                    </strong>

                @endif
            </div>
        </article>

    </section>

    <section class="fmu-card">

        <div class="fmu-actions">

            <a
                href="{{ route('admin.pegawai.index') }}"
                class="fmu-btn"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Daftar
            </a>

            <div class="fmu-actions-right">

                <a
                    href="{{
                        route(
                            'admin.pegawai.edit',
                            $pegawai->id
                        )
                    }}"
                    class="fmu-btn fmu-btn-primary"
                >
                    <i class="fa-solid fa-user-pen"></i>
                    Edit Pegawai
                </a>
            </div>
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

        button.addEventListener('click', function () {
            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';
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
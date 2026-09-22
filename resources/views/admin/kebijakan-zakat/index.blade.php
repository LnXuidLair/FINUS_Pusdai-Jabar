@extends('layouts.app')

@section('title', 'Kebijakan Zakat')
@section('hide-page-header', '1')

@php
    $tab = in_array($activeTab, ['barang', 'muzakki', 'amil', 'mustahik'], true)
        ? $activeTab
        : 'barang';

    $dateValue = static fn ($value) => $value
        ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d')
        : '';

    $autoModal = old('form_context')
        ? 'kz-modal-' . old('form_context')
        : match (true) {
            (bool) $editBarang => 'kz-modal-barang',
            (bool) $editHarga => 'kz-modal-harga',
            (bool) $editMuzakki => 'kz-modal-muzakki',
            (bool) $editAmil => 'kz-modal-amil',
            (bool) $editMustahik => 'kz-modal-mustahik',
            default => null,
        };
@endphp

@push('styles')
<style>
    .kz-page {
        --kz-dark: #0E5423;
        --kz-green: #179B40;
        --kz-light: #22BA51;
        --kz-soft: #EAF8EE;
        --kz-soft-2: #F5FBF7;
        --kz-text: #172033;
        --kz-muted: #64748B;
        --kz-border: #E1EAE4;
        position: relative;
        padding-bottom: 32px;
        color: var(--kz-text);
    }
    .kz-page::before {
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
    .kz-heading {
        position: relative;
        z-index: 1;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 22px;
        margin-bottom: 17px;
        padding: 27px 29px;
        border: 0;
        border-radius: 22px;
        background:
            radial-gradient(circle at 91% 7%, rgba(255, 255, 255, .16), transparent 30%),
            linear-gradient(135deg, #0E5423 0%, #179B40 48%, #22BA51 100%);
        color: #fff;
        box-shadow: 0 18px 38px rgba(14, 84, 35, .17), inset 0 1px 0 rgba(255, 255, 255, .13);
    }
    .kz-heading::after {
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
    .kz-heading-main {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }
    .kz-heading-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 58px;
        min-width: 58px;
        height: 58px;
        border: 1px solid rgba(255, 255, 255, .21);
        border-radius: 17px;
        background: rgba(255, 255, 255, .14);
        color: #fff;
        font-size: 22px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .15);
        backdrop-filter: blur(8px);
    }
    .kz-heading h1 {
        margin: 0;
        color: #fff !important;
        font-size: 23px;
        font-weight: 800;
        line-height: 1.3;
        letter-spacing: 0;
    }
    .kz-heading p {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 4px 0 0;
        color: rgba(255, 255, 255, .82) !important;
        font-size: 13px;
        line-height: 1.5;
    }
    .kz-heading p::before {
        content: "";
        width: 7px;
        min-width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #A5FFAE;
        box-shadow: 0 0 0 4px rgba(165, 255, 174, .13);
    }
    .kz-heading-actions {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 9px;
        flex-wrap: wrap;
    }
    .kz-heading-actions .kz-button {
        min-height: 45px;
        padding: 0 17px;
        border-radius: 12px;
        font-size: 13px;
        box-shadow: 0 9px 20px rgba(0, 70, 27, .16);
    }
    .kz-heading-actions .kz-button-primary {
        border-color: rgba(255, 255, 255, .70);
        background: #fff;
        color: var(--kz-dark) !important;
    }
    .kz-heading-actions .kz-button-primary:hover,
    .kz-heading-actions .kz-button-primary:focus-visible {
        background: #F3FFF6;
        color: var(--kz-dark) !important;
        transform: translateY(-2px);
    }
    .kz-heading-actions .kz-button-primary i {
        color: var(--kz-green);
    }
    .kz-heading-actions .kz-button-secondary {
        border-color: rgba(255, 255, 255, .28);
        background: rgba(4, 75, 28, .22);
        color: #fff !important;
        box-shadow: none;
        backdrop-filter: blur(8px);
    }
    .kz-heading-actions .kz-button-secondary:hover,
    .kz-heading-actions .kz-button-secondary:focus-visible {
        border-color: rgba(255, 255, 255, .45);
        background: rgba(255, 255, 255, .18);
        color: #fff !important;
        transform: translateY(-2px);
    }
    .kz-tabs {
        display: flex;
        gap: 6px;
        margin: 0 0 16px;
        padding: 5px;
        overflow-x: auto;
        border: 1px solid var(--kz-border);
        border-radius: 8px;
        background: #fff;
    }
    .kz-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 0 15px;
        border-radius: 6px;
        color: #536273;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none !important;
        white-space: nowrap;
    }
    .kz-tab:hover {
        background: var(--kz-soft-2);
        color: var(--kz-dark);
    }
    .kz-tab.active {
        background: var(--kz-soft);
        color: var(--kz-dark);
        box-shadow: inset 0 0 0 1px rgba(23, 155, 64, .22);
    }
    .kz-alert {
        margin-bottom: 14px;
        padding: 12px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
    }
    .kz-alert-success {
        border: 1px solid #B8DEC2;
        background: #ECF8EF;
        color: #166534;
    }
    .kz-alert-danger {
        border: 1px solid #F0C4C8;
        background: #FFF2F3;
        color: #B42318;
    }
    .kz-layout {
        display: grid;
        grid-template-columns: minmax(300px, 380px) minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }
    .kz-form-panels {
        display: block;
        margin: 0;
    }
    .kz-list-layout {
        grid-template-columns: minmax(0, 1fr);
    }
    .kz-section-gap {
        margin-top: 16px;
    }
    .kz-form-modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        z-index: 2060;
        width: min(700px, calc(100vw - 32px));
        max-height: calc(100vh - 32px);
        overflow: auto;
        transform: translate(-50%, -50%);
        box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
    }
    .kz-form-modal.is-open {
        display: block;
    }
    .kz-form-modal .kz-panel-head {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fff;
    }
    .kz-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 2050;
        background: rgba(15, 23, 42, .56);
        backdrop-filter: blur(2px);
    }
    body.kz-modal-open {
        overflow: hidden;
    }
    .kz-modal-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        min-width: 34px;
        height: 34px;
        padding: 0;
        border: 1px solid #D5E0D8;
        border-radius: 6px;
        background: #fff;
        color: #526171;
        cursor: pointer;
    }
    .kz-modal-close:hover,
    .kz-modal-close:focus-visible {
        border-color: #179B40;
        color: #0E5423;
        outline: none;
    }
    .kz-panel {
        border: 1px solid rgba(14, 84, 35, .09);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 18px 42px rgba(15, 23, 42, .075);
    }
    .kz-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 54px;
        padding: 12px 16px;
        border-bottom: 1px solid #E4EBE6;
    }
    .kz-panel-head h2 {
        margin: 0;
        color: #172033;
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 0;
    }
    .kz-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        padding: 0 8px;
        border-radius: 14px;
        background: #EEF4FF;
        color: #2458A6;
        font-size: 12px;
        font-weight: 800;
    }
    .kz-panel-body {
        padding: 16px;
    }
    .kz-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }
    .kz-field-full {
        grid-column: 1 / -1;
    }
    .kz-field label {
        display: block;
        margin-bottom: 5px;
        color: #344054;
        font-size: 12px;
        font-weight: 700;
    }
    .kz-field input,
    .kz-field select,
    .kz-field textarea {
        width: 100%;
        min-height: 40px;
        padding: 8px 10px;
        border: 1px solid #CBD8CF;
        border-radius: 6px;
        background: #fff;
        color: #172033;
        font-size: 13px;
        outline: none;
    }
    .kz-field textarea {
        min-height: 76px;
        resize: vertical;
    }
    .kz-field input:focus,
    .kz-field select:focus,
    .kz-field textarea:focus {
        border-color: #179B40;
        box-shadow: 0 0 0 3px rgba(23, 155, 64, .11);
    }
    .kz-check-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 18px;
        padding-top: 3px;
    }
    .kz-check {
        display: inline-flex !important;
        align-items: center;
        gap: 8px;
        margin: 0 !important;
        color: #344054;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }
    .kz-check input {
        width: 16px;
        height: 16px;
        min-height: 0;
        margin: 0;
        accent-color: #179B40;
    }
    .kz-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 15px;
        padding-top: 14px;
        border-top: 1px solid #E7EDE9;
    }
    .kz-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 39px;
        padding: 0 13px;
        border: 1px solid transparent;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none !important;
        cursor: pointer;
    }
    .kz-button-primary {
        background: #179B40;
        color: #fff !important;
    }
    .kz-button-primary:hover {
        background: #117D32;
    }
    .kz-button-secondary {
        border-color: #CBD8CF;
        background: #fff;
        color: #344054 !important;
    }
    .kz-button-secondary:hover {
        background: #F5F8F6;
    }
    .kz-table-wrap {
        overflow-x: auto;
    }
    .kz-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        font-size: 12px;
    }
    .kz-table th {
        padding: 11px 12px;
        border-bottom: 1px solid #DCE6DF;
        background: #F7FAF8;
        color: #526171;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .04em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .kz-table td {
        padding: 12px;
        border-bottom: 1px solid #EDF1EE;
        color: #344054;
        vertical-align: top;
    }
    .kz-table tr:last-child td {
        border-bottom: 0;
    }
    .kz-table strong {
        color: #172033;
    }
    .kz-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 7px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }
    .kz-badge-active {
        background: #E8F6EC;
        color: #147034;
    }
    .kz-badge-inactive {
        background: #F1F3F5;
        color: #667085;
    }
    .kz-empty {
        padding: 34px 18px;
        color: #667085;
        text-align: center;
    }
    .kz-empty i {
        display: block;
        margin-bottom: 9px;
        color: #9CB4A3;
        font-size: 24px;
    }
    .kz-nowrap {
        white-space: nowrap;
    }
    @media (max-width: 1100px) {
        .kz-layout {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 640px) {
        .kz-heading {
            align-items: flex-start;
            flex-direction: column;
            padding: 23px 20px;
            border-radius: 18px;
        }
        .kz-heading-actions {
            width: 100%;
            justify-content: flex-start;
        }
        .kz-heading-actions .kz-button {
            flex: 1 1 150px;
        }
        .kz-heading-icon {
            width: 51px;
            min-width: 51px;
            height: 51px;
            border-radius: 15px;
        }
        .kz-heading h1 {
            font-size: 20px;
        }
        .kz-form-grid {
            grid-template-columns: 1fr;
        }
        .kz-field-full {
            grid-column: auto;
        }
        .kz-panel-body {
            padding: 13px;
        }
    }
</style>
@endpush

@push('dark-styles')
<style data-finus-dark-local="admin/kebijakan-zakat/index.blade.php">
html[data-finus-theme="dark"] body .kz-page{--kz-dark:#B9F4C7;--kz-green:#64DD81;--kz-light:#7BE795;--kz-soft:#173620;--kz-soft-2:#14251A;--kz-text:#F1F6F3;--kz-muted:#9EAEA4;--kz-border:#293D31}
html[data-finus-theme="dark"] body .kz-page::before{background:rgba(38,184,79,.035)!important}
html[data-finus-theme="dark"] body .kz-heading{border:1px solid rgba(126,255,135,.10)!important;background:radial-gradient(circle at 91% 7%,rgba(255,255,255,.10),transparent 30%),linear-gradient(135deg,#0B5A27,#14863A 50%,#1DB04B)!important;box-shadow:0 18px 38px rgba(0,0,0,.24)!important}
html[data-finus-theme="dark"] body .kz-heading h1{color:#FFF!important}
html[data-finus-theme="dark"] body .kz-heading p{color:rgba(255,255,255,.82)!important}
html[data-finus-theme="dark"] body .kz-heading-actions .kz-button-primary{border-color:rgba(255,255,255,.70)!important;background:#FFF!important;color:#0E5423!important}
html[data-finus-theme="dark"] body .kz-heading-actions .kz-button-secondary{border-color:rgba(255,255,255,.28)!important;background:rgba(4,75,28,.28)!important;color:#FFF!important}
html[data-finus-theme="dark"] body .kz-tabs,html[data-finus-theme="dark"] body .kz-panel{border-color:#293D31!important;background:#111A15!important;box-shadow:0 18px 42px rgba(0,0,0,.22)!important}
html[data-finus-theme="dark"] body .kz-tab{color:#AFC0B5!important}
html[data-finus-theme="dark"] body .kz-tab:hover{background:#16271D!important;color:#D8EEE0!important}
html[data-finus-theme="dark"] body .kz-tab.active{background:#1B3C25!important;color:#CFF7D8!important;box-shadow:inset 0 0 0 1px rgba(100,221,129,.20)!important}
html[data-finus-theme="dark"] body .kz-panel-head{border-color:#293D31!important;background:linear-gradient(180deg,#17251D,#121D17)!important}
html[data-finus-theme="dark"] body .kz-panel-head h2,html[data-finus-theme="dark"] body .kz-table strong,html[data-finus-theme="dark"] body .kz-field label{color:#F1F6F3!important}
html[data-finus-theme="dark"] body .kz-field input,html[data-finus-theme="dark"] body .kz-field select,html[data-finus-theme="dark"] body .kz-field textarea{border-color:#31493A!important;background:#0C1610!important;color:#F1F6F3!important}
html[data-finus-theme="dark"] body .kz-table th{border-color:#293D31!important;background:#17261D!important;color:#D2E1D6!important}
html[data-finus-theme="dark"] body .kz-table td{border-color:#24372B!important;color:#DCE7E0!important}
html[data-finus-theme="dark"] body .kz-button-secondary,html[data-finus-theme="dark"] body .kz-modal-close{border-color:#30493A!important;background:#14211A!important;color:#DCE7E0!important}
</style>
@endpush

@section('content')
@include('layouts.partials.finus-ui')
<div class="kz-page">
    <header class="kz-heading">
        <div class="kz-heading-main">
            <span class="kz-heading-icon"><i class="fa-solid fa-scale-balanced"></i></span>
            <div>
                <h1>Kebijakan Zakat</h1>
                <p>Barang, harga, perhitungan muzakki, hak amil, dan penyaluran mustahik</p>
            </div>
        </div>
        <div class="kz-heading-actions" aria-label="Aksi kebijakan zakat">
            @if ($tab === 'barang')
                <button class="kz-button kz-button-secondary" type="button" data-kz-modal-open="kz-modal-harga">
                    <i class="fa-solid fa-tag"></i>
                    {{ $editHarga ? 'Ubah Harga' : 'Tambah Harga' }}
                </button>
                <button class="kz-button kz-button-primary" type="button" data-kz-modal-open="kz-modal-barang">
                    <i class="fa-solid fa-plus"></i>
                    {{ $editBarang ? 'Ubah Barang' : 'Tambah Barang' }}
                </button>
            @elseif ($tab === 'muzakki')
                <button class="kz-button kz-button-primary" type="button" data-kz-modal-open="kz-modal-muzakki">
                    <i class="fa-solid fa-plus"></i>
                    {{ $editMuzakki ? 'Ubah Aturan' : 'Tambah Aturan' }}
                </button>
            @elseif ($tab === 'amil')
                <button class="kz-button kz-button-primary" type="button" data-kz-modal-open="kz-modal-amil">
                    <i class="fa-solid fa-plus"></i>
                    {{ $editAmil ? 'Ubah Kebijakan' : 'Tambah Kebijakan' }}
                </button>
            @else
                <button class="kz-button kz-button-primary" type="button" data-kz-modal-open="kz-modal-mustahik">
                    <i class="fa-solid fa-plus"></i>
                    {{ $editMustahik ? 'Ubah Kebijakan' : 'Tambah Kebijakan' }}
                </button>
            @endif
        </div>
    </header>

    @if (session('success'))
        <div class="kz-alert kz-alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="kz-alert kz-alert-danger">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <nav class="kz-tabs" aria-label="Bagian kebijakan zakat">
        <a class="kz-tab {{ $tab === 'barang' ? 'active' : '' }}"
           href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'barang']) }}">
            <i class="fa-solid fa-boxes-stacked"></i>
            Barang & Harga
        </a>
        <a class="kz-tab {{ $tab === 'muzakki' ? 'active' : '' }}"
           href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'muzakki']) }}">
            <i class="fa-solid fa-hand-holding-heart"></i>
            Muzakki
        </a>
        <a class="kz-tab {{ $tab === 'amil' ? 'active' : '' }}"
           href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'amil']) }}">
            <i class="fa-solid fa-user-tie"></i>
            Hak Amil
        </a>
        <a class="kz-tab {{ $tab === 'mustahik' ? 'active' : '' }}"
           href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'mustahik']) }}">
            <i class="fa-solid fa-people-group"></i>
            Mustahik
        </a>
    </nav>

    @if ($tab === 'barang')
        <div class="kz-form-panels">
            @php $barangForm = $editBarang; @endphp
            <section id="kz-modal-barang" class="kz-panel kz-form-modal" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
                <div class="kz-panel-head">
                    <h2>{{ $barangForm ? 'Ubah Barang Zakat' : 'Tambah Barang Zakat' }}</h2>
                </div>
                <div class="kz-panel-body">
                    <form method="POST" action="{{ $barangForm
                        ? route('admin.kebijakan-zakat.barang.update', $barangForm)
                        : route('admin.kebijakan-zakat.barang.store') }}">
                        @csrf
                        <input type="hidden" name="form_context" value="barang">
                        @if ($barangForm) @method('PATCH') @endif
                        <div class="kz-form-grid">
                            <div class="kz-field">
                                <label for="kode_barang">Kode</label>
                                <input id="kode_barang" name="kode" type="text" maxlength="30" required
                                       value="{{ old('kode', $barangForm?->kode) }}">
                            </div>
                            <div class="kz-field">
                                <label for="nama_barang">Nama barang</label>
                                <input id="nama_barang" name="nama" type="text" maxlength="100" required
                                       value="{{ old('nama', $barangForm?->nama) }}">
                            </div>
                            <div class="kz-field">
                                <label for="kategori_barang">Kategori</label>
                                <select id="kategori_barang" name="kategori" required>
                                    @foreach ($kategoriBarangLabels as $value => $label)
                                        <option value="{{ $value }}" {{ old('kategori', $barangForm?->kategori ?? 'makanan_pokok') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="kz-field">
                                <label for="satuan_dasar">Satuan dasar</label>
                                <select id="satuan_dasar" name="satuan_dasar" required>
                                    @foreach ($satuanBarangLabels as $value => $label)
                                        <option value="{{ $value }}" {{ old('satuan_dasar', $barangForm?->satuan_dasar ?? 'kg') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="metode_penilaian">Metode penilaian</label>
                                <select id="metode_penilaian" name="metode_penilaian" required>
                                    @foreach ($metodePenilaianLabels as $value => $label)
                                        <option value="{{ $value }}" {{ old('metode_penilaian', $barangForm?->metode_penilaian ?? 'harga_pasar') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="coa_persediaan_id">Akun persediaan</label>
                                <select id="coa_persediaan_id" name="coa_persediaan_id">
                                    <option value="">Belum dipetakan</option>
                                    @foreach ($akunPersediaan as $akun)
                                        <option value="{{ $akun->id }}" {{ (string) old('coa_persediaan_id', $barangForm?->coa_persediaan_id) === (string) $akun->id ? 'selected' : '' }}>
                                            {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="kz-field kz-field-full kz-check-row">
                                <label class="kz-check">
                                    <input type="hidden" name="aktif" value="0">
                                    <input type="checkbox" name="aktif" value="1" {{ old('aktif', $barangForm?->aktif ?? true) ? 'checked' : '' }}>
                                    Aktif
                                </label>
                            </div>
                        </div>
                        <div class="kz-actions">
                            <button class="kz-button kz-button-primary" type="submit">
                                <i class="fa-solid fa-floppy-disk"></i>
                                {{ $barangForm ? 'Simpan Perubahan' : 'Tambah Barang' }}
                            </button>
                            @if ($barangForm)
                                <a class="kz-button kz-button-secondary" href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'barang']) }}">
                                    <i class="fa-solid fa-xmark"></i> Batal
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </section>

            @php $hargaForm = $editHarga; @endphp
            <section id="kz-modal-harga" class="kz-panel kz-form-modal" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
                <div class="kz-panel-head">
                    <h2>{{ $hargaForm ? 'Ubah Harga Barang' : 'Tambah Harga Barang' }}</h2>
                </div>
                <div class="kz-panel-body">
                    <form method="POST" action="{{ $hargaForm
                        ? route('admin.kebijakan-zakat.harga-barang.update', $hargaForm)
                        : route('admin.kebijakan-zakat.harga-barang.store') }}">
                        @csrf
                        <input type="hidden" name="form_context" value="harga">
                        @if ($hargaForm) @method('PATCH') @endif
                        <div class="kz-form-grid">
                            <div class="kz-field kz-field-full">
                                <label for="barang_zakat_id">Barang</label>
                                <select id="barang_zakat_id" name="barang_zakat_id" required>
                                    <option value="">Pilih barang</option>
                                    @foreach ($barangZakat as $barang)
                                        <option value="{{ $barang->id }}" {{ (string) old('barang_zakat_id', $hargaForm?->barang_zakat_id) === (string) $barang->id ? 'selected' : '' }}>
                                            {{ $barang->nama }} ({{ $barang->satuan_dasar }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="kz-field">
                                <label for="wilayah_harga">Wilayah</label>
                                <input id="wilayah_harga" name="wilayah" type="text" maxlength="100" required
                                       value="{{ old('wilayah', $hargaForm?->wilayah ?? 'Jawa Barat') }}">
                            </div>
                            <div class="kz-field">
                                <label for="harga_per_satuan">Harga per satuan (Rp)</label>
                                <input id="harga_per_satuan" name="harga_per_satuan" type="number" min="1" required
                                       value="{{ old('harga_per_satuan', $hargaForm?->harga_per_satuan) }}">
                            </div>
                            <div class="kz-field">
                                <label for="harga_mulai">Mulai berlaku</label>
                                <input id="harga_mulai" name="berlaku_mulai" type="date" required
                                       value="{{ old('berlaku_mulai', $dateValue($hargaForm?->berlaku_mulai)) }}">
                            </div>
                            <div class="kz-field">
                                <label for="harga_sampai">Selesai berlaku</label>
                                <input id="harga_sampai" name="berlaku_sampai" type="date"
                                       value="{{ old('berlaku_sampai', $dateValue($hargaForm?->berlaku_sampai)) }}">
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="sumber_harga">Sumber harga</label>
                                <input id="sumber_harga" name="sumber_harga" type="text" maxlength="255" required
                                       value="{{ old('sumber_harga', $hargaForm?->sumber_harga) }}">
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="status_harga">Status</label>
                                <select id="status_harga" name="status" required>
                                    @foreach ($statusHargaLabels as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $hargaForm?->status ?? 'draft') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="catatan_harga">Catatan</label>
                                <textarea id="catatan_harga" name="catatan">{{ old('catatan', $hargaForm?->catatan) }}</textarea>
                            </div>
                        </div>
                        <div class="kz-actions">
                            <button class="kz-button kz-button-primary" type="submit">
                                <i class="fa-solid fa-floppy-disk"></i>
                                {{ $hargaForm ? 'Simpan Perubahan' : 'Tambah Harga' }}
                            </button>
                            @if ($hargaForm)
                                <a class="kz-button kz-button-secondary" href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'barang']) }}">
                                    <i class="fa-solid fa-xmark"></i> Batal
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <section class="kz-panel">
            <div class="kz-panel-head">
                <h2>Master Barang Zakat</h2>
                <span class="kz-count">{{ $barangZakat->count() }}</span>
            </div>
            <div class="kz-table-wrap">
                <table class="kz-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Barang</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Harga Terbaru</th>
                            <th>Akun Persediaan</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangZakat as $item)
                            <tr>
                                <td><strong>{{ $item->kode }}</strong></td>
                                <td>{{ $item->nama }}<br><small>{{ $metodePenilaianLabels[$item->metode_penilaian] ?? $item->metode_penilaian }}</small></td>
                                <td>{{ $kategoriBarangLabels[$item->kategori] ?? $item->kategori }}</td>
                                <td>{{ $satuanBarangLabels[$item->satuan_dasar] ?? $item->satuan_dasar }}</td>
                                <td>
                                    @if ($item->hargaTerbaru)
                                        <strong>Rp{{ number_format($item->hargaTerbaru->harga_per_satuan, 0, ',', '.') }}</strong><br>
                                        <small>{{ $item->hargaTerbaru->wilayah }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->coaPersediaan ? $item->coaPersediaan->kode_akun . ' - ' . $item->coaPersediaan->nama_akun : '-' }}</td>
                                <td><span class="kz-badge {{ $item->aktif ? 'kz-badge-active' : 'kz-badge-inactive' }}">{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td>
                                    <a class="kz-button kz-button-secondary" title="Ubah barang"
                                       href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'barang', 'edit_barang' => $item->id]) }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="kz-empty"><i class="fa-regular fa-folder-open"></i>Belum ada barang zakat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="kz-panel kz-section-gap">
            <div class="kz-panel-head">
                <h2>Riwayat Harga Barang</h2>
                <span class="kz-count">{{ $hargaBarang->count() }}</span>
            </div>
            <div class="kz-table-wrap">
                <table class="kz-table">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Wilayah</th>
                            <th>Harga</th>
                            <th>Sumber</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($hargaBarang as $item)
                            <tr>
                                <td><strong>{{ $item->barang->nama }}</strong><br><small>per {{ $item->barang->satuan_dasar }}</small></td>
                                <td>{{ $item->wilayah }}</td>
                                <td><strong>Rp{{ number_format($item->harga_per_satuan, 0, ',', '.') }}</strong></td>
                                <td>{{ $item->sumber_harga }}</td>
                                <td class="kz-nowrap">{{ $item->berlaku_mulai->format('d/m/Y') }}<br><small>{{ $item->berlaku_sampai?->format('d/m/Y') ?? 'Tanpa batas' }}</small></td>
                                <td><span class="kz-badge {{ $item->status === 'disetujui' ? 'kz-badge-active' : 'kz-badge-inactive' }}">{{ $statusHargaLabels[$item->status] ?? $item->status }}</span></td>
                                <td>
                                    <a class="kz-button kz-button-secondary" title="Ubah harga"
                                       href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'barang', 'edit_harga' => $item->id]) }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="kz-empty"><i class="fa-regular fa-folder-open"></i>Belum ada harga barang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @elseif ($tab === 'muzakki')
        @php $form = $editMuzakki; @endphp
        <div class="kz-layout kz-list-layout">
            <section id="kz-modal-muzakki" class="kz-panel kz-form-modal" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
                <div class="kz-panel-head">
                    <h2>{{ $form ? 'Ubah Aturan Muzakki' : 'Tambah Aturan Muzakki' }}</h2>
                </div>
                <div class="kz-panel-body">
                    <form method="POST" action="{{ $form
                        ? route('admin.kebijakan-zakat.muzakki.update', $form)
                        : route('admin.kebijakan-zakat.muzakki.store') }}">
                        @csrf
                        <input type="hidden" name="form_context" value="muzakki">
                        @if ($form) @method('PATCH') @endif

                        <div class="kz-form-grid">
                            <div class="kz-field">
                                <label for="tahun">Tahun</label>
                                <input id="tahun" name="tahun" type="number" min="2000" max="2100" required
                                       value="{{ old('tahun', $form?->tahun ?? now()->year) }}">
                            </div>
                            <div class="kz-field">
                                <label for="persentase_zakat">Kadar zakat (%)</label>
                                <input id="persentase_zakat" name="persentase_zakat" type="number" min="0.01" max="100" step="0.01" required
                                       value="{{ old('persentase_zakat', $form?->persentase_zakat ?? '2.50') }}">
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="nisab_penghasilan_tahunan">Nisab penghasilan tahunan (Rp)</label>
                                <input id="nisab_penghasilan_tahunan" name="nisab_penghasilan_tahunan" type="number" min="0" required
                                       value="{{ old('nisab_penghasilan_tahunan', $form?->nisab_penghasilan_tahunan) }}">
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="nisab_penghasilan_bulanan">Nisab penghasilan bulanan (Rp)</label>
                                <input id="nisab_penghasilan_bulanan" name="nisab_penghasilan_bulanan" type="number" min="0" required
                                       value="{{ old('nisab_penghasilan_bulanan', $form?->nisab_penghasilan_bulanan) }}">
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="nisab_maal">Nisab zakat maal (Rp)</label>
                                <input id="nisab_maal" name="nisab_maal" type="number" min="0" required
                                       value="{{ old('nisab_maal', $form?->nisab_maal) }}">
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="zakat_fitrah_per_jiwa">Zakat fitrah per jiwa (Rp)</label>
                                <input id="zakat_fitrah_per_jiwa" name="zakat_fitrah_per_jiwa" type="number" min="0" required
                                       value="{{ old('zakat_fitrah_per_jiwa', $form?->zakat_fitrah_per_jiwa) }}">
                            </div>
                            <div class="kz-field">
                                <label for="beras_fitrah_kg">Beras per jiwa (kg)</label>
                                <input id="beras_fitrah_kg" name="beras_fitrah_kg" type="number" min="0.01" max="100" step="0.01" required
                                       value="{{ old('beras_fitrah_kg', $form?->beras_fitrah_kg ?? '2.50') }}">
                            </div>
                            <div class="kz-field">
                                <label for="beras_fitrah_liter">Beras per jiwa (liter)</label>
                                <input id="beras_fitrah_liter" name="beras_fitrah_liter" type="number" min="0.01" max="100" step="0.01" required
                                       value="{{ old('beras_fitrah_liter', $form?->beras_fitrah_liter ?? '3.50') }}">
                            </div>
                            <div class="kz-field">
                                <label for="muzakki_mulai">Mulai berlaku</label>
                                <input id="muzakki_mulai" name="berlaku_mulai" type="date" required
                                       value="{{ old('berlaku_mulai', $dateValue($form?->berlaku_mulai)) }}">
                            </div>
                            <div class="kz-field">
                                <label for="muzakki_sampai">Selesai berlaku</label>
                                <input id="muzakki_sampai" name="berlaku_sampai" type="date"
                                       value="{{ old('berlaku_sampai', $dateValue($form?->berlaku_sampai)) }}">
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="sumber">Dasar aturan</label>
                                <textarea id="sumber" name="sumber">{{ old('sumber', $form?->sumber) }}</textarea>
                            </div>
                            <div class="kz-field kz-field-full kz-check-row">
                                <label class="kz-check">
                                    <input type="hidden" name="aktif" value="0">
                                    <input type="checkbox" name="aktif" value="1"
                                           {{ old('aktif', $form?->aktif ?? true) ? 'checked' : '' }}>
                                    Aktif
                                </label>
                            </div>
                        </div>
                        <div class="kz-actions">
                            <button class="kz-button kz-button-primary" type="submit">
                                <i class="fa-solid fa-floppy-disk"></i>
                                {{ $form ? 'Simpan Perubahan' : 'Tambah Aturan' }}
                            </button>
                            @if ($form)
                                <a class="kz-button kz-button-secondary" href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'muzakki']) }}">
                                    <i class="fa-solid fa-xmark"></i> Batal
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </section>

            <section class="kz-panel">
                <div class="kz-panel-head">
                    <h2>Riwayat Aturan Muzakki</h2>
                    <span class="kz-count">{{ $pengaturanMuzakki->count() }}</span>
                </div>
                <div class="kz-table-wrap">
                    <table class="kz-table">
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th>Nisab Penghasilan</th>
                                <th>Fitrah</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengaturanMuzakki as $item)
                                <tr>
                                    <td><strong>{{ $item->tahun }}</strong><br>{{ number_format((float) $item->persentase_zakat, 2, ',', '.') }}%</td>
                                    <td>Rp{{ number_format($item->nisab_penghasilan_tahunan, 0, ',', '.') }}<br><small>Rp{{ number_format($item->nisab_penghasilan_bulanan, 0, ',', '.') }}/bulan</small></td>
                                    <td>Rp{{ number_format($item->zakat_fitrah_per_jiwa, 0, ',', '.') }}<br><small>{{ number_format((float) $item->beras_fitrah_kg, 2, ',', '.') }} kg/jiwa</small></td>
                                    <td class="kz-nowrap">{{ $item->berlaku_mulai->format('d/m/Y') }}<br><small>{{ $item->berlaku_sampai?->format('d/m/Y') ?? 'Tanpa batas' }}</small></td>
                                    <td><span class="kz-badge {{ $item->aktif ? 'kz-badge-active' : 'kz-badge-inactive' }}">{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                                    <td>
                                        <a class="kz-button kz-button-secondary" title="Ubah aturan"
                                           href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'muzakki', 'edit_muzakki' => $item->id]) }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="kz-empty"><i class="fa-regular fa-folder-open"></i>Belum ada aturan muzakki.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    @elseif ($tab === 'amil')
        @php $form = $editAmil; @endphp
        <div class="kz-layout kz-list-layout">
            <section id="kz-modal-amil" class="kz-panel kz-form-modal" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
                <div class="kz-panel-head">
                    <h2>{{ $form ? 'Ubah Kebijakan Amil' : 'Tambah Kebijakan Amil' }}</h2>
                </div>
                <div class="kz-panel-body">
                    <form method="POST" action="{{ $form
                        ? route('admin.kebijakan-zakat.amil.update', $form)
                        : route('admin.kebijakan-zakat.amil.store') }}">
                        @csrf
                        <input type="hidden" name="form_context" value="amil">
                        @if ($form) @method('PATCH') @endif
                        <div class="kz-form-grid">
                            <div class="kz-field kz-field-full">
                                <label for="jenis_sumber">Sumber dana</label>
                                <select id="jenis_sumber" name="jenis_sumber" required>
                                    @foreach ($jenisSumberLabels as $value => $label)
                                        <option value="{{ $value }}" {{ old('jenis_sumber', $form?->jenis_sumber ?? 'zakat') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="persentase_amil">Persentase hak amil (%)</label>
                                <input id="persentase_amil" name="persentase_amil" type="number" min="0" max="100" step="0.01" required
                                       value="{{ old('persentase_amil', $form?->persentase_amil ?? '12.50') }}">
                            </div>
                            <div class="kz-field">
                                <label for="amil_mulai">Mulai berlaku</label>
                                <input id="amil_mulai" name="berlaku_mulai" type="date" required
                                       value="{{ old('berlaku_mulai', $dateValue($form?->berlaku_mulai)) }}">
                            </div>
                            <div class="kz-field">
                                <label for="amil_sampai">Selesai berlaku</label>
                                <input id="amil_sampai" name="berlaku_sampai" type="date"
                                       value="{{ old('berlaku_sampai', $dateValue($form?->berlaku_sampai)) }}">
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="dasar_amil">Dasar aturan</label>
                                <input id="dasar_amil" name="dasar_aturan" type="text" maxlength="255"
                                       value="{{ old('dasar_aturan', $form?->dasar_aturan) }}">
                            </div>
                            <div class="kz-field kz-field-full kz-check-row">
                                <label class="kz-check">
                                    <input type="hidden" name="potong_infak_terikat" value="0">
                                    <input type="checkbox" name="potong_infak_terikat" value="1"
                                           {{ old('potong_infak_terikat', $form?->potong_infak_terikat ?? false) ? 'checked' : '' }}>
                                    Berlaku untuk infak terikat
                                </label>
                                <label class="kz-check">
                                    <input type="hidden" name="aktif" value="0">
                                    <input type="checkbox" name="aktif" value="1"
                                           {{ old('aktif', $form?->aktif ?? true) ? 'checked' : '' }}>
                                    Aktif
                                </label>
                            </div>
                        </div>
                        <div class="kz-actions">
                            <button class="kz-button kz-button-primary" type="submit">
                                <i class="fa-solid fa-floppy-disk"></i>
                                {{ $form ? 'Simpan Perubahan' : 'Tambah Kebijakan' }}
                            </button>
                            @if ($form)
                                <a class="kz-button kz-button-secondary" href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'amil']) }}">
                                    <i class="fa-solid fa-xmark"></i> Batal
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </section>

            <section class="kz-panel">
                <div class="kz-panel-head">
                    <h2>Riwayat Kebijakan Amil</h2>
                    <span class="kz-count">{{ $kebijakanAmil->count() }}</span>
                </div>
                <div class="kz-table-wrap">
                    <table class="kz-table">
                        <thead>
                            <tr>
                                <th>Sumber</th>
                                <th>Hak Amil</th>
                                <th>Dasar Aturan</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kebijakanAmil as $item)
                                <tr>
                                    <td><strong>{{ $jenisSumberLabels[$item->jenis_sumber] ?? $item->jenis_sumber }}</strong></td>
                                    <td>{{ number_format((float) $item->persentase_amil, 2, ',', '.') }}%</td>
                                    <td>{{ $item->dasar_aturan ?: '-' }}</td>
                                    <td class="kz-nowrap">{{ $item->berlaku_mulai->format('d/m/Y') }}<br><small>{{ $item->berlaku_sampai?->format('d/m/Y') ?? 'Tanpa batas' }}</small></td>
                                    <td><span class="kz-badge {{ $item->aktif ? 'kz-badge-active' : 'kz-badge-inactive' }}">{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                                    <td>
                                        <a class="kz-button kz-button-secondary" title="Ubah kebijakan"
                                           href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'amil', 'edit_amil' => $item->id]) }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="kz-empty"><i class="fa-regular fa-folder-open"></i>Belum ada kebijakan amil.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    @else
        @php $form = $editMustahik; @endphp
        <div class="kz-layout kz-list-layout">
            <section id="kz-modal-mustahik" class="kz-panel kz-form-modal" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">
                <div class="kz-panel-head">
                    <h2>{{ $form ? 'Ubah Kebijakan Mustahik' : 'Tambah Kebijakan Mustahik' }}</h2>
                </div>
                <div class="kz-panel-body">
                    <form method="POST" action="{{ $form
                        ? route('admin.kebijakan-zakat.mustahik.update', $form)
                        : route('admin.kebijakan-zakat.mustahik.store') }}">
                        @csrf
                        <input type="hidden" name="form_context" value="mustahik">
                        @if ($form) @method('PATCH') @endif
                        <div class="kz-form-grid">
                            <div class="kz-field">
                                <label for="asnaf">Asnaf</label>
                                <select id="asnaf" name="asnaf" required>
                                    @foreach ($asnafLabels as $value => $label)
                                        <option value="{{ $value }}" {{ old('asnaf', $form?->asnaf) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="kz-field">
                                <label for="prioritas">Urutan prioritas</label>
                                <input id="prioritas" name="prioritas" type="number" min="1" max="8" required
                                       value="{{ old('prioritas', $form?->prioritas ?? 1) }}">
                            </div>
                            <div class="kz-field">
                                <label for="target_persentase">Target alokasi (%)</label>
                                <input id="target_persentase" name="target_persentase" type="number" min="0" max="100" step="0.01"
                                       value="{{ old('target_persentase', $form?->target_persentase) }}">
                            </div>
                            <div class="kz-field">
                                <label for="batas_bantuan">Batas per penerima (Rp)</label>
                                <input id="batas_bantuan" name="batas_bantuan" type="number" min="0"
                                       value="{{ old('batas_bantuan', $form?->batas_bantuan) }}">
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="bentuk_penyaluran">Bentuk penyaluran</label>
                                <select id="bentuk_penyaluran" name="bentuk_penyaluran" required>
                                    @foreach ($bentukPenyaluranLabels as $value => $label)
                                        <option value="{{ $value }}" {{ old('bentuk_penyaluran', $form?->bentuk_penyaluran ?? 'keduanya') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="kriteria">Kriteria kelayakan</label>
                                <textarea id="kriteria" name="kriteria" required>{{ old('kriteria', $form?->kriteria) }}</textarea>
                            </div>
                            <div class="kz-field kz-field-full">
                                <label for="dasar_mustahik">Dasar aturan</label>
                                <input id="dasar_mustahik" name="dasar_aturan" type="text" maxlength="255"
                                       value="{{ old('dasar_aturan', $form?->dasar_aturan) }}">
                            </div>
                            <div class="kz-field">
                                <label for="mustahik_mulai">Mulai berlaku</label>
                                <input id="mustahik_mulai" name="berlaku_mulai" type="date" required
                                       value="{{ old('berlaku_mulai', $dateValue($form?->berlaku_mulai)) }}">
                            </div>
                            <div class="kz-field">
                                <label for="mustahik_sampai">Selesai berlaku</label>
                                <input id="mustahik_sampai" name="berlaku_sampai" type="date"
                                       value="{{ old('berlaku_sampai', $dateValue($form?->berlaku_sampai)) }}">
                            </div>
                            <div class="kz-field kz-field-full kz-check-row">
                                <label class="kz-check">
                                    <input type="hidden" name="aktif" value="0">
                                    <input type="checkbox" name="aktif" value="1"
                                           {{ old('aktif', $form?->aktif ?? true) ? 'checked' : '' }}>
                                    Aktif
                                </label>
                            </div>
                        </div>
                        <div class="kz-actions">
                            <button class="kz-button kz-button-primary" type="submit">
                                <i class="fa-solid fa-floppy-disk"></i>
                                {{ $form ? 'Simpan Perubahan' : 'Tambah Kebijakan' }}
                            </button>
                            @if ($form)
                                <a class="kz-button kz-button-secondary" href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'mustahik']) }}">
                                    <i class="fa-solid fa-xmark"></i> Batal
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </section>

            <section class="kz-panel">
                <div class="kz-panel-head">
                    <h2>Riwayat Kebijakan Mustahik</h2>
                    <span class="kz-count">{{ $kebijakanMustahik->count() }}</span>
                </div>
                <div class="kz-table-wrap">
                    <table class="kz-table">
                        <thead>
                            <tr>
                                <th>Asnaf</th>
                                <th>Prioritas</th>
                                <th>Target</th>
                                <th>Batas Bantuan</th>
                                <th>Bentuk</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kebijakanMustahik as $item)
                                <tr>
                                    <td><strong>{{ $asnafLabels[$item->asnaf] ?? $item->asnaf }}</strong><br><small>{{ \Illuminate\Support\Str::limit($item->kriteria, 52) }}</small></td>
                                    <td>{{ $item->prioritas }}</td>
                                    <td>{{ $item->target_persentase !== null ? number_format((float) $item->target_persentase, 2, ',', '.') . '%' : '-' }}</td>
                                    <td>{{ $item->batas_bantuan !== null ? 'Rp' . number_format($item->batas_bantuan, 0, ',', '.') : '-' }}</td>
                                    <td>{{ $bentukPenyaluranLabels[$item->bentuk_penyaluran] ?? $item->bentuk_penyaluran }}</td>
                                    <td class="kz-nowrap">{{ $item->berlaku_mulai->format('d/m/Y') }}<br><small>{{ $item->berlaku_sampai?->format('d/m/Y') ?? 'Tanpa batas' }}</small></td>
                                    <td><span class="kz-badge {{ $item->aktif ? 'kz-badge-active' : 'kz-badge-inactive' }}">{{ $item->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                                    <td>
                                        <a class="kz-button kz-button-secondary" title="Ubah kebijakan"
                                           href="{{ route('admin.kebijakan-zakat.index', ['tab' => 'mustahik', 'edit_mustahik' => $item->id]) }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="kz-empty"><i class="fa-regular fa-folder-open"></i>Belum ada kebijakan mustahik.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(() => {
    const modals = Array.from(document.querySelectorAll('.kz-form-modal'));

    if (!modals.length) {
        return;
    }

    const backdrop = document.createElement('div');
    backdrop.className = 'kz-modal-backdrop';
    backdrop.hidden = true;
    document.body.appendChild(backdrop);

    let activeModal = null;
    let triggerElement = null;

    const closeModal = () => {
        if (!activeModal) {
            return;
        }

        activeModal.classList.remove('is-open');
        activeModal.setAttribute('aria-hidden', 'true');
        backdrop.hidden = true;
        document.body.classList.remove('kz-modal-open');
        activeModal = null;

        if (triggerElement) {
            triggerElement.focus();
            triggerElement = null;
        }
    };

    const openModal = (id, trigger = null) => {
        const modal = document.getElementById(id);

        if (!modal) {
            return;
        }

        closeModal();
        activeModal = modal;
        triggerElement = trigger;
        backdrop.hidden = false;
        document.body.classList.add('kz-modal-open');
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');

        window.setTimeout(() => {
            const focusTarget = modal.querySelector(
                'input:not([type="hidden"]), select, textarea, button'
            );
            (focusTarget || modal).focus();
        }, 30);
    };

    modals.forEach(modal => {
        const header = modal.querySelector('.kz-panel-head');

        if (!header) {
            return;
        }

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'kz-modal-close';
        closeButton.setAttribute('data-kz-modal-close', '');
        closeButton.setAttribute('aria-label', 'Tutup form');
        closeButton.setAttribute('title', 'Tutup');
        closeButton.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
        header.appendChild(closeButton);
    });

    document.addEventListener('click', event => {
        const openButton = event.target.closest('[data-kz-modal-open]');

        if (openButton) {
            openModal(openButton.dataset.kzModalOpen, openButton);
            return;
        }

        if (event.target.closest('[data-kz-modal-close]')) {
            closeModal();
        }
    });

    backdrop.addEventListener('click', closeModal);

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    const autoModal = @json($autoModal);

    if (autoModal) {
        openModal(autoModal);
    }
})();
</script>
@endpush

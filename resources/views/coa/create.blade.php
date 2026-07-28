@extends('layouts.app')

@section('title', 'Tambah Akun COA')
@section('hide-page-header', '1')

@include('layouts.partials.finus-ui')

@section('content')
<div class="fr-page">
    <section class="fr-hero fr-reveal">
        <div class="fr-hero-main">
            <span class="fr-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-folder-plus"></i>
            </span>
            <div>
                <h1 class="fr-hero-title">Tambah Akun Baru</h1>
                <p class="fr-hero-subtitle">
                    Tambahkan kode akun ke Chart of Accounts untuk mendukung pencatatan transaksi FINUS.
                </p>
            </div>
        </div>

        <div class="fr-hero-actions">
            <a href="{{ route('admin.coa.index') }}" class="fr-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke COA
            </a>
        </div>
    </section>

    @if($errors->any())
        <div class="fr-alert fr-alert-danger fr-reveal">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Data belum dapat disimpan.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <section class="fr-grid-main-aside">
        <article class="fr-card fr-reveal">
            <header class="fr-card-head">
                <div class="fr-card-title-row">
                    <span class="fr-card-icon"><i class="fa-solid fa-pen-to-square"></i></span>
                    <div>
                        <h2 class="fr-card-title">Form Data Akun</h2>
                        <p class="fr-card-subtitle">Kolom bertanda bintang wajib diisi.</p>
                    </div>
                </div>
            </header>

            <div class="fr-card-body">
                <form method="POST" action="{{ route('admin.coa.store') }}" novalidate>
                    @csrf

                    <div class="fr-form-grid">
                        <div>
                            <label for="kode_akun" class="fr-label">
                                <span>Kode Akun <span class="fr-required">*</span></span>
                            </label>

                            <div class="fr-input-wrap">
                                <span class="fr-input-icon"><i class="fa-solid fa-hashtag"></i></span>
                                <input
                                    type="text"
                                    id="kode_akun"
                                    name="kode_akun"
                                    value="{{ old('kode_akun') }}"
                                    class="fr-input @error('kode_akun') is-invalid @enderror"
                                    placeholder="Contoh: 1101"
                                    inputmode="numeric"
                                    autocomplete="off"
                                    autofocus
                                    required
                                >
                            </div>

                            <p class="fr-help">Gunakan kode yang belum pernah dipakai pada akun lain.</p>
                            @error('kode_akun')
                                <p class="fr-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="header_akun" class="fr-label">
                                <span>Kelompok Akun <span class="fr-required">*</span></span>
                            </label>

                            <div class="fr-input-wrap">
                                <span class="fr-input-icon"><i class="fa-solid fa-layer-group"></i></span>
                                <select
                                    id="header_akun"
                                    name="header_akun"
                                    class="fr-select @error('header_akun') is-invalid @enderror"
                                    required
                                >
                                    <option value="" disabled @selected(old('header_akun') === null)>Pilih kelompok akun</option>
                                    @foreach([1 => 'Aset', 2 => 'Kewajiban', 3 => 'Ekuitas', 4 => 'Pendapatan', 5 => 'Beban'] as $key => $label)
                                        <option value="{{ $key }}" @selected((string) old('header_akun') === (string) $key)>
                                            {{ $key }} — {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <p class="fr-help">Kelompok menentukan klasifikasi akun dalam laporan keuangan.</p>
                            @error('header_akun')
                                <p class="fr-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="fr-field-full">
                            <label for="nama_akun" class="fr-label">
                                <span>Nama Akun <span class="fr-required">*</span></span>
                            </label>

                            <div class="fr-input-wrap">
                                <span class="fr-input-icon"><i class="fa-solid fa-file-signature"></i></span>
                                <input
                                    type="text"
                                    id="nama_akun"
                                    name="nama_akun"
                                    value="{{ old('nama_akun') }}"
                                    class="fr-input @error('nama_akun') is-invalid @enderror"
                                    placeholder="Contoh: Kas, Bank, Zakat, atau Biaya Operasional"
                                    autocomplete="off"
                                    required
                                >
                            </div>

                            <p class="fr-help">Tuliskan nama akun yang singkat, spesifik, dan mudah dikenali.</p>
                            @error('nama_akun')
                                <p class="fr-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="fr-form-actions">
                        <a href="{{ route('admin.coa.index') }}" class="fr-btn">
                            <i class="fa-solid fa-xmark"></i>
                            Batal
                        </a>

                        <button type="submit" class="fr-btn fr-btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </article>

        <aside class="fr-card fr-reveal">
            <header class="fr-card-head">
                <div class="fr-card-title-row">
                    <span class="fr-card-icon"><i class="fa-solid fa-circle-info"></i></span>
                    <div>
                        <h2 class="fr-card-title">Panduan Pengisian</h2>
                        <p class="fr-card-subtitle">Periksa data sebelum menyimpan.</p>
                    </div>
                </div>
            </header>

            <div class="fr-card-body">
                <ul class="fr-tip-list">
                    <li class="fr-tip-item">
                        <span class="fr-tip-icon"><i class="fa-solid fa-hashtag"></i></span>
                        <span>
                            <strong class="fr-tip-title">Kode harus unik</strong>
                            <span class="fr-tip-copy">Satu kode hanya boleh digunakan oleh satu akun.</span>
                        </span>
                    </li>
                    <li class="fr-tip-item">
                        <span class="fr-tip-icon"><i class="fa-solid fa-layer-group"></i></span>
                        <span>
                            <strong class="fr-tip-title">Pilih kelompok yang tepat</strong>
                            <span class="fr-tip-copy">Kelompok akun memengaruhi penyajian laporan keuangan.</span>
                        </span>
                    </li>
                    <li class="fr-tip-item">
                        <span class="fr-tip-icon"><i class="fa-solid fa-spell-check"></i></span>
                        <span>
                            <strong class="fr-tip-title">Gunakan nama yang konsisten</strong>
                            <span class="fr-tip-copy">Hindari singkatan yang sulit dipahami pengguna lain.</span>
                        </span>
                    </li>
                </ul>
            </div>
        </aside>
    </section>
</div>
@endsection
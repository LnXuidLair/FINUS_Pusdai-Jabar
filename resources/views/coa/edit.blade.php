@extends('layouts.app')

@section('title', 'Edit Akun COA')
@section('hide-page-header', '1')

@include('layouts.partials.finus-ui')

@section('content')
<div class="fr-page">
    <section class="fr-hero fr-reveal">
        <div class="fr-hero-main">
            <span class="fr-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-pen-ruler"></i>
            </span>
            <div>
                <h1 class="fr-hero-title">Edit Akun</h1>
                <p class="fr-hero-subtitle">
                    Perbarui kode, nama, atau kelompok akun tanpa mengubah alur transaksi FINUS.
                </p>
            </div>
        </div>

        <div class="fr-hero-actions">
            <span class="fr-hero-badge">
                <i class="fa-solid fa-hashtag"></i>
                {{ $coa->kode_akun }}
            </span>
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
                <strong>Perubahan belum dapat disimpan.</strong>
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
                        <h2 class="fr-card-title">Perbarui Data Akun</h2>
                        <p class="fr-card-subtitle">Nilai lama telah dimasukkan secara otomatis.</p>
                    </div>
                </div>
            </header>

            <div class="fr-card-body">
                <form method="POST" action="{{ route('admin.coa.update', $coa) }}" novalidate>
                    @csrf
                    @method('PUT')

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
                                    value="{{ old('kode_akun', $coa->kode_akun) }}"
                                    class="fr-input @error('kode_akun') is-invalid @enderror"
                                    placeholder="Contoh: 1101"
                                    inputmode="numeric"
                                    autocomplete="off"
                                    autofocus
                                    required
                                >
                            </div>

                            <p class="fr-help">Pastikan kode tidak sama dengan akun lain.</p>
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
                                    @foreach([1 => 'Aset', 2 => 'Kewajiban', 3 => 'Ekuitas', 4 => 'Pendapatan', 5 => 'Beban'] as $key => $label)
                                        <option
                                            value="{{ $key }}"
                                            @selected((string) old('header_akun', $coa->header_akun) === (string) $key)
                                        >
                                            {{ $key }} — {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <p class="fr-help">Perubahan kelompok dapat memengaruhi klasifikasi laporan.</p>
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
                                    value="{{ old('nama_akun', $coa->nama_akun) }}"
                                    class="fr-input @error('nama_akun') is-invalid @enderror"
                                    placeholder="Contoh: Kas, Bank, Zakat, atau Biaya Operasional"
                                    autocomplete="off"
                                    required
                                >
                            </div>

                            <p class="fr-help">Gunakan nama yang singkat, spesifik, dan mudah dikenali.</p>
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
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </article>

        <aside class="fr-card fr-reveal">
            <header class="fr-card-head">
                <div class="fr-card-title-row">
                    <span class="fr-card-icon"><i class="fa-solid fa-shield-halved"></i></span>
                    <div>
                        <h2 class="fr-card-title">Catatan Perubahan</h2>
                        <p class="fr-card-subtitle">Jaga konsistensi data keuangan.</p>
                    </div>
                </div>
            </header>

            <div class="fr-card-body">
                <ul class="fr-tip-list">
                    <li class="fr-tip-item">
                        <span class="fr-tip-icon"><i class="fa-solid fa-link"></i></span>
                        <span>
                            <strong class="fr-tip-title">Periksa akun yang sudah dipakai</strong>
                            <span class="fr-tip-copy">Pastikan perubahan tetap sesuai dengan transaksi yang terkait.</span>
                        </span>
                    </li>
                    <li class="fr-tip-item">
                        <span class="fr-tip-icon"><i class="fa-solid fa-code-compare"></i></span>
                        <span>
                            <strong class="fr-tip-title">Hindari perubahan kode tanpa alasan</strong>
                            <span class="fr-tip-copy">Kode yang konsisten memudahkan pelacakan dan pelaporan.</span>
                        </span>
                    </li>
                    <li class="fr-tip-item">
                        <span class="fr-tip-icon"><i class="fa-solid fa-circle-check"></i></span>
                        <span>
                            <strong class="fr-tip-title">Periksa kembali sebelum menyimpan</strong>
                            <span class="fr-tip-copy">Pastikan kode, nama, dan kelompok akun sudah benar.</span>
                        </span>
                    </li>
                </ul>
            </div>
        </aside>
    </section>
</div>
@endsection

{{-- FINUS DARK MODE LOCAL: coa/edit.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="coa/edit.blade.php">
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-body,.fr-card-body) { background:transparent !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-icon,.fr-card-icon,.fmu-stat-icon,.fr-stat-icon) { box-shadow:inset 0 1px 0 rgba(255,255,255,.025) !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-hero-badge,.fr-hero-badge) { border-color:rgba(255,255,255,.18) !important; background:rgba(4,35,15,.36) !important; color:#F5FFF7 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-side-note,.fr-tip-item,.fr-breakdown-item) { border-color:#293D31 !important; background:#101B14 !important; }
</style>
@endpush


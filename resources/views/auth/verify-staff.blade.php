@extends('layouts.guest')

@section('portal', 'staff')

@section('title', 'FINUS | Verifikasi Pegawai')
@section('header-title', 'Verifikasi Pegawai')
@section('panel-eyebrow', 'Aktivasi Pegawai')
@section('panel-title', 'Verifikasi Data')
@section('panel-copy', 'Cocokkan identitas dengan data yang dibuat admin.')
@section('hero-title', 'Aktivasi Pegawai FINUS')
@section('hero-copy', 'Nama dan NIP harus sama dengan data pegawai yang telah didaftarkan oleh admin.')

@section('content')
<form method="POST" action="{{ route('verify.staff') }}" class="auth-form" data-loading-title="Memverifikasi data pegawai...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">ID</span>
        <div>
            <p class="auth-context-title">Gunakan identitas sesuai data admin</p>
            <p class="auth-context-copy">Penulisan nama dan NIP harus sama agar sistem dapat menemukan akun pegawai.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="name" class="auth-label"><span class="auth-label-icon" aria-hidden="true">Aa</span>Nama Lengkap <span class="auth-required">*</span></label>
        <input id="name" type="text" name="name" value="{{ old('name') }}"
               class="auth-field" placeholder="Nama sesuai data pegawai"
               autocomplete="name" required autofocus
               @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
        @error('name')<p class="auth-error" id="name-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-field-group">
        <label for="nip" class="auth-label"><span class="auth-label-icon" aria-hidden="true">#</span>NIP <span class="auth-required">*</span></label>
        <input id="nip" type="text" name="nip" value="{{ old('nip') }}"
               class="auth-field" placeholder="Nomor induk pegawai"
               autocomplete="off" inputmode="numeric" required
               @error('nip') aria-invalid="true" aria-describedby="nip-error" @enderror>
        @error('nip')<p class="auth-error" id="nip-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Memverifikasi..." data-loading-title="Memverifikasi data pegawai...">
        Verifikasi
    </button>

    <p class="auth-footer-text">
        Akun sudah aktif?
        <a href="{{ route('login.staff') }}" class="auth-link">Kembali ke login pegawai</a>
    </p>
</form>

@isset($verifiedPegawai)
    <div class="auth-dialog-overlay">
        <div class="auth-dialog" role="dialog" aria-modal="true" aria-labelledby="verified-title">
            <div class="auth-dialog-body">
                <div class="auth-dialog-icon" aria-hidden="true">✓</div>
                <h3 class="auth-dialog-title" id="verified-title">Verifikasi Berhasil</h3>
                <p class="auth-dialog-copy">Data pegawai ditemukan dan sesuai dengan data yang dibuat oleh admin.</p>

                <dl class="auth-dialog-details">
                    <div class="auth-dialog-detail"><dt>Nama Pegawai</dt><dd>{{ $verifiedPegawai->nama_pegawai }}</dd></div>
                    <div class="auth-dialog-detail"><dt>NIP</dt><dd>{{ $verifiedPegawai->nip }}</dd></div>
                    <div class="auth-dialog-detail"><dt>Jabatan</dt><dd>{{ $verifiedPegawai->jabatan }}</dd></div>
                </dl>
            </div>
            <div class="auth-dialog-footer">
                <a href="{{ route('register.staff.account') }}" class="auth-button" data-loading-title="Membuka aktivasi akun...">
                    Lanjut Buat Password
                </a>
            </div>
        </div>
    </div>
@endisset
@endsection

{{-- FINUS DARK MODE LOCAL: auth/verify-staff.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="auth/verify-staff.blade.php">
html[data-finus-theme="dark"] body .auth-panel { background:linear-gradient(160deg,#0F1813 0%,#0B120E 100%) !important; }
html[data-finus-theme="dark"] body .auth-panel-inner { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-title,.auth-context-title,.auth-label,.auth-dialog-title) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-copy,.auth-footer-text,.auth-help,.auth-code-copy,.auth-dialog-copy,.auth-checkbox) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .auth-field { border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .auth-field:focus { border-color:#64DD81 !important; background:#0F1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.12) !important; }
html[data-finus-theme="dark"] body :where(.auth-info-card,.auth-email-preview,.auth-dialog-details) { border-color:#293D31 !important; background:#101B14 !important; color:#C7D6CC !important; }
</style>
@endpush


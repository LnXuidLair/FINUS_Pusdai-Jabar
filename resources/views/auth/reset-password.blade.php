@php
    $portalSection = match($portal ?? 'jamaah') {
        'admin' => 'admin',
        'pegawai' => 'staff',
        default => 'jamaah',
    };
@endphp

@extends('layouts.guest')

@section('portal', $portalSection)
@section('title', 'FINUS | Reset Password')
@section('header-title', 'Pemulihan Akun')
@section('panel-eyebrow', 'Keamanan Akun')
@section('panel-title', 'Buat Password Baru')
@section('panel-copy', 'Verifikasi pemulihan berhasil. Buat password baru untuk akun Anda.')
@section('hero-title', 'Keamanan Akun FINUS')
@section('hero-copy', 'Selesaikan proses reset password untuk memperoleh kembali akses ke akun Anda.')

@section('content')
<form method="POST" action="{{ route('password.update') }}" class="auth-form" data-loading-title="Membuat ulang password...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">✓</span>
        <div>
            <p class="auth-context-title">Verifikasi pemulihan berhasil</p>
            <p class="auth-context-copy">Masukkan password baru dan konfirmasi password untuk menyelesaikan proses.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="password" class="auth-label"><span class="auth-label-icon" aria-hidden="true">••</span>Password Baru <span class="auth-required">*</span></label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Minimal 8 karakter" autocomplete="new-password"
                   data-password-strength="resetStrength" required autofocus
                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password" aria-pressed="false">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
            </button>
        </div>

        <div class="auth-password-strength" id="resetStrength" aria-live="polite">
            <div class="auth-strength-bars" aria-hidden="true">
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
            </div>
            <span class="auth-strength-text" data-strength-text>Masukkan minimal 8 karakter</span>
        </div>
        <p class="auth-help"><b>i</b>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol.</p>
        @error('password')<p class="auth-error" id="password-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-field-group">
        <label for="password_confirmation" class="auth-label"><span class="auth-label-icon" aria-hidden="true">✓</span>Konfirmasi Password <span class="auth-required">*</span></label>
        <div class="auth-input-wrap">
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="auth-field auth-password-field" placeholder="Ulangi password"
                   autocomplete="new-password" required data-password-match="#password" data-match-message="reset-password-match">
            <button type="button" data-toggle-password data-target="#password_confirmation" aria-label="Tampilkan password" aria-pressed="false">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
            </button>
        </div>
        <p id="reset-password-match" class="auth-match-message" aria-live="polite"></p>
        @error('password_confirmation')<p class="auth-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Menyimpan..." data-loading-title="Membuat ulang password...">
        Simpan Password Baru
    </button>
</form>
@endsection

{{-- FINUS DARK MODE LOCAL: auth/reset-password.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="auth/reset-password.blade.php">
html[data-finus-theme="dark"] body .auth-panel { background:linear-gradient(160deg,#0F1813 0%,#0B120E 100%) !important; }
html[data-finus-theme="dark"] body .auth-panel-inner { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-title,.auth-context-title,.auth-label,.auth-dialog-title) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-copy,.auth-footer-text,.auth-help,.auth-code-copy,.auth-dialog-copy,.auth-checkbox) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .auth-field { border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .auth-field:focus { border-color:#64DD81 !important; background:#0F1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.12) !important; }
html[data-finus-theme="dark"] body :where(.auth-info-card,.auth-email-preview,.auth-dialog-details) { border-color:#293D31 !important; background:#101B14 !important; color:#C7D6CC !important; }
</style>
@endpush


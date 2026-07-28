@extends('layouts.guest')

@section('title', 'FINUS | Daftar Jamaah')
@section('header-title', 'Registrasi Jamaah')
@section('panel-eyebrow', 'Pendaftaran Jamaah')
@section('panel-title', 'Buat Akun Jamaah')
@section('panel-copy', 'Gunakan email aktif untuk menerima kode verifikasi.')
@section('hero-title', 'Jamaah Masjid PUSDAI')
@section('hero-copy', 'Daftarkan akun untuk mengakses layanan jamaah Masjid PUSDAI dengan aman dan mudah.')

@section('content')
<form method="POST" action="{{ route('register.jamaah.post') }}" class="auth-form" data-loading-title="Mendaftarkan akun jamaah...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">✉</span>
        <div>
            <p class="auth-context-title">Verifikasi email diperlukan</p>
            <p class="auth-context-copy">Kode enam digit akan dikirim setelah formulir pendaftaran berhasil diproses.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="name" class="auth-label"><span class="auth-label-icon" aria-hidden="true">Aa</span>Nama Jamaah <span class="auth-required">*</span></label>
        <input id="name" type="text" name="name" value="{{ old('name') }}"
               class="auth-field" placeholder="Masukkan nama lengkap"
               autocomplete="name" required autofocus
               @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
        @error('name')<p class="auth-error" id="name-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-field-group">
        <label for="email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Alamat Email <span class="auth-required">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="nama@email.com"
               autocomplete="email" inputmode="email" required
               aria-describedby="email-help" @error('email') aria-invalid="true" @enderror>
        <p class="auth-help" id="email-help"><b>i</b>Gunakan email pribadi aktif yang dapat menerima kode verifikasi.</p>
        @error('email')<p class="auth-error" id="email-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-field-group">
        <label for="password" class="auth-label"><span class="auth-label-icon" aria-hidden="true">••</span>Password <span class="auth-required">*</span></label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Minimal 8 karakter" autocomplete="new-password"
                   data-password-strength="jamaahStrength" required
                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password" aria-pressed="false">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
            </button>
        </div>

        <div class="auth-password-strength" id="jamaahStrength" aria-live="polite">
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
                   autocomplete="new-password" required data-password-match="#password" data-match-message="jamaah-password-match">
            <button type="button" data-toggle-password data-target="#password_confirmation" aria-label="Tampilkan password" aria-pressed="false">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
            </button>
        </div>
        <p id="jamaah-password-match" class="auth-match-message" aria-live="polite"></p>
        @error('password_confirmation')<p class="auth-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Mendaftarkan..." data-loading-title="Mendaftarkan akun jamaah...">
        Buat Akun dan Verifikasi
    </button>

    <p class="auth-footer-text">
        Sudah memiliki akun?
        <a href="{{ route('login.jamaah') }}" class="auth-link">Masuk sebagai jamaah</a>
    </p>
</form>
@endsection
@extends('layouts.guest')

@section('title', 'FINUS | Daftar Jamaah')
@section('header-title', 'Registrasi Jamaah')
@section('panel-title', 'Buat Akun Jamaah')
@section('panel-copy', 'Gunakan email aktif untuk menerima kode verifikasi.')
@section('hero-title', 'Jamaah Masjid PUSDAI')
@section('hero-copy', 'Daftarkan akun untuk mengakses layanan jamaah Masjid PUSDAI.')

@section('content')
<form method="POST" action="{{ route('register.jamaah.post') }}" class="auth-form" data-loading-title="Mendaftarkan akun jamaah...">
    @csrf

    <div>
        <label for="name" class="auth-label"><span class="auth-label-icon">◆</span>Nama Jamaah</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}"
               class="auth-field" placeholder="Masukkan nama lengkap"
               autocomplete="name" required autofocus>
        @error('name')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="email" class="auth-label"><span class="auth-label-icon">✉</span>Alamat Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="nama@email.com"
               autocomplete="email" required>
        <p class="auth-help"><b>i</b>Gunakan email pribadi aktif yang dapat menerima kode verifikasi.</p>
        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password" class="auth-label"><span class="auth-label-icon">●</span>Password</label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Minimal 8 karakter" autocomplete="new-password"
                   data-password-strength="jamaahStrength" required>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="Tampilkan password">
            </button>
        </div>

        <div class="auth-password-strength" id="jamaahStrength">
            <div class="auth-strength-bars">
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
            </div>
            <span class="auth-strength-text" data-strength-text>Masukkan minimal 8 karakter</span>
        </div>

        @error('password')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password_confirmation" class="auth-label"><span class="auth-label-icon">✓</span>Konfirmasi Password</label>
        <div class="auth-input-wrap">
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="auth-field auth-password-field" placeholder="Ulangi password"
                   autocomplete="new-password" required>
            <button type="button" data-toggle-password data-target="#password_confirmation" aria-label="Tampilkan password">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="Tampilkan password">
            </button>
        </div>
        @error('password_confirmation')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Mendaftarkan..." data-loading-title="Mendaftarkan akun jamaah...">
        Buat Akun dan Verifikasi
    </button>

    <p class="auth-footer-text">
        Sudah memiliki akun?
        <a href="{{ route('login.jamaah') }}" class="auth-link">Masuk</a>
    </p>
</form>
@endsection
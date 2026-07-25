@extends('layouts.guest')

@section('title', 'FINUS | Login Jamaah')
@section('header-title', 'Selamat Datang Jamaah')
@section('panel-title', 'Login Jamaah')
@section('panel-copy', 'Masuk menggunakan email yang telah diverifikasi.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Terhubung dengan informasi dan layanan keuangan Masjid PUSDAI secara transparan.')

@section('content')
<form method="POST" action="{{ route('login.jamaah') }}" class="auth-form" data-loading-title="Memeriksa akun jamaah...">
    @csrf

    <div>
        <label for="email" class="auth-label"><span class="auth-label-icon">✉</span>Alamat Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="nama@email.com"
               autocomplete="email" required autofocus>
        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password" class="auth-label"><span class="auth-label-icon">●</span>Password</label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Masukkan password" autocomplete="current-password" required>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="Tampilkan password">
            </button>
        </div>
        @error('password')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <div class="auth-form-row">
        <label class="auth-checkbox"><input type="checkbox" name="remember">Ingat saya</label>
        <a href="{{ route('password.request') }}" class="auth-link text-xs">Lupa password?</a>
    </div>

    <button type="submit" class="auth-button" data-loading-text="Sedang masuk..." data-loading-title="Memeriksa akun jamaah...">
        Masuk sebagai Jamaah
    </button>

    <p class="auth-footer-text">
        Belum memiliki akun?
        <a href="{{ route('register.jamaah') }}" class="auth-link">Daftar sekarang</a>
    </p>
</form>
@endsection
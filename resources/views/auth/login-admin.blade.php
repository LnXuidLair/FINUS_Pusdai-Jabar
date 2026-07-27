@extends('layouts.guest')

@section('title', 'FINUS | Login Admin')
@section('header-title', 'Selamat Datang Admin')
@section('panel-title', 'Login Admin')
@section('panel-copy', 'Masuk untuk mengelola operasional dan keuangan masjid.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Kelola data pegawai, penggajian, transaksi, dan laporan keuangan dalam satu sistem.')

@section('content')
@if(session('admin_email'))
    <div class="auth-alert auth-alert-success">
        <div class="auth-alert-icon">✓</div>
        <div>
            <p class="auth-alert-title">Akun admin berhasil dibuat</p>
            <p class="auth-alert-message">Gunakan email berikut: <strong class="block break-all">{{ session('admin_email') }}</strong></p>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('login.admin') }}" class="auth-form" data-loading-title="Memeriksa akun admin...">
    @csrf

    <div>
        <label for="email" class="auth-label"><span class="auth-label-icon">✉</span>Email Admin</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="nama@AdminFinusPusdai.org"
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

    <button type="submit" class="auth-button" data-loading-text="Sedang masuk..." data-loading-title="Memeriksa akun admin...">
        Masuk
    </button>

    @unless(\App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->exists())
        <p class="auth-footer-text">Admin belum dibuat. <a href="{{ route('register.admin') }}" class="auth-link">Daftar admin</a></p>
    @endunless
</form>
@endsection
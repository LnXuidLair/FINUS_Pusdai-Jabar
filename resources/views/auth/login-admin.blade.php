@extends('layouts.guest')

@section('title', 'FINUS | Login Admin')
@section('header-title', 'Selamat Datang Admin')
@section('panel-eyebrow', 'Akses Pengelola')
@section('panel-title', 'Login Admin')
@section('panel-copy', 'Masuk untuk mengelola operasional dan keuangan masjid.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Kelola data pegawai, penggajian, transaksi, dan laporan keuangan dalam satu sistem yang tertib.')

@section('content')
@if(session('admin_email'))
    <div class="auth-alert auth-alert-success" role="status">
        <div class="auth-alert-icon" aria-hidden="true">✓</div>
        <div>
            <p class="auth-alert-title">Akun admin berhasil dibuat</p>
            <p class="auth-alert-message">Gunakan email berikut: <strong class="auth-block auth-break-all">{{ session('admin_email') }}</strong></p>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('login.admin') }}" class="auth-form" data-loading-title="Memeriksa akun admin...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">A</span>
        <div>
            <p class="auth-context-title">Akses khusus administrator</p>
            <p class="auth-context-copy">Pastikan Anda menggunakan akun admin resmi FINUS PUSDAI.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Email Admin <span class="auth-required">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="nama@AdminFinusPusdai.org"
               autocomplete="username" inputmode="email" required autofocus
               @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
        @error('email')<p class="auth-error" id="email-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-field-group">
        <label for="password" class="auth-label"><span class="auth-label-icon" aria-hidden="true">••</span>Password <span class="auth-required">*</span></label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Masukkan password" autocomplete="current-password" required
                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password" aria-pressed="false">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
            </button>
        </div>
        @error('password')<p class="auth-error" id="password-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-form-row">
        <label class="auth-checkbox"><input type="checkbox" name="remember" value="1" @checked(old('remember'))>Ingat saya</label>
        <a href="{{ route('password.request') }}" class="auth-link auth-link-small">Lupa password?</a>
    </div>

    <button type="submit" class="auth-button" data-loading-text="Sedang masuk..." data-loading-title="Memeriksa akun admin...">
        Masuk sebagai Admin
    </button>

    @unless(\App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->exists())
        <p class="auth-footer-text">Admin belum dibuat. <a href="{{ route('register.admin') }}" class="auth-link">Daftar admin</a></p>
    @endunless
</form>
@endsection

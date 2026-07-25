@extends('layouts.guest')

@section('title', 'FINUS | Login Pegawai')
@section('header-title', 'Selamat Datang Pegawai')
@section('panel-title', 'Login Pegawai')
@section('panel-copy', 'Gunakan akun pegawai yang sudah diaktivasi.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Akses dashboard dan aktivitas kerja sesuai jabatan yang ditetapkan oleh admin.')

@section('content')
@if(session('account_activated'))
    <div class="auth-dialog-overlay" id="activatedModal">
        <div class="auth-dialog" role="dialog" aria-modal="true">
            <div class="auth-dialog-body">
                <div class="auth-dialog-icon">✓</div>
                <h3 class="auth-dialog-title">Akun Berhasil Diaktifkan</h3>
                <p class="auth-dialog-copy">Akun pegawai telah aktif. Silakan masuk menggunakan email dan password baru.</p>
            </div>
            <div class="auth-dialog-footer">
                <button type="button" class="auth-button" onclick="document.getElementById('activatedModal').remove()" data-no-loading>
                    Lanjut ke Login
                </button>
            </div>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('login.staff') }}" class="auth-form" data-loading-title="Memeriksa akun pegawai...">
    @csrf

    <div>
        <label for="email" class="auth-label"><span class="auth-label-icon">✉</span>Email Pegawai</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="nama@StaffFinusPusdai.ac.id"
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

    <button type="submit" class="auth-button" data-loading-text="Sedang masuk..." data-loading-title="Memeriksa akun pegawai...">
        Masuk sebagai Pegawai
    </button>

    <p class="auth-footer-text">
        Belum aktivasi akun?
        <a href="{{ route('register.staff') }}" class="auth-link">Verifikasi pegawai</a>
    </p>
</form>
@endsection
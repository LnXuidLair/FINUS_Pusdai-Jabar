@extends('layouts.guest')

@section('title', 'FINUS | Aktivasi Akun Pegawai')
@section('header-title', 'Aktivasi Akun Pegawai')
@section('panel-title', 'Buat Password')
@section('panel-copy', 'Data berhasil diverifikasi. Selesaikan aktivasi akun.')
@section('hero-title', 'Akun Pegawai Terverifikasi')
@section('hero-copy', 'Buat password yang kuat untuk masuk ke dashboard sesuai jabatan Anda.')

@section('content')
<form method="POST" action="{{ route('register.staff.post') }}" class="auth-form" data-loading-title="Mengaktifkan akun pegawai...">
    @csrf

    <div class="auth-info-card">
        Identitas pegawai telah cocok dengan data admin. Buat password untuk menyelesaikan aktivasi.
    </div>

    <div>
        <label for="staff_email" class="auth-label"><span class="auth-label-icon">✉</span>Email Pegawai</label>
        <input id="staff_email" type="email" value="{{ $pegawai->email }}" class="auth-field" readonly>
    </div>

    <div>
        <label for="password" class="auth-label"><span class="auth-label-icon">●</span>Password</label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Minimal 8 karakter" autocomplete="new-password"
                   data-password-strength="staffStrength" required autofocus>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="Tampilkan password">
            </button>
        </div>

        <div class="auth-password-strength" id="staffStrength">
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

    <button type="submit" class="auth-button" data-loading-text="Mengaktifkan..." data-loading-title="Mengaktifkan akun pegawai...">
        Aktifkan Akun
    </button>
</form>
@endsection
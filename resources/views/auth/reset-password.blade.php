@extends('layouts.guest')
@section('title', 'FINUS | Reset Password')
@section('header-title', 'Pemulihan Akun')
@section('panel-title', 'Buat Password Baru')
@section('panel-copy', 'Gunakan password baru untuk mengamankan akun.')
@section('hero-title', 'Keamanan Akun FINUS')
@section('hero-copy', 'Selesaikan proses reset password untuk memperoleh kembali akses ke akun Anda.')
@section('content')
<form method="POST" action="{{ route('password.update') }}" class="auth-form" data-loading-title="Memperbarui password...">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div>
        <label for="email" class="auth-label"><span class="auth-label-icon">✉</span>Alamat Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $email) }}"
               class="auth-field" autocomplete="email" required autofocus>
        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password" class="auth-label"><span class="auth-label-icon">●</span>Password Baru</label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Minimal 8 karakter" autocomplete="new-password"
                   data-password-strength="resetStrength" required>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="Tampilkan password">
            </button>
        </div>
        <div class="auth-password-strength" id="resetStrength">
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
    <button type="submit" class="auth-button" data-loading-text="Menyimpan..." data-loading-title="Memperbarui password...">
        Simpan Password Baru
    </button>
</form>
@endsection
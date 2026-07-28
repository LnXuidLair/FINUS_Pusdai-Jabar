@extends('layouts.guest')

@section('title', 'FINUS | Reset Password')
@section('header-title', 'Pemulihan Akun')
@section('panel-eyebrow', 'Keamanan Akun')
@section('panel-title', 'Buat Password Baru')
@section('panel-copy', 'Gunakan password baru untuk mengamankan akun.')
@section('hero-title', 'Keamanan Akun FINUS')
@section('hero-copy', 'Selesaikan proses reset password untuk memperoleh kembali akses ke akun Anda.')

@section('content')
<form method="POST" action="{{ route('password.update') }}" class="auth-form" data-loading-title="Memperbarui password...">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">✓</span>
        <div>
            <p class="auth-context-title">Buat password yang berbeda</p>
            <p class="auth-context-copy">Hindari menggunakan kembali password lama atau password dari akun lain.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Alamat Email <span class="auth-required">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email', $email) }}"
               class="auth-field" autocomplete="email" inputmode="email" required autofocus
               @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
        @error('email')<p class="auth-error" id="email-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-field-group">
        <label for="password" class="auth-label"><span class="auth-label-icon" aria-hidden="true">••</span>Password Baru <span class="auth-required">*</span></label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Minimal 8 karakter" autocomplete="new-password"
                   data-password-strength="resetStrength" required
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

    <button type="submit" class="auth-button" data-loading-text="Menyimpan..." data-loading-title="Memperbarui password...">
        Simpan Password Baru
    </button>
</form>
@endsection
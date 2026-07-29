@extends('layouts.guest')

@section('title', 'FINUS | Aktivasi Akun Pegawai')
@section('header-title', 'Aktivasi Akun Pegawai')
@section('panel-eyebrow', 'Langkah Terakhir')
@section('panel-title', 'Buat Password')
@section('panel-copy', 'Data berhasil diverifikasi. Selesaikan aktivasi akun.')
@section('hero-title', 'Akun Pegawai Terverifikasi')
@section('hero-copy', 'Buat password yang kuat untuk masuk ke dashboard sesuai jabatan Anda.')

@section('content')
<form method="POST" action="{{ route('register.staff.post') }}" class="auth-form" data-loading-title="Mengaktifkan akun pegawai...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">✓</span>
        <div>
            <p class="auth-context-title">Identitas pegawai sudah cocok</p>
            <p class="auth-context-copy">Buat password untuk menyelesaikan aktivasi dan melindungi akun Anda.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="staff_email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Email Pegawai</label>
        <input id="staff_email" type="email" value="{{ $pegawai->email }}" class="auth-field" readonly aria-readonly="true">
    </div>

    <div class="auth-field-group">
        <label for="password" class="auth-label"><span class="auth-label-icon" aria-hidden="true">••</span>Password <span class="auth-required">*</span></label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Minimal 8 karakter" autocomplete="new-password"
                   data-password-strength="staffStrength" required autofocus
                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password" aria-pressed="false">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
            </button>
        </div>

        <div class="auth-password-strength" id="staffStrength" aria-live="polite">
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
                   autocomplete="new-password" required data-password-match="#password" data-match-message="staff-password-match">
            <button type="button" data-toggle-password data-target="#password_confirmation" aria-label="Tampilkan password" aria-pressed="false">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
            </button>
        </div>
        <p id="staff-password-match" class="auth-match-message" aria-live="polite"></p>
        @error('password_confirmation')<p class="auth-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Mengaktifkan..." data-loading-title="Mengaktifkan akun pegawai...">
        Aktifkan Akun
    </button>
</form>
@endsection
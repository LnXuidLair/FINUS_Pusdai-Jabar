@extends('layouts.guest')

@section('title', 'FINUS | Lupa Password')
@section('header-title', 'Pemulihan Akun')
@section('panel-eyebrow', 'Keamanan Akun')
@section('panel-title', 'Lupa Password')
@section('panel-copy', 'Kami akan mengirim tautan reset ke email akun Anda.')
@section('hero-title', 'Pemulihan Akun FINUS')
@section('hero-copy', 'Masukkan email terdaftar untuk mendapatkan tautan pembuatan password baru.')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="auth-form" data-loading-title="Mengirim tautan reset...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">↗</span>
        <div>
            <p class="auth-context-title">Tautan reset dikirim melalui email</p>
            <p class="auth-context-copy">Periksa kotak masuk dan folder spam setelah permintaan berhasil dikirim.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Alamat Email <span class="auth-required">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="Alamat email terdaftar"
               autocomplete="email" inputmode="email" required autofocus
               @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
        @error('email')<p class="auth-error" id="email-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Mengirim..." data-loading-title="Mengirim tautan reset password...">
        Kirim Tautan Reset
    </button>

    <p class="auth-footer-text">
        Sudah mengingat password?
        <a href="{{ route('home') }}" class="auth-link">Kembali ke beranda</a>
    </p>
</form>
@endsection
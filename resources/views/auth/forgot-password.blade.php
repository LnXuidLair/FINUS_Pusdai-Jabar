@extends('layouts.guest')

@section('title', 'FINUS | Lupa Password')
@section('header-title', 'Pemulihan Akun')
@section('panel-title', 'Lupa Password')
@section('panel-copy', 'Kami akan mengirim tautan reset ke email akun Anda.')
@section('hero-title', 'Pemulihan Akun FINUS')
@section('hero-copy', 'Masukkan email terdaftar untuk mendapatkan tautan pembuatan password baru.')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="auth-form" data-loading-title="Mengirim tautan reset...">
    @csrf

    <div class="auth-info-card">
        Tautan reset password akan dikirim ke alamat email yang terdaftar pada akun FINUS.
    </div>

    <div>
        <label for="email" class="auth-label"><span class="auth-label-icon">✉</span>Alamat Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="Alamat email terdaftar"
               autocomplete="email" required autofocus>
        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
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
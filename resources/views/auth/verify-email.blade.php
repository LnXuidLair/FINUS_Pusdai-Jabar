@extends('layouts.guest')
@section('title', 'FINUS | Verifikasi Kode')
@section('header-title', 'Verifikasi Akun Jamaah')
@section('panel-title', 'Masukkan Kode')
@section('panel-copy', 'Kode verifikasi telah dikirim ke email Anda.')
@section('hero-title', 'Verifikasi Akun Jamaah')
@section('hero-copy', 'Masukkan kode verifikasi agar akun jamaah dapat digunakan.')
@section('content')
<div class="auth-code-intro">
    <div class="auth-code-icon">✉</div>
    <p class="auth-code-copy">
        Kami telah mengirim kode verifikasi enam digit ke email Anda.
        Masukkan kode tersebut sebelum masa berlakunya berakhir.
    </p>
</div>
<form method="POST" action="{{ route('verification.code.verify') }}" class="auth-form" data-loading-title="Memverifikasi akun jamaah...">
    @csrf
    <div>
        <label for="code" class="auth-label"><span class="auth-label-icon">#</span>Kode Verifikasi</label>
        <input id="code" type="text" name="code" value="{{ old('code') }}"
               class="auth-field auth-code-field" placeholder="000000"
               inputmode="numeric" autocomplete="one-time-code"
               maxlength="6" data-verification-code required autofocus>
        @error('code')<p class="auth-error justify-center">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="auth-button" data-loading-text="Memverifikasi..." data-loading-title="Memverifikasi akun jamaah...">
        Verifikasi
    </button>
</form>
<form method="POST" action="{{ route('verification.send') }}" class="mt-3" data-loading-title="Mengirim ulang kode...">
    @csrf
    <button type="submit" class="auth-button-secondary" data-loading-text="Mengirim..." data-loading-title="Mengirim ulang kode verifikasi...">
        Kirim Ulang Kode
    </button>
</form>
<form method="POST" action="{{ route('logout') }}" class="mt-3" data-loading-title="Keluar dari akun...">
    @csrf
    <button type="submit" class="auth-button-muted" data-loading-text="Keluar..." data-loading-title="Keluar dari akun...">
        Keluar
    </button>
</form>
@endsection
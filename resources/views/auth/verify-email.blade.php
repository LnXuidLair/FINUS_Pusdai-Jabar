@extends('layouts.guest')

@section('portal', 'jamaah')

@section('title', 'FINUS | Verifikasi Kode')
@section('header-title', 'Verifikasi Akun Jamaah')
@section('panel-eyebrow', 'Langkah Verifikasi')
@section('panel-title', 'Masukkan Kode')
@section('panel-copy', 'Kode verifikasi telah dikirim ke email Anda.')
@section('hero-title', 'Verifikasi Akun Jamaah')
@section('hero-copy', 'Masukkan kode verifikasi agar akun jamaah dapat digunakan dengan aman.')

@section('content')
<div class="auth-code-intro">
    <div class="auth-code-icon" aria-hidden="true">✉</div>
    <p class="auth-code-copy">
        Kami telah mengirim kode verifikasi enam digit ke email Anda.
        Masukkan kode tersebut sebelum masa berlakunya berakhir.
    </p>
</div>

<form method="POST" action="{{ route('verification.code.verify') }}" class="auth-form" data-loading-title="Memverifikasi akun jamaah...">
    @csrf

    <div class="auth-field-group">
        <label for="code" class="auth-label"><span class="auth-label-icon" aria-hidden="true">#</span>Kode Verifikasi <span class="auth-required">*</span></label>
        <input id="code" type="text" name="code" value="{{ old('code') }}"
               class="auth-field auth-code-field" placeholder="000000"
               inputmode="numeric" autocomplete="one-time-code"
               maxlength="6" pattern="[0-9]{6}" data-verification-code required autofocus
               @error('code') aria-invalid="true" aria-describedby="code-error" @enderror>
        <p class="auth-help"><b>i</b>Masukkan enam angka tanpa spasi atau tanda baca.</p>
        @error('code')<p class="auth-error auth-error-center" id="code-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Memverifikasi..." data-loading-title="Memverifikasi akun jamaah...">
        Verifikasi Akun
    </button>
</form>

<div class="auth-form-actions-stack">
    <form method="POST" action="{{ route('verification.send') }}" data-loading-title="Mengirim ulang kode...">
        @csrf
        <button type="submit" class="auth-button-secondary" data-loading-text="Mengirim..." data-loading-title="Mengirim ulang kode verifikasi...">
            Kirim Ulang Kode
        </button>
    </form>

    <form method="POST" action="{{ route('logout.jamaah') }}" data-loading-title="Keluar dari akun...">
        @csrf
        <button type="submit" class="auth-button-muted" data-loading-text="Keluar..." data-loading-title="Keluar dari akun...">
            Batalkan dan Keluar
        </button>
    </form>
</div>
@endsection
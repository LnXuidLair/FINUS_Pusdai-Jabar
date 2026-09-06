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

{{-- FINUS DARK MODE LOCAL: auth/verify-email.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="auth/verify-email.blade.php">
html[data-finus-theme="dark"] body .auth-panel { background:linear-gradient(160deg,#0F1813 0%,#0B120E 100%) !important; }
html[data-finus-theme="dark"] body .auth-panel-inner { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-title,.auth-context-title,.auth-label,.auth-dialog-title) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-copy,.auth-footer-text,.auth-help,.auth-code-copy,.auth-dialog-copy,.auth-checkbox) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .auth-field { border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .auth-field:focus { border-color:#64DD81 !important; background:#0F1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.12) !important; }
html[data-finus-theme="dark"] body :where(.auth-info-card,.auth-email-preview,.auth-dialog-details) { border-color:#293D31 !important; background:#101B14 !important; color:#C7D6CC !important; }
</style>
@endpush


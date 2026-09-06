@extends('layouts.guest')

@section('portal', 'jamaah')
@section('title', 'FINUS | Verifikasi OTP Reset Password')
@section('header-title', 'Verifikasi OTP')
@section('panel-eyebrow', 'Pemulihan Akun Jamaah')
@section('panel-title', 'Masukkan Kode OTP')
@section('panel-copy', 'Kode OTP telah dikirim ke email akun Jamaah Anda.')
@section('hero-title', 'Reset Password Jamaah')
@section('hero-copy', 'Masukkan kode enam digit sebelum masa berlakunya berakhir.')

@section('content')
<div class="auth-code-intro">
    <div class="auth-code-icon" aria-hidden="true">✉</div>
    <p class="auth-code-copy">
        Kode OTP dikirim ke <strong>{{ $maskedEmail }}</strong> dan berlaku selama 20 detik.
    </p>
</div>

@if(session('status') === 'password-otp-sent')
    <div class="auth-alert auth-alert-success" role="status">
        <div class="auth-alert-icon" aria-hidden="true">✓</div>
        <div>
            <p class="auth-alert-title">Kode OTP Dikirim</p>
            <p class="auth-alert-message">Silakan periksa email dan masukkan kode enam digit.</p>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('password.otp.verify') }}" class="auth-form" data-loading-title="Memverifikasi kode OTP...">
    @csrf

    <div class="auth-field-group">
        <label for="code" class="auth-label"><span class="auth-label-icon" aria-hidden="true">#</span>Kode OTP <span class="auth-required">*</span></label>
        <input id="code" type="text" name="code" value="{{ old('code') }}"
               class="auth-field auth-code-field" placeholder="000000"
               inputmode="numeric" autocomplete="one-time-code"
               maxlength="6" pattern="[0-9]{6}" data-verification-code required autofocus
               @error('code') aria-invalid="true" aria-describedby="code-error" @enderror>
        <p class="auth-help"><b>i</b>Sisa waktu: <strong id="otpCountdown">{{ $secondsRemaining }}</strong> detik.</p>
        @error('code')<p class="auth-error auth-error-center" id="code-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Memverifikasi..." data-loading-title="Memverifikasi kode OTP...">
        Verifikasi OTP
    </button>
</form>

<div class="auth-form-actions-stack">
    <form method="POST" action="{{ route('password.otp.resend') }}" data-loading-title="Mengirim ulang OTP...">
        @csrf
        <button type="submit" class="auth-button-secondary" data-loading-text="Mengirim..." data-loading-title="Mengirim ulang kode OTP...">
            Kirim Ulang Kode
        </button>
    </form>

    <a href="{{ route('login.jamaah') }}" class="auth-button-muted" style="text-decoration:none;">
        Batalkan dan Kembali ke Login
    </a>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const countdown = document.getElementById('otpCountdown');
    if (!countdown) return;

    let remaining = Number(countdown.textContent || 0);
    const timer = window.setInterval(() => {
        remaining = Math.max(0, remaining - 1);
        countdown.textContent = String(remaining);
        if (remaining <= 0) window.clearInterval(timer);
    }, 1000);
})();
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: auth/verify-password-otp.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="auth/verify-password-otp.blade.php">
html[data-finus-theme="dark"] body .auth-panel { background:linear-gradient(160deg,#0F1813 0%,#0B120E 100%) !important; }
html[data-finus-theme="dark"] body .auth-panel-inner { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-title,.auth-context-title,.auth-label,.auth-dialog-title) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-copy,.auth-footer-text,.auth-help,.auth-code-copy,.auth-dialog-copy,.auth-checkbox) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .auth-field { border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .auth-field:focus { border-color:#64DD81 !important; background:#0F1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.12) !important; }
html[data-finus-theme="dark"] body :where(.auth-info-card,.auth-email-preview,.auth-dialog-details) { border-color:#293D31 !important; background:#101B14 !important; color:#C7D6CC !important; }
</style>
@endpush


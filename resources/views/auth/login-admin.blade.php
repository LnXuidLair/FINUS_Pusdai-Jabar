@extends('layouts.guest')

@section('portal', 'admin')

@section('title', 'FINUS | Login Admin')
@section('header-title', 'Selamat Datang Admin')
@section('panel-eyebrow', 'Akses Pengelola')
@section('panel-title', 'Login Admin')
@section('panel-copy', 'Masuk untuk mengelola operasional dan keuangan masjid.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Kelola data pegawai, penggajian, transaksi, dan laporan keuangan dalam satu sistem yang tertib.')

@section('content')
@if(session('admin_email'))
    <div class="auth-alert auth-alert-success" role="status">
        <div class="auth-alert-icon" aria-hidden="true">✓</div>
        <div>
            <p class="auth-alert-title">Akun admin berhasil dibuat</p>
            <p class="auth-alert-message">
                Gunakan email berikut: <strong class="auth-block auth-break-all">{{ session('admin_email') }}</strong>
            </p>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('login.admin') }}" class="auth-form" data-loading-title="Memasuki portal admin...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">A</span>
        <div>
            <p class="auth-context-title">Akses khusus administrator</p>
            <p class="auth-context-copy">Pastikan Anda menggunakan akun admin resmi FINUS PUSDAI.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Email Admin <span class="auth-required">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="nama@AdminFinusPusdai.org"
               autocomplete="username" inputmode="email" required autofocus
               @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
        @error('email')<p class="auth-error" id="email-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-field-group">
        <label for="password" class="auth-label"><span class="auth-label-icon" aria-hidden="true">••</span>Password <span class="auth-required">*</span></label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Masukkan password" autocomplete="current-password" required
                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password" aria-pressed="false">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
            </button>
        </div>
        @error('password')<p class="auth-error" id="password-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-form-row">
        <label class="auth-checkbox"><input type="checkbox" name="remember" value="1" @checked(old('remember'))>Ingat saya</label>
        <a href="{{ route('password.request', ['portal' => 'admin']) }}" class="auth-link auth-link-small">Lupa password?</a>
    </div>

    <button type="submit" class="auth-button" data-loading-text="Sedang masuk..." data-loading-title="Memasuki portal admin...">
        Masuk
    </button>

    @unless(\App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->exists())
        <p class="auth-footer-text">Admin belum dibuat. <a href="{{ route('register.admin') }}" class="auth-link">Daftar admin</a></p>
    @endunless
</form>
@endsection

{{-- FINUS DARK MODE LOCAL: auth/login-admin.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="auth/login-admin.blade.php">
html[data-finus-theme="dark"] body .auth-panel { background:linear-gradient(160deg,#0F1813 0%,#0B120E 100%) !important; }
html[data-finus-theme="dark"] body .auth-panel-inner { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-title,.auth-context-title,.auth-label,.auth-dialog-title) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-copy,.auth-footer-text,.auth-help,.auth-code-copy,.auth-dialog-copy,.auth-checkbox) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .auth-field { border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .auth-field:focus { border-color:#64DD81 !important; background:#0F1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.12) !important; }
html[data-finus-theme="dark"] body :where(.auth-info-card,.auth-email-preview,.auth-dialog-details) { border-color:#293D31 !important; background:#101B14 !important; color:#C7D6CC !important; }
</style>
@endpush


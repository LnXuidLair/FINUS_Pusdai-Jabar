@extends('layouts.guest')

@section('portal', 'staff')

@section('title', 'FINUS | Login Pegawai')
@section('header-title', 'Selamat Datang Pegawai')
@section('panel-eyebrow', 'Akses Pegawai')
@section('panel-title', 'Login Pegawai')
@section('panel-copy', 'Gunakan akun pegawai yang sudah diaktivasi.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Akses dashboard dan aktivitas kerja sesuai jabatan yang ditetapkan oleh admin.')

@section('content')
@if(session('account_activated'))
    <div class="auth-dialog-overlay" id="activatedModal" data-dismissible="true">
        <div class="auth-dialog" role="dialog" aria-modal="true" aria-labelledby="activated-title">
            <div class="auth-dialog-body">
                <div class="auth-dialog-icon" aria-hidden="true">✓</div>
                <h3 class="auth-dialog-title" id="activated-title">Akun Berhasil Diaktifkan</h3>
                <p class="auth-dialog-copy">Akun pegawai telah aktif. Silakan masuk menggunakan email dan password baru.</p>
            </div>
            <div class="auth-dialog-footer">
                <button type="button" class="auth-button" onclick="document.getElementById('activatedModal')?.remove()" data-no-loading>
                    Lanjut ke Login
                </button>
            </div>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('login.staff') }}" class="auth-form" data-loading-title="Memasuki portal pegawai...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">P</span>
        <div>
            <p class="auth-context-title">Portal operasional pegawai</p>
            <p class="auth-context-copy">Akun hanya dapat dipakai setelah data pegawai diverifikasi dan password yang telah dibuat.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Email Pegawai <span class="auth-required">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="nama@staffpusdai.finus.id"
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
        <a href="{{ route('password.request', ['portal' => 'pegawai']) }}" class="auth-link auth-link-small">Lupa password?</a>
    </div>

    <button type="submit" class="auth-button" data-loading-text="Sedang masuk..." data-loading-title="Memasuki portal pegawai...">
        Masuk
    </button>

    <p class="auth-footer-text">
        Belum aktivasi akun?
        <a href="{{ route('register.staff') }}" class="auth-link">Verifikasi pegawai</a>
    </p>
</form>
@endsection

{{-- FINUS DARK MODE LOCAL: auth/login-staff.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="auth/login-staff.blade.php">
html[data-finus-theme="dark"] body .auth-panel { background:linear-gradient(160deg,#0F1813 0%,#0B120E 100%) !important; }
html[data-finus-theme="dark"] body .auth-panel-inner { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-title,.auth-context-title,.auth-label,.auth-dialog-title) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-copy,.auth-footer-text,.auth-help,.auth-code-copy,.auth-dialog-copy,.auth-checkbox) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .auth-field { border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .auth-field:focus { border-color:#64DD81 !important; background:#0F1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.12) !important; }
html[data-finus-theme="dark"] body :where(.auth-info-card,.auth-email-preview,.auth-dialog-details) { border-color:#293D31 !important; background:#101B14 !important; color:#C7D6CC !important; }
</style>
@endpush


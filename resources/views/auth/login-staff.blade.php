@extends('layouts.guest')

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

<form method="POST" action="{{ route('login.staff') }}" class="auth-form" data-loading-title="Memeriksa akun pegawai...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">P</span>
        <div>
            <p class="auth-context-title">Portal operasional pegawai</p>
            <p class="auth-context-copy">Akun hanya dapat dipakai setelah data pegawai diverifikasi dan password dibuat.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Email Pegawai <span class="auth-required">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="nama@StaffFinusPusdai.ac.id"
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
        <a href="{{ route('password.request') }}" class="auth-link auth-link-small">Lupa password?</a>
    </div>

    <button type="submit" class="auth-button" data-loading-text="Sedang masuk..." data-loading-title="Memeriksa akun pegawai...">
        Masuk
    </button>

    <p class="auth-footer-text">
        Belum aktivasi akun?
        <a href="{{ route('register.staff') }}" class="auth-link">Verifikasi pegawai</a>
    </p>
</form>
@endsection
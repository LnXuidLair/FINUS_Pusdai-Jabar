@extends('layouts.guest')

@section('portal', 'jamaah')

@section('title', 'FINUS | Login Jamaah')
@section('header-title', 'Selamat Datang Jamaah')
@section('panel-eyebrow', 'Akses Jamaah')
@section('panel-title', 'Login Jamaah')
@section('panel-copy', 'Masuk menggunakan email yang telah diverifikasi.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Terhubung dengan informasi dan layanan keuangan Masjid PUSDAI secara mudah dan transparan.')

@section('content')
<form method="POST" action="{{ route('login.jamaah') }}" class="auth-form" data-loading-title="Memasuki dashboard...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">J</span>
        <div>
            <p class="auth-context-title">Portal layanan jamaah</p>
            <p class="auth-context-copy">Gunakan akun yang sudah menyelesaikan verifikasi email.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Alamat Email <span class="auth-required">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="auth-field" placeholder="nama@email.com"
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
        <a href="{{ route('password.request', ['portal' => 'jamaah']) }}" class="auth-link auth-link-small">Lupa password?</a>
    </div>

    <button type="submit" class="auth-button" data-loading-text="Sedang masuk..." data-loading-title="Memasuki dashboard...">
        Masuk
    </button>

    <p class="auth-footer-text">
        Belum memiliki akun?
        <a href="{{ route('register.jamaah') }}" class="auth-link">Daftar sekarang</a>
    </p>
</form>
@endsection
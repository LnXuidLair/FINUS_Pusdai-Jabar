@extends('layouts.guest')

@section('title', 'FINUS | Login Jamaah')
@section('header-title', 'Selamat Datang Jamaah')
@section('panel-title', 'Login Jamaah')
@section('panel-copy', 'Masuk menggunakan Email yang telah diverifikasi.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Terhubung dengan informasi dan layanan keuangan Masjid PUSDAI secara transparan.')

@section('content')
<form method="POST" action="{{ route('login.jamaah') }}" class="space-y-4">
    @csrf
    <div>
        <label for="email" class="mb-1 block text-sm font-bold text-green-950">Alamat Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
            class="auth-field" placeholder="nama@email.com" autocomplete="email" required autofocus>
        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password" class="mb-1 block text-sm font-bold text-green-950">Password</label>
        <div class="relative">
            <input id="password" type="password" name="password" class="auth-field pr-12" placeholder="Masukkan Password" autocomplete="current-password" required>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan Password" class="absolute right-4 top-1/2 -translate-y-1/2">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="Tampilkan Password" class="h-6 w-6 cursor-pointer select-none">
            </button>
        </div>
        @error('password')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <div class="flex items-center justify-between gap-3 text-sm">
        <label class="flex items-center gap-2 text-slate-700">
            <input type="checkbox" name="remember" class="rounded border-green-700 text-green-700">
            Ingat saya
        </label>
        <a href="{{ route('password.request') }}" class="auth-link">Lupa password?</a>
    </div>
    <button type="submit" class="auth-button">Masuk</button>
    <p class="text-center text-sm text-slate-600">
        Belum punya akun? <a href="{{ route('register.jamaah') }}" class="auth-link">Daftar sekarang</a>
    </p>
</form>
@endsection
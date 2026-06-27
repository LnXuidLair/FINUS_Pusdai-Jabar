@extends('layouts.guest')

@section('title', 'FINUS | Login Jamaah')
@section('header-title', 'Selamat Datang Jamaah')
@section('panel-title', 'Login Jamaah')
@section('panel-copy', 'Masuk menggunakan akun Gmail yang telah diverifikasi.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Terhubung dengan informasi dan layanan keuangan Masjid PUSDAI secara transparan.')

@section('content')
<form method="POST" action="{{ route('login.jamaah') }}" class="space-y-4">
    @csrf
    <div>
        <label for="email" class="mb-1 block text-sm font-bold text-green-950">Alamat Gmail</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
            class="auth-field" placeholder="nama@gmail.com" autocomplete="email" required autofocus>
        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password" class="mb-1 block text-sm font-bold text-green-950">Password</label>
        <input id="password" type="password" name="password" class="auth-field"
            placeholder="Masukkan password" autocomplete="current-password" required>
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
        Belum punya akun? <a href="{{ route('register.jamaah') }}" class="auth-link">Daftar jamaah</a>
    </p>
</form>
@endsection

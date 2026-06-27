@extends('layouts.guest')

@section('title', 'FINUS | Daftar Jamaah')
@section('header-title', 'Registrasi Jamaah')
@section('panel-title', 'Buat Akun Jamaah')
@section('panel-copy', 'Gunakan Gmail aktif untuk menerima tautan verifikasi.')
@section('hero-title', 'Jamaah Masjid PUSDAI')
@section('hero-copy', 'Daftarkan akun untuk mengakses layanan jamaah dengan identitas email yang terverifikasi.')

@section('content')
<form method="POST" action="{{ route('register.jamaah.post') }}" class="space-y-4">
    @csrf
    <div>
        <label for="email" class="mb-1 block text-sm font-bold text-green-950">Alamat Gmail</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
            class="auth-field" placeholder="nama@gmail.com"
            pattern="[A-Za-z0-9._%+\-]+@gmail\.com" autocomplete="email" required autofocus>
        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        <p class="mt-1 text-xs text-slate-600">Gunakan alamat @gmail.com yang aktif.</p>
    </div>

    <div>
        <label for="password" class="mb-1 block text-sm font-bold text-green-950">Password</label>
        <input id="password" type="password" name="password" class="auth-field"
            placeholder="Minimal 8 karakter" autocomplete="new-password" required>
        @error('password')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password_confirmation" class="mb-1 block text-sm font-bold text-green-950">Konfirmasi Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation"
            class="auth-field" placeholder="Ulangi password" autocomplete="new-password" required>
    </div>

    <button type="submit" class="auth-button">Buat Akun dan Verifikasi</button>
    <p class="text-center text-sm text-slate-600">
        Sudah memiliki akun? <a href="{{ route('login.jamaah') }}" class="auth-link">Masuk</a>
    </p>
</form>
@endsection

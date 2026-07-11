@extends('layouts.guest')

@section('title', 'FINUS | Login Admin')
@section('header-title', 'Selamat Datang Admin')
@section('panel-title', 'Login Admin')
@section('panel-copy', 'Masuk untuk mengelola operasional dan keuangan masjid.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Kelola data pegawai, penggajian, transaksi, dan laporan keuangan dalam satu sistem.')

@section('content')
@if(session('admin_email'))
    <div class="mb-5 rounded-xl border border-green-300 bg-green-50 p-4 text-center">
        <p class="font-bold text-green-800">Akun admin berhasil dibuat</p>
        <p class="mt-1 text-sm text-gray-700">Gunakan email berikut untuk login:</p>
        <p class="mt-2 break-all font-bold text-green-900">{{ session('admin_email') }}</p>
    </div>
@endif
<form method="POST" action="{{ route('login.admin') }}" class="space-y-4">
    @csrf
    <div>
        <label for="email" class="mb-1 block text-sm font-bold text-green-950">Email Admin</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
            class="auth-field" placeholder="nama@AdminFinusPusdai.org"
            autocomplete="email" required autofocus>
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
    @unless(\App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->exists())
        <p class="text-center text-sm text-slate-600">
            Admin belum dibuat. <a href="{{ route('register.admin') }}" class="auth-link">Daftar admin</a>
        </p>
    @endunless
</form>
@endsection
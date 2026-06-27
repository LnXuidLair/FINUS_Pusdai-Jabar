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
            class="auth-field" placeholder="nama@AdminFinusPusdai.ac.id"
            autocomplete="email" required autofocus>
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

    @unless(\App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->exists())
        <p class="text-center text-sm text-slate-600">
            Admin belum dibuat. <a href="{{ route('register.admin') }}" class="auth-link">Daftar admin</a>
        </p>
    @endunless
</form>
@endsection

@extends('layouts.guest')

@section('title', 'FINUS | Login Pegawai')
@section('header-title', 'Selamat Datang Pegawai')
@section('panel-title', 'Login Pegawai')
@section('panel-copy', 'Gunakan akun pegawai yang sudah diaktivasi.')
@section('hero-title', 'Masjid PUSDAI Jawa Barat')
@section('hero-copy', 'Akses dashboard dan aktivitas kerja sesuai jabatan yang ditetapkan oleh admin.')

@section('content')
@if(session('account_activated'))
<div id="activated-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-7 text-center shadow-2xl">
        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-3xl text-green-700">&#10003;</div>
        <h3 class="text-2xl font-extrabold text-green-800">Account Activated</h3>
        <p class="my-4 text-gray-600">Akun pegawai berhasil diaktifkan. Silakan login.</p>
        <button type="button" onclick="document.getElementById('activated-modal').remove()" class="auth-button">
            Lanjut ke Login
        </button>
    </div>
</div>
@endif
<form method="POST" action="{{ route('login.staff') }}" class="space-y-4">
    @csrf
    <div>
        <label for="email" class="mb-1 block text-sm font-bold text-green-950">Email Pegawai</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
            class="auth-field" placeholder="nama@StaffFinusPusdai.ac.id"
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
    <p class="text-center text-sm text-slate-600">
        Belum aktivasi akun? <a href="{{ route('register.staff') }}" class="auth-link">Verifikasi pegawai</a>
    </p>
</form>
@endsection
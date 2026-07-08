@extends('layouts.guest')

@section('title', 'FINUS | Daftar Jamaah')
@section('header-title', 'Registrasi Jamaah')
@section('panel-title', 'Buat Akun Jamaah')
@section('panel-copy', 'Gunakan Email aktif untuk menerima tautan verifikasi.')
@section('hero-title', 'Jamaah Masjid PUSDAI')
@section('hero-copy', 'Daftarkan akun untuk mengakses layanan jamaah Masjid PUSDAI.')

@section('content')
<form method="POST" action="{{ route('register.jamaah.post') }}" class="space-y-4">
    @csrf
    <div>
        <label for="name" class="mb-1 block text-sm font-bold text-green-950">Nama Jamaah</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}"
            class="auth-field" placeholder="Masukkan nama lengkap"
            autocomplete="name" required autofocus>
        @error('name')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="email" class="mb-1 block text-sm font-bold text-green-950">Alamat Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
            class="auth-field" placeholder="nama@email.com"
            autocomplete="email" required>
        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        <p class="mt-1 text-xs text-slate-600">Jangan menggunakan email perusahaan atau instansi manapun.</p>
    </div>
    <div>
        <label for="password" class="mb-1 block text-sm font-bold text-green-950">Password</label>
        <div class="relative">
            <input id="password" type="password" name="password" class="auth-field pr-12" placeholder="Minimal 8 Karakter" autocomplete="new-password" required>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan Password" class="absolute right-4 top-1/2 -translate-y-1/2">
                <img src="{{asset('assets/images/ShowPassword.png')}}" alt="Tampilkan Password" class="h-6 w-6 cursor-pointer select-none">
            </button>
        </div>
        @error('password')<p class="auth-error">{{$message}}</p>@enderror
    </div>
    <div>
        <label for="password_confirmation" class="mb-1 block text-sm font-bold text-green-950">Konfirmasi Password</label>
        <div class="relative">
            <input id="password_confirmation" type="password" name="password_confirmation"
                class="auth-field pr-12" placeholder="Ulangi Password" autocomplete="new-password" required>
            <button type="button" data-toggle-password data-target="#password_confirmation" aria-label="Tampilkan Password" class="absolute right-4 top-1/2 -translate-y-1/2">
                <img src="{{asset('assets/images/ShowPassword.png')}}" alt="Tampilkan Password" class="h-6 w-6 cursor-pointer select-none">
            </button>
        </div>
        @error('password_confirmation')<p class="auth-error">{{$message}}</p>@enderror
    </div>
    <button type="submit" class="auth-button">Buat Akun dan Verifikasi</button>
    <p class="text-center text-sm text-slate-600">
        Sudah memiliki akun? <a href="{{ route('login.jamaah') }}" class="auth-link">Masuk</a>
    </p>
</form>
@endsection
@extends('layouts.guest')

@section('title', 'FINUS | Aktivasi Akun Pegawai')
@section('header-title', 'Aktivasi Akun Pegawai')
@section('panel-title', 'Buat Password')
@section('panel-copy', 'Data berhasil diverifikasi. Selesaikan aktivasi akun.')
@section('hero-title', 'Akun Pegawai Terverifikasi')
@section('hero-copy', 'Buat password yang kuat untuk masuk ke dashboard sesuai jabatan Anda.')

@section('content')
<form method="POST" action="{{ route('register.staff.post') }}" class="space-y-4">
    @csrf
    <div>
        <label class="mb-1 block text-sm font-bold text-green-950">Email Pegawai</label>
        <input type="email" value="{{ $pegawai->email }}" class="auth-field" readonly>
    </div>
    <div>
        <label for="password" class="mb-1 block text-sm font-bold text-green-950">Password</label>
        <div class="relative">
            <input id="password" type="password" name="password" class="auth-field pr-12" placeholder="Minimal 8 Karakter" autocomplete="new-password" required autofocus>
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
    <button type="submit" class="auth-button">Aktifkan Akun</button>
</form>
@endsection
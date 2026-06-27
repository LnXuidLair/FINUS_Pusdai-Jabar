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
        <label for="password" class="mb-1 block text-sm font-bold text-green-950">Password Baru</label>
        <input id="password" type="password" name="password" class="auth-field"
            placeholder="Minimal 8 karakter" autocomplete="new-password" required autofocus>
        @error('password')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password_confirmation" class="mb-1 block text-sm font-bold text-green-950">Konfirmasi Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation"
            class="auth-field" placeholder="Ulangi password" autocomplete="new-password" required>
    </div>

    <button class="auth-button">Aktifkan Akun</button>
</form>
@endsection

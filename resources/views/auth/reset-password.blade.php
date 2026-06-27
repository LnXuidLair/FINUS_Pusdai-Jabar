@extends('layouts.guest')

@section('title', 'FINUS | Reset Password')
@section('header-title', 'Pemulihan Akun')
@section('panel-title', 'Buat Password Baru')
@section('panel-copy', 'Gunakan password baru untuk mengamankan akun.')
@section('hero-title', 'Keamanan Akun FINUS')
@section('hero-copy', 'Selesaikan proses reset password untuk memperoleh kembali akses ke akun Anda.')

@section('content')
<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div>
        <label for="email" class="mb-1 block text-sm font-bold text-green-950">Alamat Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" class="auth-field" required>
        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password" class="mb-1 block text-sm font-bold text-green-950">Password Baru</label>
        <input id="password" type="password" name="password" class="auth-field" placeholder="Minimal 8 karakter" required>
        @error('password')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password_confirmation" class="mb-1 block text-sm font-bold text-green-950">Konfirmasi Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="auth-field"
            placeholder="Ulangi password" required>
    </div>
    <button class="auth-button">Simpan Password</button>
</form>
@endsection

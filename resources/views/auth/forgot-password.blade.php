@extends('layouts.guest')

@section('title', 'FINUS | Lupa Password')
@section('header-title', 'Pemulihan Akun')
@section('panel-title', 'Lupa Password')
@section('panel-copy', 'Kami akan mengirim tautan reset ke email akun Anda.')
@section('hero-title', 'Pemulihan Akun FINUS')
@section('hero-copy', 'Masukkan email terdaftar untuk mendapatkan tautan pembuatan password baru.')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf
    <div>
        <label for="email" class="mb-1 block text-sm font-bold text-green-950">Alamat Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
            class="auth-field" placeholder="Alamat email terdaftar" required autofocus>
        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <button class="auth-button">Kirim Tautan Reset</button>
    <p class="text-center text-sm text-slate-600"><a href="{{ route('home') }}" class="auth-link">Kembali ke beranda</a></p>
</form>
@endsection

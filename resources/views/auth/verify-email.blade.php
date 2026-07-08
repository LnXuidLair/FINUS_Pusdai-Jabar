@extends('layouts.guest')

@section('title', 'FINUS | Verifikasi Kode')
@section('header-title', 'Verifikasi Akun Jamaah')
@section('panel-title', 'Masukkan Kode')
@section('panel-copy', 'Kode verifikasi telah dikirim ke email Anda.')
@section('hero-title', 'Verifikasi Akun Jamaah')
@section('hero-copy', 'Masukkan kode verifikasi agar akun jamaah dapat digunakan.')

@section('content')
<div class="text-center">
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-2xl text-green-700">✉</div>
    <p class="mb-5 text-sm leading-relaxed text-gray-700">Kami telah mengirim kode verifikasi 6 digit ke email Anda. Masukkan kode tersebut untuk mengaktifkan akun jamaah.</p>
    <form method="POST" action="{{ route('verification.code.verify') }}" class="space-y-4">
        @csrf
        <div class="text-left">
            <label for="code" class="mb-1 block text-sm font-bold text-green-950">Kode Verifikasi</label>
            <input id="code" type="text" name="code" value="{{ old('code') }}" class="auth-field text-center text-xl font-extrabold tracking-[0.35em]" placeholder="000000" inputmode="numeric" maxlength="6" required autofocus>
            @error('code')<p class="auth-error text-center">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="auth-button">Verifikasi Akun</button>
    </form>
    <form method="POST" action="{{ route('verification.send') }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full rounded-lg border border-green-700 py-3 font-bold text-green-800 hover:bg-green-50">
            Kirim Ulang Kode
        </button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full rounded-lg border border-slate-300 py-3 font-bold text-slate-700 hover:bg-slate-50">
            Keluar
        </button>
    </form>
</div>
@endsection
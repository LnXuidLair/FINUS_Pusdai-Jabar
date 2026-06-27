@extends('layouts.guest')

@section('title', 'FINUS | Verifikasi Email')
@section('header-title', 'Verifikasi Email Jamaah')
@section('panel-title', 'Periksa Gmail Anda')
@section('panel-copy', 'Verifikasi diperlukan sebelum masuk ke dashboard jamaah.')
@section('hero-title', 'Verifikasi Akun Jamaah')
@section('hero-copy', 'Tautan verifikasi menjaga agar akun jamaah menggunakan alamat Gmail yang valid.')

@section('content')
<div class="text-center">
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-2xl text-green-700">&#9993;</div>
    <p class="mb-5 text-sm leading-relaxed text-gray-700">
        Tautan verifikasi telah dikirim ke Gmail Anda. Buka email tersebut sebelum mengakses dashboard jamaah.
    </p>
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="auth-button">Kirim Ulang Verifikasi</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button class="w-full rounded-lg border border-green-700 py-3 font-bold text-green-800 hover:bg-green-50">Keluar</button>
    </form>
</div>
@endsection

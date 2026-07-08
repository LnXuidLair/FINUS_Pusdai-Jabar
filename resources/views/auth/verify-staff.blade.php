@extends('layouts.guest')

@section('title', 'FINUS | Verifikasi Pegawai')
@section('header-title', 'Verifikasi Pegawai')
@section('panel-title', 'Verifikasi Data')
@section('panel-copy', 'Cocokkan identitas dengan data yang dibuat admin.')
@section('hero-title', 'Aktivasi Pegawai FINUS')
@section('hero-copy', 'Nama dan NIP harus sama dengan data pegawai yang telah didaftarkan oleh admin.')

@section('content')
<form method="POST" action="{{ route('verify.staff') }}" class="space-y-4">
    @csrf
    <div>
        <label for="name" class="mb-1 block text-sm font-bold text-green-950">Nama Lengkap</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}"
            class="auth-field" placeholder="Nama sesuai data pegawai" required autofocus>
        @error('name')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="nip" class="mb-1 block text-sm font-bold text-green-950">NIP</label>
        <input id="nip" type="text" name="nip" value="{{ old('nip') }}"
            class="auth-field" placeholder="Nomor induk pegawai" required>
        @error('nip')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <button class="auth-button">Verifikasi</button>
    <p class="text-center text-sm text-slate-600">
        Akun sudah aktif? <a href="{{ route('login.staff') }}" class="auth-link">Kembali ke login</a>
    </p>
</form>
@isset($verifiedPegawai)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-7 text-center shadow-2xl">
        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-3xl text-green-700">&#10003;</div>
        <h3 class="text-2xl font-extrabold text-green-800">Verify Success</h3>
        <dl class="my-5 space-y-3 rounded-xl bg-green-50 p-4 text-left">
            <div><dt class="text-xs text-gray-500">Nama Pegawai</dt><dd class="font-bold">{{ $verifiedPegawai->nama_pegawai }}</dd></div>
            <div><dt class="text-xs text-gray-500">NIP</dt><dd class="font-bold">{{ $verifiedPegawai->nip }}</dd></div>
            <div><dt class="text-xs text-gray-500">Jabatan</dt><dd class="font-bold">{{ $verifiedPegawai->jabatan }}</dd></div>
        </dl>
        <a href="{{ route('register.staff.account') }}" class="auth-button">Lanjut</a>
    </div>
</div>
@endisset
@endsection
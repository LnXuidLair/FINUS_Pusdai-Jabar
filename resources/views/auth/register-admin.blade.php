@extends('layouts.guest')

@section('title', 'FINUS | Daftar Admin')
@section('header-title', 'Registrasi Admin')
@section('panel-title', 'Buat Akun Admin')
@section('panel-copy', 'Akun admin hanya dapat dibuat satu kali.')
@section('hero-title', 'Admin FINUS PUSDAI')
@section('hero-copy', 'Buat akun pengelola utama sebelum menyiapkan data pegawai, jabatan, gaji, dan laporan.')

@section('content')
<form method="POST" action="{{ route('register.admin.post') }}" class="space-y-4">
    @csrf
    <div>
        <label for="admin-name" class="mb-1 block text-sm font-bold text-green-950">Nama Admin</label>
        <input id="admin-name" type="text" name="name" value="{{ old('name') }}"
            class="auth-field" placeholder="Nama lengkap admin" required autofocus>
        @error('name')<p class="auth-error">{{ $message }}</p>@enderror
        <p class="mt-2 text-xs text-slate-600">
            Email login: <strong id="admin-email-preview" class="text-green-800">nama@AdminFinusPusdai.org</strong>
        </p>
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
    <button type="submit" class="auth-button">Buat Akun</button>
</form>
<script>
(() => {
    const input = document.getElementById('admin-name');
    const preview = document.getElementById('admin-email-preview');
    const update = () => {
        const local = input.value
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .toLowerCase().trim().replace(/[^a-z0-9]+/g, '.').replace(/^\.+|\.+$/g, '');
        preview.textContent = `${local || 'nama'}@AdminFinusPusdai.org`;
    };
    input.addEventListener('input', update);
    update();
})();
</script>
@endsection
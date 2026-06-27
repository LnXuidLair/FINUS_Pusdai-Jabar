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
            Email login: <strong id="admin-email-preview" class="text-green-800">nama@AdminFinusPusdai.ac.id</strong>
        </p>
    </div>

    <div>
        <label for="password" class="mb-1 block text-sm font-bold text-green-950">Password</label>
        <input id="password" type="password" name="password" class="auth-field"
            placeholder="Minimal 8 karakter" autocomplete="new-password" required>
        @error('password')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <button class="auth-button">Buat Akun Admin</button>
</form>

<script>
(() => {
    const input = document.getElementById('admin-name');
    const preview = document.getElementById('admin-email-preview');
    const update = () => {
        const local = input.value
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .toLowerCase().trim().replace(/[^a-z0-9]+/g, '.').replace(/^\.+|\.+$/g, '');
        preview.textContent = `${local || 'nama'}@AdminFinusPusdai.ac.id`;
    };
    input.addEventListener('input', update);
    update();
})();
</script>
@endsection

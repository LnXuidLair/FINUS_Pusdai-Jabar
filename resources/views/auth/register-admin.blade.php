@extends('layouts.guest')

@section('title', 'FINUS | Daftar Admin')
@section('header-title', 'Registrasi Admin')
@section('panel-title', 'Buat Akun Admin')
@section('panel-copy', 'Akun admin hanya dapat dibuat satu kali.')
@section('hero-title', 'Admin FINUS PUSDAI')
@section('hero-copy', 'Buat akun pengelola utama sebelum menyiapkan data pegawai, jabatan, gaji, dan laporan.')

@section('content')
<form method="POST" action="{{ route('register.admin.post') }}" class="auth-form" data-loading-title="Membuat akun admin...">
    @csrf

    <div class="auth-info-card">
        Akun ini akan menjadi pengelola utama FINUS dan hanya dapat dibuat satu kali.
    </div>

    <div>
        <label for="admin-name" class="auth-label"><span class="auth-label-icon">◆</span>Nama Admin</label>
        <input id="admin-name" type="text" name="name" value="{{ old('name') }}"
               class="auth-field" placeholder="Nama lengkap admin"
               autocomplete="name" required autofocus>
        @error('name')<p class="auth-error">{{ $message }}</p>@enderror

        <div class="auth-email-preview">
            Email login dibuat otomatis:
            <strong id="admin-email-preview">nama@AdminFinusPusdai.org</strong>
        </div>
    </div>

    <div>
        <label for="password" class="auth-label"><span class="auth-label-icon">●</span>Password</label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Minimal 8 karakter" autocomplete="new-password"
                   data-password-strength="adminStrength" required>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="Tampilkan password">
            </button>
        </div>

        <div class="auth-password-strength" id="adminStrength">
            <div class="auth-strength-bars">
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
            </div>
            <span class="auth-strength-text" data-strength-text>Masukkan minimal 8 karakter</span>
        </div>

        @error('password')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password_confirmation" class="auth-label"><span class="auth-label-icon">✓</span>Konfirmasi Password</label>
        <div class="auth-input-wrap">
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="auth-field auth-password-field" placeholder="Ulangi password"
                   autocomplete="new-password" required>
            <button type="button" data-toggle-password data-target="#password_confirmation" aria-label="Tampilkan password">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="Tampilkan password">
            </button>
        </div>
        @error('password_confirmation')<p class="auth-error">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Membuat akun..." data-loading-title="Membuat akun admin...">
        Buat Akun Admin
    </button>
</form>
@endsection

@push('scripts')
<script>
(() => {
    const input = document.getElementById('admin-name');
    const preview = document.getElementById('admin-email-preview');
    if (!input || !preview) return;

    const update = () => {
        const local = input.value
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '.')
            .replace(/^\.+|\.+$/g, '');

        preview.textContent = `${local || 'nama'}@AdminFinusPusdai.org`;
    };

    input.addEventListener('input', update);
    update();
})();
</script>
@endpush
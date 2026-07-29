@extends('layouts.guest')

@section('title', 'FINUS | Daftar Admin')
@section('header-title', 'Registrasi Admin')
@section('panel-eyebrow', 'Penyiapan Sistem')
@section('panel-title', 'Buat Akun Admin')
@section('panel-copy', 'Akun admin hanya dapat dibuat satu kali.')
@section('hero-title', 'Admin FINUS PUSDAI')
@section('hero-copy', 'Buat akun pengelola utama sebelum menyiapkan data pegawai, jabatan, gaji, dan laporan.')

@section('content')
<form method="POST" action="{{ route('register.admin.post') }}" class="auth-form" data-loading-title="Membuat akun admin...">
    @csrf

    <div class="auth-context-card">
        <span class="auth-context-icon" aria-hidden="true">1</span>
        <div>
            <p class="auth-context-title">Satu akun administrator utama</p>
            <p class="auth-context-copy">Pastikan nama dan password sudah benar sebelum akun dibuat.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="admin-name" class="auth-label"><span class="auth-label-icon" aria-hidden="true">Aa</span>Nama Admin <span class="auth-required">*</span></label>
        <input id="admin-name" type="text" name="name" value="{{ old('name') }}"
               class="auth-field" placeholder="Nama lengkap admin"
               autocomplete="name" required autofocus
               @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
        @error('name')<p class="auth-error" id="name-error" role="alert">{{ $message }}</p>@enderror

        <div class="auth-email-preview" aria-live="polite">
            Email login dibuat otomatis:
            <strong id="admin-email-preview">nama@AdminFinusPusdai.org</strong>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="password" class="auth-label"><span class="auth-label-icon" aria-hidden="true">••</span>Password <span class="auth-required">*</span></label>
        <div class="auth-input-wrap">
            <input id="password" type="password" name="password" class="auth-field auth-password-field"
                   placeholder="Minimal 8 karakter" autocomplete="new-password"
                   data-password-strength="adminStrength" required
                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            <button type="button" data-toggle-password data-target="#password" aria-label="Tampilkan password" aria-pressed="false">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
            </button>
        </div>

        <div class="auth-password-strength" id="adminStrength" aria-live="polite">
            <div class="auth-strength-bars" aria-hidden="true">
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
                <span class="auth-strength-bar" data-strength-bar></span>
            </div>
            <span class="auth-strength-text" data-strength-text>Masukkan minimal 8 karakter</span>
        </div>
        <p class="auth-help"><b>i</b>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol.</p>
        @error('password')<p class="auth-error" id="password-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-field-group">
        <label for="password_confirmation" class="auth-label"><span class="auth-label-icon" aria-hidden="true">✓</span>Konfirmasi Password <span class="auth-required">*</span></label>
        <div class="auth-input-wrap">
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="auth-field auth-password-field" placeholder="Ulangi password"
                   autocomplete="new-password" required data-password-match="#password" data-match-message="admin-password-match">
            <button type="button" data-toggle-password data-target="#password_confirmation" aria-label="Tampilkan password" aria-pressed="false">
                <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
            </button>
        </div>
        <p id="admin-password-match" class="auth-match-message" aria-live="polite"></p>
        @error('password_confirmation')<p class="auth-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Membuat akun..." data-loading-title="Membuat akun admin...">
        Buat Akun
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
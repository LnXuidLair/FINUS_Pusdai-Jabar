@extends('layouts.guest')

@section('portal', 'admin')

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
            <p class="auth-context-copy">Nama dapat diubah kembali melalui Profil. Email login Admin tetap dan tidak mengikuti perubahan nama.</p>
        </div>
    </div>

    <div class="auth-field-group">
        <label for="admin-name" class="auth-label"><span class="auth-label-icon" aria-hidden="true">Aa</span>Nama Admin <span class="auth-required">*</span></label>
        <input id="admin-name" type="text" name="name" value="{{ old('name') }}"
               class="auth-field" placeholder="Nama lengkap admin"
               autocomplete="name" required autofocus
               @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
        @error('name')<p class="auth-error" id="name-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="auth-field-group">
        <label for="admin-email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Email Admin</label>
        <input id="admin-email" type="email" class="auth-field"
               value="admin@pusdai.finus.id" readonly aria-readonly="true" tabindex="-1">
        <p class="auth-help"><b>i</b>Email Admin dibuat tetap oleh FINUS dan tidak berubah ketika nama Admin diubah.</p>
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

    <div class="auth-field-group">
        <label for="recovery_code" class="auth-label"><span class="auth-label-icon" aria-hidden="true">#</span>Recovery Code Admin <span class="auth-required">*</span></label>

        <div class="admin-recovery-row">
            <div class="auth-input-wrap">
                <input id="recovery_code" type="password" name="recovery_code"
                       value="{{ old('recovery_code') }}"
                       class="auth-field auth-password-field" placeholder="Generate Code atau ketik sendiri"
                       autocomplete="new-password" minlength="16" maxlength="64" required
                       @error('recovery_code') aria-invalid="true" aria-describedby="recovery-code-error" @enderror>
                <button type="button" data-toggle-password data-target="#recovery_code" aria-label="Tampilkan Recovery Code" aria-pressed="false">
                    <img src="{{ asset('assets/images/ShowPassword.png') }}" alt="">
                </button>
            </div>

            <button type="button" id="generate-admin-recovery" class="auth-button admin-recovery-generate"
                    data-generate-url="{{ route('register.admin.recovery-code.generate') }}">
                <i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i>
                <span>Generate Code</span>
            </button>
        </div>

        <p class="auth-help"><b>i</b>Anda boleh memakai Generate Code atau mengetik sendiri. Minimal 16 dan maksimal 64 karakter, tanpa spasi, serta wajib memiliki huruf besar, huruf kecil, angka, dan simbol.</p>
        <p id="recovery-generate-status" class="auth-help" aria-live="polite"></p>
        @error('recovery_code')<p class="auth-error" id="recovery-code-error" role="alert">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="auth-button" data-loading-text="Membuat akun..." data-loading-title="Membuat akun admin...">
        Buat Akun
    </button>
</form>
@endsection

@push('styles')
<style>
    .admin-recovery-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: stretch;
        gap: 10px;
    }

    .admin-recovery-row .auth-input-wrap {
        min-width: 0;
    }

    .admin-recovery-generate {
        width: auto !important;
        min-width: 150px;
        margin: 0 !important;
        padding-inline: 16px !important;
        white-space: nowrap;
    }

    .admin-recovery-generate i {
        margin-right: 6px;
    }

    @media (max-width: 575.98px) {
        .admin-recovery-row {
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
        }

        .admin-recovery-generate {
            min-width: 0;
            padding-inline: 12px !important;
        }

        .admin-recovery-generate span {
            display: none;
        }

        .admin-recovery-generate i {
            margin-right: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const button = document.getElementById('generate-admin-recovery');
    const recovery = document.getElementById('recovery_code');
    const status = document.getElementById('recovery-generate-status');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!button || !recovery || !csrf) return;

    button.addEventListener('click', async () => {
        if (button.disabled) return;

        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Membuat kode...';
        if (status) status.textContent = '';

        try {
            const response = await fetch(button.dataset.generateUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
            });

            if (!response.ok) {
                throw new Error('generate-failed');
            }

            const data = await response.json();
            const code = data.recovery_code || '';

            if (!code) {
                throw new Error('empty-code');
            }

            recovery.value = code;
            recovery.dispatchEvent(new Event('input', { bubbles: true }));

            if (status) {
                status.textContent = 'Recovery Code berhasil dibuat. Gunakan ikon mata untuk melihatnya dan simpan kode tersebut di tempat yang aman.';
            }

            recovery.focus({ preventScroll: true });
        } catch (error) {
            if (status) {
                status.textContent = 'Recovery Code belum berhasil dibuat. Silakan coba kembali atau ketik kode sendiri.';
            }
        } finally {
            button.disabled = false;
            button.textContent = originalText;
        }
    });
})();
</script>
@endpush
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

            <button
                type="button"
                id="generate-admin-recovery"
                class="auth-button admin-recovery-generate"
                data-generate-url="{{ route('register.admin.recovery-code.generate') }}"
                aria-label="Generate Recovery Code"
                title="Generate Recovery Code"
            >
                <span class="admin-recovery-generate-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M15.5 3.5 20.5 8.5M14 5l5 5M4 20l10.8-10.8M5.7 15.3l3 3M4.5 4.5v3M3 6h3M18 15v4M16 17h4" />
                    </svg>
                </span>
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
        grid-template-columns: minmax(0, 1fr) 52px;
        align-items: center;
        gap: 10px;
        width: 100%;
    }

    .admin-recovery-row .auth-input-wrap {
        width: 100%;
        min-width: 0;
    }

    .admin-recovery-generate {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 52px !important;
        min-width: 52px !important;
        max-width: 52px !important;
        height: 52px !important;
        min-height: 52px !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 14px !important;
        line-height: 1 !important;
        flex: 0 0 52px;
    }

    .admin-recovery-generate-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 21px;
        height: 21px;
        flex: 0 0 21px;
    }

    .admin-recovery-generate-icon svg {
        display: block;
        width: 21px;
        height: 21px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .admin-recovery-generate:hover,
    .admin-recovery-generate:focus-visible {
        transform: translateY(-1px);
    }

    .admin-recovery-generate:focus-visible {
        outline: 3px solid rgba(23, 155, 64, .22);
        outline-offset: 2px;
    }

    .admin-recovery-generate:disabled {
        cursor: wait;
        opacity: .78;
        transform: none;
    }

    .admin-recovery-generate.is-loading .admin-recovery-generate-icon {
        width: 21px;
        height: 21px;
        border: 2px solid rgba(255, 255, 255, .45);
        border-top-color: currentColor;
        border-radius: 50%;
        animation: adminRecoverySpin .7s linear infinite;
    }

    .admin-recovery-generate.is-loading .admin-recovery-generate-icon svg {
        display: none;
    }

    @keyframes adminRecoverySpin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 575.98px) {
        .admin-recovery-row {
            grid-template-columns: minmax(0, 1fr) 50px;
            gap: 8px;
        }

        .admin-recovery-generate {
            width: 50px !important;
            min-width: 50px !important;
            max-width: 50px !important;
            height: 50px !important;
            min-height: 50px !important;
            border-radius: 13px !important;
        }

        .admin-recovery-generate-icon,
        .admin-recovery-generate-icon svg {
            width: 20px;
            height: 20px;
        }

        .admin-recovery-generate-icon {
            flex-basis: 20px;
        }
    }

    @media (max-width: 359.98px) {
        .admin-recovery-row {
            grid-template-columns: minmax(0, 1fr) 46px;
            gap: 7px;
        }

        .admin-recovery-generate {
            width: 46px !important;
            min-width: 46px !important;
            max-width: 46px !important;
            height: 46px !important;
            min-height: 46px !important;
            border-radius: 12px !important;
        }

        .admin-recovery-generate-icon,
        .admin-recovery-generate-icon svg {
            width: 18px;
            height: 18px;
        }

        .admin-recovery-generate-icon {
            flex-basis: 18px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .admin-recovery-generate {
            transition: none !important;
        }

        .admin-recovery-generate.is-loading .admin-recovery-generate-icon {
            animation-duration: 1.4s;
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

        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.classList.add('is-loading');

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
                status.textContent = 'Recovery Code berhasil dibuat. Silakan simpan kode tersebut di tempat yang aman.';
            }

            recovery.focus({ preventScroll: true });
        } catch (error) {
            if (status) {
                status.textContent = 'Recovery Code belum berhasil dibuat. Silakan coba kembali atau ketik kode sendiri.';
            }
        } finally {
            button.disabled = false;
            button.removeAttribute('aria-busy');
            button.classList.remove('is-loading');
        }
    });
})();
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: auth/register-admin.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="auth/register-admin.blade.php">
html[data-finus-theme="dark"] body .auth-panel { background:linear-gradient(160deg,#0F1813 0%,#0B120E 100%) !important; }
html[data-finus-theme="dark"] body .auth-panel-inner { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-title,.auth-context-title,.auth-label,.auth-dialog-title) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.auth-panel-copy,.auth-footer-text,.auth-help,.auth-code-copy,.auth-dialog-copy,.auth-checkbox) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .auth-field { border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .auth-field:focus { border-color:#64DD81 !important; background:#0F1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.12) !important; }
html[data-finus-theme="dark"] body :where(.auth-info-card,.auth-email-preview,.auth-dialog-details) { border-color:#293D31 !important; background:#101B14 !important; color:#C7D6CC !important; }
</style>
@endpush


@extends('layouts.guest')

@section('portal', 'staff')
@section('title', 'FINUS | Aktivasi Pegawai Berhasil')
@section('header-title', 'Aktivasi Berhasil')
@section('panel-eyebrow', 'Akun Pegawai Aktif')
@section('panel-title', 'Simpan Recovery Code')
@section('panel-copy', 'Recovery Code hanya ditampilkan pada halaman ini.')
@section('hero-title', 'Akun Pegawai FINUS Siap Digunakan')
@section('hero-copy', 'Simpan Recovery Code untuk memulihkan akun apabila Anda lupa password.')

@section('content')
<div class="auth-context-card">
    <span class="auth-context-icon" aria-hidden="true">✓</span>
    <div>
        <p class="auth-context-title">Akun berhasil diaktifkan</p>
        <p class="auth-context-copy">Gunakan email pegawai dan password yang baru dibuat untuk login.</p>
    </div>
</div>

<div class="auth-field-group">
    <label for="staff-activation-email" class="auth-label">Email Pegawai</label>
    <input id="staff-activation-email" type="text" class="auth-field"
           value="{{ $activation['email'] }}" readonly aria-readonly="true">
</div>

<div class="auth-field-group">
    <p id="staff-recovery-label" class="auth-label">Recovery Code Pegawai</p>
    <div class="staff-recovery-code-box" aria-labelledby="staff-recovery-label">
        <code id="staff-recovery-code" class="staff-recovery-code-value">{{ $activation['recovery_code'] }}</code>
        <button type="button" id="copy-staff-recovery-code" class="staff-recovery-copy-button"
                aria-label="Salin Recovery Code">
            Salin
        </button>
    </div>
    <p id="staff-recovery-copy-status" class="auth-help" aria-live="polite"></p>
    <p class="auth-help"><b>i</b>Simpan kode ini sekarang. Setelah halaman ditinggalkan, kode tidak dapat dilihat lagi dari akun pegawai. Admin tetap dapat melihatnya melalui detail pegawai.</p>
</div>

<a href="{{ route('login.staff') }}" class="auth-button" style="text-decoration:none;">
    Saya Sudah Menyimpan Kode
</a>
@endsection

@push('styles')
<style>
    .staff-recovery-code-box {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;
        min-height: 54px;
        padding: 7px 7px 7px 15px;
        border: 1px solid #9dcfb0;
        border-radius: 13px;
        background: #f4faf6;
        box-shadow: 0 0 0 4px rgba(34, 168, 83, .08);
    }

    .staff-recovery-code-value {
        min-width: 0;
        color: #214c32;
        font-family: Consolas, "Courier New", monospace;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.45;
        letter-spacing: 0;
        overflow-wrap: anywhere;
        user-select: all;
    }

    .staff-recovery-copy-button {
        min-width: 62px;
        min-height: 40px;
        padding: 0 13px;
        border: 1px solid #b8dbc4;
        border-radius: 9px;
        background: #fff;
        color: #146b35;
        font: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .staff-recovery-copy-button:hover,
    .staff-recovery-copy-button:focus-visible {
        border-color: #179b40;
        background: #eaf8ef;
        outline: none;
    }

    @media (max-width: 389.98px) {
        .staff-recovery-code-box {
            grid-template-columns: 1fr;
            padding: 12px;
        }

        .staff-recovery-copy-button {
            width: 100%;
        }
    }

    html[data-finus-theme="dark"] body .staff-recovery-code-box {
        border-color: #315740;
        background: #0c1610;
        box-shadow: 0 0 0 4px rgba(100, 221, 129, .08);
    }

    html[data-finus-theme="dark"] body .staff-recovery-code-value {
        color: #e5f4e9;
    }

    html[data-finus-theme="dark"] body .staff-recovery-copy-button {
        border-color: #315740;
        background: #14251a;
        color: #9aebb0;
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const button = document.getElementById('copy-staff-recovery-code');
    const input = document.getElementById('staff-recovery-code');
    const status = document.getElementById('staff-recovery-copy-status');

    button?.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(input.textContent.trim());
            status.textContent = 'Recovery Code berhasil disalin.';
        } catch (_) {
            const selection = window.getSelection();
            const range = document.createRange();
            range.selectNodeContents(input);
            selection.removeAllRanges();
            selection.addRange(range);
            document.execCommand('copy');
            selection.removeAllRanges();
            status.textContent = 'Recovery Code berhasil disalin.';
        }
    });
})();
</script>
@endpush

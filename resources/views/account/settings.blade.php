@extends('layouts.app')

@section('title', 'Pengaturan Akun')
@section('hide-page-header', '1')

@section('content')
@include('layouts.partials.finus-ui')

@php
    $displayName = $pegawai?->nama_pegawai ?: $user->name;
    $displayEmail = in_array($user->role, [
        \App\Models\User::ROLE_ADMIN,
        \App\Models\User::ROLE_PEGAWAI,
    ], true)
        ? strtolower((string) $user->email)
        : $user->email;
    $lastPasswordChange = $user->password_changed_at
        ? $user->password_changed_at->format('d M Y, H:i')
        : 'Belum tercatat';
    $adminRecoveryCode = '';
    $initial = mb_strtoupper(mb_substr(trim($displayName ?: 'U'), 0, 1));

    if ($user->isAdmin()) {
        try {
            $adminRecoveryCode = (string) ($user->recovery_code ?? '');
        } catch (\Throwable) {
            $adminRecoveryCode = '';
        }
    }
@endphp

<div class="fmu-page account-settings-page">
    <section class="fmu-hero account-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-gear"></i></span>
            <div>
                <h1>Pengaturan Akun</h1>
                <p>Kelola password, Recovery Code, dan keamanan akun {{ $portalLabel }} Anda.</p>
            </div>
        </div>
    </section>

    @if(session('status') === 'password-updated')
        <div class="account-success-alert" role="status">
            <span class="account-success-icon"><i class="fa-solid fa-circle-check"></i></span>
            <div>
                <strong>Password berhasil diperbarui</strong>
                <p>Gunakan password terbaru pada login berikutnya.</p>
            </div>
        </div>
    @endif

    <div class="account-settings-grid">
        <section class="fmu-card account-main-card">
            <div class="fmu-card-head">
                <div class="fmu-card-head-main">
                    <span class="fmu-card-icon"><i class="fa-solid fa-shield-halved"></i></span>
                    <div>
                        <h2>Keamanan Akun</h2>
                        <p>Atur akses masuk dan metode pemulihan akun FINUS.</p>
                    </div>
                </div>
            </div>

            <div class="fmu-card-body account-security-list">
                <article class="account-security-item account-password-item">
                    <span class="account-security-icon"><i class="fa-solid fa-key"></i></span>
                    <div class="account-security-copy">
                        <div class="account-section-title-row">
                            <div>
                                <h3>Password</h3>
                                <p>Gunakan password yang kuat dan berbeda dari akun lain.</p>
                            </div>
                            <span class="account-meta-badge">
                                <i class="fa-regular fa-clock"></i>
                                {{ $lastPasswordChange }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route($passwordRoute) }}" class="fmu-btn fmu-btn-primary account-item-action">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Ubah Password
                    </a>
                </article>

                @if($user->isAdmin())
                    <article class="account-security-item account-recovery-item">
                        <div class="account-recovery-heading">
                            <span class="account-security-icon"><i class="fa-solid fa-shield-keyhole"></i></span>
                            <div class="account-security-copy">
                                <div class="account-section-title-row">
                                    <div>
                                        <h3>Recovery Code Admin</h3>
                                        <p>Gunakan kode ini untuk membantu pemulihan akses akun Admin.</p>
                                    </div>
                                    @if($adminRecoveryCode !== '')
                                        <span class="fmu-badge" style="--badge-color:#179B40;--badge-soft:#EAF8EE">Sudah dibuat</span>
                                    @else
                                        <span class="fmu-badge" style="--badge-color:#D97706;--badge-soft:#FFF7E6">Belum dibuat</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="account-recovery-note">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Recovery Code dapat dibuat otomatis dengan <strong>Generate Code</strong> atau diisi manual minimal {{ $adminRecoveryMinLength }} karakter.</span>
                        </div>

                        @if($adminRecoveryCode !== '')
                            <div class="account-field-block">
                                <label class="fmu-label" for="adminRecoveryCurrent">Recovery Code Saat Ini</label>
                                <div class="account-control-row">
                                    <div class="fmu-input-icon-wrap account-control-grow">
                                        <i class="fa-solid fa-shield-halved"></i>
                                        <input
                                            id="adminRecoveryCurrent"
                                            type="password"
                                            class="fmu-control"
                                            value="{{ $adminRecoveryCode }}"
                                            readonly
                                            autocomplete="off"
                                        >
                                    </div>
                                    <button type="button" class="fmu-btn account-icon-btn" data-recovery-toggle="#adminRecoveryCurrent" aria-label="Tampilkan Recovery Code" title="Tampilkan / sembunyikan Recovery Code">
                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="fmu-btn" data-copy-recovery="#adminRecoveryCurrent" title="Salin Recovery Code">
                                        <i class="fa-solid fa-copy" aria-hidden="true"></i>
                                        Salin
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="account-divider"></div>

                        <form method="POST" action="{{ route('admin.recovery-code.update') }}" class="account-recovery-form">
                            @csrf
                            @method('PATCH')

                            <div class="fmu-field">
                                <label class="fmu-label" for="recovery_code">
                                    Recovery Code Baru
                                    <span class="fmu-required">*</span>
                                </label>
                                <div class="account-control-row account-control-row-wrap">
                                    <div class="fmu-input-icon-wrap account-control-grow">
                                        <i class="fa-solid fa-key"></i>
                                        <input
                                            id="recovery_code"
                                            type="password"
                                            name="recovery_code"
                                            value=""
                                            class="fmu-control @error('recovery_code') is-invalid @enderror"
                                            minlength="{{ $adminRecoveryMinLength }}"
                                            maxlength="{{ $adminRecoveryMaxLength }}"
                                            placeholder="Generate Code atau ketik sendiri"
                                            autocomplete="new-password"
                                            required
                                        >
                                    </div>
                                    <button type="button" class="fmu-btn account-icon-btn" data-recovery-toggle="#recovery_code" aria-label="Tampilkan Recovery Code" title="Tampilkan / sembunyikan Recovery Code">
                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" id="generateAdminRecoveryCode" class="fmu-btn fmu-btn-primary account-generate-btn">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                                        Generate Code
                                    </button>
                                </div>
                                <span class="fmu-help">Minimal {{ $adminRecoveryMinLength }} dan maksimal {{ $adminRecoveryMaxLength }} karakter, tanpa spasi, serta wajib memiliki huruf besar, huruf kecil, angka, dan simbol.</span>
                                @error('recovery_code')<span class="fmu-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="fmu-field">
                                <label class="fmu-label" for="admin_recovery_current_password">
                                    Password Saat Ini
                                    <span class="fmu-required">*</span>
                                </label>
                                <div class="account-control-row">
                                    <div class="fmu-input-icon-wrap account-control-grow">
                                        <i class="fa-solid fa-lock"></i>
                                        <input
                                            id="admin_recovery_current_password"
                                            type="password"
                                            name="current_password"
                                            class="fmu-control @error('current_password') is-invalid @enderror"
                                            placeholder="Konfirmasi password Admin"
                                            autocomplete="current-password"
                                            required
                                        >
                                    </div>
                                    <button type="button" class="fmu-btn account-icon-btn" data-recovery-toggle="#admin_recovery_current_password" aria-label="Tampilkan password" title="Tampilkan / sembunyikan password">
                                        <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <span class="fmu-help">Password saat ini diperlukan sebelum Recovery Code dapat diganti.</span>
                                @error('current_password')<span class="fmu-error">{{ $message }}</span>@enderror
                            </div>

                            <div class="account-form-actions">
                                <button type="submit" class="fmu-btn fmu-btn-primary">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    Ganti Recovery Code
                                </button>
                            </div>
                        </form>
                    </article>
                @endif

                @if($user->isPegawai())
                    <article class="account-security-item account-employee-recovery">
                        <span class="account-security-icon account-security-icon-warning"><i class="fa-solid fa-shield-halved"></i></span>
                        <div class="account-security-copy">
                            <h3>Recovery Code Pegawai</h3>
                            <p>Recovery Code otomatis berubah setelah password berhasil diubah atau di-reset. Kode terbaru dapat dilihat Admin melalui Detail Pegawai.</p>
                        </div>
                    </article>
                @endif
            </div>
        </section>

        <aside class="fmu-card account-summary-card">
            <div class="account-summary-profile">
                <span class="account-summary-avatar">{{ $initial }}</span>
                <div>
                    <span class="account-summary-eyebrow">Akun Aktif</span>
                    <h2>{{ $displayName }}</h2>
                    <p>{{ $portalLabel }}</p>
                </div>
            </div>

            <div class="account-summary-rows">
                <div class="account-summary-row">
                    <span class="account-summary-row-icon"><i class="fa-solid fa-envelope"></i></span>
                    <div>
                        <small>Email</small>
                        <strong>{{ $displayEmail }}</strong>
                    </div>
                </div>
                <div class="account-summary-row">
                    <span class="account-summary-row-icon"><i class="fa-solid fa-shield-halved"></i></span>
                    <div>
                        <small>Status</small>
                        <strong>Akun aktif</strong>
                    </div>
                </div>
            </div>

            <div class="account-summary-tip">
                <i class="fa-solid fa-lock"></i>
                <p>Jaga password dan Recovery Code agar tidak dibagikan kepada pihak lain.</p>
            </div>
        </aside>
    </div>
</div>
@endsection

@if($user->isAdmin())
    @push('scripts')
    <script>
    (() => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const generateButton = document.getElementById('generateAdminRecoveryCode');
        const recoveryInput = document.getElementById('recovery_code');

        document.querySelectorAll('[data-recovery-toggle]').forEach(button => {
            const input = document.querySelector(button.dataset.recoveryToggle);
            const icon = button.querySelector('i');
            if (!input) return;

            button.addEventListener('click', () => {
                const willShow = input.type === 'password';
                input.type = willShow ? 'text' : 'password';
                button.setAttribute('aria-label', willShow ? 'Sembunyikan' : 'Tampilkan');

                if (icon) {
                    icon.classList.toggle('fa-eye', !willShow);
                    icon.classList.toggle('fa-eye-slash', willShow);
                }
            });
        });

        document.querySelectorAll('[data-copy-recovery]').forEach(button => {
            const input = document.querySelector(button.dataset.copyRecovery);
            if (!input) return;

            button.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(input.value);
                    const original = button.innerHTML;
                    button.innerHTML = '<i class="fa-solid fa-check" aria-hidden="true"></i> Tersalin';
                    window.setTimeout(() => { button.innerHTML = original; }, 1600);
                } catch (error) {
                    input.type = 'text';
                    input.select();
                    document.execCommand('copy');
                }
            });
        });

        generateButton?.addEventListener('click', async () => {
            if (!csrfToken || !recoveryInput) return;

            const originalHtml = generateButton.innerHTML;
            generateButton.disabled = true;
            generateButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Membuat...';

            try {
                const response = await fetch(@json(route('admin.recovery-code.generate')), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal membuat Recovery Code.');
                }

                const data = await response.json();
                recoveryInput.value = data.recovery_code || '';
                recoveryInput.type = 'text';

                const toggle = document.querySelector('[data-recovery-toggle="#recovery_code"]');
                const icon = toggle?.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }

                recoveryInput.focus();
                recoveryInput.select();
            } catch (error) {
                window.alert(error.message || 'Recovery Code tidak dapat dibuat.');
            } finally {
                generateButton.disabled = false;
                generateButton.innerHTML = originalHtml;
            }
        });
    })();
    </script>
    @endpush
@endif

@push('styles')
<style data-account-settings-ui>
.account-settings-grid{display:grid;grid-template-columns:minmax(0,1.48fr) minmax(285px,.52fr);gap:18px;align-items:start}
.account-main-card,.account-summary-card{box-shadow:0 16px 38px rgba(15,23,42,.065)}
.account-security-list{display:grid;gap:16px}
.account-security-item{border:1px solid #DDE9E1;border-radius:17px;background:#FBFDFC;padding:18px}
.account-password-item{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:14px;align-items:center}
.account-security-icon{display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:14px;background:#EAF8EE;color:#0E5423;font-size:17px;flex:0 0 auto}
.account-security-icon-warning{background:#FFF4D7;color:#A16207}
.account-security-copy{min-width:0}
.account-security-copy h3{margin:0;color:#173B24;font-size:16px;font-weight:900;line-height:1.35}
.account-security-copy p{margin:5px 0 0;color:#68796E;font-size:12.5px;line-height:1.65}
.account-section-title-row{display:flex;align-items:flex-start;justify-content:space-between;gap:14px}
.account-meta-badge{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border:1px solid #DCE8E0;border-radius:999px;background:#F4F9F5;color:#708178;font-size:10.5px;font-weight:800;white-space:nowrap}
.account-item-action{white-space:nowrap}
.account-recovery-item{padding:20px}
.account-recovery-heading{display:flex;gap:14px;align-items:flex-start}
.account-recovery-note{display:flex;gap:9px;margin:16px 0 0;padding:11px 13px;border:1px solid #D7E9DC;border-radius:12px;background:#F0F8F2;color:#526C5A;font-size:11.5px;line-height:1.55}
.account-recovery-note i{margin-top:2px;color:#179B40}
.account-field-block{margin-top:18px}
.account-control-row{display:flex;align-items:stretch;gap:8px}
.account-control-row-wrap{flex-wrap:nowrap}
.account-control-grow{flex:1;min-width:0}
.account-icon-btn{width:46px;min-width:46px;padding:0}
.account-generate-btn{white-space:nowrap}
.account-divider{height:1px;margin:20px 0;background:#E5ECE7}
.account-recovery-form{display:grid;gap:17px}
.account-form-actions{display:flex;justify-content:flex-end;padding-top:2px}
.account-success-alert{display:flex;align-items:center;gap:12px;margin-bottom:16px;padding:14px 16px;border:1px solid #B9E1C3;border-radius:15px;background:#F0FAF3;color:#166534}
.account-success-icon{display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:11px;background:#DDF4E3;color:#179B40;font-size:18px}
.account-success-alert strong{font-size:13px;font-weight:900}
.account-success-alert p{margin:2px 0 0;color:#4E7259;font-size:11.5px}
.account-summary-card{position:sticky;top:96px;overflow:hidden}
.account-summary-profile{display:flex;align-items:center;gap:14px;padding:21px;border-bottom:1px solid #E1EAE4;background:linear-gradient(180deg,#FFFFFF,#FAFDFB)}
.account-summary-avatar{display:inline-flex;align-items:center;justify-content:center;width:54px;height:54px;border-radius:16px;background:linear-gradient(135deg,#0E5423,#22BA51);color:#fff;font-size:20px;font-weight:900;box-shadow:0 10px 24px rgba(14,84,35,.16)}
.account-summary-eyebrow{display:block;margin-bottom:3px;color:#179B40;font-size:10px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}
.account-summary-profile h2{margin:0;color:#173B24;font-size:16px;font-weight:900;line-height:1.3;word-break:break-word}
.account-summary-profile p{margin:3px 0 0;color:#748278;font-size:11.5px}
.account-summary-rows{display:grid;gap:0;padding:6px 21px}
.account-summary-row{display:flex;gap:11px;align-items:center;padding:14px 0;border-bottom:1px solid #EDF2EE}
.account-summary-row:last-child{border-bottom:0}
.account-summary-row-icon{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:10px;background:#EAF8EE;color:#179B40;flex:0 0 auto}
.account-summary-row small{display:block;margin-bottom:3px;color:#819087;font-size:10.5px;font-weight:700}
.account-summary-row strong{display:block;color:#294536;font-size:12px;font-weight:850;word-break:break-word}
.account-summary-tip{display:flex;gap:10px;margin:5px 16px 16px;padding:12px 13px;border:1px solid #DCEAE1;border-radius:12px;background:#F5FAF6;color:#61756A}
.account-summary-tip i{margin-top:2px;color:#179B40}
.account-summary-tip p{margin:0;font-size:10.8px;line-height:1.55}
@media(max-width:991.98px){.account-settings-grid{grid-template-columns:1fr}.account-summary-card{position:static}.account-password-item{grid-template-columns:auto minmax(0,1fr)}.account-item-action{grid-column:1/-1;width:100%}}
@media(max-width:767.98px){.account-section-title-row{flex-direction:column}.account-control-row{flex-wrap:wrap}.account-control-grow{flex-basis:calc(100% - 54px)}.account-generate-btn{width:100%}.account-security-item{padding:16px}.account-recovery-item{padding:17px}.account-password-item{grid-template-columns:1fr}.account-security-icon{width:44px;height:44px}.account-form-actions .fmu-btn{width:100%}}
</style>
@endpush

@push('dark-styles')
<style data-finus-dark-local="account/settings.blade.php">
html[data-finus-theme="dark"] .account-settings-page{color:#EEF7F1}
html[data-finus-theme="dark"] .account-settings-page .fmu-card{border-color:#264033!important;background:#0D1D14!important;box-shadow:0 18px 42px rgba(0,0,0,.16)!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-card-head{border-color:#263D31!important;background:#102219!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-card-head h2{color:#F3F8F4!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-card-head p{color:#9CB0A3!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-card-icon{background:#123B20!important;color:#63DF82!important}
html[data-finus-theme="dark"] .account-security-item{border-color:#294336;background:#102219}
html[data-finus-theme="dark"] .account-security-copy h3{color:#F3F8F4}
html[data-finus-theme="dark"] .account-security-copy p{color:#A7B8AD}
html[data-finus-theme="dark"] .account-security-icon{background:#123B20;color:#67E487}
html[data-finus-theme="dark"] .account-security-icon-warning{background:#3A2B10;color:#F7C55B}
html[data-finus-theme="dark"] .account-meta-badge{border-color:#30483A;background:#14261B;color:#A9B9AF}
html[data-finus-theme="dark"] .account-recovery-note{border-color:#285037;background:#102B1A;color:#A8BCAD}
html[data-finus-theme="dark"] .account-divider{background:#294136}
html[data-finus-theme="dark"] .account-settings-page .fmu-label{color:#DDE9E1!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-help{color:#879D8F!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-control{border-color:#315040!important;background:#09170F!important;color:#EFF8F1!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-control::placeholder{color:#73897B!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-control[readonly]{background:#0A1810!important;color:#C8D8CD!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-input-icon-wrap>i{color:#67DE83!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-btn:not(.fmu-btn-primary){border-color:#365343!important;background:#122219!important;color:#EAF4ED!important;box-shadow:none!important}
html[data-finus-theme="dark"] .account-settings-page .fmu-btn:not(.fmu-btn-primary):hover{background:#183022!important;color:#FFFFFF!important}
html[data-finus-theme="dark"] .account-success-alert{border-color:#285D38;background:#102D1A;color:#7BE493}
html[data-finus-theme="dark"] .account-success-icon{background:#164326;color:#7BE493}
html[data-finus-theme="dark"] .account-success-alert p{color:#A6B9AC}
html[data-finus-theme="dark"] .account-summary-profile{border-color:#284033;background:#102219}
html[data-finus-theme="dark"] .account-summary-profile h2{color:#F1F7F3}
html[data-finus-theme="dark"] .account-summary-profile p{color:#9BAEA2}
html[data-finus-theme="dark"] .account-summary-rows{background:#0D1D14}
html[data-finus-theme="dark"] .account-summary-row{border-color:#263C31}
html[data-finus-theme="dark"] .account-summary-row-icon{background:#123B20;color:#69E289}
html[data-finus-theme="dark"] .account-summary-row small{color:#899D90}
html[data-finus-theme="dark"] .account-summary-row strong{color:#E5EFE8}
html[data-finus-theme="dark"] .account-summary-tip{border-color:#2A4436;background:#102219;color:#9DAFA4}
</style>
@endpush

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

    if ($user->isAdmin()) {
        try {
            $adminRecoveryCode = (string) ($user->recovery_code ?? '');
        } catch (\Throwable) {
            $adminRecoveryCode = '';
        }
    }
@endphp

<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-gear"></i></span>
            <div>
                <h1>Pengaturan Akun</h1>
                <p>Kelola keamanan dan akses akun {{ $portalLabel }} Anda.</p>
            </div>
        </div>
        <div class="fmu-hero-actions">
            <a href="{{ route($profileRoute) }}" class="fmu-btn" style="background:#fff;color:#0E5423!important">
                <i class="fa-solid fa-user"></i>
                Profil Saya
            </a>
        </div>
    </section>

    @if(session('status') === 'password-updated')
        <div class="fmu-card mb-3" style="border-color:#B7E4C1;background:#F0FBF3">
            <div class="fmu-card-body" style="display:flex;align-items:center;gap:12px;color:#166534">
                <i class="fa-solid fa-circle-check" style="font-size:22px"></i>
                <div>
                    <strong>Password berhasil diperbarui.</strong>
                    <div style="font-size:13px;margin-top:2px">Gunakan password terbaru pada login berikutnya.</div>
                </div>
            </div>
        </div>
    @endif

    <div class="fmu-grid" style="grid-template-columns:minmax(0,1.35fr) minmax(280px,.65fr);align-items:start">
        <section class="fmu-card">
            <div class="fmu-card-head">
                <div class="fmu-card-head-main">
                    <span class="fmu-card-icon"><i class="fa-solid fa-lock"></i></span>
                    <div>
                        <h2>Keamanan Akun</h2>
                        <p>Atur password dan pemulihan akun FINUS.</p>
                    </div>
                </div>
            </div>

            <div class="fmu-card-body">
                <div style="display:grid;gap:16px">
                    <article style="display:flex;gap:14px;align-items:flex-start;padding:18px;border:1px solid #E4ECE6;border-radius:18px;background:#FBFDFC">
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:46px;height:46px;border-radius:14px;background:#EAF8EE;color:#0E5423;flex:0 0 auto">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <div style="flex:1;min-width:0">
                            <h3 style="margin:0 0 5px;font-size:16px;font-weight:900;color:#173b24">Password</h3>
                            <p style="margin:0;color:#68796e;font-size:13px;line-height:1.6">
                                Ubah password secara berkala dan jangan menggunakan password yang sama dengan akun lain.
                            </p>
                            <div style="margin-top:9px;font-size:12px;color:#829087">
                                Perubahan terakhir: <strong>{{ $lastPasswordChange }}</strong>
                            </div>
                        </div>
                        <a href="{{ route($passwordRoute) }}" class="fmu-btn fmu-btn-primary" style="white-space:nowrap">
                            <i class="fa-solid fa-pen-to-square"></i>
                            Ubah Password
                        </a>
                    </article>

                    @if($user->isAdmin())
                        <article style="padding:18px;border:1px solid #CFE6D6;border-radius:18px;background:#F8FCF9">
                            <div style="display:flex;gap:14px;align-items:flex-start">
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:46px;height:46px;border-radius:14px;background:#EAF8EE;color:#0E5423;flex:0 0 auto">
                                    <i class="fa-solid fa-shield-keyhole"></i>
                                </span>
                                <div style="flex:1;min-width:0">
                                    <div style="display:flex;align-items:center;gap:9px;flex-wrap:wrap">
                                        <h3 style="margin:0;font-size:16px;font-weight:900;color:#173b24">Recovery Code Admin</h3>
                                        @if($adminRecoveryCode !== '')
                                            <span class="fmu-badge" style="--badge-color:#179B40;--badge-soft:#EAF8EE">Sudah dibuat</span>
                                        @else
                                            <span class="fmu-badge" style="--badge-color:#D97706;--badge-soft:#FFF7E6">Belum dibuat</span>
                                        @endif
                                    </div>
                                    <p style="margin:6px 0 0;color:#68796e;font-size:13px;line-height:1.65">
                                        Recovery Code pertama dibuat saat akun Admin dibuat. Di halaman ini Admin dapat melihat atau mengganti kode dengan <strong>Generate Code</strong> atau kode manual minimal {{ $adminRecoveryMinLength }} karakter.
                                    </p>
                                </div>
                            </div>

                            @if($adminRecoveryCode !== '')
                                <div style="margin-top:16px">
                                    <label class="fmu-label" for="adminRecoveryCurrent">Recovery Code Saat Ini</label>
                                    <div style="display:flex;gap:8px;align-items:stretch">
                                        <div class="fmu-input-icon-wrap" style="flex:1">
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
                                        <button type="button" class="fmu-btn" data-recovery-toggle="#adminRecoveryCurrent" aria-label="Tampilkan Recovery Code" title="Tampilkan / sembunyikan Recovery Code">
                                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="fmu-btn" data-copy-recovery="#adminRecoveryCurrent" title="Salin Recovery Code">
                                            <i class="fa-solid fa-copy" aria-hidden="true"></i>
                                            Salin
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.recovery-code.update') }}" style="margin-top:18px">
                                @csrf
                                @method('PATCH')

                                <div class="fmu-form-grid">
                                    <div class="fmu-field fmu-field-full">
                                        <label class="fmu-label" for="recovery_code">
                                            Recovery Code Baru
                                            <span class="fmu-required">*</span>
                                        </label>
                                        <div style="display:flex;gap:8px;align-items:stretch">
                                            <div class="fmu-input-icon-wrap" style="flex:1">
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
                                            <button type="button" class="fmu-btn" data-recovery-toggle="#recovery_code" aria-label="Tampilkan Recovery Code" title="Tampilkan / sembunyikan Recovery Code">
                                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" id="generateAdminRecoveryCode" class="fmu-btn fmu-btn-primary" style="white-space:nowrap">
                                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                                Generate Code
                                            </button>
                                        </div>
                                        <span class="fmu-help">
                                            Minimal {{ $adminRecoveryMinLength }} dan maksimal {{ $adminRecoveryMaxLength }} karakter, tanpa spasi, serta wajib memiliki huruf besar, huruf kecil, angka, dan simbol.
                                        </span>
                                        @error('recovery_code')<span class="fmu-error">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="fmu-field fmu-field-full">
                                        <label class="fmu-label" for="admin_recovery_current_password">Password Saat Ini <span class="fmu-required">*</span></label>
                                        <div style="display:flex;gap:8px;align-items:stretch">
                                            <div class="fmu-input-icon-wrap" style="flex:1">
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
                                            <button type="button" class="fmu-btn" data-recovery-toggle="#admin_recovery_current_password" aria-label="Tampilkan password" title="Tampilkan / sembunyikan password">
                                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <span class="fmu-help">Password saat ini diperlukan sebelum Recovery Code dapat diganti.</span>
                                        @error('current_password')<span class="fmu-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>

                                <div class="fmu-actions" style="padding-left:0;padding-right:0;padding-bottom:0">
                                    <button type="submit" class="fmu-btn fmu-btn-primary">
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        Ganti Recovery Code
                                    </button>
                                </div>
                            </form>
                        </article>
                    @endif

                    @if($user->isPegawai())
                        <article style="display:flex;gap:14px;align-items:flex-start;padding:18px;border:1px solid #F2D7A6;border-radius:18px;background:#FFF9EE">
                            <span style="display:inline-flex;align-items:center;justify-content:center;width:46px;height:46px;border-radius:14px;background:#FFF0CB;color:#9A6700;flex:0 0 auto">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <div>
                                <h3 style="margin:0 0 5px;font-size:16px;font-weight:900;color:#6F4B00">Recovery Code Pegawai</h3>
                                <p style="margin:0;color:#7C6840;font-size:13px;line-height:1.6">
                                    Recovery Code akan otomatis diganti setelah password berhasil diubah atau di-reset. Kode terbaru dapat dilihat Admin melalui Detail Pegawai.
                                </p>
                            </div>
                        </article>
                    @endif
                </div>
            </div>
        </section>

        <aside class="fmu-card">
            <div class="fmu-card-head">
                <div class="fmu-card-head-main">
                    <span class="fmu-card-icon"><i class="fa-solid fa-circle-user"></i></span>
                    <div>
                        <h2>Akun Aktif</h2>
                        <p>Ringkasan akun saat ini.</p>
                    </div>
                </div>
            </div>
            <div class="fmu-card-body">
                <div style="display:grid;gap:12px">
                    <div>
                        <small style="display:block;color:#819087;margin-bottom:4px">Nama</small>
                        <strong style="color:#173b24;word-break:break-word">{{ $displayName }}</strong>
                    </div>
                    <div>
                        <small style="display:block;color:#819087;margin-bottom:4px">Email</small>
                        <strong style="color:#173b24;word-break:break-word">{{ $displayEmail }}</strong>
                    </div>
                    <div>
                        <small style="display:block;color:#819087;margin-bottom:4px">Portal</small>
                        <strong style="color:#173b24">{{ $portalLabel }}</strong>
                    </div>
                </div>
            </div>
            <div class="fmu-actions" style="justify-content:stretch">
                <a href="{{ route($profileRoute) }}" class="fmu-btn" style="width:100%;justify-content:center">
                    <i class="fa-solid fa-user"></i>
                    Lihat Profil
                </a>
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

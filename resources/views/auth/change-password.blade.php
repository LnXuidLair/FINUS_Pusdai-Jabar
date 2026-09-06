@extends('layouts.app')

@section('title', 'Ubah Password')
@section('hide-page-header', '1')

@section('content')
@include('layouts.partials.finus-ui')

<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-key"></i></span>
            <div>
                <h1>Ubah Password</h1>
                <p>Masukkan password saat ini sebelum membuat password baru.</p>
            </div>
        </div>
        <div class="fmu-hero-actions">
            <a href="{{ route($backRoute) }}" class="fmu-btn" style="background:#fff;color:#0E5423!important">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Pengaturan
            </a>
        </div>
    </section>

    <div class="fmu-grid" style="grid-template-columns:minmax(0,1.2fr) minmax(280px,.55fr);align-items:start">
        <form method="POST" action="{{ route($updateRoute) }}" class="fmu-card">
            @csrf
            @method('PATCH')

            <div class="fmu-card-head">
                <div class="fmu-card-head-main">
                    <span class="fmu-card-icon"><i class="fa-solid fa-lock"></i></span>
                    <div>
                        <h2>Perbarui Password</h2>
                        <p>Password lama diperlukan untuk memverifikasi pemilik akun.</p>
                    </div>
                </div>
            </div>

            <div class="fmu-card-body">
                <div class="fmu-form-grid" style="grid-template-columns:1fr">
                    <div class="fmu-field">
                        <label class="fmu-label" for="current_password">Password Saat Ini <span class="fmu-required">*</span></label>
                        <div class="fmu-input-icon-wrap" style="position:relative">
                            <i class="fa-solid fa-lock"></i>
                            <input id="current_password" type="password" name="current_password" class="fmu-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password saat ini" autocomplete="current-password" required autofocus style="padding-right:46px">
                            <button type="button" class="password-eye" data-password-toggle="current_password" aria-label="Tampilkan password" aria-pressed="false">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="fmu-field">
                        <label class="fmu-label" for="password">Password Baru <span class="fmu-required">*</span></label>
                        <div class="fmu-input-icon-wrap" style="position:relative">
                            <i class="fa-solid fa-key"></i>
                            <input id="password" type="password" name="password" class="fmu-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" autocomplete="new-password" required style="padding-right:46px">
                            <button type="button" class="password-eye" data-password-toggle="password" aria-label="Tampilkan password" aria-pressed="false">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <span class="fmu-help">Gunakan password baru yang berbeda dari password saat ini.</span>
                        @error('password')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="fmu-field">
                        <label class="fmu-label" for="password_confirmation">Konfirmasi Password Baru <span class="fmu-required">*</span></label>
                        <div class="fmu-input-icon-wrap" style="position:relative">
                            <i class="fa-solid fa-check"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="fmu-control @error('password_confirmation') is-invalid @enderror" placeholder="Ulangi password baru" autocomplete="new-password" required style="padding-right:46px">
                            <button type="button" class="password-eye" data-password-toggle="password_confirmation" aria-label="Tampilkan password" aria-pressed="false">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="fmu-actions">
                <a href="{{ route($backRoute) }}" class="fmu-btn">
                    <i class="fa-solid fa-xmark"></i>
                    Batal
                </a>
                <button type="submit" class="fmu-btn fmu-btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Password
                </button>
            </div>
        </form>

        <aside class="fmu-side-note">
            <h3><i class="fa-solid fa-shield-halved mr-2"></i>Keamanan Password</h3>
            <p>Gunakan kombinasi password yang kuat dan jangan membagikannya kepada siapa pun.</p>
            <ul>
                <li>Minimal 8 karakter.</li>
                <li>Password baru harus berbeda dari password lama.</li>
                <li>Jangan menggunakan informasi yang mudah ditebak.</li>
                @if(($portal ?? '') === 'staff')
                    <li>Recovery Code Pegawai otomatis berubah setelah password berhasil diperbarui.</li>
                @endif
            </ul>
        </aside>
    </div>
</div>

<style>
.password-eye{position:absolute;right:12px;top:50%;transform:translateY(-50%);width:34px;height:34px;border:0;border-radius:10px;background:transparent;color:#597064;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:.18s ease}.password-eye:hover{background:#EAF8EE;color:#0E5423}.password-eye:focus-visible{outline:2px solid #22BA51;outline-offset:2px}
</style>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        const icon = button.querySelector('i');
        if (!input || !icon) return;

        const willShow = input.type === 'password';
        input.type = willShow ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !willShow);
        icon.classList.toggle('fa-eye-slash', willShow);
        button.setAttribute('aria-pressed', willShow ? 'true' : 'false');
        button.setAttribute('aria-label', willShow ? 'Sembunyikan password' : 'Tampilkan password');
    });
});
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: auth/change-password.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="auth/change-password.blade.php">
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-body,.fr-card-body) { background:transparent !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-icon,.fr-card-icon,.fmu-stat-icon,.fr-stat-icon) { box-shadow:inset 0 1px 0 rgba(255,255,255,.025) !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-hero-badge,.fr-hero-badge) { border-color:rgba(255,255,255,.18) !important; background:rgba(4,35,15,.36) !important; color:#F5FFF7 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-side-note,.fr-tip-item,.fr-breakdown-item) { border-color:#293D31 !important; background:#101B14 !important; }
</style>
@endpush


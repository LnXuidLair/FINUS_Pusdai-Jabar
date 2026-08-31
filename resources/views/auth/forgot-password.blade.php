@php
    $meta = match($portal) {
        'admin' => [
            'portal' => 'admin',
            'panel' => 'Recovery Code Admin',
            'copy' => 'Gunakan email Admin dan Recovery Code yang Anda buat melalui Pengaturan Akun.',
            'hero' => 'Pemulihan Akun Admin',
            'heroCopy' => 'Verifikasi Recovery Code Admin untuk membuat password baru.',
            'loginRoute' => 'login.admin',
        ],
        'pegawai' => [
            'portal' => 'staff',
            'panel' => 'Recovery Code Pegawai',
            'copy' => 'Gunakan NIP dan Recovery Code aktif terbaru akun Pegawai Anda.',
            'hero' => 'Pemulihan Akun Pegawai',
            'heroCopy' => 'Masukkan NIP dan Recovery Code terbaru. Jika password pernah berubah, minta kode terbaru kepada Admin.',
            'loginRoute' => 'login.staff',
        ],
        default => [
            'portal' => 'jamaah',
            'panel' => 'OTP Email Jamaah',
            'copy' => 'Kode OTP enam digit akan dikirim ke email akun Jamaah.',
            'hero' => 'Pemulihan Akun Jamaah',
            'heroCopy' => 'Masukkan email terdaftar untuk menerima kode OTP reset password.',
            'loginRoute' => 'login.jamaah',
        ],
    };
@endphp

@extends('layouts.guest')

@section('portal', $meta['portal'])
@section('title', 'FINUS | Lupa Password')
@section('header-title', 'Pemulihan Akun')
@section('panel-eyebrow', 'Keamanan Akun')
@section('panel-title', $meta['panel'])
@section('panel-copy', $meta['copy'])
@section('hero-title', $meta['hero'])
@section('hero-copy', $meta['heroCopy'])

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="auth-form" data-loading-title="Memverifikasi pemulihan akun...">
    @csrf
    <input type="hidden" name="portal" value="{{ $portal }}">

    @if($portal === 'jamaah')
        <div class="auth-context-card">
            <span class="auth-context-icon" aria-hidden="true">#</span>
            <div>
                <p class="auth-context-title">OTP dikirim ke email Jamaah</p>
                <p class="auth-context-copy">Kode OTP berlaku selama 20 detik setelah dikirim.</p>
            </div>
        </div>

        <div class="auth-field-group">
            <label for="email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Alamat Email <span class="auth-required">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="auth-field" placeholder="Email akun Jamaah"
                   autocomplete="email" inputmode="email" required autofocus
                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email')<p class="auth-error" id="email-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="auth-button" data-loading-text="Mengirim OTP..." data-loading-title="Mengirim kode OTP...">
            Kirim Kode OTP
        </button>
    @elseif($portal === 'pegawai')
        <div class="auth-context-card">
            <span class="auth-context-icon" aria-hidden="true">ID</span>
            <div>
                <p class="auth-context-title">Gunakan Recovery Code Pegawai</p>
                <p class="auth-context-copy">Recovery Code berubah setiap kali password Pegawai diubah atau direset. Jika kode lama tidak berlaku, minta Recovery Code terbaru kepada Admin.</p>
            </div>
        </div>

        <div class="auth-field-group">
            <label for="nip" class="auth-label"><span class="auth-label-icon" aria-hidden="true">#</span>NIP <span class="auth-required">*</span></label>
            <input id="nip" type="text" name="nip" value="{{ old('nip') }}"
                   class="auth-field" placeholder="Masukkan NIP Pegawai"
                   autocomplete="off" inputmode="numeric" required autofocus
                   @error('nip') aria-invalid="true" aria-describedby="nip-error" @enderror>
            @error('nip')<p class="auth-error" id="nip-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="auth-field-group">
            <label for="recovery_code" class="auth-label"><span class="auth-label-icon" aria-hidden="true">#</span>Recovery Code <span class="auth-required">*</span></label>
            <input id="recovery_code" type="text" name="recovery_code" value="{{ old('recovery_code') }}"
                   class="auth-field" placeholder="FINUS-XXXX-XXXX-XXXX-XXXX"
                   autocomplete="off" required
                   @error('recovery_code') aria-invalid="true" aria-describedby="recovery-code-error" @enderror>
            @error('recovery_code')<p class="auth-error" id="recovery-code-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="auth-button" data-loading-text="Memverifikasi..." data-loading-title="Memeriksa Recovery Code Pegawai...">
            Verifikasi Recovery Code
        </button>
    @else
        <div class="auth-context-card">
            <span class="auth-context-icon" aria-hidden="true">A</span>
            <div>
                <p class="auth-context-title">Recovery Code khusus Admin</p>
                <p class="auth-context-copy">Recovery Code Admin dibuat saat akun Admin pertama kali dibuat dan disimpan terenkripsi oleh FINUS.</p>
            </div>
        </div>

        <div class="auth-field-group">
            <label for="email" class="auth-label"><span class="auth-label-icon" aria-hidden="true">@</span>Email Admin <span class="auth-required">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email', 'admin@pusdai.finus.id') }}"
                   class="auth-field" placeholder="admin@pusdai.finus.id"
                   autocomplete="email" inputmode="email" required autofocus
                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email')<p class="auth-error" id="email-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="auth-field-group">
            <label for="recovery_code" class="auth-label"><span class="auth-label-icon" aria-hidden="true">#</span>Recovery Code Admin <span class="auth-required">*</span></label>
            <input id="recovery_code" type="password" name="recovery_code"
                   class="auth-field" placeholder="Masukkan Recovery Code Admin"
                   autocomplete="off" required
                   @error('recovery_code') aria-invalid="true" aria-describedby="recovery-code-error" @enderror>
            @error('recovery_code')<p class="auth-error" id="recovery-code-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="auth-button" data-loading-text="Memverifikasi..." data-loading-title="Memeriksa Recovery Code Admin...">
            Verifikasi Recovery Code
        </button>
    @endif

    <p class="auth-footer-text">
        Sudah mengingat password?
        <a href="{{ route($meta['loginRoute']) }}" class="auth-link">Kembali ke login</a>
    </p>
</form>
@endsection

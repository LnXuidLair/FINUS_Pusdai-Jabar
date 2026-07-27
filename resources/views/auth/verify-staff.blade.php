@extends('layouts.guest')
@section('title', 'FINUS | Verifikasi Pegawai')
@section('header-title', 'Verifikasi Pegawai')
@section('panel-title', 'Verifikasi Data')
@section('panel-copy', 'Cocokkan identitas dengan data yang dibuat admin.')
@section('hero-title', 'Aktivasi Pegawai FINUS')
@section('hero-copy', 'Nama dan NIP harus sama dengan data pegawai yang telah didaftarkan oleh admin.')
@section('content')
<form method="POST" action="{{ route('verify.staff') }}" class="auth-form" data-loading-title="Memverifikasi data pegawai...">
    @csrf
    <div class="auth-info-card">
        Masukkan nama lengkap dan NIP persis seperti data pegawai yang dibuat oleh admin.
    </div>
    <div>
        <label for="name" class="auth-label"><span class="auth-label-icon">◆</span>Nama Lengkap</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}"
               class="auth-field" placeholder="Nama sesuai data pegawai"
               autocomplete="name" required autofocus>
        @error('name')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="nip" class="auth-label"><span class="auth-label-icon">#</span>NIP</label>
        <input id="nip" type="text" name="nip" value="{{ old('nip') }}"
               class="auth-field" placeholder="Nomor induk pegawai"
               autocomplete="off" required>
        @error('nip')<p class="auth-error">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="auth-button" data-loading-text="Memverifikasi..." data-loading-title="Memverifikasi data pegawai...">
        Verifikasi
    </button>
    <p class="auth-footer-text">Akun sudah aktif?
        <a href="{{ route('login.staff') }}" class="auth-link">Kembali ke login</a>
    </p>
</form>
@isset($verifiedPegawai)
    <div class="auth-dialog-overlay">
        <div class="auth-dialog" role="dialog" aria-modal="true">
            <div class="auth-dialog-body">
                <div class="auth-dialog-icon">✓</div>
                <h3 class="auth-dialog-title">Verifikasi Berhasil</h3>
                <p class="auth-dialog-copy">Data pegawai ditemukan dan sesuai dengan data yang dibuat oleh admin.</p>
                <dl class="auth-dialog-details">
                    <div class="auth-dialog-detail"><dt>Nama Pegawai</dt><dd>{{ $verifiedPegawai->nama_pegawai }}</dd></div>
                    <div class="auth-dialog-detail"><dt>NIP</dt><dd>{{ $verifiedPegawai->nip }}</dd></div>
                    <div class="auth-dialog-detail"><dt>Jabatan</dt><dd>{{ $verifiedPegawai->jabatan }}</dd></div>
                </dl>
            </div>
            <div class="auth-dialog-footer">
                <a href="{{ route('register.staff.account') }}" class="auth-button" data-loading-title="Membuka aktivasi akun...">
                    Lanjut
                </a>
            </div>
        </div>
    </div>
@endisset
@endsection
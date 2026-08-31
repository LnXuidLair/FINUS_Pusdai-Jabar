@extends('layouts.guest')

@section('portal', 'staff')
@section('title', 'FINUS | Aktivasi Pegawai Berhasil')
@section('header-title', 'Aktivasi Berhasil')
@section('panel-eyebrow', 'Akun Pegawai Aktif')
@section('panel-title', 'Simpan Recovery Code')
@section('panel-copy', 'Recovery Code hanya ditampilkan kepada Pegawai pada tahap ini.')
@section('hero-title', 'Akun Pegawai FINUS Siap Digunakan')
@section('hero-copy', 'Simpan Recovery Code untuk digunakan apabila Anda lupa password.')

@section('content')
<div class="auth-context-card">
    <span class="auth-context-icon" aria-hidden="true">✓</span>
    <div>
        <p class="auth-context-title">Akun berhasil diaktifkan</p>
        <p class="auth-context-copy">Gunakan email Pegawai dan password yang baru dibuat untuk login.</p>
    </div>
</div>

<div class="auth-field-group">
    <label class="auth-label">Email Pegawai</label>
    <input type="text" class="auth-field" value="{{ $activation['email'] }}" readonly aria-readonly="true">
</div>

<div class="auth-field-group">
    <label class="auth-label">Recovery Code Pegawai</label>
    <input type="text" class="auth-field auth-code-field"
           value="{{ $activation['recovery_code'] }}" readonly aria-readonly="true">
    <p class="auth-help"><b>i</b>Simpan kode ini. Setelah halaman ini ditinggalkan, Pegawai tidak dapat melihatnya lagi dari akun sendiri. Admin tetap dapat melihat kode melalui Detail Pegawai. Recovery Code akan otomatis berubah jika password Pegawai diubah atau direset.</p>
</div>

<a href="{{ route('login.staff') }}" class="auth-button" style="text-decoration:none;">
    Saya Sudah Menyimpan Kode
</a>
@endsection
@extends('layouts.app')
@section('title', 'Detail Pegawai')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
@php
    $initial = mb_strtoupper(mb_substr(trim($pegawai->nama_pegawai ?? 'P'), 0, 1));
@endphp
<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-address-card"></i></span>
            <div><h1>Detail Pegawai</h1><p>Informasi identitas, jabatan, kontak, dan status aktivasi pegawai.</p></div>
        </div>
        <div class="fmu-hero-actions"><a href="{{ route('admin.pegawai.edit', $pegawai->id) }}" class="fmu-btn" style="background:#fff;color:#0E5423!important"><i class="fa-solid fa-pen"></i>Edit</a></div>
    </section>

    <section class="fmu-card">
        <div class="fmu-card-body">
            <div class="fmu-grid" style="grid-template-columns:minmax(230px,.55fr) minmax(0,1.45fr);align-items:start">
                <aside class="fmu-side-note text-center">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:82px;height:82px;border-radius:24px;background:linear-gradient(135deg,#0E5423,#22BA51);color:#fff;font-size:29px;font-weight:900;box-shadow:0 14px 30px rgba(14,84,35,.18)">{{ $initial }}</span>
                    <h3 class="mt-3">{{ $pegawai->nama_pegawai }}</h3>
                    <p>{{ $pegawai->jabatan ?: 'Jabatan belum ditentukan' }}</p>
                    <span class="fmu-badge mt-2" style="--badge-color:{{ $pegawai->is_verified ? '#179B40' : '#D97706' }};--badge-soft:{{ $pegawai->is_verified ? '#EAF8EE' : '#FFF7E6' }}">{{ $pegawai->is_verified ? 'Terverifikasi' : 'Belum Terverifikasi' }}</span>
                </aside>
                <div class="fmu-grid fmu-grid-2">
                    @foreach([
                        ['NIP', $pegawai->nip, 'fa-id-card'],
                        ['Jabatan', $pegawai->jabatan, 'fa-briefcase'],
                        ['Email', $pegawai->email ?: '-', 'fa-envelope'],
                        ['Telepon', $pegawai->no_telp ?: '-', 'fa-phone'],
                    ] as [$label, $value, $icon])
                        <article class="fmu-stat">
                            <span class="fmu-stat-icon"><i class="fa-solid {{ $icon }}"></i></span>
                            <div class="fmu-stat-copy"><small>{{ $label }}</small><strong style="font-size:15px;word-break:break-word">{{ $value }}</strong></div>
                        </article>
                    @endforeach
                    <article class="fmu-card fmu-field-full">
                        <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-location-dot"></i></span><div><h3>Alamat</h3><p>Alamat domisili pegawai.</p></div></div></div>
                        <div class="fmu-card-body">{{ $pegawai->alamat ?: 'Alamat belum tersedia.' }}</div>
                    </article>
                </div>
            </div>
        </div>
        <div class="fmu-actions"><a href="{{ route('admin.pegawai.index') }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali ke Daftar</a><div class="fmu-actions-right"><a href="{{ route('admin.pegawai.edit', $pegawai->id) }}" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-user-pen"></i>Edit Pegawai</a></div></div>
    </section>
</div>
@endsection
@extends('layouts.app')
@section('title', 'Tambah Presensi')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero">
    <div class="fmu-hero-main">
        <span class="fmu-hero-icon"><i class="fa-solid fa-calendar-plus"></i></span>
        <div>
            <h1>Tambah Presensi</h1>
            <p>Kelola status kehadiran pegawai untuk tanggal yang dipilih.</p>
        </div>
    </div>
    <div class="fmu-hero-actions"><span class="fmu-hero-badge"><i class="fa-solid fa-circle-info"></i>Presensi Admin</span></div>
</section>
    <form method="POST" action="{{ route('admin.presensi.store') }}" class="fmu-card">
        @csrf
        <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-calendar-plus"></i></span><div><h2>Informasi Presensi</h2><p>Pilih pegawai, tanggal, dan status kehadiran.</p></div></div></div>
        <div class="fmu-card-body"><div class="fmu-form-grid">
            <div class="fmu-field fmu-field-full"><label class="fmu-label" for="id_pegawai">Pegawai <span class="fmu-required">*</span></label><select id="id_pegawai" name="id_pegawai" class="fmu-select @error('id_pegawai') is-invalid @enderror" required><option value="">Pilih pegawai</option>@foreach($pegawais as $pegawai)<option value="{{ $pegawai->id }}" @selected((string) old('id_pegawai') === (string) $pegawai->id)>{{ $pegawai->nama_pegawai }} — {{ $pegawai->jabatan ?? 'Pegawai' }}</option>@endforeach</select>@error('id_pegawai')<span class="fmu-error">{{ $message }}</span>@enderror</div>
            <div class="fmu-field"><label class="fmu-label" for="tanggal">Tanggal <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-calendar-day"></i><input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="fmu-control @error('tanggal') is-invalid @enderror" required></div>@error('tanggal')<span class="fmu-error">{{ $message }}</span>@enderror</div>
            <div class="fmu-field"><label class="fmu-label" for="status">Status <span class="fmu-required">*</span></label><select id="status" name="status" class="fmu-select @error('status') is-invalid @enderror" required><option value="hadir" @selected(old('status','hadir') === 'hadir')>Hadir</option><option value="izin" @selected(old('status','hadir') === 'izin')>Izin</option><option value="lembur" @selected(old('status','hadir') === 'lembur')>Lembur</option><option value="tidak hadir" @selected(old('status','hadir') === 'tidak hadir')>Tidak Hadir</option></select>@error('status')<span class="fmu-error">{{ $message }}</span>@enderror</div>
        </div></div>
        <div class="fmu-actions"><a href="{{ route('admin.presensi.index') }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a><button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Presensi</button></div>
    </form>
</div>
@endsection
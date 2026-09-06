@extends('layouts.app')
@section('title', 'Edit Presensi')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-calendar-pen"></i></span>
            <div>
                <h1>Edit Presensi</h1>
                <p>Perubahan yang dilakukan admin otomatis dianggap sudah disetujui.</p>
            </div>
        </div>
        <div class="fmu-hero-actions">
            <span class="fmu-hero-badge"><i class="fa-solid fa-circle-check"></i>Otomatis Di-ACC</span>
        </div>
    </section>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.presensi.update', $presensi) }}" class="fmu-card">
        @csrf
        @method('PUT')
        <div class="fmu-card-head">
            <div class="fmu-card-head-main">
                <span class="fmu-card-icon"><i class="fa-solid fa-calendar-pen"></i></span>
                <div><h2>Informasi Presensi</h2><p>Perbarui data presensi yang dipilih.</p></div>
            </div>
        </div>
        <div class="fmu-card-body">
            <div class="fmu-form-grid">
                <div class="fmu-field fmu-field-full">
                    <label class="fmu-label" for="id_pegawai">Pegawai <span class="fmu-required">*</span></label>
                    <select id="id_pegawai" name="id_pegawai" class="fmu-select @error('id_pegawai') is-invalid @enderror" required>
                        @foreach($pegawais as $pegawai)
                            <option value="{{ $pegawai->id }}" @selected((string) old('id_pegawai', $presensi->id_pegawai) === (string) $pegawai->id)>
                                {{ $pegawai->nama_pegawai }} — {{ $pegawai->jabatan ?? 'Pegawai' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_pegawai')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field">
                    <label class="fmu-label" for="tanggal">Tanggal <span class="fmu-required">*</span></label>
                    <div class="fmu-input-icon-wrap">
                        <i class="fa-solid fa-calendar-day"></i>
                        <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', $presensi->tanggal ? \Carbon\Carbon::parse($presensi->tanggal)->format('Y-m-d') : '') }}" class="fmu-control @error('tanggal') is-invalid @enderror" required>
                    </div>
                    @error('tanggal')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field">
                    <label class="fmu-label" for="status">Status <span class="fmu-required">*</span></label>
                    <select id="status" name="status" class="fmu-select @error('status') is-invalid @enderror" required>
                        <option value="hadir" @selected(old('status', $presensi->status) === 'hadir')>Hadir</option>
                        <option value="izin" @selected(old('status', $presensi->status) === 'izin')>Izin</option>
                        <option value="sakit" @selected(old('status', $presensi->status) === 'sakit')>Sakit</option>
                        <option value="lembur" @selected(old('status', $presensi->status) === 'lembur')>Lembur</option>
                        <option value="tidak hadir" @selected(old('status', $presensi->status) === 'tidak hadir')>Tidak Hadir</option>
                    </select>
                    @error('status')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field fmu-field-full">
                    <label class="fmu-label" for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" class="fmu-textarea @error('keterangan') is-invalid @enderror" placeholder="Isi alasan izin, sakit, atau keterangan lembur bila diperlukan">{{ old('keterangan', $presensi->keterangan) }}</textarea>
                    @error('keterangan')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="fmu-actions">
            <a href="{{ route('admin.presensi.index') }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a>
            <button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-floppy-disk"></i>Perbarui Presensi</button>
        </div>
    </form>
</div>
@endsection

{{-- FINUS DARK MODE LOCAL: presensi/edit.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="presensi/edit.blade.php">
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-body,.fr-card-body) { background:transparent !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-icon,.fr-card-icon,.fmu-stat-icon,.fr-stat-icon) { box-shadow:inset 0 1px 0 rgba(255,255,255,.025) !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-hero-badge,.fr-hero-badge) { border-color:rgba(255,255,255,.18) !important; background:rgba(4,35,15,.36) !important; color:#F5FFF7 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-side-note,.fr-tip-item,.fr-breakdown-item) { border-color:#293D31 !important; background:#101B14 !important; }
</style>
@endpush


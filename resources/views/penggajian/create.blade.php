@extends('layouts.app')
@section('title', 'Proses Penggajian')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero"><div class="fmu-hero-main"><span class="fmu-hero-icon"><i class="fa-solid fa-money-check-dollar"></i></span><div><h1>Proses Penggajian</h1><p>Pilih pegawai dan periode untuk menghitung serta mencatat penggajian.</p></div></div><div class="fmu-hero-actions"><span class="fmu-hero-badge"><i class="fa-solid fa-calculator"></i>Otomatis dari Presensi</span></div></section>
    <div class="fmu-grid" style="grid-template-columns:minmax(0,1.3fr) minmax(260px,.6fr);align-items:start">
        <form method="POST" action="{{ route('admin.penggajian.store') }}" class="fmu-card">
            @csrf
            <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-file-signature"></i></span><div><h2>Parameter Penggajian</h2><p>Pastikan pegawai dan periode telah dipilih dengan benar.</p></div></div></div>
            <div class="fmu-card-body"><div class="fmu-form-grid">
                <div class="fmu-field fmu-field-full"><label class="fmu-label" for="id_pegawai">Pegawai <span class="fmu-required">*</span></label><select id="id_pegawai" name="id_pegawai" class="fmu-select @error('id_pegawai') is-invalid @enderror" required><option value="">Pilih pegawai</option>@foreach($pegawais as $pegawai)<option value="{{ $pegawai->id }}" data-name="{{ $pegawai->nama_pegawai }}" data-role="{{ $pegawai->jabatan }}" @selected((string) old('id_pegawai') === (string) $pegawai->id)>{{ $pegawai->nama_pegawai }} — {{ $pegawai->jabatan }}</option>@endforeach</select>@error('id_pegawai')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="periode">Periode <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-calendar"></i><input type="month" id="periode" name="periode" value="{{ old('periode', now()->format('Y-m')) }}" class="fmu-control @error('periode') is-invalid @enderror" required></div>@error('periode')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="status_penggajian">Status <span class="fmu-required">*</span></label><select id="status_penggajian" name="status_penggajian" class="fmu-select @error('status_penggajian') is-invalid @enderror" required><option value="belum_dibayar" @selected(old('status_penggajian','belum_dibayar') === 'belum_dibayar')>Belum Dibayar</option><option value="sudah_dibayar" @selected(old('status_penggajian') === 'sudah_dibayar')>Sudah Dibayar</option></select>@error('status_penggajian')<span class="fmu-error">{{ $message }}</span>@enderror</div>
            </div></div>
            <div class="fmu-actions"><a href="{{ route('admin.penggajian.index') }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a><button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-gears"></i>Proses Penggajian</button></div>
        </form>
        <aside class="fmu-side-note"><h3><i class="fa-solid fa-circle-info mr-2"></i>Ringkasan</h3><p id="payrollEmployeeSummary">Pilih pegawai untuk menampilkan ringkasan.</p><ul><li>Gaji dihitung dari konfigurasi jabatan dan presensi.</li><li>Periksa periode agar tidak terjadi data ganda.</li><li>Status dapat diperbarui sesuai proses pembayaran.</li></ul></aside>
    </div>
</div>
@endsection
@push('scripts')
<script>
(() => {
    const employee = document.getElementById('id_pegawai'); const period = document.getElementById('periode'); const summary = document.getElementById('payrollEmployeeSummary');
    const sync = () => { const option = employee?.selectedOptions?.[0]; summary.textContent = option?.value ? `${option.dataset.name} — ${option.dataset.role}. Periode ${period?.value || '-'}.` : 'Pilih pegawai untuk menampilkan ringkasan.'; };
    employee?.addEventListener('change',sync); period?.addEventListener('change',sync); sync();
})();
</script>
@endpush
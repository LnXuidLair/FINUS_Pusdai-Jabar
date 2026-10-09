@extends('layouts.app')
@section('title', $category->exists ? 'Ubah Kategori Transaksi' : 'Tambah Kategori Transaksi')
@section('hide-page-header', '1')

@php
    $editing = $category->exists;
@endphp

@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero"><div class="fmu-hero-main"><span class="fmu-hero-icon"><i class="fa-solid fa-tags"></i></span><div><h1>{{ $editing ? 'Ubah Kategori Transaksi' : 'Tambah Kategori Transaksi' }}</h1><p>Kategori dipilih pengguna, sedangkan akun jurnal ditentukan dari pemetaan ini.</p></div></div></section>
    <form method="POST" action="{{ $editing ? route('admin.transaction-categories.update', $category) : route('admin.transaction-categories.store') }}" class="fmu-card">
        @csrf @if($editing) @method('PUT') @endif
        <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-diagram-project"></i></span><div><h2>Konfigurasi Kategori</h2><p>Pilih akun tujuan. Sumber dana ditentukan otomatis dari kode akun.</p></div></div></div>
        <div class="fmu-card-body">
            <div class="fmu-form-grid">
                <div class="fmu-field"><label class="fmu-label" for="code">Kode Kategori <span class="fmu-required">*</span></label><input id="code" name="code" class="fmu-control" maxlength="50" value="{{ old('code', $category->code) }}" required>@error('code')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="name">Nama Kategori <span class="fmu-required">*</span></label><input id="name" name="name" class="fmu-control" maxlength="150" value="{{ old('name', $category->name) }}" required>@error('name')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="coa_id">Akun Tujuan Pengeluaran <span class="fmu-required">*</span></label><select id="coa_id" name="coa_id" class="fmu-control" required><option value="">Pilih akun</option>@foreach($accounts as $account)@php($fundType = \App\Models\TransactionCategory::fundTypeForAccountCode($account->kode_akun))<option value="{{ $account->id }}" data-fund-label="{{ $fundLabels[$fundType] ?? 'Belum dipetakan' }}" @selected((string) old('coa_id', $category->coa_id) === (string) $account->id)>{{ $account->kode_akun }} - {{ $account->nama_akun }}</option>@endforeach</select><span class="fmu-help" id="derivedFundSource">Sumber dana akan mengikuti kode akun yang dipilih.</span>@error('coa_id')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="form_type">Tipe Form <span class="fmu-required">*</span></label><select id="form_type" name="form_type" class="fmu-control" required>@foreach($formLabels as $value => $label)<option value="{{ $value }}" @selected(old('form_type', $category->form_type ?? 'general') === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="fmu-field fmu-field-full"><span class="fmu-label">Kebutuhan Data Khusus</span><div class="tc-check-grid"><label><input type="checkbox" name="requires_asnaf" value="1" @checked(old('requires_asnaf', $category->requires_asnaf))><span>Rincian asnaf</span></label><label><input type="checkbox" name="requires_employee" value="1" @checked(old('requires_employee', $category->requires_employee))><span>Pegawai penerima</span></label><label><input type="checkbox" name="requires_assignment_proof" value="1" @checked(old('requires_assignment_proof', $category->requires_assignment_proof))><span>Bukti surat tugas</span></label><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true))><span>Kategori aktif</span></label></div></div>
            </div>
        </div>
        <div class="fmu-actions"><a href="{{ route('admin.transaction-categories.index') }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a><button class="fmu-btn fmu-btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i>Simpan Kategori</button></div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const account = document.getElementById('coa_id');
    const source = document.getElementById('derivedFundSource');
    const syncSource = () => {
        const selected = account?.selectedOptions?.[0];
        source.textContent = selected?.value
            ? `Sumber dana otomatis: ${selected.dataset.fundLabel || 'Belum dipetakan'}`
            : 'Sumber dana akan mengikuti kode akun yang dipilih.';
    };
    account?.addEventListener('change', syncSource);
    syncSource();
})();
</script>
@endpush

@push('styles')
<style>.tc-check-grid{display:grid;grid-template-columns:repeat(3,minmax(180px,1fr));gap:10px}.tc-check-grid label{display:flex;align-items:center;gap:9px;padding:11px;border:1px solid #dce7df;border-radius:6px;background:#fbfdfb;color:#33443a}.tc-check-grid input{accent-color:#169b43}@media(max-width:800px){.tc-check-grid{grid-template-columns:1fr}}</style>
@endpush

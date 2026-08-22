@extends('layouts.app')
@section('title', 'Tambah Pengeluaran')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero"><div class="fmu-hero-main"><span class="fmu-hero-icon"><i class="fa-solid fa-receipt"></i></span><div><h1>Tambah Pengeluaran</h1><p>Catat pengeluaran masjid dan lampirkan bukti pembayaran bila tersedia.</p></div></div><div class="fmu-hero-actions"><span class="fmu-hero-badge"><i class="fa-solid fa-shield-halved"></i>Pencatatan Keuangan</span></div></section>
    <form method="POST" action="{{ route('admin.pengeluaran.store') }}" enctype="multipart/form-data" class="fmu-card" id="expenseForm">
        @csrf
        <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-pen-to-square"></i></span><div><h2>Data Pengeluaran</h2><p>Isi kategori, deskripsi, nominal, dan tanggal transaksi.</p></div></div></div>
        <div class="fmu-card-body">
            <div class="fmu-form-grid">
                <div class="fmu-field"><label class="fmu-label" for="kategori">Kategori <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-tags"></i><input id="kategori" name="kategori" value="{{ old('kategori') }}" class="fmu-control @error('kategori') is-invalid @enderror" placeholder="Contoh: Biaya operasional" required></div>@error('kategori')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="tanggal">Tanggal <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-calendar-day"></i><input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="fmu-control @error('tanggal') is-invalid @enderror" required></div>@error('tanggal')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field fmu-field-full"><label class="fmu-label" for="deskripsi">Deskripsi <span class="fmu-required">*</span></label><textarea id="deskripsi" name="deskripsi" class="fmu-textarea @error('deskripsi') is-invalid @enderror" placeholder="Jelaskan keperluan pengeluaran secara singkat dan jelas" required>{{ old('deskripsi') }}</textarea>@error('deskripsi')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="jumlah">Jumlah <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-rupiah-sign"></i><input type="number" min="1" step="1" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" class="fmu-control @error('jumlah') is-invalid @enderror" placeholder="0" required></div><span class="fmu-help" id="expenseAmountPreview">Rp 0</span>@error('jumlah')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="bukti_pembayaran">Bukti Pembayaran</label><label class="fmu-upload" for="bukti_pembayaran"><span><i class="fa-solid fa-cloud-arrow-up"></i><strong>Pilih bukti pembayaran</strong><span>JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.</span><span class="fmu-file-name" id="expenseFileName"></span></span></label><input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf" class="d-none">@error('bukti_pembayaran')<span class="fmu-error">{{ $message }}</span>@enderror</div>
            </div>
        </div>
        <div class="fmu-actions"><a href="{{ route('admin.pengeluaran.index') }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a><button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Pengeluaran</button></div>
    </form>
</div>
@endsection
@push('scripts')
<script>
(() => {
    const amount = document.getElementById('jumlah'); const preview = document.getElementById('expenseAmountPreview');
    const syncAmount = () => { const value = Number(amount?.value || 0); preview.textContent = new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(value); };
    amount?.addEventListener('input', syncAmount); syncAmount();
    const input = document.getElementById('bukti_pembayaran'); const label = document.getElementById('expenseFileName');
    input?.addEventListener('change', () => { const file = input.files?.[0]; label.textContent = file?.name || ''; label.classList.toggle('is-visible', Boolean(file)); });
})();
</script>
@endpush
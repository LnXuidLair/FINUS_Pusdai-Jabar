@extends('layouts.app')
@section('title', 'Tambah Pengeluaran')
@section('hide-page-header', '1')
@php
    $pengeluaranStoreRoute = request()->routeIs('pegawai.keuangan.*')
        ? 'pegawai.keuangan.pengeluaran.store'
        : 'admin.pengeluaran.store';
    $pengeluaranIndexRoute = request()->routeIs('pegawai.keuangan.*')
        ? 'pegawai.keuangan.pengeluaran.index'
        : 'admin.pengeluaran.index';
@endphp
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero"><div class="fmu-hero-main"><span class="fmu-hero-icon"><i class="fa-solid fa-receipt"></i></span><div><h1>Tambah Pengeluaran</h1><p>Catat pengeluaran masjid dan lampirkan bukti pembayaran bila tersedia.</p></div></div><div class="fmu-hero-actions"><span class="fmu-hero-badge"><i class="fa-solid fa-shield-halved"></i>Pencatatan Keuangan</span></div></section>
    <form method="POST" action="{{ route($pengeluaranStoreRoute) }}" enctype="multipart/form-data" class="fmu-card" id="expenseForm">
        @csrf
        <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-pen-to-square"></i></span><div><h2>Data Pengeluaran</h2><p>Isi kategori, deskripsi, nominal, dan tanggal transaksi.</p></div></div></div>
        <div class="fmu-card-body">
            <div class="fmu-form-grid">
                <div class="fmu-field">
                    <label class="fmu-label" for="kategori">Kategori (Akun COA) <span class="fmu-required">*</span></label>
                    <div class="fmu-input-icon-wrap">
                        <i class="fa-solid fa-tags"></i>
                        <select id="kategori" name="kategori" class="fmu-control @error('kategori') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori Beban --</option>
                            @php
                                $penjelas = [
                                    '5101' => 'Administrasi, ATK, Kesekretariatan',
                                    '5102' => 'Honor Khatib, Imam, Muadzin, Dakwah',
                                    '5103' => 'Pemeliharaan Fisik, Sarana & Bangunan',
                                    '5105' => 'Rapat, Jamuan, Acara',
                                    '5106' => 'Biaya Rekening Bank, Pajak',
                                    '5107' => 'Listrik, Air, AC, Sound System',
                                    '5108' => 'Peralatan & Bahan Kebersihan',
                                    '5109' => 'PHBI, Seminar, Honor Narasumber',
                                    '5110' => 'Inventaris & Peralatan Baru',
                                    '5111' => 'Penyaluran Mustahik & Program',
                                ];
                            @endphp
                            @foreach($coaBeban ?? [] as $coa)
                                @php
                                    $val = $coa->nama_akun;
                                    $info = $penjelas[$coa->kode_akun] ?? null;
                                @endphp
                                <option value="{{ $val }}" {{ old('kategori') === $val ? 'selected' : '' }}>
                                    {{ $coa->kode_akun }} - {{ $coa->nama_akun }} {{ $info ? '('.$info.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('kategori')<span class="fmu-error">{{ $message }}</span>@enderror
                    <span class="fmu-help" style="font-size: 11.5px; color: #64748b; margin-top: 5px; display: block;">
                        <i class="fa-solid fa-circle-info" style="color: #179b40; margin-right: 4px;"></i>
                        Biaya Honorarium / Gaji Pegawai dicatat otomatis melalui menu <strong>Penggajian</strong>.
                    </span>
                </div>
                <div class="fmu-field"><label class="fmu-label" for="tanggal">Tanggal <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-calendar-day"></i><input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="fmu-control @error('tanggal') is-invalid @enderror" required></div>@error('tanggal')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field fmu-field-full"><label class="fmu-label" for="deskripsi">Deskripsi <span class="fmu-required">*</span></label><textarea id="deskripsi" name="deskripsi" class="fmu-textarea @error('deskripsi') is-invalid @enderror" placeholder="Jelaskan keperluan pengeluaran secara singkat dan jelas" required>{{ old('deskripsi') }}</textarea>@error('deskripsi')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="jumlah">Jumlah <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-rupiah-sign"></i><input type="number" min="1" step="1" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" class="fmu-control @error('jumlah') is-invalid @enderror" placeholder="0" required></div><span class="fmu-help" id="expenseAmountPreview">Rp 0</span>@error('jumlah')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="bukti_pembayaran">Bukti Pembayaran</label><label class="fmu-upload" for="bukti_pembayaran"><span><i class="fa-solid fa-cloud-arrow-up"></i><strong>Pilih bukti pembayaran</strong><span>JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.</span><span class="fmu-file-name" id="expenseFileName"></span></span></label><input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf" class="d-none">@error('bukti_pembayaran')<span class="fmu-error">{{ $message }}</span>@enderror</div>
            </div>
        </div>
        <div class="fmu-actions"><a href="{{ route($pengeluaranIndexRoute) }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a><button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Pengeluaran</button></div>
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

{{-- FINUS DARK MODE LOCAL: pengeluaran/create.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="pengeluaran/create.blade.php">
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-body,.fr-card-body) { background:transparent !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-icon,.fr-card-icon,.fmu-stat-icon,.fr-stat-icon) { box-shadow:inset 0 1px 0 rgba(255,255,255,.025) !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-hero-badge,.fr-hero-badge) { border-color:rgba(255,255,255,.18) !important; background:rgba(4,35,15,.36) !important; color:#F5FFF7 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-side-note,.fr-tip-item,.fr-breakdown-item) { border-color:#293D31 !important; background:#101B14 !important; }
</style>
@endpush


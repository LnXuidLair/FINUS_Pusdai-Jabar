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
    $oldZakatDetails = old('zakat_details', [
        ['asnaf' => '', 'jumlah_penerima' => 1, 'nominal' => ''],
    ]);
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
                        <select id="kategori" name="coa_debit_id" class="fmu-control @error('coa_debit_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori Beban --</option>
                            @foreach($kelompokPengeluaran ?? [] as $group => $label)
                                @php($accounts = ($coaBebanGrouped ?? collect())->get($group, collect()))
                                @continue($accounts->isEmpty())
                                <optgroup label="{{ $label }}">
                                    @foreach($accounts as $coa)
                                        <option value="{{ $coa->id }}" data-account-code="{{ $coa->kode_akun }}" @selected((string) old('coa_debit_id') === (string) $coa->id)>
                                            {{ $coa->kode_akun }} - {{ $coa->nama_akun }}{{ $coa->penjelasan_pengeluaran ? ' ('.$coa->penjelasan_pengeluaran.')' : '' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    @error('coa_debit_id')<span class="fmu-error">{{ $message }}</span>@enderror
                    <span class="fmu-help" style="font-size: 11.5px; color: #64748b; margin-top: 5px; display: block;">
                        <i class="fa-solid fa-circle-info" style="color: #179b40; margin-right: 4px;"></i>
                        Gaji dan honorarium dicatat melalui menu <strong>Penggajian</strong>. Hak amil dialokasikan otomatis dari kebijakan zakat.
                    </span>
                </div>
                <div class="fmu-field"><label class="fmu-label" for="tanggal">Tanggal <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-calendar-day"></i><input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="fmu-control @error('tanggal') is-invalid @enderror" required></div><span class="fmu-help" id="recognitionDateHelp">Tanggal transaksi atau pembayaran.</span>@error('tanggal')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field fmu-field-full" id="infakRestrictionField" hidden>
                    <label class="fmu-label" for="restriction_type">Sifat Infak/Sedekah <span class="fmu-required">*</span></label>
                    <select id="restriction_type" name="restriction_type" class="fmu-control @error('restriction_type') is-invalid @enderror" disabled>
                        <option value="">-- Pilih Sifat Dana --</option>
                        <option value="mutlaqah" @selected(old('restriction_type') === 'mutlaqah')>Tidak Terikat (Mutlaqah)</option>
                        <option value="muqayyadah" @selected(old('restriction_type') === 'muqayyadah')>Terikat (Muqayyadah)</option>
                    </select>
                    <span class="fmu-help">Dana terikat hanya boleh disalurkan sesuai amanah pemberi.</span>
                    @error('restriction_type')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>
                <div class="fmu-field fmu-field-full"><label class="fmu-label" for="deskripsi">Deskripsi <span class="fmu-required">*</span></label><textarea id="deskripsi" name="deskripsi" class="fmu-textarea @error('deskripsi') is-invalid @enderror" placeholder="Jelaskan keperluan pengeluaran secara singkat dan jelas" required>{{ old('deskripsi') }}</textarea>@error('deskripsi')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="jumlah">Jumlah <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-rupiah-sign"></i><input type="number" min="1" step="1" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" class="fmu-control @error('jumlah') is-invalid @enderror" placeholder="0" required></div><span class="fmu-help" id="expenseAmountPreview">Rp 0</span>@error('jumlah')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="bukti_pembayaran">Bukti Pembayaran</label><label class="fmu-upload" for="bukti_pembayaran"><span><i class="fa-solid fa-cloud-arrow-up"></i><strong>Pilih bukti pembayaran</strong><span>JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.</span><span class="fmu-file-name" id="expenseFileName"></span></span></label><input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf" class="d-none">@error('bukti_pembayaran')<span class="fmu-error">{{ $message }}</span>@enderror</div>
            </div>

            <section class="zakat-detail-section" id="zakatDetailSection" hidden>
                <div class="zakat-detail-heading">
                    <div>
                        <h3>Rincian Penerima Zakat</h3>
                        <p>Tambahkan golongan, jumlah orang, dan nominal penyaluran untuk setiap golongan.</p>
                    </div>
                    <button type="button" class="fmu-btn" id="addZakatDetail"><i class="fa-solid fa-plus"></i>Tambah Golongan</button>
                </div>

                <div class="zakat-detail-labels" aria-hidden="true">
                    <span>Golongan Mustahik</span><span>Jumlah Penerima</span><span>Nominal Penyaluran</span><span></span>
                </div>
                <div id="zakatDetailRows"></div>

                <div class="zakat-detail-summary">
                    <span><strong id="zakatRecipientTotal">0</strong> penerima</span>
                    <span>Total rincian: <strong id="zakatNominalTotal">Rp 0</strong></span>
                    <span id="zakatDifference">Selisih: Rp 0</span>
                </div>
                @error('zakat_details')<span class="fmu-error">{{ $message }}</span>@enderror
                @error('zakat_details.*.asnaf')<span class="fmu-error">{{ $message }}</span>@enderror
                @error('zakat_details.*.jumlah_penerima')<span class="fmu-error">{{ $message }}</span>@enderror
                @error('zakat_details.*.nominal')<span class="fmu-error">{{ $message }}</span>@enderror
            </section>
        </div>
        <div class="fmu-actions"><a href="{{ route($pengeluaranIndexRoute) }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a><button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Pengeluaran</button></div>
    </form>
</div>
@endsection
@push('scripts')
<template id="zakatDetailTemplate">
    <div class="zakat-detail-row" data-zakat-detail-row>
        <div class="fmu-field">
            <label class="fmu-label">Golongan</label>
            <select class="fmu-control" data-zakat-field="asnaf">
                <option value="">Pilih golongan</option>
                @foreach($asnafLabels ?? [] as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="fmu-field">
            <label class="fmu-label">Jumlah Penerima</label>
            <input type="number" min="1" step="1" value="1" class="fmu-control" data-zakat-field="jumlah_penerima">
        </div>
        <div class="fmu-field">
            <label class="fmu-label">Nominal</label>
            <div class="fmu-input-icon-wrap"><i class="fa-solid fa-rupiah-sign"></i><input type="number" min="1" step="1" class="fmu-control" data-zakat-field="nominal" placeholder="0"></div>
        </div>
        <button type="button" class="zakat-remove-row" data-remove-zakat-detail title="Hapus golongan" aria-label="Hapus golongan"><i class="fa-solid fa-trash-can"></i></button>
    </div>
</template>
<script>
(() => {
    const amount = document.getElementById('jumlah'); const preview = document.getElementById('expenseAmountPreview');
    const syncAmount = () => { const value = Number(amount?.value || 0); preview.textContent = new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(value); };
    amount?.addEventListener('input', syncAmount); syncAmount();
    const input = document.getElementById('bukti_pembayaran'); const label = document.getElementById('expenseFileName');
    input?.addEventListener('change', () => { const file = input.files?.[0]; label.textContent = file?.name || ''; label.classList.toggle('is-visible', Boolean(file)); });

    const category = document.getElementById('kategori');
    const restrictionField = document.getElementById('infakRestrictionField');
    const restrictionType = document.getElementById('restriction_type');
    const recognitionDateHelp = document.getElementById('recognitionDateHelp');
    const section = document.getElementById('zakatDetailSection');
    const rows = document.getElementById('zakatDetailRows');
    const template = document.getElementById('zakatDetailTemplate');
    const addButton = document.getElementById('addZakatDetail');
    const recipientTotal = document.getElementById('zakatRecipientTotal');
    const nominalTotal = document.getElementById('zakatNominalTotal');
    const difference = document.getElementById('zakatDifference');
    const initialDetails = @json($oldZakatDetails);
    const rupiah = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);

    const reindexRows = () => {
        const currentRows = [...rows.querySelectorAll('[data-zakat-detail-row]')];
        currentRows.forEach((row, index) => {
            row.querySelectorAll('[data-zakat-field]').forEach(field => {
                field.name = `zakat_details[${index}][${field.dataset.zakatField}]`;
            });
            row.querySelector('[data-remove-zakat-detail]').disabled = currentRows.length === 1;
        });
    };

    const syncZakatTotals = () => {
        let people = 0; let detailAmount = 0;
        rows.querySelectorAll('[data-zakat-detail-row]').forEach(row => {
            people += Number(row.querySelector('[data-zakat-field="jumlah_penerima"]')?.value || 0);
            detailAmount += Number(row.querySelector('[data-zakat-field="nominal"]')?.value || 0);
        });
        const mainAmount = Number(amount?.value || 0);
        const delta = mainAmount - detailAmount;
        recipientTotal.textContent = new Intl.NumberFormat('id-ID').format(people);
        nominalTotal.textContent = rupiah(detailAmount);
        difference.textContent = `Selisih: ${rupiah(Math.abs(delta))}`;
        difference.classList.toggle('is-balanced', mainAmount > 0 && delta === 0);
    };

    const addRow = (data = {}) => {
        const row = template.content.firstElementChild.cloneNode(true);
        row.querySelector('[data-zakat-field="asnaf"]').value = data.asnaf || '';
        row.querySelector('[data-zakat-field="jumlah_penerima"]').value = data.jumlah_penerima || 1;
        row.querySelector('[data-zakat-field="nominal"]').value = data.nominal || '';
        row.querySelector('[data-remove-zakat-detail]').addEventListener('click', () => {
            row.remove(); reindexRows(); syncZakatTotals();
        });
        row.querySelectorAll('input, select').forEach(field => field.addEventListener('input', syncZakatTotals));
        rows.appendChild(row); reindexRows(); syncZakatTotals();
    };

    const syncZakatVisibility = () => {
        const selectedCode = category?.selectedOptions?.[0]?.dataset?.accountCode;
        const visible = selectedCode === '5210';
        section.hidden = !visible;
        section.querySelectorAll('input, select').forEach(field => { field.disabled = !visible; });
        if (visible && !rows.children.length) addRow();
        const isInfak = selectedCode === '5311';
        restrictionField.hidden = !isInfak;
        restrictionType.disabled = !isInfak;
        restrictionType.required = isInfak;
        recognitionDateHelp.textContent = ['5210', '5311', '5411'].includes(selectedCode)
            ? 'Gunakan tanggal saat manfaat diterima mustahik atau penerima manfaat.'
            : selectedCode === '2201'
                ? 'Gunakan tanggal pengembalian pokok wakaf temporer kepada wakif.'
                : 'Tanggal transaksi atau pembayaran.';
        syncZakatTotals();
    };

    (initialDetails.length ? initialDetails : [{}]).forEach(addRow);
    addButton?.addEventListener('click', () => addRow());
    category?.addEventListener('change', syncZakatVisibility);
    amount?.addEventListener('input', syncZakatTotals);
    syncZakatVisibility();
})();
</script>
@endpush

@push('styles')
<style>
.zakat-detail-section{margin-top:28px;padding-top:24px;border-top:1px solid #dce7df}.zakat-detail-heading{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:18px}.zakat-detail-heading h3{margin:0 0 4px;font-size:18px;color:#17233a}.zakat-detail-heading p{margin:0;color:#64748b}.zakat-detail-labels,.zakat-detail-row{display:grid;grid-template-columns:minmax(180px,1.3fr) minmax(130px,.7fr) minmax(180px,1fr) 44px;gap:14px;align-items:end}.zakat-detail-labels{padding:0 0 8px;color:#64748b;font-size:12px;font-weight:700}.zakat-detail-row{padding:14px 0;border-top:1px solid #edf2ee}.zakat-detail-row .fmu-label{display:none}.zakat-remove-row{width:44px;height:44px;border:1px solid #efc7c7;border-radius:6px;background:#fff;color:#c43f3f;cursor:pointer}.zakat-remove-row:disabled{opacity:.35;cursor:not-allowed}.zakat-detail-summary{display:flex;justify-content:flex-end;gap:24px;flex-wrap:wrap;margin-top:12px;padding-top:14px;border-top:1px solid #dce7df;color:#475569}.zakat-detail-summary .is-balanced{color:#15803d;font-weight:700}@media(max-width:760px){.zakat-detail-heading{align-items:flex-start;flex-direction:column}.zakat-detail-labels{display:none}.zakat-detail-row{grid-template-columns:1fr}.zakat-detail-row .fmu-label{display:block}.zakat-remove-row{justify-self:end}.zakat-detail-summary{justify-content:flex-start;gap:10px 18px}}
</style>
@endpush

{{-- FINUS DARK MODE LOCAL: pengeluaran/create.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="pengeluaran/create.blade.php">
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-body,.fr-card-body) { background:transparent !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-icon,.fr-card-icon,.fmu-stat-icon,.fr-stat-icon) { box-shadow:inset 0 1px 0 rgba(255,255,255,.025) !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-hero-badge,.fr-hero-badge) { border-color:rgba(255,255,255,.18) !important; background:rgba(4,35,15,.36) !important; color:#F5FFF7 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-side-note,.fr-tip-item,.fr-breakdown-item) { border-color:#293D31 !important; background:#101B14 !important; }
html[data-finus-theme="dark"] body .zakat-detail-section,html[data-finus-theme="dark"] body .zakat-detail-row,html[data-finus-theme="dark"] body .zakat-detail-summary{border-color:#293D31 !important}html[data-finus-theme="dark"] body .zakat-detail-heading h3{color:#F1F6F3 !important}html[data-finus-theme="dark"] body .zakat-remove-row{background:#101B14 !important;border-color:#673333 !important;color:#ff9b9b !important}
</style>
@endpush


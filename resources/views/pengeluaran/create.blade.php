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
        ['nama_penerima' => '', 'asnaf' => '', 'jumlah_penerima' => 1, 'nominal' => ''],
    ]);
    $oldTargetAsnaf = old('target_asnaf', $targetAsnaf ?? []);
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
                <div class="zakat-period-panel">
                    <div class="zakat-period-heading">
                        <div>
                            <h3>Batch Akhir Periode</h3>
                            <p>Satu periode hanya memiliki satu transaksi penyaluran. Tanggal transaksi mengikuti hari terakhir bulan.</p>
                        </div>
                        <div class="zakat-balance">
                            <span>Saldo zakat tersedia</span>
                            <strong>{{ 'Rp '.number_format($saldoZakat ?? 0, 0, ',', '.') }}</strong>
                            <small>Hak amil sudah dialokasikan saat penerimaan</small>
                        </div>
                    </div>
                    <div class="zakat-period-fields">
                        <div class="fmu-field">
                            <label class="fmu-label" for="periode_zakat">Periode penyaluran <span class="fmu-required">*</span></label>
                            <input type="month" id="periode_zakat" name="periode_zakat" value="{{ old('periode_zakat', now()->format('Y-m')) }}" class="fmu-control @error('periode_zakat') is-invalid @enderror">
                            @error('periode_zakat')<span class="fmu-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="zakat-period-note">
                            <i class="fa-solid fa-scale-balanced"></i>
                            <span>Acuan hak amil periode ini <strong>{{ number_format($persentaseAmil ?? 0, 2, ',', '.') }}%</strong>. Nilai ini tidak dipotong lagi dari saldo lama.</span>
                        </div>
                    </div>

                    <div class="zakat-target-heading">
                        <div><strong>Target Mustahik Per Asnaf</strong><span>Opsional. Jika digunakan, total target harus 100% dari dana mustahik.</span></div>
                        <strong id="zakatTargetTotal">0%</strong>
                    </div>
                    <div class="zakat-target-grid">
                        @foreach($asnafLabels ?? [] as $value => $label)
                            <label class="zakat-target-item">
                                <span>{{ $label }}</span>
                                <span class="zakat-target-control"><input type="number" min="0" max="100" step="0.01" name="target_asnaf[{{ $value }}]" value="{{ $oldTargetAsnaf[$value] ?? 0 }}" data-zakat-target><b>%</b></span>
                            </label>
                        @endforeach
                    </div>
                    @error('target_asnaf')<span class="fmu-error">{{ $message }}</span>@enderror
                    @error('target_asnaf.*')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="zakat-detail-heading">
                    <div>
                        <h3>Rincian Penerima Zakat</h3>
                        <p>Satu baris dapat digunakan untuk satu mustahik atau satu kelompok penerima.</p>
                    </div>
                    <button type="button" class="fmu-btn" id="addZakatDetail"><i class="fa-solid fa-plus"></i>Tambah Penerima</button>
                </div>

                <div class="zakat-detail-labels" aria-hidden="true">
                    <span>Nama / Program</span><span>Golongan Mustahik</span><span>Jumlah</span><span>Nominal</span><span></span>
                </div>
                <div id="zakatDetailRows"></div>

                <div class="zakat-detail-summary">
                    <span><strong id="zakatRecipientTotal">0</strong> penerima</span>
                    <span>Total rincian: <strong id="zakatNominalTotal">Rp 0</strong></span>
                    <span id="zakatDifference">Selisih: Rp 0</span>
                </div>
                @error('zakat_details')<span class="fmu-error">{{ $message }}</span>@enderror
                @error('zakat_details.*.nama_penerima')<span class="fmu-error">{{ $message }}</span>@enderror
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
            <label class="fmu-label">Nama / Program</label>
            <input type="text" maxlength="150" class="fmu-control" data-zakat-field="nama_penerima" placeholder="Nama mustahik atau program">
        </div>
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
        <details class="zakat-recipient-details">
            <summary>Identitas penerima (opsional)</summary>
            <div class="zakat-recipient-details-grid">
                <div class="fmu-field"><label class="fmu-label">NIK / Identitas</label><input type="text" maxlength="30" class="fmu-control" data-zakat-field="nik_penerima"></div>
                <div class="fmu-field"><label class="fmu-label">No. HP</label><input type="text" maxlength="30" class="fmu-control" data-zakat-field="no_hp_penerima"></div>
                <div class="fmu-field"><label class="fmu-label">Alamat</label><input type="text" maxlength="500" class="fmu-control" data-zakat-field="alamat_penerima"></div>
            </div>
        </details>
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
    const period = document.getElementById('periode_zakat');
    const transactionDate = document.getElementById('tanggal');
    const targetInputs = [...document.querySelectorAll('[data-zakat-target]')];
    const targetTotal = document.getElementById('zakatTargetTotal');
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
        row.querySelector('[data-zakat-field="nama_penerima"]').value = data.nama_penerima || '';
        row.querySelector('[data-zakat-field="asnaf"]').value = data.asnaf || '';
        row.querySelector('[data-zakat-field="jumlah_penerima"]').value = data.jumlah_penerima || 1;
        row.querySelector('[data-zakat-field="nominal"]').value = data.nominal || '';
        row.querySelector('[data-zakat-field="nik_penerima"]').value = data.nik_penerima || '';
        row.querySelector('[data-zakat-field="no_hp_penerima"]').value = data.no_hp_penerima || '';
        row.querySelector('[data-zakat-field="alamat_penerima"]').value = data.alamat_penerima || '';
        row.querySelector('[data-remove-zakat-detail]').addEventListener('click', () => {
            row.remove(); reindexRows(); syncZakatTotals();
        });
        row.querySelectorAll('input, select').forEach(field => field.addEventListener('input', syncZakatTotals));
        rows.appendChild(row); reindexRows(); syncZakatTotals();
    };

    const syncPeriodEnd = () => {
        if (!period?.value || !transactionDate) return;
        const [year, month] = period.value.split('-').map(Number);
        const lastDay = new Date(year, month, 0).getDate();
        transactionDate.value = `${year}-${String(month).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;
    };

    const syncTargetTotal = () => {
        const total = targetInputs.reduce((sum, field) => sum + Number(field.value || 0), 0);
        targetTotal.textContent = `${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(total)}%`;
        targetTotal.classList.toggle('is-balanced', Math.abs(total - 100) < 0.01 || total === 0);
    };

    const syncZakatVisibility = () => {
        const selectedCode = category?.selectedOptions?.[0]?.dataset?.accountCode;
        const visible = selectedCode === '5210';
        section.hidden = !visible;
        section.querySelectorAll('input, select').forEach(field => { field.disabled = !visible; });
        if (visible && !rows.children.length) addRow();
        if (visible) syncPeriodEnd();
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
    period?.addEventListener('change', syncPeriodEnd);
    targetInputs.forEach(field => field.addEventListener('input', syncTargetTotal));
    category?.addEventListener('change', syncZakatVisibility);
    amount?.addEventListener('input', syncZakatTotals);
    syncTargetTotal(); syncZakatVisibility();
})();
</script>
@endpush

@push('styles')
<style>
.zakat-detail-section{margin-top:28px;padding-top:24px;border-top:1px solid #dce7df}.zakat-period-panel{margin-bottom:28px;padding-bottom:24px;border-bottom:1px solid #dce7df}.zakat-period-heading,.zakat-detail-heading{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:18px}.zakat-period-heading h3,.zakat-detail-heading h3{margin:0 0 4px;font-size:18px;color:#17233a}.zakat-period-heading p,.zakat-detail-heading p{margin:0;color:#64748b}.zakat-balance{text-align:right}.zakat-balance span,.zakat-balance small{display:block;color:#64748b}.zakat-balance strong{display:block;margin:2px 0;color:#08752f;font-size:20px}.zakat-period-fields{display:grid;grid-template-columns:minmax(220px,.7fr) minmax(280px,1.3fr);gap:16px;align-items:end}.zakat-period-note{display:flex;align-items:center;gap:10px;min-height:46px;padding:10px 12px;border:1px solid #cfe2d5;border-radius:6px;background:#f6fbf7;color:#365443}.zakat-target-heading{display:flex;align-items:end;justify-content:space-between;gap:16px;margin:20px 0 10px}.zakat-target-heading div span{display:block;margin-top:3px;color:#64748b;font-size:12px}.zakat-target-heading>strong{color:#b45309}.zakat-target-heading>strong.is-balanced{color:#15803d}.zakat-target-grid{display:grid;grid-template-columns:repeat(4,minmax(140px,1fr));gap:10px}.zakat-target-item{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:9px 10px;border:1px solid #dce7df;border-radius:6px}.zakat-target-item>span:first-child{font-size:12px;font-weight:700;color:#475569}.zakat-target-control{display:flex;align-items:center;gap:4px}.zakat-target-control input{width:64px;border:0;background:transparent;text-align:right;color:#17233a}.zakat-detail-labels,.zakat-detail-row{display:grid;grid-template-columns:minmax(190px,1.15fr) minmax(150px,.9fr) minmax(100px,.55fr) minmax(170px,1fr) 44px;gap:14px;align-items:end}.zakat-detail-labels{padding:0 0 8px;color:#64748b;font-size:12px;font-weight:700}.zakat-detail-row{padding:14px 0;border-top:1px solid #edf2ee}.zakat-detail-row .fmu-label{display:none}.zakat-recipient-details{grid-column:1/-1}.zakat-recipient-details summary{cursor:pointer;color:#08752f;font-size:12px;font-weight:700}.zakat-recipient-details-grid{display:grid;grid-template-columns:1fr 1fr 1.5fr;gap:12px;margin-top:12px}.zakat-recipient-details-grid .fmu-label{display:block}.zakat-remove-row{width:44px;height:44px;border:1px solid #efc7c7;border-radius:6px;background:#fff;color:#c43f3f;cursor:pointer}.zakat-remove-row:disabled{opacity:.35;cursor:not-allowed}.zakat-detail-summary{display:flex;justify-content:flex-end;gap:24px;flex-wrap:wrap;margin-top:12px;padding-top:14px;border-top:1px solid #dce7df;color:#475569}.zakat-detail-summary .is-balanced{color:#15803d;font-weight:700}@media(max-width:980px){.zakat-target-grid{grid-template-columns:repeat(2,minmax(140px,1fr))}.zakat-detail-labels{display:none}.zakat-detail-row{grid-template-columns:1fr 1fr}.zakat-detail-row .fmu-label{display:block}.zakat-remove-row{justify-self:end}}@media(max-width:760px){.zakat-period-heading,.zakat-detail-heading{align-items:flex-start;flex-direction:column}.zakat-balance{text-align:left}.zakat-period-fields,.zakat-target-grid,.zakat-detail-row,.zakat-recipient-details-grid{grid-template-columns:1fr}.zakat-remove-row{justify-self:end}.zakat-detail-summary{justify-content:flex-start;gap:10px 18px}}
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
html[data-finus-theme="dark"] body .zakat-period-panel{border-color:#293D31 !important}html[data-finus-theme="dark"] body .zakat-period-heading h3{color:#F1F6F3 !important}html[data-finus-theme="dark"] body .zakat-period-note,html[data-finus-theme="dark"] body .zakat-target-item{background:#101B14 !important;border-color:#293D31 !important;color:#D7E7DC !important}html[data-finus-theme="dark"] body .zakat-target-item>span:first-child,html[data-finus-theme="dark"] body .zakat-target-control input{color:#D7E7DC !important}
</style>
@endpush


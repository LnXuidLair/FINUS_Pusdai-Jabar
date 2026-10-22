@extends('layouts.app')
@section('title', 'Tambah Pengeluaran')
@section('hide-page-header', '1')
@section('hide-global-errors', '1')
@php
    $pengeluaranStoreRoute = request()->routeIs('pegawai.keuangan.*')
        ? 'pegawai.keuangan.pengeluaran.store'
        : 'admin.pengeluaran.store';
    $pengeluaranIndexRoute = request()->routeIs('pegawai.keuangan.*')
        ? 'pegawai.keuangan.pengeluaran.index'
        : 'admin.pengeluaran.index';
    $submittedZakatDetails = collect(old('zakat_details', []))->keyBy('asnaf');
    $oldZakatDetails = collect($asnafLabels ?? [])->map(fn ($label, $asnaf) => array_merge(
        ['nama_penerima' => '', 'jumlah_penerima' => 0, 'nominal' => ''],
        $submittedZakatDetails->get($asnaf, []),
        ['asnaf' => $asnaf]
    ))->values()->all();
    $oldTargetAsnaf = old('target_asnaf', $targetAsnaf ?? []);
    $cashFundBalances = config('cash_transfers.enabled') ? app(\App\Services\Accounting\CashTransferService::class)->balances() : null;
    $defaultPaymentAccountId = ($akunPembayaran ?? collect())->firstWhere('kode_akun', '1101')?->id;
@endphp
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero"><div class="fmu-hero-main"><span class="fmu-hero-icon"><i class="fa-solid fa-receipt"></i></span><div><h1>Tambah Pengeluaran</h1><p>Catat pengeluaran masjid dan lampirkan bukti pembayaran bila tersedia.</p></div></div><div class="fmu-hero-actions"><span class="fmu-hero-badge"><i class="fa-solid fa-shield-halved"></i>Pencatatan Keuangan</span></div></section>
    @if(config('cash_transfers.enabled') && auth('admin')->check())
    <p><a href="{{ route('admin.cash-transfers.index') }}">Lihat saldo golongan per Kas/Bank atau pindahkan saldo</a></p>
    @endif
    <form method="POST" action="{{ route($pengeluaranStoreRoute) }}" enctype="multipart/form-data" class="fmu-card" id="expenseForm">
        @csrf
        @if($errors->any())
            <div id="expenseErrorSummary" role="alert" tabindex="-1" class="fmu-expense-error-summary">
                <strong><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> Pengeluaran belum berhasil disimpan</strong>
                <p>Periksa penyebab berikut, lalu perbaiki datanya dan coba kembali:</p>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <div id="expenseClientError" role="alert" tabindex="-1" class="fmu-expense-error-summary" hidden></div>
        <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-pen-to-square"></i></span><div><h2>Data Pengeluaran</h2><p>Isi kategori, deskripsi, nominal, dan tanggal transaksi.</p></div></div></div>
        <div class="fmu-card-body">
            <div class="fmu-form-grid">
                <div class="fmu-field">
                    <label class="fmu-label" for="kategori">Kategori Pengeluaran <span class="fmu-required">*</span></label>
                    <div class="fmu-input-icon-wrap">
                        <i class="fa-solid fa-tags"></i>
                        <select id="kategori" name="transaction_category_id" class="fmu-control @error('transaction_category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori Pengeluaran --</option>
                            @foreach($fundTypeLabels ?? [] as $group => $label)
                                @php($categories = ($transactionCategoriesGrouped ?? collect())->get($group, collect()))
                                @continue($categories->isEmpty())
                                <optgroup label="{{ $label }}">
                                    @foreach($categories as $item)
                                        <option value="{{ $item->id }}"
                                            data-form-type="{{ $item->form_type }}"
                                            data-fund="{{ $item->resolvedFundType() }}"
                                            data-fund-balance="{{ (int) round($saldoDanaJurnal[$item->resolvedFundType()] ?? 0) }}"
                                            data-fund-label="{{ $fundTypeLabels[$item->resolvedFundType()] ?? 'Belum dipetakan' }}"
                                            data-debit-code="{{ $item->coa?->kode_akun }}"
                                            data-debit-name="{{ $item->coa?->nama_akun }}"
                                            data-requires-asnaf="{{ $item->requires_asnaf ? 1 : 0 }}"
                                            data-requires-employee="{{ $item->requires_employee ? 1 : 0 }}"
                                            data-requires-assignment-proof="{{ $item->requires_assignment_proof ? 1 : 0 }}"
                                            @selected((string) old('transaction_category_id') === (string) $item->id)>
                                            {{ $item->name }}{{ $item->coa ? ' ('.$item->coa->kode_akun.' - '.$item->coa->nama_akun.')' : '' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    @error('transaction_category_id')<span class="fmu-error">{{ $message }}</span>@enderror
                    <span class="fmu-help" style="font-size: 11.5px; color: #64748b; margin-top: 5px; display: block;">
                        <i class="fa-solid fa-circle-info" style="color: #179b40; margin-right: 4px;"></i>
                        Gaji rutin dicatat otomatis melalui menu <strong>Penggajian</strong>. Pilih akun ini hanya untuk honorarium tugas atau kegiatan.
                    </span>
                </div>
                <div class="fmu-field"><label class="fmu-label" for="tanggal">Tanggal <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-calendar-day"></i><input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="fmu-control @error('tanggal') is-invalid @enderror" required></div><span class="fmu-help" id="recognitionDateHelp">Tanggal transaksi atau pembayaran.</span>@error('tanggal')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field">
                    <span class="fmu-label">Sumber Dana</span>
                    <div class="fmu-derived-value"><i class="fa-solid fa-layer-group"></i><strong id="fundSourceLabel">Pilih kategori terlebih dahulu</strong></div>
                    <span class="fmu-help">Ditentukan otomatis dari kode akun kategori. Saldo dihitung dari jurnal yang sudah diposting.</span>
                </div>
                <div class="fmu-field">
                    <label class="fmu-label" for="coa_kredit_id">Sumber Pembayaran <span class="fmu-required">*</span></label>
                    <div class="fmu-input-icon-wrap">
                        <i class="fa-solid fa-building-columns"></i>
                        <select id="coa_kredit_id" name="coa_kredit_id" class="fmu-control @error('coa_kredit_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kas atau Bank --</option>
                            @foreach($akunPembayaran ?? [] as $akun)
                                <option value="{{ $akun->id }}" data-code="{{ $akun->kode_akun }}" data-saldo="{{ (int) ($saldoKasBankJurnal[$akun->id] ?? 0) }}" @selected((string) old('coa_kredit_id', $defaultPaymentAccountId) === (string) $akun->id)>
                                    {{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <span class="fmu-help">Pilih Kas untuk pembayaran tunai atau Bank untuk transfer.</span>
                    @error('coa_kredit_id')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>
                <div class="fmu-field fmu-field-full"><label class="fmu-label" for="deskripsi">Deskripsi <span class="fmu-required">*</span></label><textarea id="deskripsi" name="deskripsi" class="fmu-textarea @error('deskripsi') is-invalid @enderror" placeholder="Jelaskan keperluan pengeluaran secara singkat dan jelas" required>{{ old('deskripsi') }}</textarea>@error('deskripsi')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="jumlah">Jumlah <span class="fmu-required">*</span></label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-rupiah-sign"></i><input type="number" min="1" step="1" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" class="fmu-control @error('jumlah') is-invalid @enderror" placeholder="0" required></div><span class="fmu-help" id="expenseAmountPreview">Rp 0</span>@error('jumlah')<span class="fmu-error">{{ $message }}</span>@enderror</div>
                <div class="fmu-field"><label class="fmu-label" for="bukti_pembayaran">Bukti Pembayaran</label><label class="fmu-upload" for="bukti_pembayaran"><span><i class="fa-solid fa-cloud-arrow-up"></i><strong>Pilih bukti pembayaran</strong><span>JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.</span><span class="fmu-file-name" id="expenseFileName"></span></span></label><input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf" class="d-none">@error('bukti_pembayaran')<span class="fmu-error">{{ $message }}</span>@enderror</div>
            </div>

            <div class="fmu-expense-balance-status" id="expenseBalanceStatus" role="status" aria-live="polite">
                <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
                <span>Pilih kategori dan sumber pembayaran untuk melihat saldo dana serta Kas/Bank yang tersedia.</span>
            </div>
            <section class="journal-preview" aria-live="polite">
                <div class="journal-preview-head"><div><strong>Pratinjau Jurnal</strong><span>Akun ditentukan otomatis dari kategori dan sumber pembayaran.</span></div><span class="journal-preview-badge"><i class="fa-solid fa-lock"></i>Otomatis</span></div>
                <div class="journal-preview-row"><span>Debit</span><strong id="journalDebitAccount">Pilih kategori pengeluaran</strong><b id="journalDebitAmount">Rp 0</b></div>
                <div class="journal-preview-row"><span>Kredit</span><strong id="journalCreditAccount">Pilih sumber pembayaran</strong><b id="journalCreditAmount">Rp 0</b></div>
            </section>

            <section class="honorarium-section" id="honorariumSection" hidden>
                <div class="honorarium-heading">
                    <div>
                        <h3 id="honorariumHeadingTitle">Data Honorarium</h3>
                        <p id="honorariumHeadingHelp">Pilih penerima dan lampirkan dokumen pendukung jika tersedia. Surat pengantar magang, surat tugas, atau surat persetujuan dapat digunakan.</p>
                    </div>
                    <span><i class="fa-solid fa-user-check"></i> Penghasilan Tambahan</span>
                </div>
                <div class="fmu-form-grid">
                    <div class="fmu-field" id="honorariumRecipientTypeField" hidden>
                        <label class="fmu-label" for="jenis_penerima">Jenis Penerima <span class="fmu-required">*</span></label>
                        <select id="jenis_penerima" name="jenis_penerima" class="fmu-control" disabled>
                            <option value="pegawai" @selected(old('jenis_penerima', 'pegawai') === 'pegawai')>Pegawai PUSDAI</option>
                            <option value="eksternal" @selected(old('jenis_penerima') === 'eksternal')>Pihak luar / mahasiswa magang / penceramah</option>
                        </select>
                        <span class="fmu-help">Nama penerima eksternal akan tercatat pada deskripsi pengeluaran.</span>
                    </div>
                    <div class="fmu-field" id="honorariumEmployeeField">
                        <label class="fmu-label" for="id_pegawai">Pegawai Penerima <span class="fmu-required" id="employeeRequiredMark">*</span></label>
                        <div class="fmu-input-icon-wrap">
                            <i class="fa-solid fa-user"></i>
                            <select id="id_pegawai" name="id_pegawai" class="fmu-control @error('id_pegawai') is-invalid @enderror" disabled>
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($pegawaiList ?? [] as $pegawai)
                                    <option value="{{ $pegawai->id }}" @selected((string) old('id_pegawai') === (string) $pegawai->id)>
                                        {{ $pegawai->nama_pegawai }}{{ $pegawai->jabatan ? ' - '.$pegawai->jabatan : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <span class="fmu-help">Honorarium akan tampil pada riwayat penghasilan pegawai ini.</span>
                        @error('id_pegawai')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="fmu-field" id="honorariumExternalField" hidden>
                        <label class="fmu-label" for="nama_penerima_eksternal">Nama Penerima Eksternal <span class="fmu-required">*</span></label>
                        <input id="nama_penerima_eksternal" name="nama_penerima_eksternal" type="text" maxlength="150" value="{{ old('nama_penerima_eksternal') }}" class="fmu-control @error('nama_penerima_eksternal') is-invalid @enderror" placeholder="Contoh: Ahmad, mahasiswa magang" disabled>
                        <span class="fmu-help">Masukkan nama sesuai dokumen atau bukti pembayaran.</span>
                        @error('nama_penerima_eksternal')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="fmu-field">
                        <label class="fmu-label" for="bukti_surat_tugas"><span id="assignmentDocumentLabel">Dokumen Pendukung Honorarium</span> <span id="assignmentRequiredMark" class="fmu-required" hidden>*</span></label>
                        <label class="fmu-upload" for="bukti_surat_tugas"><span><i class="fa-solid fa-file-signature"></i><strong>Pilih dokumen pendukung</strong><span>Surat pengantar magang, surat tugas, persetujuan, atau dokumen terkait (JPG, PNG, PDF; maksimal 2 MB).</span><span class="fmu-file-name" id="assignmentFileName"></span></span></label>
                        <input type="file" id="bukti_surat_tugas" name="bukti_surat_tugas" accept=".jpg,.jpeg,.png,.pdf" class="d-none" disabled>
                        <span class="fmu-help" id="assignmentDocumentHelp">Opsional. Dokumen diterbitkan oleh pihak berwenang; FINUS hanya menyimpannya.</span>
                        @error('bukti_surat_tugas')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </section>

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
                            <small>Seluruh saldo zakat tersedia untuk asnaf yang berhak</small>
                        </div>
                    </div>
                    <div class="zakat-period-fields">
                        <div class="fmu-field">
                            <label class="fmu-label" for="periode_zakat">Periode penyaluran <span class="fmu-required">*</span></label>
                            <input type="month" id="periode_zakat" name="periode_zakat" value="{{ old('periode_zakat', now()->format('Y-m')) }}" class="fmu-control @error('periode_zakat') is-invalid @enderror">
                            @error('periode_zakat')<span class="fmu-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="zakat-period-note"><i class="fa-solid fa-scale-balanced"></i><span>Penyaluran zakat dicatat penuh kepada mustahik tanpa potongan alokasi amil.</span></div>
                    </div>

                    <div class="zakat-target-heading">
                        <div><strong>Persentase Penyaluran Per Asnaf</strong><span>Total persentase wajib 100%. Nominal tiap asnaf dihitung otomatis dari jumlah pengeluaran.</span></div>
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
                        <p>Isi penanggung jawab untuk setiap asnaf dengan persentase lebih dari 0%. Nominal dihitung otomatis; selisih pembulatan disesuaikan agar total tetap tepat.</p>
                    </div>
                </div>

                <div class="zakat-detail-labels" aria-hidden="true">
                    <span>Penanggung Jawab</span><span>Golongan Asnaf</span><span>Total Nominal Golongan</span>
                </div>
                <div id="zakatDetailRows"></div>

                <div class="zakat-detail-summary">
                    <span><strong id="zakatRecipientTotal">0</strong> golongan disalurkan</span>
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
            <label class="fmu-label">Penanggung Jawab</label>
            <input type="text" maxlength="150" class="fmu-control" data-zakat-field="nama_penerima" placeholder="Nama penanggung jawab asnaf">
        </div>
        <div class="fmu-field">
            <label class="fmu-label">Golongan</label>
            <input type="hidden" data-zakat-field="asnaf">
            <span class="fmu-control" data-asnaf-label></span>
        </div>
        <div class="fmu-field">
            <label class="fmu-label">Nominal</label>
            <div class="fmu-input-icon-wrap"><i class="fa-solid fa-rupiah-sign"></i><input type="number" min="0" step="1" class="fmu-control" data-zakat-field="nominal" placeholder="0" readonly></div>
        </div>
        <details class="zakat-recipient-details">
            <summary>Identitas penanggung jawab (opsional)</summary>
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
    const assignmentInput = document.getElementById('bukti_surat_tugas'); const assignmentLabel = document.getElementById('assignmentFileName');
    const recipientType = document.getElementById('jenis_penerima');
    const recipientTypeField = document.getElementById('honorariumRecipientTypeField');
    const employeeField = document.getElementById('honorariumEmployeeField');
    const externalField = document.getElementById('honorariumExternalField');
    const externalInput = document.getElementById('nama_penerima_eksternal');
    const employeeRequiredMark = document.getElementById('employeeRequiredMark');
    const documentRequiredMark = document.getElementById('assignmentRequiredMark');
    const documentLabel = document.getElementById('assignmentDocumentLabel');
    const documentHelp = document.getElementById('assignmentDocumentHelp');
    const honorariumHeadingTitle = document.getElementById('honorariumHeadingTitle');
    assignmentInput?.addEventListener('change', () => { const file = assignmentInput.files?.[0]; assignmentLabel.textContent = file?.name || ''; assignmentLabel.classList.toggle('is-visible', Boolean(file)); });

    const form = document.getElementById('expenseForm');
    const clientError = document.getElementById('expenseClientError');
    const balanceStatus = document.getElementById('expenseBalanceStatus');
    const category = document.getElementById('kategori');
    const fundSourceLabel = document.getElementById('fundSourceLabel');
    const paymentAccount = document.getElementById('coa_kredit_id');
    const journalDebitAccount = document.getElementById('journalDebitAccount');
    const journalCreditAccount = document.getElementById('journalCreditAccount');
    const journalDebitAmount = document.getElementById('journalDebitAmount');
    const journalCreditAmount = document.getElementById('journalCreditAmount');
    const honorariumSection = document.getElementById('honorariumSection');
    const employeeInput = document.getElementById('id_pegawai');
    const recognitionDateHelp = document.getElementById('recognitionDateHelp');
    const section = document.getElementById('zakatDetailSection');
    const rows = document.getElementById('zakatDetailRows');
    const template = document.getElementById('zakatDetailTemplate');
    const recipientTotal = document.getElementById('zakatRecipientTotal');
    const nominalTotal = document.getElementById('zakatNominalTotal');
    const difference = document.getElementById('zakatDifference');
    const period = document.getElementById('periode_zakat');
    const transactionDate = document.getElementById('tanggal');
    const targetInputs = [...document.querySelectorAll('[data-zakat-target]')];
    const targetTotal = document.getElementById('zakatTargetTotal');
    const initialDetails = @json($oldZakatDetails);
    const rupiah = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);

    const cashFundBalances = @json($cashFundBalances);
    const syncBalanceStatus = () => {
        const cat = category?.selectedOptions?.[0];
        const bank = paymentAccount?.selectedOptions?.[0];
        if (!cat?.value || !bank?.value) {
            balanceStatus.textContent = 'Pilih kategori dan sumber pembayaran untuk melihat saldo yang tersedia.';
            balanceStatus.classList.remove('is-insufficient');
            return;
        }
        const dana = Number(cat.dataset.fundBalance || 0);
        const kas = Number(bank.dataset.saldo || 0);
        const nominal = Number(amount?.value || 0);
        const lowFund = nominal > 0 && nominal > dana;
        const fundAtSource = cashFundBalances === null ? kas : Number(cashFundBalances[cat.dataset.fund]?.[bank.dataset.code] || 0);
        const lowCash = nominal > 0 && nominal > Math.min(kas, fundAtSource);
        balanceStatus.textContent = `Saldo ${cat.dataset.fundLabel || 'dana'}: ${rupiah(dana)} · Saldo ${bank.textContent.trim()}: ${rupiah(kas)}. Golongan pada sumber ini: ${rupiah(fundAtSource)}.`
            + (lowFund || lowCash ? ` Jumlah transaksi melebihi saldo ${[lowFund ? 'dana' : '', lowCash ? 'sumber pembayaran' : ''].filter(Boolean).join(' dan ')} yang tersedia. Pengeluaran akan ditolak.` : ' Saldo berasal dari jurnal yang sudah diposting.');
        balanceStatus.classList.toggle('is-insufficient', lowFund || lowCash);
    };

    // Validasi HTML oleh browser bisa menghentikan submit SEBELUM Laravel menerima
    // request. Tampilkan ringkasan sendiri agar pengguna tidak merasa tombol mati.
    form?.addEventListener('invalid', event => {
        const invalidField = event.target;
        const fieldLabel = invalidField?.labels?.[0]?.textContent?.trim()
            || invalidField?.getAttribute('name') || 'data pengeluaran';
        clientError.textContent = `Periksa kolom "${fieldLabel}": ${invalidField.validationMessage || 'nilai belum valid'}.`;
        clientError.hidden = false;
        clientError.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }, true);
    form?.addEventListener('input', () => { clientError.hidden = true; });
    form?.addEventListener('change', () => { clientError.hidden = true; });
    for (const fileField of [input, assignmentInput]) {
        fileField?.addEventListener('change', () => {
            const file = fileField.files?.[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024 || !/\.(jpe?g|png|pdf)$/i.test(file.name)) {
                fileField.value = '';
                const fileLabel = fileField === input ? 'Bukti pembayaran' : 'Dokumen pendukung';
                clientError.textContent = `${fileLabel} harus berupa JPG, JPEG, PNG, atau PDF dengan ukuran maksimal 2 MB. Pilih file lain.`;
                clientError.hidden = false;
                clientError.scrollIntoView({ block: 'center', behavior: 'smooth' });
            }
        });
    }
    if (document.getElementById('expenseErrorSummary')) {
        requestAnimationFrame(() => document.getElementById('expenseErrorSummary')?.scrollIntoView({ block: 'center' }));
    }
    form?.addEventListener('submit', () => {
        const button = form.querySelector('button[type="submit"]');
        if (button) {
            button.disabled = true;
            button.textContent = 'Menyimpan pengeluaran...';
        }
    });

    const reindexRows = () => {
        const currentRows = [...rows.querySelectorAll('[data-zakat-detail-row]')];
        currentRows.forEach((row, index) => {
            row.querySelectorAll('[data-zakat-field]').forEach(field => {
                field.name = `zakat_details[${index}][${field.dataset.zakatField}]`;
            });
        });
    };

    const syncZakatTotals = () => {
        let people = 0; let detailAmount = 0;
        const mainAmount = Math.max(0, Math.floor(Number(amount?.value || 0)));
        let cumulative = 0; let allocated = 0;
        rows.querySelectorAll('[data-zakat-detail-row]').forEach(row => {
            const asnaf = row.querySelector('[data-zakat-field="asnaf"]').value;
            const target = targetInputs.find(input => input.name === `target_asnaf[${asnaf}]`);
            const weight = Math.round(Number(target?.value || 0) * 100);
            cumulative += weight;
            const subtotal = Math.floor(mainAmount * cumulative / 10000);
            const nominal = subtotal - allocated;
            allocated = subtotal;
            row.querySelector('[data-zakat-field="nominal"]').value = nominal;
            row.querySelector('[data-zakat-field="nama_penerima"]').required = weight > 0 && !section.hidden;
            if (weight > 0) people++;
            detailAmount += nominal;
        });
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
        const asnafLabels = @json($asnafLabels ?? []);
        row.querySelector('[data-asnaf-label]').textContent = asnafLabels[data.asnaf] || '';
        row.querySelector('[data-zakat-field="nominal"]').value = data.nominal || '';
        row.querySelector('[data-zakat-field="nik_penerima"]').value = data.nik_penerima || '';
        row.querySelector('[data-zakat-field="no_hp_penerima"]').value = data.no_hp_penerima || '';
        row.querySelector('[data-zakat-field="alamat_penerima"]').value = data.alamat_penerima || '';
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
        const selected = category?.selectedOptions?.[0];
        const formType = selected?.dataset?.formType || '';
        const isHonorarium = formType === 'honorarium';
        const isExternal = isHonorarium && recipientType?.value === 'eksternal';
        const requiresEmployee = isHonorarium ? !isExternal : selected?.dataset?.requiresEmployee === '1';
        const requiresAssignmentProof = !isHonorarium && selected?.dataset?.requiresAssignmentProof === '1';
        const showHonorarium = isHonorarium || requiresEmployee || requiresAssignmentProof;
        honorariumSection.hidden = !showHonorarium;
        recipientTypeField.hidden = !isHonorarium;
        recipientType.disabled = !isHonorarium;
        employeeField.hidden = !showHonorarium || isExternal || (!isHonorarium && !requiresEmployee);
        employeeInput.disabled = !showHonorarium || isExternal || (!isHonorarium && !requiresEmployee);
        employeeInput.required = requiresEmployee;
        employeeRequiredMark.hidden = !requiresEmployee;
        externalField.hidden = !isExternal;
        externalInput.disabled = !isExternal;
        externalInput.required = isExternal;
        assignmentInput.disabled = !showHonorarium;
        assignmentInput.required = requiresAssignmentProof;
        documentRequiredMark.hidden = !requiresAssignmentProof;
        documentLabel.textContent = isHonorarium ? 'Dokumen Pendukung Honorarium' : 'Dokumen Pendukung Pengeluaran';
        documentHelp.textContent = requiresAssignmentProof
            ? 'Wajib untuk kategori pengeluaran ini.'
            : 'Opsional. FINUS menerima dokumen dari instansi asal, PUSDAI, atau pihak berwenang lainnya.';
        honorariumHeadingTitle.textContent = isHonorarium ? 'Data Honorarium' : 'Data Pendukung Pengeluaran';
        const visible = selected?.dataset?.requiresAsnaf === '1' || formType === 'zakat_distribution';
        section.hidden = !visible;
        section.querySelectorAll('input, select').forEach(field => { field.disabled = !visible; });
        if (visible && !rows.children.length) addRow();
        if (visible) syncPeriodEnd();
        recognitionDateHelp.textContent = ['zakat_distribution', 'wakaf_distribution'].includes(formType)
            ? 'Gunakan tanggal saat manfaat diterima mustahik atau penerima manfaat.'
            : formType === 'temporary_wakaf_return'
                ? 'Gunakan tanggal pengembalian pokok wakaf temporer kepada wakif.'
                : 'Tanggal transaksi atau pembayaran.';
        syncZakatTotals();
    };

    recipientType?.addEventListener('change', syncZakatVisibility);

    const syncCategoryMapping = () => {
        const selected = category?.selectedOptions?.[0];
        fundSourceLabel.textContent = selected?.value
            ? selected.dataset.fundLabel || 'Belum dipetakan'
            : 'Pilih kategori terlebih dahulu';
        journalDebitAccount.textContent = selected?.value
            ? `${selected.dataset.debitCode || ''} ${selected.dataset.debitName || ''}`.trim()
            : 'Pilih kategori pengeluaran';
        syncZakatVisibility();
        syncBalanceStatus();
    };

    const syncJournalPreview = () => {
        const formatted = rupiah(Number(amount?.value || 0));
        journalDebitAmount.textContent = formatted;
        journalCreditAmount.textContent = formatted;
        const selectedPayment = paymentAccount?.selectedOptions?.[0];
        journalCreditAccount.textContent = selectedPayment?.value
            ? selectedPayment.textContent.trim()
            : 'Pilih sumber pembayaran';
        syncBalanceStatus();
    };

    (initialDetails.length ? initialDetails : [{}]).forEach(addRow);
    period?.addEventListener('change', syncPeriodEnd);
    targetInputs.forEach(field => field.addEventListener('input', () => { syncTargetTotal(); syncZakatTotals(); }));
    category?.addEventListener('change', syncCategoryMapping);
    paymentAccount?.addEventListener('change', syncJournalPreview);
    amount?.addEventListener('input', () => { syncZakatTotals(); syncJournalPreview(); });
    syncTargetTotal(); syncCategoryMapping(); syncJournalPreview();
})();
</script>
@endpush

@push('styles')
<style>
#honorariumSection [hidden], #honorariumSection[hidden] { display: none !important; }
.fmu-expense-error-summary{margin:16px 20px;padding:15px 17px;border:1px solid #f0a5a5;border-left:5px solid #c93232;border-radius:6px;background:#fff3f3;color:#922020;font-size:14px}
.fmu-expense-error-summary strong{display:block;font-size:15px}.fmu-expense-error-summary p{margin:7px 0}.fmu-expense-error-summary ul{margin:5px 0 0;padding-left:20px}.fmu-expense-error-summary[hidden]{display:none!important}
.fmu-expense-balance-status{display:flex;align-items:center;gap:10px;margin-top:18px;padding:12px 14px;border:1px solid #cfe2d5;border-radius:6px;background:#f6fbf7;color:#1d6a37;font-size:13px}.fmu-expense-balance-status.is-insufficient{border-color:#e7ae60;background:#fff9ec;color:#944600}
html[data-finus-theme="dark"] body .fmu-expense-error-summary{background:#321719;color:#ffcaca;border-color:#874343}html[data-finus-theme="dark"] body .fmu-expense-balance-status{background:#102c1c;color:#b0ebbe;border-color:#305a40}html[data-finus-theme="dark"] body .fmu-expense-balance-status.is-insufficient{background:#382816;color:#ffd399;border-color:#876030}
.fmu-derived-value{display:flex;align-items:center;gap:10px;min-height:48px;padding:11px 14px;border:1px solid #dbe5de;border-radius:6px;background:#f4faf6;color:#176d35}.fmu-derived-value i{color:#169b43}.fmu-derived-value strong{font-size:14px}
.journal-preview{margin-top:22px;padding:16px;border:1px solid #cfe2d5;border-radius:6px;background:#f8fcf9}.journal-preview-head{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:10px}.journal-preview-head div strong,.journal-preview-head div span{display:block}.journal-preview-head div strong{color:#193925}.journal-preview-head div span{margin-top:2px;color:#64748b;font-size:12px}.journal-preview-badge{display:inline-flex;align-items:center;gap:6px;color:#08752f;font-size:11px;font-weight:800}.journal-preview-row{display:grid;grid-template-columns:60px minmax(0,1fr) 150px;gap:12px;padding:10px 0;border-top:1px solid #e2ece5}.journal-preview-row>span{color:#64748b;font-size:12px;text-transform:uppercase}.journal-preview-row>strong{color:#24352a}.journal-preview-row>b{text-align:right;color:#08752f}.honorarium-section,.zakat-detail-section{margin-top:28px;padding-top:24px;border-top:1px solid #dce7df}.honorarium-heading{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:18px}.honorarium-heading h3{margin:0 0 4px;font-size:18px;color:#17233a}.honorarium-heading p{margin:0;color:#64748b}.honorarium-heading>span{padding:8px 11px;border-radius:6px;background:#eaf8ee;color:#08752f;font-size:12px;font-weight:700}.zakat-period-panel{margin-bottom:28px;padding-bottom:24px;border-bottom:1px solid #dce7df}.zakat-period-heading,.zakat-detail-heading{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:18px}.zakat-period-heading h3,.zakat-detail-heading h3{margin:0 0 4px;font-size:18px;color:#17233a}.zakat-period-heading p,.zakat-detail-heading p{margin:0;color:#64748b}.zakat-balance{text-align:right}.zakat-balance span,.zakat-balance small{display:block;color:#64748b}.zakat-balance strong{display:block;margin:2px 0;color:#08752f;font-size:20px}.zakat-period-fields{display:grid;grid-template-columns:minmax(220px,.7fr) minmax(280px,1.3fr);gap:16px;align-items:end}.zakat-period-note{display:flex;align-items:center;gap:10px;min-height:46px;padding:10px 12px;border:1px solid #cfe2d5;border-radius:6px;background:#f6fbf7;color:#365443}.zakat-target-heading{display:flex;align-items:end;justify-content:space-between;gap:16px;margin:20px 0 10px}.zakat-target-heading div span{display:block;margin-top:3px;color:#64748b;font-size:12px}.zakat-target-heading>strong{color:#b45309}.zakat-target-heading>strong.is-balanced{color:#15803d}.zakat-target-grid{display:grid;grid-template-columns:repeat(4,minmax(140px,1fr));gap:10px}.zakat-target-item{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:9px 10px;border:1px solid #dce7df;border-radius:6px}.zakat-target-item>span:first-child{font-size:12px;font-weight:700;color:#475569}.zakat-target-control{display:flex;align-items:center;gap:4px}.zakat-target-control input{width:64px;border:0;background:transparent;text-align:right;color:#17233a}.zakat-detail-labels,.zakat-detail-row{display:grid;grid-template-columns:minmax(190px,1.15fr) minmax(150px,.9fr) minmax(170px,1fr);gap:14px;align-items:end}.zakat-detail-labels{padding:0 0 8px;color:#64748b;font-size:12px;font-weight:700}.zakat-detail-row{padding:14px 0;border-top:1px solid #edf2ee}.zakat-detail-row .fmu-label{display:none}.zakat-recipient-details{grid-column:1/-1}.zakat-recipient-details summary{cursor:pointer;color:#08752f;font-size:12px;font-weight:700}.zakat-recipient-details-grid{display:grid;grid-template-columns:1fr 1fr 1.5fr;gap:12px;margin-top:12px}.zakat-recipient-details-grid .fmu-label{display:block}.zakat-remove-row{width:44px;height:44px;border:1px solid #efc7c7;border-radius:6px;background:#fff;color:#c43f3f;cursor:pointer}.zakat-remove-row:disabled{opacity:.35;cursor:not-allowed}.zakat-detail-summary{display:flex;justify-content:flex-end;gap:24px;flex-wrap:wrap;margin-top:12px;padding-top:14px;border-top:1px solid #dce7df;color:#475569}.zakat-detail-summary .is-balanced{color:#15803d;font-weight:700}@media(max-width:980px){.zakat-target-grid{grid-template-columns:repeat(2,minmax(140px,1fr))}.zakat-detail-labels{display:none}.zakat-detail-row{grid-template-columns:1fr 1fr}.zakat-detail-row .fmu-label{display:block}.zakat-remove-row{justify-self:end}}@media(max-width:760px){.journal-preview-row{grid-template-columns:55px minmax(0,1fr)}.journal-preview-row>b{grid-column:2;text-align:left}.honorarium-heading,.zakat-period-heading,.zakat-detail-heading{align-items:flex-start;flex-direction:column}.zakat-balance{text-align:left}.zakat-period-fields,.zakat-target-grid,.zakat-detail-row,.zakat-recipient-details-grid{grid-template-columns:1fr}.zakat-remove-row{justify-self:end}.zakat-detail-summary{justify-content:flex-start;gap:10px 18px}}
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
html[data-finus-theme="dark"] body .honorarium-section,html[data-finus-theme="dark"] body .zakat-detail-section,html[data-finus-theme="dark"] body .zakat-detail-row,html[data-finus-theme="dark"] body .zakat-detail-summary{border-color:#293D31 !important}html[data-finus-theme="dark"] body .honorarium-heading h3,html[data-finus-theme="dark"] body .zakat-detail-heading h3{color:#F1F6F3 !important}html[data-finus-theme="dark"] body .honorarium-heading>span{background:#143522 !important;color:#91e6aa !important}html[data-finus-theme="dark"] body .zakat-remove-row{background:#101B14 !important;border-color:#673333 !important;color:#ff9b9b !important}
html[data-finus-theme="dark"] body .fmu-derived-value{background:#101B14 !important;border-color:#293D31 !important;color:#91e6aa !important}
html[data-finus-theme="dark"] body .zakat-period-panel{border-color:#293D31 !important}html[data-finus-theme="dark"] body .zakat-period-heading h3{color:#F1F6F3 !important}html[data-finus-theme="dark"] body .zakat-period-note,html[data-finus-theme="dark"] body .zakat-target-item{background:#101B14 !important;border-color:#293D31 !important;color:#D7E7DC !important}html[data-finus-theme="dark"] body .zakat-target-item>span:first-child,html[data-finus-theme="dark"] body .zakat-target-control input{color:#D7E7DC !important}
</style>
@endpush

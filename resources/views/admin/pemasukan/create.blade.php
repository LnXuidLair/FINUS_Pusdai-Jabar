@extends('layouts.app')
@section('title', 'Tambah Pemasukan')
@section('hide-page-header', '1')
@section('hide-global-errors', '1')
@php
    $isPegawai = request()->routeIs('pegawai.keuangan.*');
    $pemasukanIndexRoute = $isPegawai ? 'pegawai.keuangan.pemasukan.index' : 'admin.pemasukan.index';
    $pemasukanStoreRoute = $isPegawai ? 'pegawai.keuangan.pemasukan.store' : 'admin.pemasukan.store';
@endphp
@section('content')
@include('layouts.partials.finus-ui')
<style>
.pm-create-page{padding:6px 0 52px;color:#0f172a}
.pm-create-hero{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;position:relative;overflow:hidden;border-radius:24px;background:linear-gradient(120deg,#085123,#139d42 76%,#38c86b);color:#fff;padding:30px 36px;margin-bottom:24px;box-shadow:0 18px 42px rgba(15,90,45,.13)}
.pm-create-hero::after{content:'';position:absolute;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.08);right:-65px;bottom:-174px;pointer-events:none}
.pm-create-hero-left{display:flex;align-items:center;gap:18px;z-index:1}
.pm-create-hero-icon{display:flex;align-items:center;justify-content:center;width:68px;height:68px;border-radius:20px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.22);font-size:27px;flex-shrink:0}
.pm-create-hero h1{font-size:27px;font-weight:800;margin:0 0 6px;color:#fff}
.pm-create-hero p{font-size:13.5px;color:rgba(255,255,255,.84);margin:0;line-height:1.6}
.pm-create-badge{z-index:1;border-radius:14px;border:1px solid rgba(255,255,255,.28);background:rgba(0,0,0,.07);padding:13px 17px;font-size:12px;font-weight:700;white-space:nowrap}
.pm-create-badge i{margin-right:7px}
.pm-create-card{border:1px solid #dce8e2;border-radius:22px;background:#fff;box-shadow:0 12px 34px rgba(16,52,31,.07);overflow:hidden}
.pm-create-heading{display:flex;align-items:center;gap:15px;padding:23px 28px;border-bottom:1px solid #e2eae5}
.pm-create-heading-icon{display:grid;place-items:center;width:54px;height:54px;border-radius:15px;background:#ecfdf2;color:#16a34a;font-size:21px;flex-shrink:0}
.pm-create-heading h2{font-size:19px;font-weight:800;margin:0 0 3px;color:#10221a}
.pm-create-heading p{margin:0;font-size:12.5px;color:#64748b}
.pm-create-body{padding:29px 29px 14px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:22px}
.pm-create-body>.pm-fgroup,.pm-create-body>div[hidden]{min-width:0}
.pm-create-body>.pm-fgroup:has(#pm_golongan),.pm-create-body>#pmParkingFields,.pm-create-body>#pmWakafRules,.pm-create-body>#pmMixedPayments,.pm-create-body>#pmDonaturGroup,.pm-create-body>.pm-fgroup:has(#pm_ket),.pm-create-body>.pm-fgroup:has(#pm_bukti){grid-column:1/-1}
.pm-fgroup{margin-bottom:19px}
.pm-flabel{display:block;font-size:12.5px;font-weight:750;letter-spacing:.01em;color:#334155;margin-bottom:9px}
.pm-flabel span{color:#dc2626}
.pm-finput,.pm-fselect,.pm-ftarea{width:100%;min-height:49px;padding:11px 15px;border:1px solid #d8e5de;border-radius:13px;background:#fbfdfc;color:#17283f;font-size:14px;font-family:inherit;outline:none;transition:border-color .2s,box-shadow .2s}
.pm-ftarea{min-height:116px;resize:vertical}
.pm-finput:focus,.pm-fselect:focus,.pm-ftarea:focus{border-color:#159c47;box-shadow:0 0 0 3px rgba(22,163,74,.12);background:#fff}
.pm-finput:disabled,.pm-fselect:disabled{opacity:.68;cursor:not-allowed}
.pm-fhint{font-size:11.5px;color:#718096;margin-top:7px;line-height:1.65}
.pm-frow{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
.pm-create-body>.pm-frow{grid-column:1/-1}
.pm-create-body [hidden]{display:none !important}
.pm-create-foot{display:flex;align-items:center;justify-content:space-between;gap:15px;flex-wrap:wrap;padding:22px 29px;border-top:1px solid #e2eae5;background:#fbfdfc}
.pm-cancel{display:inline-flex;align-items:center;gap:8px;border-radius:13px;padding:12px 20px;border:1px solid #d8e5de;background:#fff;color:#425466;text-decoration:none;font-size:13.5px;font-weight:700}
.pm-cancel:hover{text-decoration:none;color:#0f5630;background:#f0faf4}
.pm-submit{display:inline-flex;align-items:center;gap:10px;border:0;border-radius:13px;padding:13px 23px;background:linear-gradient(110deg,#0e6731,#19b94b);color:#fff;font-size:14px;font-weight:750;cursor:pointer;box-shadow:0 8px 20px rgba(18,135,57,.16)}
.pm-submit:disabled{opacity:.65;cursor:wait}
.pm-create-note{font-size:12px;color:#64748b;line-height:1.65;margin:0 0 20px;padding:13px 16px;border-left:3px solid #16a34a;background:#f4fbf6;border-radius:8px;grid-column:1/-1}
.pm-create-errors{background:#fff1f2;border:1px solid #fda4af;color:#9f1239;padding:16px 22px;border-radius:15px;margin-bottom:22px}
.pm-create-errors strong{display:block;margin-bottom:8px}
.pm-create-errors ul{padding-left:20px;margin:0}
.pm-create-errors li{margin:3px 0}
.pm-field-error{display:block;color:#b91c1c;font-size:12px;margin-top:6px}
.pm-file-upload{position:relative;display:flex;min-height:134px;align-items:center;justify-content:center;flex-direction:column;text-align:center;gap:8px;border:2px dashed #b6dbbf;border-radius:17px;background:#f7fcf8;cursor:pointer;padding:18px;color:#106731}
.pm-file-upload i{font-size:27px;color:#139c42}
.pm-file-upload strong{font-size:14px}
.pm-file-upload span{font-size:12px;color:#64748b}
.pm-file-upload input{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer}
.pm-create-amount-preview{margin-top:7px;font-size:12px;color:#159447;font-weight:700}
@media(max-width:750px){.pm-create-hero{padding:23px 21px}.pm-create-hero-icon{width:52px;height:52px}.pm-create-hero h1{font-size:22px}.pm-create-body{display:block;padding:22px 18px 10px}.pm-create-heading{padding:18px}.pm-frow{grid-template-columns:1fr;gap:0}.pm-create-foot{padding:18px}}
html[data-finus-theme="dark"] .pm-create-card{background:#14231a;border-color:#355141;color:#ebf7f0}
html[data-finus-theme="dark"] .pm-create-heading,html[data-finus-theme="dark"] .pm-create-foot{background:#14231a;border-color:#355141}
html[data-finus-theme="dark"] .pm-create-heading h2,html[data-finus-theme="dark"] .pm-flabel{color:#ecf7ef}
html[data-finus-theme="dark"] .pm-finput,html[data-finus-theme="dark"] .pm-fselect,html[data-finus-theme="dark"] .pm-ftarea{background:#101b15;color:#e5f3ea;border-color:#355141}
html[data-finus-theme="dark"] .pm-create-note{background:#173423;color:#d2e7db}
html[data-finus-theme="dark"] .pm-file-upload{background:#153323;border-color:#356747;color:#e9f7ed}
</style>
<div class="pm-create-page">
    <section class="pm-create-hero">
        <div class="pm-create-hero-left"><span class="pm-create-hero-icon"><i class="fa-solid fa-hand-holding-dollar"></i></span><div>
            <h1>Tambah Pemasukan</h1>
            <p>Catat penerimaan kas masjid secara manual dan lampirkan bukti transaksi bila tersedia.</p>
        </div></div>
        <span class="pm-create-badge"><i class="fa-solid fa-shield-halved"></i>Pencatatan Keuangan</span>
    </section>
    <form method="POST" action="{{ route($pemasukanStoreRoute) }}" enctype="multipart/form-data" class="pm-create-card" id="pmTambahForm">
        @csrf
        @if($errors->any())
            <div class="pm-create-errors" role="alert" tabindex="-1" id="pemasukanErrorSummary">
                <strong><i class="fa-solid fa-circle-exclamation"></i> Pemasukan belum berhasil disimpan</strong>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="pm-create-heading"><span class="pm-create-heading-icon"><i class="fa-solid fa-pen-to-square"></i></span><div>
            <h2>Data Pemasukan</h2>
            <p>Lengkapi kategori, nominal, tanggal, metode pembayaran, dan dokumen pendukung.</p>
        </div></div>
        <div class="pm-create-body">
            <p class="pm-create-note"><i class="fa-solid fa-circle-info"></i> Halaman ini hanya untuk <strong>penerimaan manual</strong>. Pembayaran jamaah melalui Midtrans tetap diproses otomatis dan tidak perlu diinput ulang.</p>


                {{-- Golongan --}}
                <div class="pm-fgroup">
                    <label class="pm-flabel" for="pm_golongan">Golongan <span>*</span></label>
                    <select name="jenis_ziswaf" id="pm_golongan" class="pm-fselect" required>
                        <option value="">-- Pilih Golongan --</option>
                        @foreach($golonganLabels as $val => $lbl)
                            <option value="{{ $val }}" {{ old('jenis_ziswaf') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="pmParkingFields" hidden>
                    <p style="font-size:12px;color:#64748b;line-height:1.6;">Parkir dicatat sebagai setoran per shift ke Dana Operasional. Lampirkan laporan/rekap parkir; penerimaan ini langsung dijurnal tanpa alur pembayaran jamaah. Tunai masuk Kas, QRIS masuk Bank, dan pembayaran gabungan dipisahkan sesuai nominal masing-masing.</p>
                    <div class="pm-frow">
                        <div class="pm-fgroup">
                            <label class="pm-flabel" for="pm_parkir_pegawai">Penanggung Jawab Shift <span>*</span></label>
                            <select name="id_pegawai" id="pm_parkir_pegawai" class="pm-fselect" disabled>
                                <option value="">-- Pilih Petugas Parkir --</option>
                                @foreach($pegawaiPenanggungJawab as $pegawaiItem)
                                    <option value="{{ $pegawaiItem->id }}" @selected((string) old('id_pegawai') === (string) $pegawaiItem->id)>
                                        {{ $pegawaiItem->nama_pegawai }} · {{ $pegawaiItem->jabatan ?: 'Pegawai Umum' }}
                                    </option>
                                @endforeach
                            </select>
                            @if($pegawaiPenanggungJawab->isEmpty())
                                <small>Belum ada petugas parkir terverifikasi. Lengkapi data pegawai dengan jabatan Parkir terlebih dahulu.</small>
                            @endif
                        </div>
                        <div class="pm-fgroup">
                            <label class="pm-flabel" for="pm_parkir_shift">Shift <span>*</span></label>
                            <select name="shift" id="pm_parkir_shift" class="pm-fselect" disabled>
                                <option value="pagi" @selected(old('shift', 'pagi') === 'pagi')>Pagi</option>
                                <option value="siang" @selected(old('shift') === 'siang')>Siang</option>
                                <option value="malam" @selected(old('shift') === 'malam')>Malam</option>
                            </select>
                        </div>
                    </div>
                    <div class="pm-fgroup">
                        <label class="pm-flabel" for="pm_parkir_rekap">Nomor Laporan / Rekap</label>
                        <input type="text" name="nomor_rekap" id="pm_parkir_rekap" class="pm-finput" disabled
                            maxlength="80" placeholder="Contoh: PKR-270926-P1" value="{{ old('nomor_rekap') }}">
                    </div>
                </div>

                <div class="pm-fgroup" id="pmWakafTypeGroup" hidden>
                    <label class="pm-flabel" for="pm_wakaf_type">Jenis Penerimaan Wakaf <span>*</span></label>
                    <select name="wakaf_type" id="pm_wakaf_type" class="pm-fselect" disabled>
                        <option value="permanen" @selected(old('wakaf_type', 'permanen') === 'permanen')>Wakaf Permanen</option>
                        <option value="hasil_pengelolaan" @selected(old('wakaf_type') === 'hasil_pengelolaan')>Hasil Pengelolaan Wakaf</option>
                    </select>
                </div>

                <div class="pm-frow" id="pmWakafRules" hidden>
                    <div class="pm-fgroup" id="pmWakafReturnGroup" hidden>
                        <label class="pm-flabel" for="pm_wakaf_return_date">Tanggal Pengembalian Pokok <span>*</span></label>
                        <input type="date" name="wakaf_return_date" id="pm_wakaf_return_date" class="pm-finput"
                            value="{{ old('wakaf_return_date') }}" disabled>
                    </div>
                    <div class="pm-fgroup" id="pmNazhirGroup" hidden>
                        <label class="pm-flabel" for="pm_persentase_nazhir">Imbalan Nazhir (%) <span>*</span></label>
                        <input type="number" name="persentase_nazhir" id="pm_persentase_nazhir" class="pm-finput"
                            min="0" max="10" step="0.01" value="{{ old('persentase_nazhir', 10) }}" disabled>
                        <div class="pm-fhint">Maksimal 10% dari hasil pengelolaan neto yang terealisasi.</div>
                    </div>
                </div>

                <div class="pm-frow">
                    {{-- Nominal --}}
                    <div class="pm-fgroup">
                        <label class="pm-flabel" for="pm_nominal" id="pmNominalLabel">Nominal (Rp) <span>*</span></label>
                        <input type="number" name="nominal" id="pm_nominal" class="pm-finput"
                            min="1000" step="1" placeholder="Contoh: 1250 atau 500000"
                            value="{{ old('nominal') }}" required>
                        <div class="pm-create-amount-preview" id="pmNominalPreview">Rp 0</div>
                    </div>
                    {{-- Tanggal --}}
                    <div class="pm-fgroup">
                        <label class="pm-flabel" for="pm_tanggal">Tanggal <span>*</span></label>
                        <input type="date" name="tanggal" id="pm_tanggal" class="pm-finput"
                            value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                            max="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>

                {{-- Metode --}}
                <div class="pm-fgroup">
                    <label class="pm-flabel" for="pm_metode">Metode Pembayaran <span>*</span></label>
                    <select name="metode_pembayaran" id="pm_metode" class="pm-fselect" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="manual_transfer" {{ old('metode_pembayaran') === 'manual_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="qris_manual"     {{ old('metode_pembayaran') === 'qris_manual'     ? 'selected' : '' }}>QRIS</option>
                        <option value="qris_tunai" {{ old('metode_pembayaran') === 'qris_tunai' ? 'selected' : '' }}>QRIS + Tunai / Cash</option>
                        <option value="tunai"           {{ old('metode_pembayaran') === 'tunai'           ? 'selected' : '' }}>Tunai / Cash</option>
                    </select>
                </div>

                <div id="pmMixedPayments" hidden>
                    <div class="pm-frow">
                        <div class="pm-fgroup"><label class="pm-flabel" for="pm_nominal_qris">Nominal QRIS (Rp) <span>*</span></label><input type="number" name="nominal_qris" id="pm_nominal_qris" class="pm-finput" min="1" max="999999999999" step="1" value="{{ old('nominal_qris') }}" disabled><div class="pm-fhint">Dicatat ke Bank · 1102.</div></div>
                        <div class="pm-fgroup"><label class="pm-flabel" for="pm_nominal_tunai">Nominal Tunai / Cash (Rp) <span>*</span></label><input type="number" name="nominal_tunai" id="pm_nominal_tunai" class="pm-finput" min="1" max="999999999999" step="1" value="{{ old('nominal_tunai') }}" disabled><div class="pm-fhint">Dicatat ke Kas · 1101.</div></div>
                    </div>
                    <p class="pm-fhint">Jumlah uang masuk dihitung otomatis dari nominal QRIS + tunai.</p>
                </div>

                {{-- Nama Donatur --}}
                <div class="pm-fgroup" id="pmDonaturGroup">
                    <label class="pm-flabel" for="pm_donatur">Nama Donatur / Muzakki</label>
                    <input type="text" name="nama_donatur" id="pm_donatur" class="pm-finput"
                        placeholder="Opsional — kosongkan jika anonim"
                        value="{{ old('nama_donatur') }}" maxlength="255">
                    <div class="pm-fhint">Nama dicatat di keterangan, tidak membuat akun baru.</div>
                </div>

                {{-- Keterangan --}}
                <div class="pm-fgroup">
                    <label class="pm-flabel" for="pm_ket" id="pmKeteranganLabel">Keterangan</label>
                    <textarea name="keterangan" id="pm_ket" class="pm-ftarea"
                        placeholder="Keterangan tambahan (opsional)…" maxlength="1000">{{ old('keterangan') }}</textarea>
                </div>

                {{-- Bukti --}}
                <div class="pm-fgroup">
                    <label class="pm-flabel" for="pm_bukti" id="pmBuktiLabel">Bukti Pembayaran</label>
                    <label class="pm-file-upload" for="pm_bukti"><i class="fa-solid fa-cloud-arrow-up"></i><strong>Pilih bukti pembayaran</strong><span>JPG, JPEG, PNG, atau PDF · Maksimal 2 MB</span><span id="pmSelectedFile" aria-live="polite">Belum ada file dipilih</span><input type="file" name="bukti_pembayaran" id="pm_bukti" accept=".jpg,.jpeg,.png,.pdf"></label>
                    @error('bukti_pembayaran')<span class="pm-field-error">{{ $message }}</span>@enderror
                </div>

            
        </div>
            <div class="pm-create-foot">
                <a class="pm-cancel" href="{{ route($pemasukanIndexRoute) }}"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
                <button type="submit" class="pm-submit" id="pmSubmitBtn">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pemasukan
                </button>
            </div>

    </form>
</div>
<script>
    const pmGolongan = document.getElementById('pm_golongan');
    const pmParkingFields = document.getElementById('pmParkingFields');
    const pmParkingEmployee = document.getElementById('pm_parkir_pegawai');
    const pmParkingShift = document.getElementById('pm_parkir_shift');
    const pmParkingReport = document.getElementById('pm_parkir_rekap');
    const pmNominalLabel = document.getElementById('pmNominalLabel');
    const pmDonaturGroup = document.getElementById('pmDonaturGroup');
    const pmDonatur = document.getElementById('pm_donatur');
    const pmKeteranganLabel = document.getElementById('pmKeteranganLabel');
    const pmKeterangan = document.getElementById('pm_ket');
    const pmBuktiLabel = document.getElementById('pmBuktiLabel');
    const pmBukti = document.getElementById('pm_bukti');
    const pmWakafTypeGroup = document.getElementById('pmWakafTypeGroup');
    const pmWakafType = document.getElementById('pm_wakaf_type');
    const pmWakafRules = document.getElementById('pmWakafRules');
    const pmWakafReturnGroup = document.getElementById('pmWakafReturnGroup');
    const pmWakafReturn = document.getElementById('pm_wakaf_return_date');
    const pmNazhirGroup = document.getElementById('pmNazhirGroup');
    const pmNazhir = document.getElementById('pm_persentase_nazhir');

    function pmSyncPostingRules() {
        const isWakaf = pmGolongan.value === 'wakaf';
        const isParkir = pmGolongan.value === 'parkir';
        const paymentMethod = document.getElementById('pm_metode');
        const transferOption = paymentMethod?.querySelector('option[value="manual_transfer"]');
        if (transferOption) {
            transferOption.disabled = isParkir;
            transferOption.hidden = isParkir;
            if (isParkir && paymentMethod.value === 'manual_transfer') paymentMethod.value = '';
        }
        const mixedOption = paymentMethod.querySelector('option[value="qris_tunai"]');
        mixedOption.disabled = !isParkir;
        mixedOption.hidden = !isParkir;
        if (!isParkir && paymentMethod.value === 'qris_tunai') paymentMethod.value = '';
        const isMixed = isParkir && paymentMethod.value === 'qris_tunai';
        document.getElementById('pmMixedPayments').hidden = !isMixed;
        const qrisInput = document.getElementById('pm_nominal_qris');
        const cashInput = document.getElementById('pm_nominal_tunai');
        [qrisInput, cashInput].forEach(input => { input.disabled = !isMixed; input.required = isMixed; });
        const totalInput = document.getElementById('pm_nominal');
        if (totalInput.readOnly && !isMixed) totalInput.value = '';
        totalInput.readOnly = isMixed;
        if (isMixed) totalInput.value = Number(qrisInput.value || 0) + Number(cashInput.value || 0);
        const isTemporary = isWakaf && pmWakafType.value === 'temporer';
        const isManagementResult = isWakaf && pmWakafType.value === 'hasil_pengelolaan';

        pmParkingFields.hidden = !isParkir;
        pmParkingEmployee.disabled = !isParkir;
        pmParkingEmployee.required = isParkir;
        pmParkingShift.disabled = !isParkir;
        pmParkingShift.required = isParkir;
        pmParkingReport.disabled = !isParkir;
        pmDonaturGroup.hidden = isParkir;
        pmDonatur.disabled = isParkir;
        pmBukti.required = isParkir;
        pmNominalLabel.innerHTML = isParkir
            ? 'Jumlah Uang Masuk (Rp) <span>*</span>'
            : 'Nominal (Rp) <span>*</span>';
        pmKeteranganLabel.textContent = isParkir ? 'Catatan Laporan' : 'Keterangan';
        pmKeterangan.placeholder = isParkir
            ? 'Catatan laporan atau serah terima shift'
            : 'Keterangan tambahan (opsional)…';
        pmBuktiLabel.innerHTML = isParkir
            ? 'Bukti Laporan Parkir <span>*</span>'
            : 'Bukti Pembayaran';

        pmWakafTypeGroup.hidden = !isWakaf;
        pmWakafType.disabled = !isWakaf;
        pmWakafType.required = isWakaf;
        pmWakafRules.hidden = !(isTemporary || isManagementResult);
        pmWakafReturnGroup.hidden = !isTemporary;
        pmWakafReturn.disabled = !isTemporary;
        pmWakafReturn.required = isTemporary;
        pmNazhirGroup.hidden = !isManagementResult;
        pmNazhir.disabled = !isManagementResult;
        pmNazhir.required = isManagementResult;
    }

    document.getElementById('pm_metode').addEventListener('change', pmSyncPostingRules);
    ['pm_nominal_qris', 'pm_nominal_tunai'].forEach(id => document.getElementById(id).addEventListener('input', pmSyncPostingRules));
    pmGolongan.addEventListener('change', pmSyncPostingRules);
    pmWakafType.addEventListener('change', pmSyncPostingRules);
    pmSyncPostingRules();


    // Memperlihatkan nama bukti file agar admin dapat mengonfirmasi pilihannya.
    document.getElementById('pm_bukti')?.addEventListener('change', function() {
        document.getElementById('pmSelectedFile').textContent = this.files?.[0]?.name || 'Belum ada file dipilih';
    });
    const nominalEl = document.getElementById('pm_nominal');
    const nominalPreview = document.getElementById('pmNominalPreview');
    const rupiahPreview = number => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(number) || 0);
    const updateNominalPreview = () => { nominalPreview.textContent = rupiahPreview(nominalEl.value); };
    nominalEl.addEventListener('input', updateNominalPreview);
    document.getElementById('pm_nominal_qris').addEventListener('input', updateNominalPreview);
    document.getElementById('pm_nominal_tunai').addEventListener('input', updateNominalPreview);
    updateNominalPreview();
    const pmSubmitBtn = document.getElementById('pmSubmitBtn');
    document.getElementById('pmTambahForm').addEventListener('submit', () => {
        pmSubmitBtn.disabled = true;
        pmSubmitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan…';
    });
    @if($errors->any())
        document.getElementById('pemasukanErrorSummary')?.focus();
    @endif
</script>
@endsection

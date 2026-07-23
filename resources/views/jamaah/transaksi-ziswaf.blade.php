@extends('layouts.app')

@section('content')
<style>
    .finus-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .page-hero {
        border-radius: 22px;
        background: linear-gradient(135deg, #065f46, #16a34a);
        color: white;
    }

    .info-box {
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        padding: 16px;
    }

    .formula-box {
        border-radius: 14px;
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        padding: 16px;
    }

    .small-muted {
        font-size: 13px;
        color: #64748b;
    }

    .payment-box {
        border-radius: 14px;
        border: 1px dashed #16a34a;
        background: #f0fdf4;
        padding: 15px;
    }

    .upload-box {
        border-radius: 14px;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        padding: 16px;
    }

    .upload-box label {
        font-weight: 700;
        color: #065f46;
    }

    .zakat-type-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-size: 12px;
        font-weight: 700;
    }
</style>

@php
    $paymentGatewayReady = $paymentGatewayReady ?? false;

    $jenisOptions = $config['jenisOptions'];
    $metodeOptions = $config['metodeOptions'];
    $singleJenisKey = array_key_first($jenisOptions);

    $isZakatPage = ($jenis ?? null) === 'zakat';
    $isInfakPage = ($jenis ?? null) === 'infak';
    $isWakafPage = ($jenis ?? null) === 'wakaf';
@endphp

<div class="page-hero p-4 p-md-5 mb-4">
    <h3 class="text-white font-weight-bold mb-2">{{ $config['title'] }}</h3>
    <p class="mb-0 text-white-50">
        {{ $config['subtitle'] }}
    </p>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Data belum lengkap.</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-lg-7">
        <div class="card finus-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-1 font-weight-bold">Form {{ $config['title'] }}</h5>
                <small class="text-muted">
                    @if($paymentGatewayReady)
                        Isi data transaksi dengan benar. Setelah disimpan, kamu akan diarahkan ke pembayaran otomatis.
                    @else
                        Isi data transaksi dengan benar. Transaksi akan masuk status menunggu verifikasi admin.
                    @endif
                </small>
            </div>

            <div class="card-body px-4">
                <form method="POST"
                    action="{{ route('jamaah.transaksi.store', $jenis) }}"
                    @if(! $paymentGatewayReady) enctype="multipart/form-data" @endif>
                    @csrf

                    <div class="form-group">
                        <label>Jenis Transaksi</label>

                        @if(count($jenisOptions) === 1)
                            <input type="hidden" name="jenis_ziswaf" value="{{ $singleJenisKey }}">

                            <input type="text"
                                class="form-control"
                                value="{{ $jenisOptions[$singleJenisKey] }}"
                                disabled>
                        @else
                            <select name="jenis_ziswaf"
                                id="jenis_ziswaf"
                                class="form-control @error('jenis_ziswaf') is-invalid @enderror"
                                required>
                                <option value="">Pilih jenis</option>

                                @foreach($jenisOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('jenis_ziswaf') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        @error('jenis_ziswaf')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    @if($isZakatPage)
                        <div id="kalkulator-zakat-maal" class="info-box mb-3" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="font-weight-bold mb-0">Kalkulator Zakat Maal</h6>
                                <span class="zakat-type-badge">2,5%</span>
                            </div>

                            <p class="small-muted mb-3">
                                Zakat maal dihitung dari harta yang sudah mencapai nisab dan haul.
                                Perhitungan sederhananya adalah 2,5% dari total harta wajib zakat.
                            </p>

                            <div class="form-group mb-2">
                                <label>Total Harta Wajib Zakat</label>
                                <input type="number"
                                    id="harta_maal"
                                    class="form-control"
                                    min="0"
                                    placeholder="Contoh: 10000000">
                            </div>

                            <div class="formula-box">
                                <small class="d-block text-muted mb-1">Rumus</small>
                                <strong>Zakat Maal = Total Harta × 2,5%</strong>

                                <hr>

                                <small class="d-block text-muted mb-1">Hasil Perhitungan</small>
                                <h5 class="mb-0 font-weight-bold text-success" id="hasil_maal">
                                    Rp0
                                </h5>

                                <button type="button" id="pakai_hasil_maal" class="btn btn-sm btn-success mt-3">
                                    Pakai hasil ini sebagai nominal
                                </button>
                            </div>
                        </div>

                        <div id="kalkulator-zakat-fitrah" class="info-box mb-3" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="font-weight-bold mb-0">Kalkulator Zakat Fitrah</h6>
                                <span class="zakat-type-badge">Per Jiwa</span>
                            </div>

                            <p class="small-muted mb-3">
                                Zakat fitrah dibayarkan per jiwa. Jika dibayarkan dengan uang,
                                nominalnya mengikuti nilai makanan pokok atau ketentuan lembaga zakat setempat.
                            </p>

                            <div class="form-group mb-2">
                                <label>Jumlah Jiwa</label>
                                <input type="number"
                                    id="jumlah_jiwa"
                                    class="form-control"
                                    min="1"
                                    placeholder="Contoh: 4">
                            </div>

                            <div class="form-group mb-2">
                                <label>Nominal Zakat Fitrah per Jiwa</label>
                                <input type="number"
                                    id="nominal_per_jiwa"
                                    class="form-control"
                                    min="0"
                                    placeholder="Contoh: 45000">
                            </div>

                            <div class="formula-box">
                                <small class="d-block text-muted mb-1">Rumus</small>
                                <strong>Zakat Fitrah = Jumlah Jiwa × Nominal per Jiwa</strong>

                                <hr>

                                <small class="d-block text-muted mb-1">Hasil Perhitungan</small>
                                <h5 class="mb-0 font-weight-bold text-success" id="hasil_fitrah">
                                    Rp0
                                </h5>

                                <button type="button" id="pakai_hasil_fitrah" class="btn btn-sm btn-success mt-3">
                                    Pakai hasil ini sebagai nominal
                                </button>
                            </div>
                        </div>

                        <div id="kalkulator-zakat-penghasilan" class="info-box mb-3" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="font-weight-bold mb-0">Kalkulator Zakat Penghasilan</h6>
                                <span class="zakat-type-badge">2,5%</span>
                            </div>

                            <p class="small-muted mb-3">
                                Zakat penghasilan dihitung dari penghasilan bersih yang sudah mencapai nisab.
                                Perhitungan sederhana: 2,5% dari penghasilan wajib zakat.
                            </p>

                            <div class="form-group mb-2">
                                <label>Penghasilan Utama</label>
                                <input type="number"
                                    id="penghasilan_utama"
                                    class="form-control"
                                    min="0"
                                    placeholder="Contoh: 5000000">
                            </div>

                            <div class="form-group mb-2">
                                <label>Penghasilan Lain</label>
                                <input type="number"
                                    id="penghasilan_lain"
                                    class="form-control"
                                    min="0"
                                    placeholder="Contoh: 500000">
                            </div>

                            <div class="form-group mb-2">
                                <label>Pengurang/Kebutuhan Pokok</label>
                                <input type="number"
                                    id="pengurang_penghasilan"
                                    class="form-control"
                                    min="0"
                                    placeholder="Contoh: 1000000">
                            </div>

                            <div class="formula-box">
                                <small class="d-block text-muted mb-1">Rumus</small>
                                <strong>Zakat Penghasilan = Penghasilan Bersih × 2,5%</strong>

                                <hr>

                                <small class="d-block text-muted mb-1">Penghasilan Bersih</small>
                                <h6 class="mb-2 font-weight-bold text-dark" id="penghasilan_bersih">
                                    Rp0
                                </h6>

                                <small class="d-block text-muted mb-1">Hasil Perhitungan</small>
                                <h5 class="mb-0 font-weight-bold text-success" id="hasil_penghasilan">
                                    Rp0
                                </h5>

                                <button type="button" id="pakai_hasil_penghasilan" class="btn btn-sm btn-success mt-3">
                                    Pakai hasil ini sebagai nominal
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Nominal</label>

                        <input type="number"
                            name="nominal"
                            id="nominal"
                            class="form-control @error('nominal') is-invalid @enderror"
                            value="{{ old('nominal') }}"
                            min="1000"
                            placeholder="Contoh: 50000"
                            required>

                        @error('nominal')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Metode Pembayaran</label>

                        <select name="metode_pembayaran"
                            id="metode_pembayaran"
                            class="form-control @error('metode_pembayaran') is-invalid @enderror"
                            required>
                            @foreach($metodeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('metode_pembayaran') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('metode_pembayaran')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    @if($paymentGatewayReady)
                        <div id="info-qris" class="alert alert-info" style="display: none;">
                            <strong>QRIS</strong><br>
                            Pembayaran akan diproses otomatis melalui payment gateway. Setelah berhasil, transaksi otomatis diterima oleh sistem.
                        </div>

                        <div id="info-virtual-account" class="alert alert-info" style="display: none;">
                            <strong>Virtual Account</strong><br>
                            Sistem akan membuat nomor virtual account melalui payment gateway. Setelah berhasil, transaksi otomatis diterima oleh sistem.
                        </div>

                        <div id="info-e-wallet" class="alert alert-info" style="display: none;">
                            <strong>E-Wallet</strong><br>
                            Pembayaran akan diproses melalui e-wallet yang tersedia di gateway. Setelah berhasil, transaksi otomatis diterima oleh sistem.
                        </div>

                        <div id="info-bank-transfer" class="alert alert-info" style="display: none;">
                            <strong>Bank Transfer Gateway</strong><br>
                            Pembayaran akan diproses melalui bank transfer payment gateway. Setelah berhasil, transaksi otomatis diterima oleh sistem.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <strong>Payment Gateway Belum Aktif</strong><br>
                            Untuk sementara transaksi memakai upload bukti pembayaran dan diverifikasi admin.
                        </div>

                        <div id="info-manual-transfer" class="alert alert-warning" style="display: none;">
                            <strong>Transfer Bank Manual</strong><br>
                            Silakan transfer ke rekening masjid/lembaga, lalu upload bukti pembayaran agar admin dapat melakukan verifikasi.
                        </div>

                        <div id="info-qris-manual" class="alert alert-info" style="display: none;">
                            <strong>QRIS Manual</strong><br>
                            Silakan lakukan pembayaran melalui QRIS manual, lalu upload bukti pembayaran agar admin dapat melakukan verifikasi.
                        </div>

                        <div class="form-group upload-box">
                            <label for="bukti_pembayaran">Bukti Pembayaran</label>

                            <input
                                type="file"
                                name="bukti_pembayaran"
                                id="bukti_pembayaran"
                                class="form-control-file @error('bukti_pembayaran') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png,.pdf"
                                required>

                            <small class="small-muted d-block mt-2">
                                Upload bukti transfer atau QRIS manual. Format JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.
                            </small>

                            @error('bukti_pembayaran')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Keterangan</label>

                        <textarea name="keterangan"
                            class="form-control @error('keterangan') is-invalid @enderror"
                            rows="3"
                            placeholder="Opsional">{{ old('keterangan') }}</textarea>

                        @error('keterangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-block">
                        @if($paymentGatewayReady)
                            <i class="fa fa-credit-card mr-1"></i>
                            Lanjutkan ke Pembayaran
                        @else
                            <i class="fa fa-paper-plane mr-1"></i>
                            Simpan Transaksi Manual
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card finus-card mb-4">
            <div class="card-body p-4">
                @if($isZakatPage)
                    <h5 class="font-weight-bold mb-3">Informasi Zakat</h5>

                    <div class="info-box mb-3">
                        <h6 class="font-weight-bold text-success mb-2">Zakat Maal</h6>

                        <p class="text-muted mb-2">
                            Zakat maal adalah zakat atas harta yang dimiliki seseorang apabila sudah memenuhi syarat,
                            seperti mencapai nisab dan dimiliki selama waktu tertentu.
                        </p>

                        <div class="formula-box">
                            <small class="d-block text-muted mb-1">Rumus sederhana</small>
                            <strong>Zakat Maal = Total Harta × 2,5%</strong>
                        </div>

                        <p class="small-muted mb-0 mt-2">
                            Contoh: jika harta wajib zakat Rp10.000.000, maka zakatnya:
                            Rp10.000.000 × 2,5% = Rp250.000.
                        </p>
                    </div>

                    <div class="info-box mb-3">
                        <h6 class="font-weight-bold text-success mb-2">Zakat Fitrah</h6>

                        <p class="text-muted mb-2">
                            Zakat fitrah adalah zakat yang dibayarkan pada bulan Ramadan menjelang Idulfitri.
                            Perhitungannya berdasarkan jumlah jiwa.
                        </p>

                        <div class="formula-box">
                            <small class="d-block text-muted mb-1">Rumus sederhana</small>
                            <strong>Zakat Fitrah = Jumlah Jiwa × Nominal per Jiwa</strong>
                        </div>

                        <p class="small-muted mb-0 mt-2">
                            Contoh: jika 4 jiwa dan nominal per jiwa Rp45.000, maka total zakat fitrah:
                            4 × Rp45.000 = Rp180.000.
                        </p>
                    </div>

                    <div class="info-box mb-3">
                        <h6 class="font-weight-bold text-success mb-2">Zakat Penghasilan</h6>

                        <p class="text-muted mb-2">
                            Zakat penghasilan adalah zakat dari pendapatan yang telah memenuhi nisab.
                            Perhitungan sederhananya memakai 2,5% dari penghasilan bersih.
                        </p>

                        <div class="formula-box">
                            <small class="d-block text-muted mb-1">Rumus sederhana</small>
                            <strong>Zakat Penghasilan = Penghasilan Bersih × 2,5%</strong>
                        </div>
                    </div>
                @elseif($isInfakPage)
                    <h5 class="font-weight-bold mb-3">Informasi Infak</h5>

                    <div class="info-box mb-3">
                        <h6 class="font-weight-bold text-success mb-2">Infak Jamaah</h6>

                        <p class="text-muted mb-0">
                            Infak adalah pemberian sukarela dari jamaah untuk mendukung kegiatan,
                            operasional, sosial, dan kemaslahatan masjid.
                        </p>
                    </div>

                    <div class="payment-box mb-3">
                        <strong class="text-success">
                            {{ $paymentGatewayReady ? 'Pembayaran Otomatis' : 'Pembayaran Manual' }}
                        </strong>
                        <p class="small-muted mb-0 mt-2">
                            @if($paymentGatewayReady)
                                Pembayaran akan diproses melalui payment gateway dan status akan diperbarui otomatis.
                            @else
                                Setelah melakukan pembayaran, upload bukti agar transaksi dapat diverifikasi oleh admin.
                            @endif
                        </p>
                    </div>
                @elseif($isWakafPage)
                    <h5 class="font-weight-bold mb-3">Informasi Wakaf</h5>

                    <div class="info-box mb-3">
                        <h6 class="font-weight-bold text-success mb-2">Wakaf Jamaah</h6>

                        <p class="text-muted mb-0">
                            Wakaf adalah penyerahan harta untuk kepentingan ibadah atau kemaslahatan umum
                            yang manfaatnya dapat terus digunakan.
                        </p>
                    </div>

                    <div class="payment-box mb-3">
                        <strong class="text-success">
                            {{ $paymentGatewayReady ? 'Pembayaran Otomatis' : 'Pembayaran Manual' }}
                        </strong>
                        <p class="small-muted mb-0 mt-2">
                            @if($paymentGatewayReady)
                                Pembayaran akan diproses melalui payment gateway dan status akan diperbarui otomatis.
                            @else
                                Setelah melakukan pembayaran, upload bukti agar transaksi dapat diverifikasi oleh admin.
                            @endif
                        </p>
                    </div>
                @else
                    <h5 class="font-weight-bold mb-3">Informasi Transaksi</h5>

                    <p class="text-muted">
                        @if($paymentGatewayReady)
                            Isi transaksi sesuai jenis yang dipilih, lalu lanjutkan ke pembayaran otomatis.
                        @else
                            Isi transaksi sesuai jenis yang dipilih, lalu upload bukti pembayaran untuk diverifikasi admin.
                        @endif
                    </p>
                @endif

                <a href="{{ route('jamaah.dashboard') }}" class="btn btn-light btn-block">
                    <i class="fa fa-arrow-left mr-1"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const jenisZiswaf = document.getElementById('jenis_ziswaf');
    const nominal = document.getElementById('nominal');

    const kalkulatorMaal = document.getElementById('kalkulator-zakat-maal');
    const kalkulatorFitrah = document.getElementById('kalkulator-zakat-fitrah');
    const kalkulatorPenghasilan = document.getElementById('kalkulator-zakat-penghasilan');

    const hartaMaal = document.getElementById('harta_maal');
    const hasilMaal = document.getElementById('hasil_maal');
    const pakaiHasilMaal = document.getElementById('pakai_hasil_maal');

    const jumlahJiwa = document.getElementById('jumlah_jiwa');
    const nominalPerJiwa = document.getElementById('nominal_per_jiwa');
    const hasilFitrah = document.getElementById('hasil_fitrah');
    const pakaiHasilFitrah = document.getElementById('pakai_hasil_fitrah');

    const penghasilanUtama = document.getElementById('penghasilan_utama');
    const penghasilanLain = document.getElementById('penghasilan_lain');
    const pengurangPenghasilan = document.getElementById('pengurang_penghasilan');
    const penghasilanBersih = document.getElementById('penghasilan_bersih');
    const hasilPenghasilan = document.getElementById('hasil_penghasilan');
    const pakaiHasilPenghasilan = document.getElementById('pakai_hasil_penghasilan');

    const metodePembayaran = document.getElementById('metode_pembayaran');

    const infoQris = document.getElementById('info-qris');
    const infoVirtualAccount = document.getElementById('info-virtual-account');
    const infoEWallet = document.getElementById('info-e-wallet');
    const infoBankTransfer = document.getElementById('info-bank-transfer');

    const infoManualTransfer = document.getElementById('info-manual-transfer');
    const infoQrisManual = document.getElementById('info-qris-manual');

    let nilaiMaal = 0;
    let nilaiFitrah = 0;
    let nilaiPenghasilan = 0;

    function rupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(value || 0);
    }

    function hideAllCalculator() {
        if (kalkulatorMaal) {
            kalkulatorMaal.style.display = 'none';
        }

        if (kalkulatorFitrah) {
            kalkulatorFitrah.style.display = 'none';
        }

        if (kalkulatorPenghasilan) {
            kalkulatorPenghasilan.style.display = 'none';
        }
    }

    function toggleJenisZakat() {
        if (!jenisZiswaf) {
            return;
        }

        const jenis = jenisZiswaf.value;

        hideAllCalculator();

        if (kalkulatorMaal && jenis === 'zakat_maal') {
            kalkulatorMaal.style.display = 'block';
        }

        if (kalkulatorFitrah && jenis === 'zakat_fitrah') {
            kalkulatorFitrah.style.display = 'block';
        }

        if (kalkulatorPenghasilan && jenis === 'zakat_penghasilan') {
            kalkulatorPenghasilan.style.display = 'block';
        }
    }

    function hitungMaal() {
        const harta = parseInt(hartaMaal?.value || 0);
        nilaiMaal = Math.floor(harta * 0.025);

        if (hasilMaal) {
            hasilMaal.textContent = rupiah(nilaiMaal);
        }
    }

    function hitungFitrah() {
        const jiwa = parseInt(jumlahJiwa?.value || 0);
        const perJiwa = parseInt(nominalPerJiwa?.value || 0);

        nilaiFitrah = jiwa * perJiwa;

        if (hasilFitrah) {
            hasilFitrah.textContent = rupiah(nilaiFitrah);
        }
    }

    function hitungPenghasilan() {
        const utama = parseInt(penghasilanUtama?.value || 0);
        const lain = parseInt(penghasilanLain?.value || 0);
        const pengurang = parseInt(pengurangPenghasilan?.value || 0);

        const bersih = Math.max((utama + lain) - pengurang, 0);
        nilaiPenghasilan = Math.floor(bersih * 0.025);

        if (penghasilanBersih) {
            penghasilanBersih.textContent = rupiah(bersih);
        }

        if (hasilPenghasilan) {
            hasilPenghasilan.textContent = rupiah(nilaiPenghasilan);
        }
    }

    function hideAllPaymentInfo() {
        if (infoQris) {
            infoQris.style.display = 'none';
        }

        if (infoVirtualAccount) {
            infoVirtualAccount.style.display = 'none';
        }

        if (infoEWallet) {
            infoEWallet.style.display = 'none';
        }

        if (infoBankTransfer) {
            infoBankTransfer.style.display = 'none';
        }

        if (infoManualTransfer) {
            infoManualTransfer.style.display = 'none';
        }

        if (infoQrisManual) {
            infoQrisManual.style.display = 'none';
        }
    }

    function toggleMetodePembayaran() {
        if (!metodePembayaran) {
            return;
        }

        const metode = metodePembayaran.value;

        hideAllPaymentInfo();

        if (infoQris && metode === 'qris') {
            infoQris.style.display = 'block';
        }

        if (infoVirtualAccount && metode === 'virtual_account') {
            infoVirtualAccount.style.display = 'block';
        }

        if (infoEWallet && metode === 'e_wallet') {
            infoEWallet.style.display = 'block';
        }

        if (infoBankTransfer && metode === 'bank_transfer') {
            infoBankTransfer.style.display = 'block';
        }

        if (infoManualTransfer && metode === 'manual_transfer') {
            infoManualTransfer.style.display = 'block';
        }

        if (infoQrisManual && metode === 'qris_manual') {
            infoQrisManual.style.display = 'block';
        }
    }

    if (jenisZiswaf) {
        jenisZiswaf.addEventListener('change', toggleJenisZakat);
        toggleJenisZakat();
    }

    if (hartaMaal) {
        hartaMaal.addEventListener('input', hitungMaal);
    }

    if (pakaiHasilMaal) {
        pakaiHasilMaal.addEventListener('click', function () {
            if (nominal && nilaiMaal > 0) {
                nominal.value = nilaiMaal;
            }
        });
    }

    if (jumlahJiwa) {
        jumlahJiwa.addEventListener('input', hitungFitrah);
    }

    if (nominalPerJiwa) {
        nominalPerJiwa.addEventListener('input', hitungFitrah);
    }

    if (pakaiHasilFitrah) {
        pakaiHasilFitrah.addEventListener('click', function () {
            if (nominal && nilaiFitrah > 0) {
                nominal.value = nilaiFitrah;
            }
        });
    }

    if (penghasilanUtama) {
        penghasilanUtama.addEventListener('input', hitungPenghasilan);
    }

    if (penghasilanLain) {
        penghasilanLain.addEventListener('input', hitungPenghasilan);
    }

    if (pengurangPenghasilan) {
        pengurangPenghasilan.addEventListener('input', hitungPenghasilan);
    }

    if (pakaiHasilPenghasilan) {
        pakaiHasilPenghasilan.addEventListener('click', function () {
            if (nominal && nilaiPenghasilan > 0) {
                nominal.value = nilaiPenghasilan;
            }
        });
    }

    if (metodePembayaran) {
        metodePembayaran.addEventListener('change', toggleMetodePembayaran);
        toggleMetodePembayaran();
    }
});
</script>
@endpush
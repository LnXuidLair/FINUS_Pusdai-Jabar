@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #065f46;
        --primary-hover: #047857;
        --secondary: #10b981;
        --light-green: #f0fdf4;
        --border-color: #e2e8f0;
        --focus-ring: rgba(16, 185, 129, 0.18);
        --text-dark: #0f172a;
        --text-muted: #64748b;
    }

    .finus-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
        background: #ffffff;
        border: 1px solid rgba(241, 245, 249, 0.8);
        transition: all 0.3s ease;
    }

    .page-hero {
        border-radius: 24px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 8px 24px rgba(6, 95, 70, 0.15);
        position: relative;
        overflow: hidden;
    }
    
    .page-hero::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        top: -100px;
        right: -100px;
        pointer-events: none;
    }

    .info-box {
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid var(--border-color);
        padding: 20px;
        transition: all 0.3s ease;
    }

    .formula-box {
        border-radius: 16px;
        background: var(--light-green);
        border: 1px solid #bbf7d0;
        padding: 18px;
    }

    .small-muted {
        font-size: 13px;
        color: var(--text-muted);
    }

    .zakat-type-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        background: #dcfce7;
        color: #15803d;
        font-size: 12px;
        font-weight: 700;
    }

    /* Option Cards Grid for selection */
    .option-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 12px;
        margin-top: 8px;
        margin-bottom: 20px;
    }

    .option-card {
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 18px 12px;
        background: #ffffff;
        cursor: pointer;
        text-align: center;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .option-card:hover {
        transform: translateY(-3px);
        border-color: var(--secondary);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.1);
    }

    .option-card.active {
        border-color: var(--secondary);
        background-color: var(--light-green);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);
    }

    .option-card .option-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #f1f5f9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: var(--text-muted);
        margin-bottom: 10px;
        transition: all 0.25s ease;
    }

    .option-card.active .option-icon {
        background: var(--secondary);
        color: white;
    }

    .option-card.active::after {
        content: '\f058';
        font-family: 'Font Awesome 5 Free', 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        top: 8px;
        right: 8px;
        color: var(--secondary);
        font-size: 16px;
    }

    .option-card .option-title {
        font-weight: 700;
        font-size: 14px;
        color: var(--text-dark);
        margin: 0;
    }

    /* Form improvements */
    .form-group label {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 14px;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 12px;
        border: 1.5px solid var(--border-color);
        padding: 12px 16px;
        height: auto;
        font-size: 15px;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 4px var(--focus-ring);
        outline: none;
    }

    /* Upload Box custom style */
    .custom-dropzone {
        border: 2px dashed #bbf7d0;
        background-color: var(--light-green);
        border-radius: 16px;
        padding: 24px 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
    }

    .custom-dropzone:hover {
        background-color: #e6fcf0;
        border-color: var(--secondary);
    }

    .custom-dropzone i {
        font-size: 36px;
        color: var(--secondary);
        margin-bottom: 12px;
    }

    .custom-dropzone input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-name-badge {
        display: inline-flex;
        align-items: center;
        background: white;
        border: 1px solid #bbf7d0;
        padding: 6px 12px;
        border-radius: 30px;
        margin-top: 10px;
        font-size: 13px;
        color: var(--primary);
        font-weight: 600;
    }

    /* Currency Input formatting */
    .input-group-currency {
        position: relative;
    }

    .input-group-currency .currency-addon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: 700;
        color: var(--text-muted);
        pointer-events: none;
        z-index: 4;
    }

    .input-group-currency .form-control {
        padding-left: 45px;
    }

    /* Submit Button styling */
    .btn-submit-premium {
        border-radius: 14px;
        padding: 14px 20px;
        font-weight: 700;
        font-size: 16px;
        background: linear-gradient(135deg, var(--primary), var(--primary-hover));
        color: white;
        border: 0;
        box-shadow: 0 6px 20px rgba(6, 95, 70, 0.15);
        transition: all 0.25s ease;
        text-align: center;
    }

    .btn-submit-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(6, 95, 70, 0.25);
        color: white;
    }

    .btn-submit-premium:active {
        transform: translateY(0);
    }

    /* Card header badge */
    .form-header-badge {
        padding: 4px 10px;
        background-color: var(--light-green);
        color: var(--primary);
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    /* Info card premium details */
    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .summary-item:last-child {
        border-bottom: 0;
    }

    .slide-in-calc {
        animation: slideInCalc 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes slideInCalc {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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

<div class="page-hero p-4 p-md-5 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h3 class="text-white font-weight-bold mb-2" style="color: white;">{{ $config['title'] }}</h3>
        <p class="mb-0 text-white-50">
            {{ $config['subtitle'] }}
        </p>
    </div>
    <div>
        <a href="{{ route('jamaah.dashboard') }}" class="btn btn-light font-weight-bold px-4 py-2" style="border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
            <i class="fa fa-arrow-left mr-1"></i>
            Kembali ke Dashboard
        </a>
    </div>
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
    <div class="col-lg-12">
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

                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-lg-6 border-right pr-lg-4">

                    <div class="form-group">
                        <label>Jenis Transaksi</label>

                        @if(count($jenisOptions) === 1)
                            <input type="hidden" name="jenis_ziswaf" id="jenis_ziswaf" value="{{ $singleJenisKey }}">
                            <div class="option-grid">
                                <div class="option-card active" style="pointer-events: none;">
                                    <div class="option-icon">
                                        @if($singleJenisKey === 'infaq')
                                            <i class="fa-solid fa-circle-dollar-to-slot text-success"></i>
                                        @elseif($singleJenisKey === 'wakaf')
                                            <i class="fa fa-mosque text-success"></i>
                                        @else
                                            <i class="fa fa-coins text-success"></i>
                                        @endif
                                    </div>
                                    <h6 class="option-title">{{ $jenisOptions[$singleJenisKey] }}</h6>
                                </div>
                            </div>
                        @else
                            <select name="jenis_ziswaf"
                                id="jenis_ziswaf"
                                class="form-control d-none @error('jenis_ziswaf') is-invalid @enderror"
                                required>
                                <option value="">Pilih jenis</option>
                                @foreach($jenisOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('jenis_ziswaf') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="option-grid" id="jenis_ziswaf_cards">
                                @foreach($jenisOptions as $value => $label)
                                    <div class="option-card @if(old('jenis_ziswaf') === $value) active @endif" data-value="{{ $value }}">
                                        <div class="option-icon">
                                            @if($value === 'zakat_maal')
                                                <i class="fa fa-coins"></i>
                                            @elseif($value === 'zakat_penghasilan')
                                                <i class="fa fa-briefcase"></i>
                                            @else
                                                <i class="fa fa-heart"></i>
                                            @endif
                                        </div>
                                        <h6 class="option-title">{{ $label }}</h6>
                                    </div>
                                @endforeach
                            </div>
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
                        <label for="nominal">Nominal Pembayaran</label>
                        <div class="input-group-currency">
                            <span class="currency-addon">Rp</span>
                            <input type="number"
                                name="nominal"
                                id="nominal"
                                class="form-control @error('nominal') is-invalid @enderror"
                                value="{{ old('nominal') }}"
                                min="1000"
                                placeholder="50.000"
                                required>
                        </div>
                        <small class="small-muted d-block mt-2">Minimal pembayaran Rp1.000</small>
                        @error('nominal')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-6 pl-lg-4 mt-4 mt-lg-0 d-flex flex-column justify-content-between">
                    <div class="form-group">
                        <label>Metode Pembayaran</label>

                        <select name="metode_pembayaran"
                            id="metode_pembayaran"
                            class="form-control d-none @error('metode_pembayaran') is-invalid @enderror"
                            required>
                            @foreach($metodeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('metode_pembayaran') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        <div class="option-grid" id="metode_pembayaran_cards">
                            @foreach($metodeOptions as $value => $label)
                                <div class="option-card @if(old('metode_pembayaran', array_key_first($metodeOptions)) === $value) active @endif" data-value="{{ $value }}">
                                    <div class="option-icon">
                                        @if($value === 'qris' || $value === 'qris_manual')
                                            <i class="fa fa-qrcode"></i>
                                        @elseif($value === 'virtual_account')
                                            <i class="fa fa-university"></i>
                                        @elseif($value === 'e_wallet')
                                            <i class="fa fa-mobile-alt"></i>
                                        @elseif($value === 'manual_transfer')
                                            <i class="fa fa-credit-card"></i>
                                        @else
                                            <i class="fa fa-receipt"></i>
                                        @endif
                                    </div>
                                    <h6 class="option-title">{{ $label }}</h6>
                                </div>
                            @endforeach
                        </div>

                        @error('metode_pembayaran')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    @if($paymentGatewayReady)
                        <div id="info-qris" class="alert alert-info" style="display: none; border-radius: 12px;">
                            <strong>QRIS</strong><br>
                            Pembayaran akan diproses otomatis melalui payment gateway. Setelah berhasil, transaksi otomatis diterima oleh sistem.
                        </div>

                        <div id="info-virtual-account" class="alert alert-info" style="display: none; border-radius: 12px;">
                            <strong>Virtual Account</strong><br>
                            Sistem akan membuat nomor virtual account melalui payment gateway. Setelah berhasil, transaksi otomatis diterima oleh sistem.
                        </div>

                        <div id="info-e-wallet" class="alert alert-info" style="display: none; border-radius: 12px;">
                            <strong>E-Wallet</strong><br>
                            Pembayaran akan diproses melalui e-wallet yang tersedia di gateway. Setelah berhasil, transaksi otomatis diterima oleh sistem.
                        </div>

                        <div id="info-bank-transfer" class="alert alert-info" style="display: none; border-radius: 12px;">
                            <strong>Bank Transfer Gateway</strong><br>
                            Pembayaran akan diproses melalui bank transfer payment gateway. Setelah berhasil, transaksi otomatis diterima oleh sistem.
                        </div>
                    @else
                        <div class="alert alert-warning" style="border-radius: 12px;">
                            <strong>Payment Gateway Belum Aktif</strong><br>
                            Untuk sementara transaksi memakai upload bukti pembayaran dan diverifikasi admin.
                        </div>

                        <div id="info-manual-transfer" class="alert alert-warning" style="display: none; border-radius: 12px;">
                            <strong>Transfer Bank Manual</strong><br>
                            Silakan transfer ke rekening masjid/lembaga, lalu upload bukti pembayaran agar admin dapat melakukan verifikasi.
                        </div>

                        <div id="info-qris-manual" class="alert alert-info" style="display: none; border-radius: 12px;">
                            <strong>QRIS Manual</strong><br>
                            Silakan lakukan pembayaran melalui QRIS manual, lalu upload bukti pembayaran agar admin dapat melakukan verifikasi.
                        </div>

                        <div class="form-group">
                            <label>Bukti Pembayaran</label>
                            <div class="custom-dropzone" id="dropzone_wrapper">
                                <i class="fa fa-cloud-upload-alt"></i>
                                <h6 class="mb-1 font-weight-bold text-dark">Klik atau Seret Bukti Transfer</h6>
                                <p class="small-muted mb-0">Format: JPG, JPEG, PNG, PDF (Maksimal 2 MB)</p>
                                <input
                                    type="file"
                                    name="bukti_pembayaran"
                                    id="bukti_pembayaran"
                                    class="@error('bukti_pembayaran') is-invalid @enderror"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required>
                                <div id="file_info_badge" class="file-name-badge d-none">
                                    <i class="fa fa-file mr-1"></i> <span id="file_name_text">nama-file.jpg</span>
                                </div>
                            </div>

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

                    <button type="submit" class="btn btn-submit-premium btn-block">
                        @if($paymentGatewayReady)
                            <i class="fa fa-credit-card mr-1"></i>
                            Lanjutkan ke Pembayaran
                        @else
                            <i class="fa fa-paper-plane mr-1"></i>
                            Kirim Transaksi & Verifikasi
                        @endif
                    </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informasi ZISWAF Sederhana & Fleksibel -->
        @if($isZakatPage)
            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <div class="info-box h-100">
                        <h6 class="font-weight-bold text-success mb-2"><i class="fa fa-coins mr-1"></i> Zakat Maal</h6>
                        <p class="text-muted small mb-3">
                            Zakat atas harta yang disimpan (tabungan, emas, perdagangan) apabila sudah mencapai nisab (setara 85 gram emas) dan haul (1 tahun kepemilikan).
                        </p>
                        <div class="formula-box py-2 px-3 small">
                            <strong>Zakat Maal = Total Harta × 2,5%</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="info-box h-100">
                        <h6 class="font-weight-bold text-success mb-2"><i class="fa-solid fa-briefcase mr-1"></i> Zakat Penghasilan</h6>
                        <p class="text-muted small mb-3">
                            Zakat atas pendapatan dari pekerjaan yang sudah mencapai nisab yang dihitung dari pendapatan bersih setelah pengeluaran kebutuhan pokok.
                        </p>
                        <div class="formula-box py-2 px-3 small">
                            <strong>Zakat Penghasilan = Pendapatan Bersih × 2,5%</strong>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($isInfakPage)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="info-box">
                        <h6 class="font-weight-bold text-success mb-2"><i class="fa-solid fa-hand-holding-heart mr-1"></i> Informasi Infak</h6>
                        <p class="text-muted small mb-0">
                            Infak adalah pemberian harta secara sukarela di jalan Allah untuk kemaslahatan umum, operasional masjid, dakwah, maupun kegiatan sosial.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($isWakafPage)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="info-box">
                        <h6 class="font-weight-bold text-success mb-2"><i class="fa-solid fa-mosque mr-1"></i> Informasi Wakaf</h6>
                        <p class="text-muted small mb-0">
                            Wakaf adalah menahan harta asal yang tahan lama lalu menyalurkan manfaatnya untuk kepentingan ibadah, sarana masjid, atau umum, di mana pahala terus mengalir.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const jenisZiswaf = document.getElementById('jenis_ziswaf');
    const nominal = document.getElementById('nominal');

    const kalkulatorMaal = document.getElementById('kalkulator-zakat-maal');
    const kalkulatorPenghasilan = document.getElementById('kalkulator-zakat-penghasilan');

    const hartaMaal = document.getElementById('harta_maal');
    const hasilMaal = document.getElementById('hasil_maal');
    const pakaiHasilMaal = document.getElementById('pakai_hasil_maal');

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
            kalkulatorMaal.classList.remove('slide-in-calc');
        }

        if (kalkulatorPenghasilan) {
            kalkulatorPenghasilan.style.display = 'none';
            kalkulatorPenghasilan.classList.remove('slide-in-calc');
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
            kalkulatorMaal.classList.add('slide-in-calc');
        }

        if (kalkulatorPenghasilan && jenis === 'zakat_penghasilan') {
            kalkulatorPenghasilan.style.display = 'block';
            kalkulatorPenghasilan.classList.add('slide-in-calc');
        }
    }

    function hitungMaal() {
        const harta = parseInt(hartaMaal?.value || 0);
        nilaiMaal = Math.floor(harta * 0.025);

        if (hasilMaal) {
            hasilMaal.textContent = rupiah(nilaiMaal);
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

    // Sync Card Clicks for Jenis Ziswaf
    const jenisCards = document.querySelectorAll('#jenis_ziswaf_cards .option-card');
    jenisCards.forEach(card => {
        card.addEventListener('click', function() {
            jenisCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const val = this.getAttribute('data-value');
            if (jenisZiswaf) {
                jenisZiswaf.value = val;
                jenisZiswaf.dispatchEvent(new Event('change'));
            }
        });
    });

    // Sync Card Clicks for Metode Pembayaran
    const metodeCards = document.querySelectorAll('#metode_pembayaran_cards .option-card');
    metodeCards.forEach(card => {
        card.addEventListener('click', function() {
            metodeCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const val = this.getAttribute('data-value');
            if (metodePembayaran) {
                metodePembayaran.value = val;
                metodePembayaran.dispatchEvent(new Event('change'));
            }
        });
    });

    // Sync File Selection for Dropzone
    const fileInput = document.getElementById('bukti_pembayaran');
    const badge = document.getElementById('file_info_badge');
    const badgeText = document.getElementById('file_name_text');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                badgeText.textContent = this.files[0].name;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        });
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
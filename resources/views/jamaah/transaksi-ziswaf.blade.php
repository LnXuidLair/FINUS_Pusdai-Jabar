@extends('layouts.app')

@section('content')
@include('layouts.partials.finus-ui')
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

    /* Warna ikon per-jenis saat non-aktif — kontras jelas */
    .option-card[data-value="zakat_maal"] .option-icon,
    .option-card[data-value="zakat_fitrah"] .option-icon,
    .option-card[data-value="zakat_penghasilan"] .option-icon {
        background: #dcfce7;
        color: #15803d;
    }

    .option-card[data-value="zakat_pertanian"] .option-icon {
        background: #fef3c7;
        color: #92400e;
    }

    .option-card[data-value="infaq"] .option-icon {
        background: #cffafe;
        color: #0e7490;
    }

    .option-card[data-value="wakaf"] .option-icon {
        background: #fef3c7;
        color: #b45309;
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

    .policy-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }

    .policy-item {
        border-radius: 12px;
        padding: 18px;
        background: linear-gradient(155deg, #064e3b 0%, #065f22 28%, #0f8a3c 58%, #16a34a 100%);
        color: #ffffff;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 4px 12px rgba(6, 78, 59, 0.25);
        transition: all 0.3s ease;
    }

    .policy-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(6, 78, 59, 0.4);
        background: linear-gradient(155deg, #065f22 0%, #0f8a3c 40%, #16a34a 100%);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .policy-item strong {
        font-weight: 700;
        font-size: 1.05rem;
    }

    .policy-item span {
        font-weight: 700;
        font-size: 1.05rem;
    }

    .policy-item small {
        display: block;
        color: rgba(235, 255, 240, .85);
        margin-bottom: 4px;
        font-weight: 600;
    }

    .calculation-status {
        min-height: 20px;
        font-size: 12px;
        font-weight: 600;
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

    $zakatPolicies = $zakatPolicies ?? [];
    $masterAsnaf = $masterAsnaf ?? collect();
    $maalPolicy = $zakatPolicies['zakat_maal'] ?? [];
    $penghasilanPolicy = $zakatPolicies['zakat_penghasilan'] ?? [];
    $fitrahPolicy = $zakatPolicies['zakat_fitrah'] ?? [];
    $pertanianBerbiayaPolicy = $zakatPolicies['zakat_pertanian_berbiaya'] ?? [];
    $pertanianAlamiPolicy = $zakatPolicies['zakat_pertanian_alami'] ?? [];
    $zakatCalculationCategories = $zakatCalculationCategories ?? [];
    $maalCategories = $zakatCalculationCategories['zakat_maal'] ?? [];
    $penghasilanCategories = $zakatCalculationCategories['zakat_penghasilan'] ?? [];
    $pertanianBerbiayaCategories = $zakatCalculationCategories['zakat_pertanian_berbiaya'] ?? [];
    $pertanianAlamiCategories = $zakatCalculationCategories['zakat_pertanian_alami'] ?? [];
    $hargaBerasPerKg = (int) ($hargaBeras?->harga_per_satuan ?? 0);
    $hargaPertanian = $hargaPertanian ?? [];
    $hargaPertanianPerKg = collect($hargaPertanian)
        ->mapWithKeys(fn ($harga, $kategori) => [$kategori => (int) ($harga?->harga_per_satuan ?? 0)])
        ->all();
    $nisabEmasGram = (float) ($maalPolicy['nisab_emas_gram'] ?? 0);
    $hargaEmasPerGram = (int) ($hargaEmas?->harga_per_satuan ?? 0);
    $nisabEmasRupiah = (int) round($nisabEmasGram * $hargaEmasPerGram);
    $formatPersentase = static fn ($value): string => rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',');
    $nisabPenghasilanTahunan = (int) round((float) ($penghasilanPolicy['nisab_rupiah'] ?? 0));
    $nisabPenghasilanBulanan = $nisabPenghasilanTahunan > 0
        ? (int) round($nisabPenghasilanTahunan / 12)
        : 0;
    $zakatPenghasilanTerbayar = $zakatPenghasilanTerbayar ?? ['per_bulan' => [], 'per_tahun' => []];
    $periodePenghasilanAwal = old('periode_penghasilan', 'bulanan');
    $bulanPenghasilanAwal = old('bulan_penghasilan', now()->format('Y-m'));
    $tahunPenghasilanAwal = (int) old('tahun_penghasilan', now()->year);

    $minimalNominal = $paymentGatewayReady ? 10000 : 1000;

    $displayJenisOptions = [];
    $pertanianDitampilkan = false;
    foreach ($jenisOptions as $value => $label) {
        if (in_array($value, ['zakat_pertanian_berbiaya', 'zakat_pertanian_alami'], true)) {
            if (! $pertanianDitampilkan) {
                $displayJenisOptions['zakat_pertanian'] = 'Zakat Pertanian';
                $pertanianDitampilkan = true;
            }
            continue;
        }
        $displayJenisOptions[$value] = $label;
    }
    $oldJenisZakat = old('jenis_ziswaf');
    $oldJenisCard = in_array($oldJenisZakat, ['zakat_pertanian_berbiaya', 'zakat_pertanian_alami'], true)
        ? 'zakat_pertanian'
        : $oldJenisZakat;
    $defaultJenisPertanian = isset($jenisOptions['zakat_pertanian_berbiaya'])
        ? 'zakat_pertanian_berbiaya'
        : (isset($jenisOptions['zakat_pertanian_alami']) ? 'zakat_pertanian_alami' : '');
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
                        Isi data transaksi dengan benar. Setelah transaksi dibuat, kamu akan diarahkan ke halaman payment gateway.
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
                                <label for="organization_id">Masjid Tujuan</label>

                                @if(($organizations ?? collect())->count() === 1)
                                    @php($selectedOrganization = $organizations->first())
                                    <input type="hidden" name="organization_id" id="organization_id" value="{{ $selectedOrganization->id }}">
                                    <div class="info-box">
                                        <div class="d-flex align-items-start">
                                            <div class="mr-3" style="font-size: 22px; color: var(--primary);">
                                                <i class="fa-solid fa-mosque"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block text-dark">{{ $selectedOrganization->name }}</strong>
                                                <small class="small-muted">
                                                    {{ $selectedOrganization->address ?: collect([$selectedOrganization->city, $selectedOrganization->province])->filter()->implode(', ') ?: 'Alamat organization belum dilengkapi.' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <select name="organization_id"
                                            id="organization_id"
                                            class="form-control @error('organization_id') is-invalid @enderror"
                                            required>
                                        <option value="">Pilih masjid tujuan</option>
                                        @foreach(($organizations ?? collect()) as $organization)
                                            <option value="{{ $organization->id }}" @selected((string) old('organization_id', $selectedOrganizationId ?? '') === (string) $organization->id)>
                                                {{ $organization->name }}
                                                @if($organization->city || $organization->province)
                                                    — {{ collect([$organization->city, $organization->province])->filter()->implode(', ') }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="small-muted d-block mt-2">Akun Jamaah bersifat global. Pilih masjid yang akan menerima zakat, infak, atau wakaf ini.</small>
                                @endif

                                @error('organization_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Jenis Transaksi</label>

                                @if(count($jenisOptions) === 1)
                                    <input type="hidden" name="jenis_ziswaf" id="jenis_ziswaf" value="{{ $singleJenisKey }}">
                                    <div class="option-grid">
                                        <div class="option-card active" data-jenis="{{ $singleJenisKey }}" style="pointer-events: none;">
                                            <div class="option-icon">
                                                @if($singleJenisKey === 'infaq')
                                                    <i class="fa-solid fa-circle-dollar-to-slot"></i>
                                                @elseif($singleJenisKey === 'wakaf')
                                                    <i class="fa-solid fa-mosque"></i>
                                                @elseif($singleJenisKey === 'zakat_penghasilan')
                                                    <i class="fa-solid fa-briefcase"></i>
                                                @elseif($singleJenisKey === 'zakat_fitrah')
                                                    <i class="fa-solid fa-bowl-rice"></i>
                                                @elseif($singleJenisKey === 'zakat_pertanian_berbiaya')
                                                    <i class="fa-solid fa-faucet-drip"></i>
                                                @elseif($singleJenisKey === 'zakat_pertanian_alami')
                                                    <i class="fa-solid fa-cloud-rain"></i>
                                                @else
                                                    <i class="fa-solid fa-hand-holding-heart"></i>
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
                                        @foreach($displayJenisOptions as $value => $label)
                                            <div class="option-card @if($oldJenisCard === $value) active @endif" data-value="{{ $value }}">
                                                <div class="option-icon">
                                                    @if($value === 'zakat_maal')
                                                        <i class="fa-solid fa-hand-holding-heart"></i>
                                                    @elseif($value === 'zakat_penghasilan')
                                                        <i class="fa-solid fa-briefcase"></i>
                                                    @elseif($value === 'zakat_fitrah')
                                                        <i class="fa-solid fa-bowl-rice"></i>
                                                    @elseif($value === 'zakat_pertanian')
                                                        <i class="fa-solid fa-wheat-awn"></i>
                                                    @elseif($value === 'infaq')
                                                        <i class="fa-solid fa-circle-dollar-to-slot"></i>
                                                    @elseif($value === 'wakaf')
                                                        <i class="fa-solid fa-mosque"></i>
                                                    @else
                                                        <i class="fa-solid fa-hand-holding-dollar"></i>
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

                            @if($isWakafPage)
                                <div class="form-group">
                                    <label for="wakaf_type">Jenis Wakaf</label>
                                    <select name="wakaf_type" id="wakaf_type" class="form-control @error('wakaf_type') is-invalid @enderror" required>
                                        <option value="permanen" @selected(old('wakaf_type', 'permanen') === 'permanen')>Wakaf Permanen</option>
                                        <option value="temporer" @selected(old('wakaf_type') === 'temporer')>Wakaf Temporer</option>
                                    </select>
                                    @error('wakaf_type')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="form-group" id="wakaf_return_group" hidden>
                                    <label for="wakaf_return_date">Tanggal Pengembalian Pokok</label>
                                    <input type="date" name="wakaf_return_date" id="wakaf_return_date"
                                        min="{{ now()->addDay()->format('Y-m-d') }}"
                                        value="{{ old('wakaf_return_date') }}"
                                        class="form-control @error('wakaf_return_date') is-invalid @enderror" disabled>
                                    <small class="small-muted d-block mt-2">Pokok wakaf temporer dicatat sebagai liabilitas dan dikembalikan pada tanggal ini.</small>
                                    @error('wakaf_return_date')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            @endif

                            @if($isZakatPage)
                                <input type="hidden"
                                    name="kategori_perhitungan"
                                    id="kategori_perhitungan"
                                    value="{{ old('kategori_perhitungan') }}">
                                <input type="hidden"
                                    name="berat_panen_kg"
                                    id="berat_panen_kg"
                                    value="{{ old('berat_panen_kg') }}">

                                <div id="kalkulator-zakat-maal" class="info-box mb-3" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-0">Kalkulator Zakat Maal</h6>
                                        <span class="zakat-type-badge">{{ $formatPersentase($maalPolicy['kadar_persentase'] ?? 0) }}%</span>
                                    </div>

                                    <p class="small-muted mb-3">
                                        Nisab {{ $maalPolicy['nisab_pokok'] ?? '-' }}
                                        @if(! empty($maalPolicy['nisab_rupiah']))
                                            (Rp{{ number_format($maalPolicy['nisab_rupiah'], 0, ',', '.') }})
                                        @endif
                                        dan haul {{ $maalPolicy['haul'] ?? '-' }}.
                                    </p>

                                    <div class="form-group mb-2">
                                        <label for="kategori_harta_maal">Kategori Harta / Barang</label>
                                        <select id="kategori_harta_maal" class="form-control @error('kategori_perhitungan') is-invalid @enderror">
                                            <option value="">Pilih kategori harta</option>
                                            @foreach($maalCategories as $value => $label)
                                                <option value="{{ $value }}" @selected(old('kategori_perhitungan') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <small class="small-muted d-block mt-2">Pilih satu kategori untuk setiap perhitungan. Kategori dengan tanggal kepemilikan berbeda dicatat terpisah.</small>
                                        @error('kategori_perhitungan')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div id="nilai-harta-umum-group" class="form-group mb-2">
                                        <label>Total Nilai Harta Wajib Zakat</label>
                                        <div class="input-group-currency">
                                            <span class="currency-addon">Rp</span>
                                            <input type="text"
                                                id="harta_maal"
                                                class="form-control currency-input"
                                                inputmode="numeric"
                                                placeholder="Contoh: 10.000.000">
                                        </div>
                                    </div>

                                    <div id="nilai-emas-group" style="display: none;">
                                        <div class="form-group mb-2">
                                            <label for="berat_emas_gram">Berat Emas yang Dimiliki</label>
                                            <div class="input-group">
                                                <input type="number"
                                                    name="berat_emas_gram"
                                                    id="berat_emas_gram"
                                                    class="form-control @error('berat_emas_gram') is-invalid @enderror"
                                                    inputmode="decimal"
                                                    min="0.01"
                                                    step="0.01"
                                                    value="{{ old('berat_emas_gram') }}"
                                                    placeholder="Contoh: 100">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">gram</span>
                                                </div>
                                            </div>
                                            @error('berat_emas_gram')
                                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="formula-box mb-3">
                                            <div class="summary-item">
                                                <span class="small-muted">Harga emas aktif</span>
                                                <strong>
                                                    {{ $hargaEmasPerGram > 0 ? 'Rp'.number_format($hargaEmasPerGram, 0, ',', '.').' / gram' : 'Belum ditetapkan' }}
                                                </strong>
                                            </div>
                                            <div class="summary-item">
                                                <span class="small-muted">Nisab emas</span>
                                                <strong>{{ $formatPersentase($nisabEmasGram) }} gram</strong>
                                            </div>
                                            <div class="summary-item">
                                                <span class="small-muted">Nilai nisab dalam rupiah</span>
                                                <strong>{{ $nisabEmasRupiah > 0 ? 'Rp'.number_format($nisabEmasRupiah, 0, ',', '.') : '-' }}</strong>
                                            </div>
                                            <div class="summary-item">
                                                <span class="small-muted">Nilai emas yang dimiliki</span>
                                                <strong id="nilai_emas_rupiah">Rp0</strong>
                                            </div>
                                            <small class="small-muted d-block mt-2">
                                                Sumber harga: {{ $hargaEmas?->sumber_harga ?? 'harga emas aktif belum tersedia' }}.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="tanggal_mulai_kepemilikan">Tanggal Mulai Kepemilikan Harta / Barang</label>
                                        <input type="date"
                                            name="tanggal_mulai_kepemilikan"
                                            id="tanggal_mulai_kepemilikan"
                                            class="form-control @error('tanggal_mulai_kepemilikan') is-invalid @enderror"
                                            max="{{ now()->toDateString() }}"
                                            value="{{ old('tanggal_mulai_kepemilikan') }}">
                                        @if(! empty($maalPolicy['batas_awal_haul']))
                                            <small class="small-muted d-block mt-2">
                                                Untuk memenuhi haul {{ $maalPolicy['haul'] ?? '' }} hari ini, kepemilikan harus dimulai paling lambat
                                                {{ \Carbon\Carbon::parse($maalPolicy['batas_awal_haul'])->translatedFormat('d F Y') }}.
                                            </small>
                                        @endif
                                        <small class="small-muted d-block mt-1">
                                            Jika harta atau barang diperoleh pada tanggal berbeda, hitung terpisah per kelompok tanggal kepemilikan.
                                        </small>
                                        @error('tanggal_mulai_kepemilikan')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="formula-box">
                                        <small class="d-block text-muted mb-1">Rumus</small>
                                        <strong id="rumus_maal">Zakat Maal = Total Harta × {{ $formatPersentase($maalPolicy['kadar_persentase'] ?? 0) }}%</strong>

                                        <hr>

                                        <small class="d-block text-muted mb-1">Hasil Perhitungan</small>
                                        <h5 class="mb-0 font-weight-bold text-success" id="hasil_maal">
                                            Rp0
                                        </h5>
                                        <div id="status_maal" class="calculation-status text-muted mt-2"></div>

                                        <button type="button" id="pakai_hasil_maal" class="btn btn-sm btn-success mt-3">
                                            Pakai hasil ini sebagai nominal
                                        </button>
                                    </div>
                                </div>

                                <div id="kalkulator-zakat-penghasilan" class="info-box mb-3" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-0">Kalkulator Zakat Penghasilan</h6>
                                        <span class="zakat-type-badge">{{ $formatPersentase($penghasilanPolicy['kadar_persentase'] ?? 0) }}%</span>
                                    </div>

                                    <p class="small-muted mb-3">
                                        Nisab tahunan {{ $penghasilanPolicy['nisab_pokok'] ?? '-' }}.
                                        Nilai rupiah mengikuti harga emas aktif
                                        @if($hargaEmasPerGram > 0)
                                            Rp{{ number_format($hargaEmasPerGram, 0, ',', '.') }} per gram
                                        @endif
                                        dan nisab bulanan dihitung otomatis dari nisab tahunan dibagi 12.
                                    </p>

                                    <div class="row mb-2">
                                        <div class="col-sm-6 mb-2">
                                            <div class="p-3 border rounded h-100">
                                                <small class="small-muted d-block">Nisab Tahunan</small>
                                                <strong>{{ $nisabPenghasilanTahunan > 0 ? 'Rp'.number_format($nisabPenghasilanTahunan, 0, ',', '.') : '-' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <div class="p-3 border rounded h-100">
                                                <small class="small-muted d-block">Nisab Bulanan</small>
                                                <strong>{{ $nisabPenghasilanBulanan > 0 ? 'Rp'.number_format($nisabPenghasilanBulanan, 0, ',', '.') : '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="kategori_penghasilan">Kategori Penghasilan</label>
                                        <select id="kategori_penghasilan" class="form-control @error('kategori_perhitungan') is-invalid @enderror">
                                            <option value="">Pilih kategori penghasilan</option>
                                            @foreach($penghasilanCategories as $value => $label)
                                                <option value="{{ $value }}" @selected(old('kategori_perhitungan') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <small class="small-muted d-block mt-2">Pilih sumber utama dari penghasilan yang sedang dihitung.</small>
                                        @error('kategori_perhitungan')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="periode_penghasilan">Metode Perhitungan</label>
                                        <select name="periode_penghasilan" id="periode_penghasilan" class="form-control @error('periode_penghasilan') is-invalid @enderror">
                                            <option value="bulanan" @selected($periodePenghasilanAwal === 'bulanan')>Bulanan</option>
                                            <option value="tahunan" @selected($periodePenghasilanAwal === 'tahunan')>Tahunan</option>
                                        </select>
                                        <small class="small-muted d-block mt-2">
                                            Bulanan memakai nisab tahunan dibagi 12. Tahunan memakai nisab penuh dan mengurangi pembayaran terverifikasi pada tahun yang sama.
                                        </small>
                                        @error('periode_penghasilan')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-2" id="bulan-penghasilan-group">
                                        <label for="bulan_penghasilan">Bulan Penghasilan</label>
                                        <input type="month"
                                            name="bulan_penghasilan"
                                            id="bulan_penghasilan"
                                            class="form-control @error('bulan_penghasilan') is-invalid @enderror"
                                            max="{{ now()->format('Y-m') }}"
                                            value="{{ $bulanPenghasilanAwal }}">
                                        @error('bulan_penghasilan')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-2" id="tahun-penghasilan-group" style="display: none;">
                                        <label for="tahun_penghasilan">Tahun Penghasilan</label>
                                        <input type="number"
                                            name="tahun_penghasilan"
                                            id="tahun_penghasilan"
                                            class="form-control @error('tahun_penghasilan') is-invalid @enderror"
                                            min="2000"
                                            max="{{ now()->year }}"
                                            value="{{ $tahunPenghasilanAwal }}">
                                        @error('tahun_penghasilan')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-2">
                                        <label for="penghasilan_bersih_input">Penghasilan/Pendapatan Bersih</label>
                                        <div class="input-group-currency">
                                            <span class="currency-addon">Rp</span>
                                            <input type="text"
                                                id="penghasilan_bersih_input"
                                                class="form-control currency-input @error('penghasilan_bersih') is-invalid @enderror"
                                                inputmode="numeric"
                                                value="{{ old('penghasilan_bersih') ? number_format(old('penghasilan_bersih'), 0, ',', '.') : '' }}"
                                                placeholder="Contoh: 8.000.000"
                                                autocomplete="off">
                                            <input type="hidden" name="penghasilan_bersih" id="penghasilan_bersih_value" value="{{ old('penghasilan_bersih') }}">
                                        </div>
                                        <small class="small-muted d-block mt-2">
                                            Masukkan jumlah yang sudah bersih setelah pengurang atau kebutuhan pokok yang digunakan.
                                        </small>
                                        @error('penghasilan_bersih')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="formula-box">
                                        <small class="d-block text-muted mb-1">Rumus</small>
                                        <strong>Zakat Penghasilan = Penghasilan Bersih × {{ $formatPersentase($penghasilanPolicy['kadar_persentase'] ?? 0) }}%</strong>

                                        <hr>

                                        <small class="d-block text-muted mb-1">Penghasilan Bersih</small>
                                        <h6 class="mb-2 font-weight-bold text-dark" id="penghasilan_bersih">
                                            Rp0
                                        </h6>

                                        <small class="d-block text-muted mb-1">Nisab yang Digunakan</small>
                                        <h6 class="mb-2 font-weight-bold text-dark" id="nisab_penghasilan_digunakan">Rp0</h6>

                                        <small class="d-block text-muted mb-1">Kewajiban Sebelum Pembayaran Terdahulu</small>
                                        <h6 class="mb-2 font-weight-bold text-dark" id="kewajiban_penghasilan">Rp0</h6>

                                        <small class="d-block text-muted mb-1">Sudah Dibayar dan Terverifikasi</small>
                                        <h6 class="mb-2 font-weight-bold text-dark" id="zakat_penghasilan_terbayar">Rp0</h6>

                                        <small class="d-block text-muted mb-1">Sisa Zakat yang Dibayar</small>
                                        <h5 class="mb-0 font-weight-bold text-success" id="hasil_penghasilan">
                                            Rp0
                                        </h5>
                                        <div id="status_penghasilan" class="calculation-status text-muted mt-2"></div>

                                        <button type="button" id="pakai_hasil_penghasilan" class="btn btn-sm btn-success mt-3">
                                            Pakai hasil ini sebagai nominal
                                        </button>
                                    </div>
                                </div>

                                <div id="kalkulator-zakat-fitrah" class="info-box mb-3" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-0">Kalkulator Zakat Fitrah</h6>
                                        <span class="zakat-type-badge">
                                            {{ $formatPersentase($fitrahPolicy['berat_fitrah_kg'] ?? 0) }} kg / jiwa
                                        </span>
                                    </div>

                                    <p class="small-muted mb-3">
                                        Ketentuan per jiwa {{ $formatPersentase($fitrahPolicy['berat_fitrah_kg'] ?? 0) }} kg
                                        atau {{ $formatPersentase($fitrahPolicy['berat_fitrah_liter'] ?? 0) }} liter beras.
                                    </p>

                                    <div class="form-group mb-2">
                                        <label for="jumlah_jiwa_fitrah">Jumlah Jiwa</label>
                                        <input type="number"
                                            id="jumlah_jiwa_fitrah"
                                            class="form-control"
                                            min="1"
                                            step="1"
                                            placeholder="Contoh: 4">
                                    </div>

                                    <div class="formula-box">
                                        <small class="d-block text-muted mb-1">Harga Beras Aktif</small>
                                        <strong>
                                            @if($hargaBerasPerKg > 0)
                                                Rp{{ number_format($hargaBerasPerKg, 0, ',', '.') }} / kg
                                                @if($hargaBeras?->wilayah)
                                                    - {{ $hargaBeras->wilayah }}
                                                @endif
                                            @else
                                                Belum ditetapkan admin
                                            @endif
                                        </strong>

                                        <hr>

                                        <small class="d-block text-muted mb-1">Hasil Perhitungan</small>
                                        <h5 class="mb-0 font-weight-bold text-success" id="hasil_fitrah">Rp0</h5>
                                        <div id="status_fitrah" class="calculation-status text-muted mt-2"></div>

                                        <button type="button" id="pakai_hasil_fitrah" class="btn btn-sm btn-success mt-3">
                                            Pakai hasil ini sebagai nominal
                                        </button>
                                    </div>
                                </div>

                                <div id="metode-pertanian-group" class="info-box mb-3" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-0">Kalkulator Zakat Pertanian</h6>
                                        <span class="zakat-type-badge">Saat panen</span>
                                    </div>
                                    <p class="small-muted mb-3">
                                        Pilih metode pengairan agar kadar zakat yang digunakan sesuai dengan biaya pengelolaan panen.
                                    </p>
                                    <div class="form-group mb-0">
                                        <label for="metode_pengairan">Metode Pengairan</label>
                                        <select id="metode_pengairan" class="form-control">
                                            @if(isset($jenisOptions['zakat_pertanian_berbiaya']))
                                                <option value="zakat_pertanian_berbiaya" @selected($oldJenisZakat === 'zakat_pertanian_berbiaya')>
                                                    Irigasi / menggunakan biaya ({{ $formatPersentase($pertanianBerbiayaPolicy['kadar_persentase'] ?? 0) }}%)
                                                </option>
                                            @endif
                                            @if(isset($jenisOptions['zakat_pertanian_alami']))
                                                <option value="zakat_pertanian_alami" @selected($oldJenisZakat === 'zakat_pertanian_alami')>
                                                    Tadah hujan / alami tanpa biaya ({{ $formatPersentase($pertanianAlamiPolicy['kadar_persentase'] ?? 0) }}%)
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div id="kalkulator-zakat-pertanian-berbiaya" class="info-box mb-3" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-0">Perhitungan Panen Berbiaya</h6>
                                        <span class="zakat-type-badge">{{ $formatPersentase($pertanianBerbiayaPolicy['kadar_persentase'] ?? 0) }}%</span>
                                    </div>
                                    <p class="small-muted mb-3">
                                        Nisab {{ $pertanianBerbiayaPolicy['nisab_pokok'] ?? '-' }}.
                                        Zakat dikeluarkan saat panen untuk pertanian yang diairi dengan irigasi / berbiaya.
                                    </p>
                                    <div class="form-group mb-2">
                                        <label for="kategori_pertanian_berbiaya">Kategori Hasil Pertanian</label>
                                        <select id="kategori_pertanian_berbiaya" class="form-control @error('kategori_perhitungan') is-invalid @enderror">
                                            <option value="">Pilih kategori</option>
                                            @foreach($pertanianBerbiayaCategories as $value => $label)
                                                <option value="{{ $value }}" @selected(old('kategori_perhitungan') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('kategori_perhitungan')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="panen_pertanian_berbiaya">Berat Bersih Hasil Panen</label>
                                        <div class="input-group">
                                            <input type="number"
                                                id="panen_pertanian_berbiaya"
                                                class="form-control @error('berat_panen_kg') is-invalid @enderror"
                                                inputmode="decimal"
                                                min="0.01"
                                                step="0.01"
                                                value="{{ old('berat_panen_kg') }}"
                                                placeholder="Contoh: 700">
                                            <div class="input-group-append">
                                                <span class="input-group-text">kg</span>
                                            </div>
                                        </div>
                                        @error('berat_panen_kg')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="formula-box">
                                        <small class="d-block text-muted mb-1">Rumus</small>
                                        <strong>Berat Panen &times; Harga/kg &times; {{ $formatPersentase($pertanianBerbiayaPolicy['kadar_persentase'] ?? 0) }}%</strong>
                                        <div class="summary-item mt-3">
                                            <span class="small-muted">Harga aktif per kilogram</span>
                                            <strong id="harga_pertanian_berbiaya">-</strong>
                                        </div>
                                        <div class="summary-item">
                                            <span class="small-muted">Nilai panen</span>
                                            <strong id="nilai_panen_pertanian_berbiaya">Rp0</strong>
                                        </div>
                                        <div class="summary-item">
                                            <span class="small-muted">Zakat dalam hasil panen</span>
                                            <strong id="berat_zakat_pertanian_berbiaya">0 kg</strong>
                                        </div>
                                        <hr>
                                        <small class="d-block text-muted mb-1">Zakat yang wajib dibayarkan</small>
                                        <h5 class="mb-0 font-weight-bold text-success" id="hasil_pertanian_berbiaya">Rp0</h5>
                                        <div id="status_pertanian_berbiaya" class="calculation-status text-muted mt-2"></div>
                                        <button type="button" id="pakai_hasil_pertanian_berbiaya" class="btn btn-sm btn-success mt-3">
                                            Pakai hasil ini sebagai nominal
                                        </button>
                                    </div>
                                </div>

                                <div id="kalkulator-zakat-pertanian-alami" class="info-box mb-3" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold mb-0">Perhitungan Panen Alami</h6>
                                        <span class="zakat-type-badge">{{ $formatPersentase($pertanianAlamiPolicy['kadar_persentase'] ?? 0) }}%</span>
                                    </div>
                                    <p class="small-muted mb-3">
                                        Nisab {{ $pertanianAlamiPolicy['nisab_pokok'] ?? '-' }}.
                                        Zakat dikeluarkan saat panen untuk pertanian tadah hujan / alami tanpa biaya irigasi.
                                    </p>
                                    <div class="form-group mb-2">
                                        <label for="kategori_pertanian_alami">Kategori Hasil Pertanian</label>
                                        <select id="kategori_pertanian_alami" class="form-control @error('kategori_perhitungan') is-invalid @enderror">
                                            <option value="">Pilih kategori</option>
                                            @foreach($pertanianAlamiCategories as $value => $label)
                                                <option value="{{ $value }}" @selected(old('kategori_perhitungan') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('kategori_perhitungan')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="panen_pertanian_alami">Berat Bersih Hasil Panen</label>
                                        <div class="input-group">
                                            <input type="number"
                                                id="panen_pertanian_alami"
                                                class="form-control @error('berat_panen_kg') is-invalid @enderror"
                                                inputmode="decimal"
                                                min="0.01"
                                                step="0.01"
                                                value="{{ old('berat_panen_kg') }}"
                                                placeholder="Contoh: 700">
                                            <div class="input-group-append">
                                                <span class="input-group-text">kg</span>
                                            </div>
                                        </div>
                                        @error('berat_panen_kg')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="formula-box">
                                        <small class="d-block text-muted mb-1">Rumus</small>
                                        <strong>Berat Panen &times; Harga/kg &times; {{ $formatPersentase($pertanianAlamiPolicy['kadar_persentase'] ?? 0) }}%</strong>
                                        <div class="summary-item mt-3">
                                            <span class="small-muted">Harga aktif per kilogram</span>
                                            <strong id="harga_pertanian_alami">-</strong>
                                        </div>
                                        <div class="summary-item">
                                            <span class="small-muted">Nilai panen</span>
                                            <strong id="nilai_panen_pertanian_alami">Rp0</strong>
                                        </div>
                                        <div class="summary-item">
                                            <span class="small-muted">Zakat dalam hasil panen</span>
                                            <strong id="berat_zakat_pertanian_alami">0 kg</strong>
                                        </div>
                                        <hr>
                                        <small class="d-block text-muted mb-1">Zakat yang wajib dibayarkan</small>
                                        <h5 class="mb-0 font-weight-bold text-success" id="hasil_pertanian_alami">Rp0</h5>
                                        <div id="status_pertanian_alami" class="calculation-status text-muted mt-2"></div>
                                        <button type="button" id="pakai_hasil_pertanian_alami" class="btn btn-sm btn-success mt-3">
                                            Pakai hasil ini sebagai nominal
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="nominal">Nominal Transaksi</label>
                                <div class="input-group-currency">
                                    <span class="currency-addon">Rp</span>
                                    <input type="text"
                                        id="nominal_display"
                                        class="form-control @error('nominal') is-invalid @enderror"
                                        inputmode="numeric"
                                        value="{{ old('nominal') ? number_format(old('nominal'), 0, ',', '.') : '' }}"
                                        placeholder="50.000"
                                        required
                                        autocomplete="off">
                                    <input type="hidden"
                                        name="nominal"
                                        id="nominal"
                                        value="{{ old('nominal') }}">
                                </div>
                                <small class="small-muted d-block mt-2">
                                    Minimal transaksi {{ $paymentGatewayReady ? 'Rp10.000' : 'Rp1.000' }}
                                </small>
                                @error('nominal')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

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
                                {{-- Info QRIS --}}
                                <div id="info-qris" class="alert alert-info" style="display: none; border-radius: 12px;">
                                    <strong><i class="fa fa-qrcode mr-1"></i> QRIS</strong><br>
                                    Pembayaran dilakukan dengan scan QR Code. Setelah lunas, transaksi otomatis diterima oleh sistem.
                                </div>

                                {{-- Info Virtual Account + sub-pilihan bank --}}
                                <div id="info-virtual-account" style="display: none;">
                                    <div class="alert alert-info mb-2" style="border-radius: 12px;">
                                        <strong><i class="fa fa-university mr-1"></i> Virtual Account</strong><br>
                                        Pilih bank untuk mendapatkan nomor VA. Setelah transfer, transaksi otomatis diterima.
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">Pilih Bank VA</label>
                                        <div class="option-grid" id="bank_va_cards" style="grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 8px;">
                                            @foreach(['bca_va' => 'BCA', 'bni_va' => 'BNI', 'bri_va' => 'BRI', 'permata_va' => 'Permata', 'other_va' => 'Lainnya'] as $vaVal => $vaLabel)
                                                <div class="option-card" data-value="{{ $vaVal }}" style="padding: 12px 8px;">
                                                    <div class="option-icon" style="width: 36px; height: 36px; font-size: 14px; margin-bottom: 6px;">
                                                        @if($vaVal === 'bca_va') <i class="fa fa-landmark"></i>
                                                        @elseif($vaVal === 'bni_va') <i class="fa fa-building"></i>
                                                        @elseif($vaVal === 'bri_va') <i class="fa fa-university"></i>
                                                        @elseif($vaVal === 'permata_va') <i class="fa fa-gem"></i>
                                                        @else <i class="fa fa-ellipsis-h"></i>
                                                        @endif
                                                    </div>
                                                    <h6 class="option-title" style="font-size: 12px;">{{ $vaLabel }}</h6>
                                                </div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="bank_va_pilihan" id="bank_va_pilihan" value="">
                                    </div>
                                </div>

                                {{-- Info E-Wallet + sub-pilihan --}}
                                <div id="info-e-wallet" style="display: none;">
                                    <div class="alert alert-info mb-2" style="border-radius: 12px;">
                                        <strong><i class="fa fa-mobile-alt mr-1"></i> E-Wallet</strong><br>
                                        Pilih dompet digital yang ingin digunakan. Setelah lunas, transaksi otomatis diterima.
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-muted">Pilih E-Wallet</label>
                                        <div class="option-grid" id="ewallet_cards" style="grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 8px;">
                                            @foreach(['gopay' => 'GoPay', 'shopeepay' => 'ShopeePay', 'dana' => 'DANA'] as $ewVal => $ewLabel)
                                                <div class="option-card" data-value="{{ $ewVal }}" style="padding: 12px 8px;">
                                                    <div class="option-icon" style="width: 36px; height: 36px; font-size: 14px; margin-bottom: 6px;">
                                                        <i class="fa fa-wallet"></i>
                                                    </div>
                                                    <h6 class="option-title" style="font-size: 12px;">{{ $ewLabel }}</h6>
                                                </div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="ewallet_pilihan" id="ewallet_pilihan" value="">
                                    </div>
                                </div>

                                {{-- Info Bank Transfer --}}
                                <div id="info-bank-transfer" class="alert alert-info" style="display: none; border-radius: 12px;">
                                    <strong><i class="fa fa-exchange-alt mr-1"></i> Bank Transfer Gateway</strong><br>
                                    Transaksi akan diproses melalui bank transfer payment gateway. Jika berhasil, sistem akan menerima status transaksi secara otomatis.
                                </div>
                            @else
                                <div class="alert alert-warning" style="border-radius: 12px;">
                                    <strong>Payment Gateway Belum Aktif</strong><br>
                                    Untuk sementara transaksi memakai upload bukti transfer dan diverifikasi admin.
                                </div>

                                <div id="info-manual-transfer" class="alert alert-warning" style="display: none; border-radius: 12px;">
                                    <strong>Transfer Bank Manual</strong><br>
                                    Silakan transfer ke rekening masjid/lembaga, lalu upload bukti transfer agar admin dapat melakukan verifikasi.
                                </div>

                                <div id="info-qris-manual" class="alert alert-info" style="display: none; border-radius: 12px;">
                                    <strong>QRIS Manual</strong><br>
                                    Silakan lakukan transfer melalui QRIS manual, lalu upload bukti transfer agar admin dapat melakukan verifikasi.
                                </div>

                                <div class="form-group">
                                    <label>Bukti Transfer</label>
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
                                    Lanjutkan Pembayaran
                                @else
                                    <i class="fa fa-paper-plane mr-1"></i>
                                    Kirim Transaksi & Verifikasi
                                @endif
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($isZakatPage)
            <div class="row mt-4">
                <div class="col-12 mb-3">
                    <div class="info-box">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                            <div>
                                <h5 class="font-weight-bold mb-1">Ketentuan Zakat Aktif</h5>
                                <p class="small-muted mb-0">Informasi ini otomatis mengikuti kebijakan yang berlaku di halaman admin.</p>
                            </div>
                            <span class="zakat-type-badge mt-2 mt-md-0">Hanya baca</span>
                        </div>

                        <div class="policy-grid">
                            @foreach($zakatPolicies as $policy)
                                <div class="policy-item">
                                    <strong class="d-block mb-2">{{ $policy['nama'] }}</strong>
                                    @if(($policy['jenis'] ?? null) === 'fitrah')
                                        <small>Ketentuan per jiwa</small>
                                        <span>{{ $formatPersentase($policy['berat_fitrah_kg'] ?? 0) }} kg / {{ $formatPersentase($policy['berat_fitrah_liter'] ?? 0) }} liter</span>
                                    @else
                                        <small>Kadar zakat</small>
                                        <span>{{ $formatPersentase($policy['kadar_persentase'] ?? 0) }}%</span>
                                    @endif
                                    <small class="mt-2">Nisab</small>
                                    <span>{{ $policy['nisab_pokok'] ?: '-' }}</span>
                                    @if(! empty($policy['nisab_rupiah']))
                                        <span class="d-block">Rp{{ number_format($policy['nisab_rupiah'], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            @endforeach
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
    const zakatPolicies = @json($zakatPolicies);
    const transactionCategory = @json($jenis);
    const organizationSelect = document.getElementById('organization_id');
    const zakatPenghasilanTerbayar = @json($zakatPenghasilanTerbayar);
    const hargaBerasPerKg = {{ $hargaBerasPerKg }};
    const hargaEmasPerGram = {{ $hargaEmasPerGram }};
    const hargaPertanianPerKg = @json($hargaPertanianPerKg);
    const nisabEmasGram = {{ $nisabEmasGram }};
    const jenisZiswaf = document.getElementById('jenis_ziswaf');
    const wakafType = document.getElementById('wakaf_type');
    const wakafReturnGroup = document.getElementById('wakaf_return_group');
    const wakafReturnDate = document.getElementById('wakaf_return_date');
    // nominal hidden (nilai numerik asli, dikirim ke server)
    const nominal = document.getElementById('nominal');
    // nominal_display (format ribuan, ditampilkan ke user)
    const nominalDisplay = document.getElementById('nominal_display');
    const kategoriPerhitungan = document.getElementById('kategori_perhitungan');
    const kategoriHartaMaal = document.getElementById('kategori_harta_maal');
    const kategoriPenghasilan = document.getElementById('kategori_penghasilan');

    const kalkulatorMaal = document.getElementById('kalkulator-zakat-maal');
    const kalkulatorPenghasilan = document.getElementById('kalkulator-zakat-penghasilan');
    const kalkulatorFitrah = document.getElementById('kalkulator-zakat-fitrah');

    const hartaMaal = document.getElementById('harta_maal');
    const nilaiHartaUmumGroup = document.getElementById('nilai-harta-umum-group');
    const nilaiEmasGroup = document.getElementById('nilai-emas-group');
    const beratEmasGram = document.getElementById('berat_emas_gram');
    const nilaiEmasRupiah = document.getElementById('nilai_emas_rupiah');
    const rumusMaal = document.getElementById('rumus_maal');
    const tanggalMulaiKepemilikan = document.getElementById('tanggal_mulai_kepemilikan');
    const hasilMaal = document.getElementById('hasil_maal');
    const statusMaal = document.getElementById('status_maal');
    const pakaiHasilMaal = document.getElementById('pakai_hasil_maal');

    const periodePenghasilan = document.getElementById('periode_penghasilan');
    const bulanPenghasilan = document.getElementById('bulan_penghasilan');
    const tahunPenghasilan = document.getElementById('tahun_penghasilan');
    const bulanPenghasilanGroup = document.getElementById('bulan-penghasilan-group');
    const tahunPenghasilanGroup = document.getElementById('tahun-penghasilan-group');
    const penghasilanBersihInput = document.getElementById('penghasilan_bersih_input');
    const penghasilanBersihValue = document.getElementById('penghasilan_bersih_value');
    const penghasilanBersih = document.getElementById('penghasilan_bersih');
    const nisabPenghasilanDigunakan = document.getElementById('nisab_penghasilan_digunakan');
    const kewajibanPenghasilan = document.getElementById('kewajiban_penghasilan');
    const zakatPenghasilanTerbayarElement = document.getElementById('zakat_penghasilan_terbayar');
    const hasilPenghasilan = document.getElementById('hasil_penghasilan');
    const statusPenghasilan = document.getElementById('status_penghasilan');
    const pakaiHasilPenghasilan = document.getElementById('pakai_hasil_penghasilan');

    const jumlahJiwaFitrah = document.getElementById('jumlah_jiwa_fitrah');
    const hasilFitrah = document.getElementById('hasil_fitrah');
    const statusFitrah = document.getElementById('status_fitrah');
    const pakaiHasilFitrah = document.getElementById('pakai_hasil_fitrah');

    const kalkulatorPertanianBerbiaya = document.getElementById('kalkulator-zakat-pertanian-berbiaya');
    const kalkulatorPertanianAlami = document.getElementById('kalkulator-zakat-pertanian-alami');
    const metodePertanianGroup = document.getElementById('metode-pertanian-group');
    const metodePengairan = document.getElementById('metode_pengairan');
    const kategoriPertanianBerbiaya = document.getElementById('kategori_pertanian_berbiaya');
    const kategoriPertanianAlami = document.getElementById('kategori_pertanian_alami');
    const beratPanen = document.getElementById('berat_panen_kg');
    const panenPertanianBerbiaya = document.getElementById('panen_pertanian_berbiaya');
    const panenPertanianAlami = document.getElementById('panen_pertanian_alami');
    const hargaPertanianBerbiaya = document.getElementById('harga_pertanian_berbiaya');
    const hargaPertanianAlami = document.getElementById('harga_pertanian_alami');
    const nilaiPanenPertanianBerbiaya = document.getElementById('nilai_panen_pertanian_berbiaya');
    const nilaiPanenPertanianAlami = document.getElementById('nilai_panen_pertanian_alami');
    const beratZakatPertanianBerbiaya = document.getElementById('berat_zakat_pertanian_berbiaya');
    const beratZakatPertanianAlami = document.getElementById('berat_zakat_pertanian_alami');
    const hasilPertanianBerbiaya = document.getElementById('hasil_pertanian_berbiaya');
    const hasilPertanianAlami = document.getElementById('hasil_pertanian_alami');
    const statusPertanianBerbiaya = document.getElementById('status_pertanian_berbiaya');
    const statusPertanianAlami = document.getElementById('status_pertanian_alami');
    const pakaiHasilPertanianBerbiaya = document.getElementById('pakai_hasil_pertanian_berbiaya');
    const pakaiHasilPertanianAlami = document.getElementById('pakai_hasil_pertanian_alami');

    const metodePembayaran = document.getElementById('metode_pembayaran');

    const infoQris = document.getElementById('info-qris');
    const infoVirtualAccount = document.getElementById('info-virtual-account');
    const infoEWallet = document.getElementById('info-e-wallet');
    const infoBankTransfer = document.getElementById('info-bank-transfer');

    const infoManualTransfer = document.getElementById('info-manual-transfer');
    const infoQrisManual = document.getElementById('info-qris-manual');

    let nilaiMaal = 0;
    let nilaiPenghasilan = 0;
    let nilaiFitrah = 0;
    let nilaiPertanianBerbiaya = 0;
    let nilaiPertanianAlami = 0;

    function toggleWakafReturnDate() {
        if (!wakafType || !wakafReturnGroup || !wakafReturnDate) return;
        const isTemporary = wakafType.value === 'temporer';
        wakafReturnGroup.hidden = !isTemporary;
        wakafReturnDate.disabled = !isTemporary;
        wakafReturnDate.required = isTemporary;
    }

    if (wakafType) {
        wakafType.addEventListener('change', toggleWakafReturnDate);
        toggleWakafReturnDate();
    }

    // ============================================================
    //  Utility: format / parse angka ribuan (ID: titik sebagai
    //  pemisah ribuan, tidak ada desimal)
    // ============================================================
    function formatRibuan(value) {
        // Hapus semua karakter selain angka
        const digits = String(value).replace(/\D/g, '');
        if (!digits) return '';
        return parseInt(digits, 10).toLocaleString('id-ID');
    }

    function unformatRibuan(value) {
        // Strip titik pemisah ribuan, kembalikan string digit
        return String(value).replace(/\./g, '').replace(/\D/g, '');
    }

    /**
     * Pasang auto-format ribuan pada sebuah input text.
     * Setiap kali user mengetik, nilai akan diformat otomatis.
     * Kembalikan fungsi getter yang mengembalikan nilai numerik asli.
     */
    function attachCurrencyInput(inputEl) {
        if (!inputEl) return () => 0;

        inputEl.addEventListener('input', function () {
            const raw = unformatRibuan(this.value);
            const cursorPos = this.selectionStart;
            const prevLen = this.value.length;

            this.value = raw ? parseInt(raw, 10).toLocaleString('id-ID') : '';

            // Pertahankan posisi kursor agar tidak loncat ke akhir
            const newLen = this.value.length;
            const diff = newLen - prevLen;
            try {
                this.setSelectionRange(cursorPos + diff, cursorPos + diff);
            } catch(e) {}
        });

        // Hanya izinkan angka dan tombol navigasi
        inputEl.addEventListener('keydown', function (e) {
            const allowed = [
                'Backspace','Delete','Tab','Escape','Enter','Home','End',
                'ArrowLeft','ArrowRight','ArrowUp','ArrowDown'
            ];
            if (allowed.includes(e.key)) return;
            if ((e.ctrlKey || e.metaKey) && ['a','c','v','x','z'].includes(e.key.toLowerCase())) return;
            if (!/^[0-9]$/.test(e.key)) e.preventDefault();
        });

        // Kembalikan getter nilai numerik
        return function () {
            return parseInt(unformatRibuan(inputEl.value) || '0', 10);
        };
    }

    // ============================================================
    //  Pasang currency format ke semua field keuangan
    // ============================================================
    const getHartaMaal          = attachCurrencyInput(hartaMaal);
    const getPenghasilanBersih  = attachCurrencyInput(penghasilanBersihInput);
    const getNominalDisplay     = attachCurrencyInput(nominalDisplay);

    // Sync nominalDisplay → nominal (hidden) setiap kali user mengetik
    if (nominalDisplay && nominal) {
        nominalDisplay.addEventListener('input', function () {
            nominal.value = unformatRibuan(this.value) || '';
        });
    }

    // ============================================================
    //  Format Rupiah untuk hasil kalkulasi
    // ============================================================
    function rupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(value || 0);
    }

    // ============================================================
    //  Kalkulator
    // ============================================================
    function hideAllCalculator() {
        if (kalkulatorMaal) {
            kalkulatorMaal.style.display = 'none';
            kalkulatorMaal.classList.remove('slide-in-calc');
        }

        if (kalkulatorPenghasilan) {
            kalkulatorPenghasilan.style.display = 'none';
            kalkulatorPenghasilan.classList.remove('slide-in-calc');
        }

        if (kalkulatorFitrah) {
            kalkulatorFitrah.style.display = 'none';
            kalkulatorFitrah.classList.remove('slide-in-calc');
        }

        [metodePertanianGroup, kalkulatorPertanianBerbiaya, kalkulatorPertanianAlami].forEach(function (element) {
            if (element) {
                element.style.display = 'none';
                element.classList.remove('slide-in-calc');
            }
        });
    }

    function syncKategoriPerhitungan() {
        if (!kategoriPerhitungan || !jenisZiswaf) {
            return;
        }

        if (jenisZiswaf.value === 'zakat_maal') {
            kategoriPerhitungan.value = kategoriHartaMaal ? kategoriHartaMaal.value : '';
        } else if (jenisZiswaf.value === 'zakat_penghasilan') {
            kategoriPerhitungan.value = kategoriPenghasilan ? kategoriPenghasilan.value : '';
        } else if (jenisZiswaf.value === 'zakat_pertanian_berbiaya') {
            kategoriPerhitungan.value = kategoriPertanianBerbiaya ? kategoriPertanianBerbiaya.value : '';
        } else if (jenisZiswaf.value === 'zakat_pertanian_alami') {
            kategoriPerhitungan.value = kategoriPertanianAlami ? kategoriPertanianAlami.value : '';
        } else {
            kategoriPerhitungan.value = '';
        }
    }

    function toggleMaalCategory() {
        const isMaal = Boolean(jenisZiswaf && jenisZiswaf.value === 'zakat_maal');
        const isGold = Boolean(kategoriHartaMaal && kategoriHartaMaal.value === 'emas_logam_mulia');

        if (nilaiHartaUmumGroup) {
            nilaiHartaUmumGroup.style.display = isGold ? 'none' : 'block';
        }

        if (nilaiEmasGroup) {
            nilaiEmasGroup.style.display = isGold ? 'block' : 'none';
        }

        if (hartaMaal) {
            hartaMaal.required = isMaal && !isGold;
        }

        if (beratEmasGram) {
            beratEmasGram.required = isMaal && isGold;
        }

        if (rumusMaal) {
            const rate = Number((zakatPolicies.zakat_maal || {}).kadar_persentase || 0)
                .toLocaleString('id-ID');
            rumusMaal.textContent = isGold
                ? 'Zakat Emas = Berat Emas × Harga per Gram × ' + rate + '%'
                : 'Zakat Maal = Total Harta × ' + rate + '%';
        }

        hitungMaal();
    }

    function toggleJenisZakat() {
        if (!jenisZiswaf) {
            return;
        }

        const jenis = jenisZiswaf.value;

        hideAllCalculator();

        if (tanggalMulaiKepemilikan) {
            tanggalMulaiKepemilikan.required = jenis === 'zakat_maal';
        }

        if (kategoriHartaMaal) {
            kategoriHartaMaal.required = jenis === 'zakat_maal';
        }

        if (kategoriPenghasilan) {
            kategoriPenghasilan.required = jenis === 'zakat_penghasilan';
        }

        if (kategoriPertanianBerbiaya) {
            kategoriPertanianBerbiaya.required = jenis === 'zakat_pertanian_berbiaya';
        }

        if (kategoriPertanianAlami) {
            kategoriPertanianAlami.required = jenis === 'zakat_pertanian_alami';
        }

        syncKategoriPerhitungan();
        toggleMaalCategory();
        togglePeriodePenghasilan();

        if (kalkulatorMaal && jenis === 'zakat_maal') {
            kalkulatorMaal.style.display = 'block';
            kalkulatorMaal.classList.add('slide-in-calc');
            hitungMaal();
        }

        if (kalkulatorPenghasilan && jenis === 'zakat_penghasilan') {
            kalkulatorPenghasilan.style.display = 'block';
            kalkulatorPenghasilan.classList.add('slide-in-calc');
        }

        if (kalkulatorFitrah && jenis === 'zakat_fitrah') {
            kalkulatorFitrah.style.display = 'block';
            kalkulatorFitrah.classList.add('slide-in-calc');
        }

        const isPertanian = jenis === 'zakat_pertanian_berbiaya' || jenis === 'zakat_pertanian_alami';
        if (metodePertanianGroup && isPertanian) {
            metodePertanianGroup.style.display = 'block';
            metodePertanianGroup.classList.add('slide-in-calc');
        }

        if (kalkulatorPertanianBerbiaya && jenis === 'zakat_pertanian_berbiaya') {
            kalkulatorPertanianBerbiaya.style.display = 'block';
            kalkulatorPertanianBerbiaya.classList.add('slide-in-calc');
            hitungPertanianBerbiaya();
        }

        if (kalkulatorPertanianAlami && jenis === 'zakat_pertanian_alami') {
            kalkulatorPertanianAlami.style.display = 'block';
            kalkulatorPertanianAlami.classList.add('slide-in-calc');
            hitungPertanianAlami();
        }
    }

    function hitungMaal() {
        const policy = zakatPolicies.zakat_maal || {};
        const isGold = Boolean(kategoriHartaMaal && kategoriHartaMaal.value === 'emas_logam_mulia');
        const beratEmas = Math.max(Number(beratEmasGram ? beratEmasGram.value : 0) || 0, 0);
        const harta = isGold
            ? Math.round(beratEmas * hargaEmasPerGram)
            : getHartaMaal();
        const nisab = isGold
            ? Math.round(nisabEmasGram * hargaEmasPerGram)
            : Number(policy.nisab_rupiah || 0);
        const rate = Number(policy.kadar_persentase || 0) / 100;
        const memenuhiNisab = isGold
            ? hargaEmasPerGram > 0 && nisabEmasGram > 0 && beratEmas >= nisabEmasGram
            : nisab <= 0 || harta >= nisab;
        const batasAwalHaul = String(policy.batas_awal_haul || '');
        const tanggalMulai = tanggalMulaiKepemilikan ? tanggalMulaiKepemilikan.value : '';
        const memenuhiHaul = Boolean(
            tanggalMulai
            && batasAwalHaul
            && tanggalMulai <= batasAwalHaul
        );

        nilaiMaal = memenuhiNisab && memenuhiHaul
            ? Math.round(harta * rate)
            : 0;

        if (hasilMaal) {
            hasilMaal.textContent = rupiah(nilaiMaal);
        }

        if (nilaiEmasRupiah) {
            nilaiEmasRupiah.textContent = rupiah(harta);
        }

        if (statusMaal) {
            if (isGold && hargaEmasPerGram <= 0) {
                statusMaal.textContent = 'Harga emas aktif belum ditetapkan admin.';
                statusMaal.className = 'calculation-status text-danger mt-2';
            } else if (isGold && beratEmas < nisabEmasGram) {
                statusMaal.textContent = 'Berat emas belum mencapai nisab ' + nisabEmasGram.toLocaleString('id-ID') + ' gram.';
                statusMaal.className = 'calculation-status text-danger mt-2';
            } else if (!memenuhiNisab) {
                statusMaal.textContent = 'Harta belum mencapai nisab ' + rupiah(nisab) + '.';
                statusMaal.className = 'calculation-status text-danger mt-2';
            } else if (!tanggalMulai) {
                statusMaal.textContent = 'Masukkan tanggal mulai kepemilikan untuk menghitung haul.';
                statusMaal.className = 'calculation-status text-warning mt-2';
            } else if (!batasAwalHaul) {
                statusMaal.textContent = 'Format haul dari kebijakan admin belum dapat dihitung.';
                statusMaal.className = 'calculation-status text-danger mt-2';
            } else if (!memenuhiHaul) {
                statusMaal.textContent = 'Harta atau barang belum memenuhi haul ' + String(policy.haul || '') + '.';
                statusMaal.className = 'calculation-status text-warning mt-2';
            } else {
                statusMaal.textContent = 'Harta atau barang telah mencapai nisab dan memenuhi haul.';
                statusMaal.className = 'calculation-status text-success mt-2';
            }
        }
    }

    function hitungPenghasilan() {
        const bersih = Math.max(getPenghasilanBersih(), 0);
        const policy = zakatPolicies.zakat_penghasilan || {};
        const periode = periodePenghasilan ? periodePenghasilan.value : 'bulanan';
        const nisabTahunan = Math.round(Number(policy.nisab_rupiah || 0));
        const nisabBulanan = nisabTahunan > 0 ? Math.round(nisabTahunan / 12) : 0;
        const nisab = periode === 'tahunan' ? nisabTahunan : nisabBulanan;
        const rate = Number(policy.kadar_persentase || 0) / 100;
        const memenuhiNisab = nisab > 0 && bersih >= nisab;
        const kewajiban = memenuhiNisab ? Math.round(bersih * rate) : 0;
        const bulan = bulanPenghasilan ? bulanPenghasilan.value : '';
        const tahun = periode === 'bulanan'
            ? String(bulan || '').slice(0, 4)
            : String(tahunPenghasilan ? tahunPenghasilan.value : '');
        const sudahDibayar = periode === 'bulanan'
            ? Number((zakatPenghasilanTerbayar.per_bulan || {})[bulan] || 0)
            : Number((zakatPenghasilanTerbayar.per_tahun || {})[tahun] || 0);

        nilaiPenghasilan = Math.max(kewajiban - sudahDibayar, 0);

        if (penghasilanBersih) {
            penghasilanBersih.textContent = rupiah(bersih);
        }

        if (nisabPenghasilanDigunakan) {
            nisabPenghasilanDigunakan.textContent = rupiah(nisab);
        }

        if (kewajibanPenghasilan) {
            kewajibanPenghasilan.textContent = rupiah(kewajiban);
        }

        if (zakatPenghasilanTerbayarElement) {
            zakatPenghasilanTerbayarElement.textContent = rupiah(sudahDibayar);
        }

        if (hasilPenghasilan) {
            hasilPenghasilan.textContent = rupiah(nilaiPenghasilan);
        }

        if (statusPenghasilan) {
            if (nisab <= 0) {
                statusPenghasilan.textContent = 'Nisab tahunan belum ditetapkan oleh admin.';
                statusPenghasilan.className = 'calculation-status text-danger mt-2';
            } else if (!memenuhiNisab) {
                statusPenghasilan.textContent = 'Penghasilan bersih belum mencapai nisab ' + periode + ' ' + rupiah(nisab) + '.';
                statusPenghasilan.className = 'calculation-status text-warning mt-2';
            } else if (nilaiPenghasilan <= 0) {
                statusPenghasilan.textContent = 'Kewajiban zakat untuk periode ini sudah terpenuhi.';
                statusPenghasilan.className = 'calculation-status text-success mt-2';
            } else {
                statusPenghasilan.textContent = periode === 'tahunan'
                    ? 'Sisa zakat tahunan setelah dikurangi pembayaran terverifikasi.'
                    : 'Penghasilan bersih telah mencapai nisab bulanan.';
                statusPenghasilan.className = 'calculation-status text-success mt-2';
            }
        }
    }

    function togglePeriodePenghasilan() {
        const tahunan = Boolean(periodePenghasilan && periodePenghasilan.value === 'tahunan');
        const penghasilanDipilih = Boolean(jenisZiswaf && jenisZiswaf.value === 'zakat_penghasilan');

        if (bulanPenghasilanGroup) {
            bulanPenghasilanGroup.style.display = tahunan ? 'none' : 'block';
        }

        if (tahunPenghasilanGroup) {
            tahunPenghasilanGroup.style.display = tahunan ? 'block' : 'none';
        }

        if (bulanPenghasilan) {
            bulanPenghasilan.required = penghasilanDipilih && !tahunan;
        }

        if (tahunPenghasilan) {
            tahunPenghasilan.required = penghasilanDipilih;
            if (!tahunan && bulanPenghasilan && bulanPenghasilan.value) {
                tahunPenghasilan.value = bulanPenghasilan.value.slice(0, 4);
            }
        }

        hitungPenghasilan();
    }

    function hitungFitrah() {
        const jumlahJiwa = Math.max(parseInt(jumlahJiwaFitrah ? jumlahJiwaFitrah.value : '0', 10) || 0, 0);
        const policy = zakatPolicies.zakat_fitrah || {};
        const beratPerJiwa = Number(policy.berat_fitrah_kg || 0);

        nilaiFitrah = Math.round(jumlahJiwa * beratPerJiwa * hargaBerasPerKg);

        if (hasilFitrah) {
            hasilFitrah.textContent = rupiah(nilaiFitrah);
        }

        if (statusFitrah) {
            if (hargaBerasPerKg <= 0) {
                statusFitrah.textContent = 'Harga beras aktif belum ditetapkan admin. Isi nominal transaksi secara manual.';
                statusFitrah.className = 'calculation-status text-warning mt-2';
            } else if (jumlahJiwa <= 0) {
                statusFitrah.textContent = 'Masukkan jumlah jiwa yang ditunaikan.';
                statusFitrah.className = 'calculation-status text-muted mt-2';
            } else {
                statusFitrah.textContent = jumlahJiwa + ' jiwa × ' + beratPerJiwa.toLocaleString('id-ID') + ' kg × ' + rupiah(hargaBerasPerKg) + '.';
                statusFitrah.className = 'calculation-status text-success mt-2';
            }
        }
    }

    function hitungPertanian(
        kategoriElement,
        beratElement,
        policy,
        hargaElement,
        nilaiPanenElement,
        beratZakatElement,
        hasilElement,
        statusElement
    ) {
        const kategori = kategoriElement ? kategoriElement.value : '';
        const berat = Math.max(Number(beratElement ? beratElement.value : 0) || 0, 0);
        const hargaPerKg = Number(hargaPertanianPerKg[kategori] || 0);
        const nisabKg = kategori === 'beras' ? 520 : 653;
        const rate = Number(policy.kadar_persentase || 0) / 100;
        const nilaiPanen = Math.round(berat * hargaPerKg);
        const memenuhiNisab = Boolean(kategori && berat >= nisabKg);
        const hasil = memenuhiNisab && hargaPerKg > 0
            ? Math.round(nilaiPanen * rate)
            : 0;

        if (beratPanen && jenisZiswaf && (
            jenisZiswaf.value === 'zakat_pertanian_berbiaya'
            || jenisZiswaf.value === 'zakat_pertanian_alami'
        )) {
            beratPanen.value = berat > 0 ? String(berat) : '';
        }

        if (hargaElement) {
            hargaElement.textContent = hargaPerKg > 0 ? rupiah(hargaPerKg) + ' / kg' : 'Belum ditetapkan';
        }

        if (nilaiPanenElement) {
            nilaiPanenElement.textContent = rupiah(nilaiPanen);
        }

        if (beratZakatElement) {
            const zakatKg = memenuhiNisab ? berat * rate : 0;
            beratZakatElement.textContent = zakatKg.toLocaleString('id-ID', {
                maximumFractionDigits: 2
            }) + ' kg';
        }

        if (hasilElement) {
            hasilElement.textContent = rupiah(hasil);
        }

        if (statusElement) {
            if (!kategori) {
                statusElement.textContent = 'Pilih kategori hasil pertanian.';
                statusElement.className = 'calculation-status text-muted mt-2';
            } else if (hargaPerKg <= 0) {
                statusElement.textContent = 'Harga aktif per kilogram belum ditetapkan admin.';
                statusElement.className = 'calculation-status text-danger mt-2';
            } else if (berat < nisabKg) {
                statusElement.textContent = 'Hasil panen belum mencapai nisab ' + nisabKg.toLocaleString('id-ID') + ' kg.';
                statusElement.className = 'calculation-status text-warning mt-2';
            } else {
                statusElement.textContent = 'Hasil panen mencapai nisab dan zakat dikeluarkan saat panen.';
                statusElement.className = 'calculation-status text-success mt-2';
            }
        }

        return hasil;
    }

    function hitungPertanianBerbiaya() {
        nilaiPertanianBerbiaya = hitungPertanian(
            kategoriPertanianBerbiaya,
            panenPertanianBerbiaya,
            zakatPolicies.zakat_pertanian_berbiaya || {},
            hargaPertanianBerbiaya,
            nilaiPanenPertanianBerbiaya,
            beratZakatPertanianBerbiaya,
            hasilPertanianBerbiaya,
            statusPertanianBerbiaya
        );
    }

    function hitungPertanianAlami() {
        nilaiPertanianAlami = hitungPertanian(
            kategoriPertanianAlami,
            panenPertanianAlami,
            zakatPolicies.zakat_pertanian_alami || {},
            hargaPertanianAlami,
            nilaiPanenPertanianAlami,
            beratZakatPertanianAlami,
            hasilPertanianAlami,
            statusPertanianAlami
        );
    }

    // ============================================================
    //  Info Metode Pembayaran
    // ============================================================
    function hideAllPaymentInfo() {
        [infoQris, infoVirtualAccount, infoEWallet, infoBankTransfer,
         infoManualTransfer, infoQrisManual].forEach(function (el) {
            if (el) el.style.display = 'none';
        });
    }

    // Sub-pilihan Bank VA
    const bankVaCards = document.querySelectorAll('#bank_va_cards .option-card');
    const bankVaPilihan = document.getElementById('bank_va_pilihan');

    bankVaCards.forEach(function (card) {
        card.addEventListener('click', function () {
            bankVaCards.forEach(function (c) { c.classList.remove('active'); });
            this.classList.add('active');
            if (bankVaPilihan) bankVaPilihan.value = this.getAttribute('data-value');
        });
    });

    // Aktifkan kartu pertama Bank VA secara default
    if (bankVaCards.length > 0) {
        bankVaCards[0].classList.add('active');
        if (bankVaPilihan) bankVaPilihan.value = bankVaCards[0].getAttribute('data-value');
    }

    // Sub-pilihan E-Wallet
    const ewalletCards = document.querySelectorAll('#ewallet_cards .option-card');
    const ewalletPilihan = document.getElementById('ewallet_pilihan');

    ewalletCards.forEach(function (card) {
        card.addEventListener('click', function () {
            ewalletCards.forEach(function (c) { c.classList.remove('active'); });
            this.classList.add('active');
            if (ewalletPilihan) ewalletPilihan.value = this.getAttribute('data-value');
        });
    });

    // Aktifkan kartu pertama E-Wallet secara default
    if (ewalletCards.length > 0) {
        ewalletCards[0].classList.add('active');
        if (ewalletPilihan) ewalletPilihan.value = ewalletCards[0].getAttribute('data-value');
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

    // ============================================================
    //  Card Clicks
    // ============================================================
    // Sync Card Clicks for Jenis Ziswaf
    const jenisCards = document.querySelectorAll('#jenis_ziswaf_cards .option-card');
    jenisCards.forEach(card => {
        card.addEventListener('click', function() {
            jenisCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const val = this.getAttribute('data-value');
            if (jenisZiswaf) {
                jenisZiswaf.value = val === 'zakat_pertanian'
                    ? (metodePengairan ? metodePengairan.value : @json($defaultJenisPertanian))
                    : val;
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

    // ============================================================
    //  Dropzone
    // ============================================================
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

    // Saat FINUS memiliki lebih dari satu masjid, ketentuan dan harga zakat
    // dimuat ulang sesuai organization yang dipilih Jamaah.
    if (organizationSelect && organizationSelect.tagName === 'SELECT' && transactionCategory === 'zakat') {
        organizationSelect.addEventListener('change', function () {
            if (! this.value) {
                return;
            }

            const url = new URL(window.location.href);
            url.searchParams.set('organization_id', this.value);
            window.location.href = url.toString();
        });
    }

    // ============================================================
    //  Event Listeners — Kalkulator
    // ============================================================
    if (jenisZiswaf) {
        jenisZiswaf.addEventListener('change', toggleJenisZakat);
        toggleJenisZakat();
    }

    if (kategoriHartaMaal) {
        kategoriHartaMaal.addEventListener('change', function () {
            syncKategoriPerhitungan();
            toggleMaalCategory();
        });
    }

    if (kategoriPenghasilan) {
        kategoriPenghasilan.addEventListener('change', syncKategoriPerhitungan);
    }

    if (metodePengairan) {
        metodePengairan.addEventListener('change', function () {
            if (jenisZiswaf) {
                jenisZiswaf.value = this.value;
                jenisZiswaf.dispatchEvent(new Event('change'));
            }
        });
    }

    if (kategoriPertanianBerbiaya) {
        kategoriPertanianBerbiaya.addEventListener('change', function () {
            syncKategoriPerhitungan();
            hitungPertanianBerbiaya();
        });
    }

    if (kategoriPertanianAlami) {
        kategoriPertanianAlami.addEventListener('change', function () {
            syncKategoriPerhitungan();
            hitungPertanianAlami();
        });
    }

    if (hartaMaal) {
        hartaMaal.addEventListener('input', hitungMaal);
    }

    if (beratEmasGram) {
        beratEmasGram.addEventListener('input', hitungMaal);
    }

    if (tanggalMulaiKepemilikan) {
        tanggalMulaiKepemilikan.addEventListener('change', hitungMaal);
    }

    if (pakaiHasilMaal) {
        pakaiHasilMaal.addEventListener('click', function () {
            if (nilaiMaal > 0) {
                // Isi nominal_display dengan format ribuan
                if (nominalDisplay) {
                    nominalDisplay.value = nilaiMaal.toLocaleString('id-ID');
                }
                // Isi nominal (hidden) dengan nilai numerik
                if (nominal) {
                    nominal.value = nilaiMaal;
                }
            }
        });
    }

    if (penghasilanBersihInput) {
        penghasilanBersihInput.addEventListener('input', function () {
            if (penghasilanBersihValue) {
                penghasilanBersihValue.value = getPenghasilanBersih();
            }
            hitungPenghasilan();
        });
    }

    if (periodePenghasilan) {
        periodePenghasilan.addEventListener('change', togglePeriodePenghasilan);
    }

    if (bulanPenghasilan) {
        bulanPenghasilan.addEventListener('change', function () {
            if (tahunPenghasilan && this.value) {
                tahunPenghasilan.value = this.value.slice(0, 4);
            }
            hitungPenghasilan();
        });
    }

    if (tahunPenghasilan) {
        tahunPenghasilan.addEventListener('input', hitungPenghasilan);
    }

    togglePeriodePenghasilan();

    if (pakaiHasilPenghasilan) {
        pakaiHasilPenghasilan.addEventListener('click', function () {
            if (nilaiPenghasilan > 0) {
                // Isi nominal_display dengan format ribuan
                if (nominalDisplay) {
                    nominalDisplay.value = nilaiPenghasilan.toLocaleString('id-ID');
                }
                // Isi nominal (hidden) dengan nilai numerik
                if (nominal) {
                    nominal.value = nilaiPenghasilan;
                }
            }
        });
    }

    if (jumlahJiwaFitrah) {
        jumlahJiwaFitrah.addEventListener('input', hitungFitrah);
    }

    if (pakaiHasilFitrah) {
        pakaiHasilFitrah.addEventListener('click', function () {
            if (nilaiFitrah > 0) {
                if (nominalDisplay) {
                    nominalDisplay.value = nilaiFitrah.toLocaleString('id-ID');
                }
                if (nominal) {
                    nominal.value = nilaiFitrah;
                }
            }
        });
    }

    if (panenPertanianBerbiaya) {
        panenPertanianBerbiaya.addEventListener('input', hitungPertanianBerbiaya);
    }

    if (panenPertanianAlami) {
        panenPertanianAlami.addEventListener('input', hitungPertanianAlami);
    }

    if (pakaiHasilPertanianBerbiaya) {
        pakaiHasilPertanianBerbiaya.addEventListener('click', function () {
            if (nilaiPertanianBerbiaya > 0 && nominalDisplay && nominal) {
                nominalDisplay.value = nilaiPertanianBerbiaya.toLocaleString('id-ID');
                nominal.value = nilaiPertanianBerbiaya;
            }
        });
    }

    if (pakaiHasilPertanianAlami) {
        pakaiHasilPertanianAlami.addEventListener('click', function () {
            if (nilaiPertanianAlami > 0 && nominalDisplay && nominal) {
                nominalDisplay.value = nilaiPertanianAlami.toLocaleString('id-ID');
                nominal.value = nilaiPertanianAlami;
            }
        });
    }

    if (metodePembayaran) {
        metodePembayaran.addEventListener('change', toggleMetodePembayaran);
        toggleMetodePembayaran();
    }

    // ============================================================
    //  Pastikan nominal hidden ter-update sebelum form submit
    // ============================================================
    const form = document.querySelector('form[action*="transaksi"]');
    if (form) {
        form.addEventListener('submit', function () {
            if (nominalDisplay && nominal) {
                nominal.value = unformatRibuan(nominalDisplay.value) || '';
            }
            if (penghasilanBersihValue) {
                penghasilanBersihValue.value = getPenghasilanBersih();
            }
        });
    }
});
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: jamaah/transaksi-ziswaf.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="jamaah/transaksi-ziswaf.blade.php">
html[data-finus-theme="dark"] body .finus-card { border-color:#293D31 !important; background:#111A15 !important; color:#F1F6F3 !important; box-shadow:0 16px 38px rgba(0,0,0,.22) !important; }
html[data-finus-theme="dark"] body .finus-card .card-header { border-color:#293D31 !important; background:linear-gradient(180deg,#17251D,#121D17) !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-card :where(.text-dark,.option-title) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-card :where(.text-muted,.small-muted) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .finus-card :where(.form-control,.currency-input) { border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-card .option-card { border-color:#293D31 !important; background:#101B14 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-card .option-card:hover { border-color:#3D5D48 !important; background:#16271D !important; }
html[data-finus-theme="dark"] body .finus-card .option-card.active { border-color:#64DD81 !important; background:#173620 !important; box-shadow:0 0 0 3px rgba(100,221,129,.10) !important; }
html[data-finus-theme="dark"] body .finus-card .option-card .option-icon { background:#17261D !important; }
html[data-finus-theme="dark"] body .finus-card :where(.info-box,.formula-box,.custom-dropzone) { border-color:#293D31 !important; background:#101B14 !important; color:#C8D6CC !important; }
html[data-finus-theme="dark"] body .policy-item { border-color:#293D31 !important; background:#111A15 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .finus-card .custom-dropzone:hover { border-color:#64DD81 !important; background:#14261A !important; }
html[data-finus-theme="dark"] body .finus-card .file-name-badge { border-color:#31503C !important; background:#173620 !important; color:#BFF4CA !important; }
html[data-finus-theme="dark"] body .finus-card .btn-light { border-color:#334B3B !important; background:#14211A !important; color:#D0DDD4 !important; }
</style>
@endpush

@extends('layouts.app')

@php
    $isPegawai = request()->routeIs('pegawai.laporan-keuangan.*');
    $filterRoute = $isPegawai ? 'pegawai.laporan-keuangan.arus-kas' : 'admin.laporan.arus-kas';

    $pemasukan = (float) ($totalPemasukan ?? 0);
    $pengeluaran = (float) ($totalPengeluaran ?? 0);
    $saldoBersih = (float) ($saldo ?? ($pemasukan - $pengeluaran));

    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');

    // Keterangan teks periode
    if ($periode === 'bulanan') {
        $teksPeriode = ($daftarBulan[$bulan] ?? '') . ' ' . $tahun;
    } elseif ($periode === 'tahunan') {
        $teksPeriode = 'Tahun ' . $tahun;
    } else {
        $teksPeriode = 'Semua Periode';
    }

    // Pastikan Parkir selalu tampil sebagai kelompok penerimaan sendiri
    // dan ditempatkan tepat sebelum Lainnya. Jika backend sudah mengirim
    // Parkir/Hasil Parkir, nilai serta rincian transaksinya tetap dipakai.
    $detailPemasukanTampil = collect($detailPemasukan ?? []);
    $isParkir = static fn ($label) => in_array(
        mb_strtolower(trim((string) $label)),
        ['parkir', 'hasil parkir'],
        true
    );

    $parkirExisting = $detailPemasukanTampil->first(
        fn ($row) => $isParkir(data_get($row, 'label'))
    );

    $parkirRow = (object) [
        'label' => 'Parkir',
        'transaksi' => (int) data_get($parkirExisting, 'transaksi', 0),
        'nominal' => (float) data_get($parkirExisting, 'nominal', 0),
        'items' => collect(data_get($parkirExisting, 'items', [])),
    ];

    $detailPemasukanTanpaParkir = $detailPemasukanTampil
        ->reject(fn ($row) => $isParkir(data_get($row, 'label')))
        ->values();

    $detailPemasukanTampil = collect();
    $parkirInserted = false;

    foreach ($detailPemasukanTanpaParkir as $row) {
        if (! $parkirInserted && strcasecmp(trim((string) data_get($row, 'label')), 'Lainnya') === 0) {
            $detailPemasukanTampil->push($parkirRow);
            $parkirInserted = true;
        }

        $detailPemasukanTampil->push($row);
    }

    if (! $parkirInserted) {
        $detailPemasukanTampil->push($parkirRow);
    }
@endphp

@section('title', 'Laporan Keuangan — ' . $teksPeriode)
@section('hide-page-header', '1')

@push('styles')
<style>
    /* ============================================================
       STYLES LAPORAN KEUANGAN
    ============================================================ */
    .lk-page {
        padding: 6px 0 60px;
    }

    /* HERO */
    .lk-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 22px;
        padding: 24px 28px;
        border-radius: 20px;
        background: linear-gradient(135deg, #0a3d1e 0%, #0e5423 45%, #179b40 100%);
        box-shadow: 0 16px 36px rgba(14,84,35,.16);
        color: #fff;
    }

    .lk-hero h1 {
        font-size: 22px;
        font-weight: 800;
        margin: 0 0 5px;
        letter-spacing: -.3px;
        color: #fff;
    }

    .lk-hero p {
        font-size: 13.5px;
        margin: 0;
        color: rgba(255,255,255,.82);
    }

    .lk-hero-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .lk-btn-print {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 12px;
        border: none;
        background: #fff;
        color: #0e5423;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: all .2s;
        box-shadow: 0 4px 14px rgba(0,0,0,.1);
    }

    .lk-btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,.15);
        background: #f0fdf4;
    }

    .lk-btn-audit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,.3);
        background: rgba(255,255,255,.16);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        backdrop-filter: blur(4px);
    }

    .lk-btn-audit:hover, .lk-btn-audit.active {
        background: #fff;
        color: #0e5423;
        border-color: #fff;
    }

    /* FILTER BAR */
    .lk-filter-card {
        background: #fff;
        border: 1px solid #e4edf2;
        border-radius: 18px;
        padding: 18px 22px;
        margin-bottom: 22px;
        box-shadow: 0 3px 14px rgba(15,23,42,.04);
    }

    .lk-filter-form {
        display: flex;
        align-items: flex-end;
        gap: 14px;
        flex-wrap: wrap;
    }

    .lk-fg {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 140px;
    }

    .lk-fl {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
    }

    .lk-fs {
        height: 42px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        transition: border-color .15s;
    }

    .lk-fs:focus {
        border-color: #179b40;
        background: #fff;
    }

    .lk-btn-submit {
        height: 42px;
        padding: 0 20px;
        background: #179b40;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: opacity .15s;
    }

    .lk-btn-submit:hover { opacity: .9; }

    /* SUMMARY STAT CARDS */
    .lk-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 768px) {
        .lk-stat-grid { grid-template-columns: 1fr; }
    }

    .lk-stat {
        background: #fff;
        border: 1px solid #e4edf2;
        border-radius: 18px;
        padding: 20px 22px;
        box-shadow: 0 3px 14px rgba(15,23,42,.04);
        position: relative;
        overflow: hidden;
    }

    .lk-stat::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
    }

    .lk-stat-in::before { background: #16a34a; }
    .lk-stat-out::before { background: #dc2626; }
    .lk-stat-bal::before { background: #2563eb; }

    .lk-stat-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .lk-stat-value {
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -.5px;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .lk-stat-in .lk-stat-value { color: #15803d; }
    .lk-stat-out .lk-stat-value { color: #b91c1c; }
    .lk-stat-bal .lk-stat-value { color: #1d4ed8; }

    .lk-stat-sub {
        font-size: 11.5px;
        color: #94a3b8;
    }

    /* SECTION CARD & TABLES */
    .lk-card {
        background: #fff;
        border: 1px solid #e4edf2;
        border-radius: 18px;
        padding: 22px 24px;
        margin-bottom: 24px;
        box-shadow: 0 3px 14px rgba(15,23,42,.04);
    }

    .lk-card-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 14px;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .lk-card-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .lk-table-wrap {
        overflow-x: auto;
    }

    .lk-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .lk-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .05em;
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    .lk-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .lk-table tr.lk-row-header:hover td {
        background: #fafcfb;
    }

    .lk-table tfoot td {
        background: #f8fafc;
        font-weight: 800;
        border-top: 2px solid #cbd5e1;
        border-bottom: none;
        color: #0f172a;
    }

    .lk-badge-kode {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        font-weight: 800;
        font-size: 11px;
        font-family: monospace;
    }

    /* SUB-TABLE RINCIAN TRANSAKSI (AUDIT DETAIL) */
    .lk-subtable-row {
        display: none;
    }

    .lk-subtable-row.open {
        display: table-row;
    }

    .lk-subtable-cell {
        padding: 0 14px 14px 44px !important;
        background: #fdfdfd;
        border-bottom: 2px solid #e2e8f0 !important;
    }

    .lk-subtable {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .lk-subtable th {
        background: #f1f5f9;
        color: #475569;
        font-size: 10.5px;
        padding: 8px 12px;
        border-bottom: 1px solid #cbd5e1;
    }

    .lk-subtable td {
        padding: 8px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .lk-btn-expand {
        background: none;
        border: none;
        color: #179b40;
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: background .15s;
    }

    .lk-btn-expand:hover {
        background: #dcfce7;
    }

    /* KOP SURAT (Hanya tampil saat Cetak / Print) */
    .lk-print-header, .lk-print-signature {
        display: none;
    }

    /* ============================================================
       PRINT MEDIA QUERY (CETAK PDF / PRINTER)
    ============================================================ */
    @media print {
        @page {
            size: A4 portrait;
            margin: 0mm !important; /* Otomatis menghilangkan header & footer bawaan browser (tanggal, judul web, URL) */
        }

        /* Sembunyikan SEMUA elemen navigasi web, topbar user profile, dan hero banner */
        .sidebar,
        .sidebar *,
        .header,
        .header *,
        .finus-topbar,
        .finus-topbar *,
        .header.finus-topbar,
        .finus-sidebar-backdrop,
        .finus-skip-link,
        .finus-mobile-bottom-nav,
        .finus-page-heading,
        .lk-hero,
        .lk-hero *,
        .lk-hero-actions,
        .lk-filter-card,
        .pm-alert-ok,
        .pm-alert-err,
        .lk-btn-expand,
        nav {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            opacity: 0 !important;
            position: absolute !important;
            top: -9999px !important;
            left: -9999px !important;
        }

        /* Hilangkan elemen dekoratif background yang menyebabkan overflow */
        .fr-page::before,
        .fr-page::after,
        .lk-page::before,
        .lk-page::after,
        .main::before,
        .main::after,
        .content-wrap::before,
        .content-wrap::after,
        .finus-page-heading::after {
            display: none !important;
            content: none !important;
        }

        html,
        body,
        body.finus-layout {
            background: #fff !important;
            color: #000 !important;
            font-size: 8.5pt !important;
            margin: 0 !important;
            padding: 10mm 15mm 15mm 15mm !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            overflow: visible !important;
        }

        body.finus-layout .content-wrap,
        .content-wrap {
            margin: 0 !important;
            margin-left: 0 !important;
            padding: 0 !important;
            padding-top: 0 !important;
            min-height: auto !important;
            width: 100% !important;
            max-width: 100% !important;
            left: 0 !important;
            position: static !important;
        }

        body.finus-layout .content-wrap .main,
        .content-wrap .main,
        .container-fluid,
        .finus-content-container,
        .fr-page,
        .lk-page {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow: visible !important;
            position: static !important;
        }

        /* Kop Surat Resmi Masjid Pusdai Jabar */
        .lk-print-header {
            display: block !important;
            text-align: center !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 0 14px 0 !important;
            padding: 0 !important;
        }

        .lk-kop-instansi {
            margin: 0 0 2px 0 !important;
            font-size: 12.5pt !important;
            font-weight: 900 !important;
            letter-spacing: 0 !important;
            color: #000 !important;
            text-transform: uppercase !important;
            line-height: 1.25 !important;
            text-align: center !important;
            width: 100% !important;
        }

        .lk-kop-subinstansi {
            margin: 0 0 3px 0 !important;
            font-size: 11pt !important;
            font-weight: 800 !important;
            letter-spacing: .02em !important;
            color: #000 !important;
            text-transform: uppercase !important;
            text-align: center !important;
            width: 100% !important;
        }

        .lk-kop-alamat {
            margin: 0 0 8px 0 !important;
            font-size: 7.8pt !important;
            color: #333 !important;
            line-height: 1.3 !important;
            text-align: center !important;
            width: 100% !important;
        }

        /* Garis Ganda Standar Kop Surat Resmi (Solid & Unbreakable) */
        .lk-kop-divider {
            width: 100% !important;
            margin: 6px 0 12px 0 !important;
            display: block !important;
            clear: both !important;
            padding: 0 !important;
        }

        .lk-kop-line-thick {
            width: 100% !important;
            height: 0 !important;
            border-top: 2px solid #000 !important;
            background: transparent !important;
            margin-bottom: 2px !important;
            display: block !important;
        }

        .lk-kop-line-thin {
            width: 100% !important;
            height: 0 !important;
            border-top: 1px solid #000 !important;
            background: transparent !important;
            display: block !important;
        }

        .lk-kop-judul-doc {
            margin: 0 !important;
            font-size: 11pt !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: .04em !important;
            color: #000 !important;
            text-align: center !important;
            width: 100% !important;
        }

        .lk-kop-periode {
            font-weight: 700 !important;
            font-size: 8.5pt !important;
            margin-top: 2px !important;
            color: #15803d !important;
            text-align: center !important;
            width: 100% !important;
        }

        /* Stat cards saat cetak */
        .lk-stat-grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important;
            margin-bottom: 14px !important;
        }

        .lk-stat {
            border: 1px solid #bbb !important;
            box-shadow: none !important;
            padding: 8px 10px !important;
        }

        .lk-stat-value {
            font-size: 12pt !important;
        }

        /* Card & Table */
        .lk-card {
            border: 1px solid #ccc !important;
            box-shadow: none !important;
            padding: 8px 10px !important;
            margin-bottom: 14px !important;
            page-break-inside: auto;
        }

        .lk-table th {
            background: #eee !important;
            color: #000 !important;
            border-bottom: 1.5px solid #000 !important;
            font-size: 8pt !important;
            padding: 5px 6px !important;
        }

        .lk-table td {
            border-bottom: 1px solid #ddd !important;
            color: #000 !important;
            font-size: 8pt !important;
            padding: 4px 6px !important;
        }

        .lk-table tfoot td {
            border-top: 2px solid #000 !important;
            background: #f5f5f5 !important;
            font-size: 8.5pt !important;
            padding: 5px 6px !important;
        }

        /* Mode cetak audit subtable */
        .lk-subtable-row.print-show {
            display: table-row !important;
        }

        .lk-subtable-cell {
            padding: 3px 6px 6px 18px !important;
        }

        .lk-subtable th {
            font-size: 7.5pt !important;
            padding: 3px 5px !important;
        }

        .lk-subtable td {
            font-size: 7.5pt !important;
            padding: 3px 5px !important;
        }

        /* HALAMAN 2 CETAK: REKAPITULASI + TANDA TANGAN */
        .lk-print-page2 {
            page-break-before: always !important;
            break-before: page !important;
            display: block !important;
            margin-top: 0 !important;
            padding-top: 8mm !important;
        }

        .lk-card-rekap {
            margin-bottom: 30px !important;
        }

        /* Tanda Tangan */
        .lk-print-signature {
            display: block !important;
            margin-top: 25px !important;
            page-break-inside: avoid !important;
        }

        .lk-sig-row {
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        .lk-sig-col {
            width: 42%;
        }

        .lk-sig-space {
            height: 65px;
        }

        .lk-sig-name {
            font-weight: 800;
            text-decoration: underline;
        }
    }
</style>
@endpush

@section('content')
@include('layouts.partials.finus-ui')

<div class="fr-page lk-page">

    {{-- ====== KOP RESMI CETAK (Muncul saat Print) ====== --}}
    <div class="lk-print-header">
        <div class="lk-kop-instansi">DEWAN KEMAKMURAN MASJID PUSAT DAKWAH ISLAM</div>
        <div class="lk-kop-subinstansi">PUSDAI JAWA BARAT</div>
        <div class="lk-kop-alamat">Jl. Diponegoro No.63, Cihaur Geulis, Kec. Cibeunying Kaler, Kota Bandung, Jawa Barat 40122</div>
        <div class="lk-kop-divider">
            <div class="lk-kop-line-thick"></div>
            <div class="lk-kop-line-thin"></div>
        </div>
        <div class="lk-kop-judul-doc">LAPORAN KEUANGAN KAS MASJID</div>
        <div class="lk-kop-periode">Periode: {{ $teksPeriode }}</div>
    </div>

    {{-- ====== HERO WEB ====== --}}
    <div class="lk-hero">
        <div>
            <h1><i class="fa-solid fa-file-invoice-dollar" style="margin-right:10px;opacity:.9;"></i>Laporan Keuangan</h1>
            <p>Rekapitulasi penerimaan masjid dan rincian pengeluaran operasional Pusdai Jabar.</p>
        </div>
        <div class="lk-hero-actions">
            {{-- Tombol Toggle Mode Audit --}}
            <button type="button" class="lk-btn-audit" id="btnAuditMode" onclick="toggleAllAuditRows()">
                <i class="fa-solid fa-list-check"></i>
                <span id="auditBtnText">Mode Audit (Buka Semua Transaksi)</span>
            </button>

            {{-- Tombol Cetak Laporan --}}
            <button type="button" class="lk-btn-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>

    {{-- ====== FILTER PERIODE (BULANAN / TAHUNAN / SEMUA) ====== --}}
    <div class="lk-filter-card">
        <form method="GET" action="{{ route($filterRoute) }}" class="lk-filter-form" id="lkFilterForm">
            <div class="lk-fg">
                <label class="lk-fl">Pilih Jenis Periode</label>
                <select name="periode" id="lk_periode" class="lk-fs" onchange="toggleFilterFields(this.value)">
                    <option value="bulanan" {{ $periode === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $periode === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    <option value="semua"   {{ $periode === 'semua'   ? 'selected' : '' }}>Semua Periode</option>
                </select>
            </div>

            <div class="lk-fg" id="fg_bulan" style="{{ $periode === 'bulanan' ? '' : 'display:none;' }}">
                <label class="lk-fl">Bulan</label>
                <select name="bulan" class="lk-fs">
                    @foreach($daftarBulan as $num => $namaBulan)
                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lk-fg" id="fg_tahun" style="{{ $periode !== 'semua' ? '' : 'display:none;' }}">
                <label class="lk-fl">Tahun</label>
                <select name="tahun" class="lk-fs">
                    @foreach($daftarTahun as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lk-fg">
                <button type="submit" class="lk-btn-submit">
                    <i class="fa-solid fa-filter"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- ====== 3 SUMMARY CARDS ====== --}}
    <div class="lk-stat-grid">
        <div class="lk-stat lk-stat-in">
            <div class="lk-stat-label">
                <i class="fa-solid fa-circle-arrow-down" style="color:#16a34a;"></i> Total Penerimaan
            </div>
            <div class="lk-stat-value">{{ $rupiah($pemasukan) }}</div>
            <div class="lk-stat-sub">Periode {{ $teksPeriode }}</div>
        </div>

        <div class="lk-stat lk-stat-out">
            <div class="lk-stat-label">
                <i class="fa-solid fa-circle-arrow-up" style="color:#dc2626;"></i> Total Pengeluaran
            </div>
            <div class="lk-stat-value">{{ $rupiah($pengeluaran) }}</div>
            <div class="lk-stat-sub">Operasional & Penggajian</div>
        </div>

        <div class="lk-stat lk-stat-bal">
            <div class="lk-stat-label">
                <i class="fa-solid fa-wallet" style="color:#2563eb;"></i>
                {{ $saldoBersih >= 0 ? 'Surplus Kas' : 'Defisit Kas' }}
            </div>
            <div class="lk-stat-value">{{ $rupiah($saldoBersih) }}</div>
            <div class="lk-stat-sub">Sisa Saldo Kas Bersih</div>
        </div>
    </div>

    {{-- ====== TABEL 1: RINCIAN PENERIMAAN SESUAI KELOMPOK GOLONGAN ====== --}}
    <div class="lk-card">
        <div class="lk-card-title-row">
            <h2 class="lk-card-title">
                <i class="fa-solid fa-hand-holding-dollar" style="color:#16a34a;"></i>
                I. Rincian Penerimaan Kas
            </h2>
            <span style="font-size:12px;color:#64748b;font-weight:600;">
                Periode: <strong>{{ $teksPeriode }}</strong>
            </span>
        </div>

        <div class="lk-table-wrap">
            <table class="lk-table">
                <thead>
                    <tr>
                        <th width="45">No.</th>
                        <th>Kelompok / Golongan Penerimaan</th>
                        <th width="150" style="text-align:center;">Jumlah Transaksi</th>
                        <th width="200" style="text-align:right;">Total Nominal</th>
                        <th width="90" style="text-align:right;">Porsi (%)</th>
                        <th width="70" style="text-align:center;" class="text-nowrap">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @php $noIn = 1; @endphp
                    @forelse($detailPemasukanTampil as $idx => $row)
                        @php
                            $persenIn = $pemasukan > 0 ? ($row->nominal / $pemasukan) * 100 : 0;
                            $rowId = 'sub_in_' . $idx;
                        @endphp
                        <tr class="lk-row-header">
                            <td>{{ $noIn++ }}</td>
                            <td>
                                <strong>{{ $row->label }}</strong>
                            </td>
                            <td style="text-align:center;">
                                {{ number_format($row->transaksi) }} transaksi
                            </td>
                            <td style="text-align:right;font-weight:700;color:#15803d;">
                                {{ $rupiah($row->nominal) }}
                            </td>
                            <td style="text-align:right;color:#64748b;">
                                {{ number_format($persenIn, 1) }}%
                            </td>
                            <td style="text-align:center;">
                                @if($row->transaksi > 0)
                                    <button type="button" class="lk-btn-expand" onclick="toggleSubRow('{{ $rowId }}', this)" title="Lihat transaksi">
                                        <i class="fa-solid fa-chevron-down"></i> Rincian
                                    </button>
                                @else
                                    <span style="color:#cbd5e1;">-</span>
                                @endif
                            </td>
                        </tr>

                        {{-- SUB-TABLE RINCIAN TRANSAKSI (AUDIT DETAIL) --}}
                        @if($row->transaksi > 0)
                            <tr class="lk-subtable-row" id="{{ $rowId }}">
                                <td colspan="6" class="lk-subtable-cell">
                                    <table class="lk-subtable">
                                        <thead>
                                            <tr>
                                                <th width="90">Tanggal</th>
                                                <th>Muzakki / Donatur</th>
                                                <th width="130">Metode</th>
                                                <th>Keterangan</th>
                                                <th width="140" style="text-align:right;">Nominal</th>
                                                <th width="65" style="text-align:center;">Bukti</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($row->items as $item)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                                    <td>
                                                        <strong>{{ $item->muzakki?->name ?? 'Donatur Umum' }}</strong>
                                                    </td>
                                                    <td>
                                                        <span style="text-transform:capitalize;">{{ str_replace('_', ' ', $item->metode_pembayaran ?? 'tunai') }}</span>
                                                    </td>
                                                    <td>{{ $item->keterangan ?: '-' }}</td>
                                                    <td style="text-align:right;font-weight:700;color:#15803d;">
                                                        {{ $rupiah($item->nominal) }}
                                                    </td>
                                                    <td style="text-align:center;">
                                                        @if($item->bukti_pembayaran)
                                                            <a href="{{ Storage::url($item->bukti_pembayaran) }}" target="_blank" style="color:#2563eb;" title="Lihat bukti transaksi">
                                                                <i class="fa-solid fa-file-image"></i>
                                                            </a>
                                                        @else
                                                            <span style="color:#cbd5e1;">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:24px;color:#94a3b8;">
                                Tidak ada data penerimaan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right;">TOTAL PENERIMAAN:</td>
                        <td style="text-align:right;color:#15803d;font-size:14px;">{{ $rupiah($pemasukan) }}</td>
                        <td style="text-align:right;">100.0%</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ====== TABEL 2: RINCIAN PENGELUARAN SESUAI KELOMPOK BEBAN COA ====== --}}
    <div class="lk-card">
        <div class="lk-card-title-row">
            <h2 class="lk-card-title">
                <i class="fa-solid fa-receipt" style="color:#dc2626;"></i>
                II. Rincian Pengeluaran Kas (Kelompok Beban)
            </h2>
            <span style="font-size:12px;color:#64748b;font-weight:600;">
                Periode: <strong>{{ $teksPeriode }}</strong>
            </span>
        </div>

        <div class="lk-table-wrap">
            <table class="lk-table">
                <thead>
                    <tr>
                        <th width="45">No.</th>
                        <th width="105">Kode Akun</th>
                        <th>Kategori / Kelompok Pengeluaran</th>
                        <th width="140" style="text-align:center;">Jumlah Transaksi</th>
                        <th width="200" style="text-align:right;">Total Nominal</th>
                        <th width="90" style="text-align:right;">Porsi (%)</th>
                        <th width="70" style="text-align:center;" class="text-nowrap">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @php $noOut = 1; @endphp
                    @forelse($detailPengeluaran as $idxOut => $row)
                        @php
                            $persenOut = $pengeluaran > 0 ? ($row->nominal / $pengeluaran) * 100 : 0;
                            $rowOutId = 'sub_out_' . $idxOut;
                        @endphp
                        <tr class="lk-row-header">
                            <td>{{ $noOut++ }}</td>
                            <td><span class="lk-badge-kode">{{ $row->kode_akun }}</span></td>
                            <td>
                                <strong>{{ $row->nama_akun }}</strong>
                                @if($row->kode_akun === '5104')
                                    <span style="font-size:11px;color:#64748b;margin-left:4px;">(Penggajian Pegawai)</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                {{ number_format($row->transaksi) }} transaksi
                            </td>
                            <td style="text-align:right;font-weight:700;color:#b91c1c;">
                                {{ $rupiah($row->nominal) }}
                            </td>
                            <td style="text-align:right;color:#64748b;">
                                {{ number_format($persenOut, 1) }}%
                            </td>
                            <td style="text-align:center;">
                                @if($row->transaksi > 0)
                                    <button type="button" class="lk-btn-expand" onclick="toggleSubRow('{{ $rowOutId }}', this)" title="Lihat transaksi">
                                        <i class="fa-solid fa-chevron-down"></i> Rincian
                                    </button>
                                @else
                                    <span style="color:#cbd5e1;">-</span>
                                @endif
                            </td>
                        </tr>

                        {{-- SUB-TABLE RINCIAN TRANSAKSI PENGELUARAN (AUDIT DETAIL) --}}
                        @if($row->transaksi > 0)
                            <tr class="lk-subtable-row" id="{{ $rowOutId }}">
                                <td colspan="7" class="lk-subtable-cell">
                                    <table class="lk-subtable">
                                        <thead>
                                            <tr>
                                                <th width="90">Tanggal</th>
                                                <th>Keterangan / Penerima</th>
                                                <th width="140">Debit / Kredit</th>
                                                <th width="140" style="text-align:right;">Nominal</th>
                                                <th width="65" style="text-align:center;">Bukti</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($row->kode_akun === '5104')
                                                {{-- Rincian dari tabel Penggajian --}}
                                                @foreach($row->items as $gaji)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($gaji->tanggal)->format('d/m/Y') }}</td>
                                                        <td>
                                                            <strong>Gaji: {{ $gaji->pegawai?->nama_pegawai ?? 'Pegawai' }}</strong>
                                                            <span style="font-size:11px;color:#64748b;">({{ $gaji->pegawai?->jabatan ?? '-' }})</span>
                                                        </td>
                                                        <td>5104 (Honor) / Kas</td>
                                                        <td style="text-align:right;font-weight:700;color:#b91c1c;">
                                                            {{ $rupiah($gaji->total_gaji) }}
                                                        </td>
                                                        <td style="text-align:center;color:#64748b;">
                                                            <span title="Tercatat dari modul penggajian">Slip</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                {{-- Rincian dari tabel Pengeluaran Operasional --}}
                                                @foreach($row->items as $peng)
                                                    @php
                                                        $nomPeng = (int) ($peng->nominal ?: $peng->jumlah);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($peng->tanggal)->format('d/m/Y') }}</td>
                                                        <td>{{ $peng->keterangan ?: ($peng->deskripsi ?: $peng->kategori) }}</td>
                                                        <td>
                                                            {{ $peng->coaDebit?->nama_akun ?? 'Beban' }} / {{ $peng->coaKredit?->nama_akun ?? 'Kas' }}
                                                        </td>
                                                        <td style="text-align:right;font-weight:700;color:#b91c1c;">
                                                            {{ $rupiah($nomPeng) }}
                                                        </td>
                                                        <td style="text-align:center;">
                                                            @if($peng->bukti_pengeluaran)
                                                                <a href="{{ Storage::url($peng->bukti_pengeluaran) }}" target="_blank" style="color:#2563eb;" title="Lihat bukti nota">
                                                                    <i class="fa-solid fa-file-invoice"></i>
                                                                </a>
                                                            @else
                                                                <span style="color:#cbd5e1;">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">
                                Tidak ada data pengeluaran pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;">TOTAL PENGELUARAN:</td>
                        <td style="text-align:right;color:#b91c1c;font-size:14px;">{{ $rupiah($pengeluaran) }}</td>
                        <td style="text-align:right;">100.0%</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ====== HALAMAN 2 CETAK: REKAPITULASI + TANDA TANGAN ====== --}}
    <div class="lk-print-page2">
        {{-- ====== TABEL 3: REKAPITULASI SURPLUS / DEFISIT ====== --}}
        <div class="lk-card lk-card-rekap">
            <div class="lk-card-title-row">
                <h2 class="lk-card-title">
                    <i class="fa-solid fa-scale-balanced" style="color:#2563eb;"></i>
                    III. Rekapitulasi Saldo Kas Bersih
                </h2>
            </div>

            <div class="lk-table-wrap">
                <table class="lk-table">
                    <tbody>
                        <tr>
                            <td style="font-weight:700;">Total Penerimaan Kas (A)</td>
                            <td style="text-align:right;font-weight:800;color:#15803d;width:250px;">{{ $rupiah($pemasukan) }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:700;">Total Pengeluaran Kas (B)</td>
                            <td style="text-align:right;font-weight:800;color:#b91c1c;width:250px;">{{ $rupiah($pengeluaran) }}</td>
                        </tr>
                        <tr style="background:#f8fafc;">
                            <td style="font-weight:900;font-size:14px;text-transform:uppercase;">
                                Sisa Saldo Kas Bersih (A - B)
                                @if($saldoBersih > 0)
                                    <span style="color:#16a34a;font-size:12px;margin-left:6px;">[ SURPLUS ]</span>
                                @elseif($saldoBersih < 0)
                                    <span style="color:#dc2626;font-size:12px;margin-left:6px;">[ DEFISIT ]</span>
                                @else
                                    <span style="color:#64748b;font-size:12px;margin-left:6px;">[ SEIMBANG ]</span>
                                @endif
                            </td>
                            <td style="text-align:right;font-weight:900;font-size:16px;color:{{ $saldoBersih >= 0 ? '#15803d' : '#b91c1c' }};">
                                {{ $rupiah($saldoBersih) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ====== KOLOM TANDA TANGAN CETAK (Hanya muncul saat Print) ====== --}}
        <div class="lk-print-signature">
            <div class="lk-sig-row">
                <div class="lk-sig-col">
                    <div>Mengetahui,</div>
                    <div style="font-weight:700;">Ketua DKM Pusdai Jabar</div>
                    <div class="lk-sig-space"></div>
                    <div class="lk-sig-name">( .................................................. )</div>
                    <div style="font-size:9pt;color:#555;">NIP/ID Pengurus</div>
                </div>

                <div class="lk-sig-col">
                    <div>Bandung, {{ now()->translatedFormat('d F Y') }}</div>
                    <div style="font-weight:700;">Bendahara / Petugas Keuangan</div>
                    <div class="lk-sig-space"></div>
                    <div class="lk-sig-name">( {{ auth()->user()->name ?? 'Petugas Keuangan' }} )</div>
                    <div style="font-size:9pt;color:#555;">FINUS Pusdai Jabar</div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    /* Toggle filter form dropdowns */
    function toggleFilterFields(val) {
        const fgBulan = document.getElementById('fg_bulan');
        const fgTahun = document.getElementById('fg_tahun');

        if (val === 'bulanan') {
            fgBulan.style.display = '';
            fgTahun.style.display = '';
        } else if (val === 'tahunan') {
            fgBulan.style.display = 'none';
            fgTahun.style.display = '';
        } else {
            fgBulan.style.display = 'none';
            fgTahun.style.display = 'none';
        }
    }

    /* Toggle single sub-row */
    function toggleSubRow(rowId, btn) {
        const row = document.getElementById(rowId);
        if (!row) return;

        const isOpen = row.classList.contains('open');
        if (isOpen) {
            row.classList.remove('open', 'print-show');
            if (btn) btn.innerHTML = '<i class="fa-solid fa-chevron-down"></i> Rincian';
        } else {
            row.classList.add('open', 'print-show');
            if (btn) btn.innerHTML = '<i class="fa-solid fa-chevron-up"></i> Tutup';
        }
    }

    /* Toggle all audit rows */
    let isAuditModeActive = false;
    function toggleAllAuditRows() {
        isAuditModeActive = !isAuditModeActive;
        const subRows = document.querySelectorAll('.lk-subtable-row');
        const expandBtns = document.querySelectorAll('.lk-btn-expand');
        const auditBtn = document.getElementById('btnAuditMode');
        const auditText = document.getElementById('auditBtnText');

        subRows.forEach(row => {
            if (isAuditModeActive) {
                row.classList.add('open', 'print-show');
            } else {
                row.classList.remove('open', 'print-show');
            }
        });

        expandBtns.forEach(btn => {
            btn.innerHTML = isAuditModeActive
                ? '<i class="fa-solid fa-chevron-up"></i> Tutup'
                : '<i class="fa-solid fa-chevron-down"></i> Rincian';
        });

        if (isAuditModeActive) {
            auditBtn.classList.add('active');
            auditText.innerText = 'Mode Audit Aktif (Tutup Rincian)';
        } else {
            auditBtn.classList.remove('active');
            auditText.innerText = 'Mode Audit (Buka Semua Transaksi)';
        }
    }
</script>
@endpush

@endsection

{{-- FINUS DARK MODE LOCAL: admin/laporan/arus-kas.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="admin/laporan/arus-kas.blade.php">
@media screen {
html[data-finus-theme="dark"] body .lk-page { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .lk-page :where(.lk-filter-card,.lk-stat,.lk-card,.lk-card-rekap,.lk-table-wrap,.lk-subtable) { border-color:#293D31 !important; background:linear-gradient(155deg,#15211A,#111A15) !important; color:#F1F6F3 !important; box-shadow:0 14px 34px rgba(0,0,0,.20) !important; }
html[data-finus-theme="dark"] body .lk-page :where(.lk-card-title,.lk-stat-value,.lk-kop-judul-doc,.lk-kop-instansi,.lk-sig-name) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .lk-page :where(.lk-stat-label,.lk-stat-sub,.lk-kop-alamat,.lk-kop-subinstansi,.lk-kop-periode) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .lk-page .lk-fs { border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .lk-page .lk-fs:focus { border-color:#64DD81 !important; background:#0F1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.12) !important; }
html[data-finus-theme="dark"] body .lk-page .lk-table th { border-color:#293D31 !important; background:#17261D !important; color:#D2E1D6 !important; }
html[data-finus-theme="dark"] body .lk-page .lk-table td { border-color:#24372B !important; background:#111A15 !important; color:#DCE7E0 !important; }
html[data-finus-theme="dark"] body .lk-page .lk-table tr.lk-row-header td { background:#15251B !important; color:#D7E5DB !important; }
html[data-finus-theme="dark"] body .lk-page .lk-table tr.lk-row-header:hover td { background:#193021 !important; }
html[data-finus-theme="dark"] body .lk-page .lk-table tfoot td { border-color:#293D31 !important; background:#17261D !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .lk-page .lk-subtable th { border-color:#293D31 !important; background:#14231A !important; color:#D2E1D6 !important; }
html[data-finus-theme="dark"] body .lk-page .lk-subtable-cell { border-color:#24372B !important; background:#101A14 !important; color:#D2DFD6 !important; }
html[data-finus-theme="dark"] body .lk-page .lk-badge-kode { border-color:#31503C !important; background:#173620 !important; color:#BFF4CA !important; }
html[data-finus-theme="dark"] body .lk-page .lk-btn-print { border-color:#304A39 !important; background:#14211A !important; color:#DCE9E0 !important; }
html[data-finus-theme="dark"] body .lk-page .lk-btn-audit { border-color:#35557A !important; background:#182B43 !important; color:#B9D0FF !important; }
html[data-finus-theme="dark"] body .lk-page :where(.lk-kop-divider,.lk-kop-line-thick,.lk-kop-line-thin) { border-color:#395144 !important; background:#395144 !important; }
}
@media print {
 html[data-finus-theme="dark"] body .lk-page, html[data-finus-theme="dark"] body .lk-page * { color:#111827 !important; }
 html[data-finus-theme="dark"] body .lk-page :where(.lk-card,.lk-card-rekap,.lk-table-wrap,.lk-subtable) { background:#fff !important; border-color:#d1d5db !important; box-shadow:none !important; }
 html[data-finus-theme="dark"] body .lk-page .lk-table th, html[data-finus-theme="dark"] body .lk-page .lk-subtable th { background:#f3f8f4 !important; color:#1f2937 !important; }
 html[data-finus-theme="dark"] body .lk-page .lk-table td, html[data-finus-theme="dark"] body .lk-page .lk-subtable-cell { background:#fff !important; color:#1f2937 !important; border-color:#d1d5db !important; }
}
</style>
@endpush


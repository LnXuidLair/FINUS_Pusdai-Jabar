@extends('layouts.app')
@section('title', 'Dashboard Jamaah')
@section('content')
@include('layouts.partials.finus-ui')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
@endphp

<style>
    .jamaah-hero {
        border-radius: 24px;
        background: linear-gradient(135deg, #065f46, #10b981);
        color: white;
        overflow: hidden;
        position: relative;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(6, 95, 70, 0.15);
    }

    .jamaah-hero::after {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        right: -90px;
        top: -100px;
        background: rgba(255, 255, 255, .12);
        border-radius: 999px;
    }

    .finus-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .finus-stat-card {
        position: relative;
        min-height: 178px;
        padding: 24px;
        border: 0;
        border-radius: 20px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .finus-stat-card::after {
        content: "";
        position: absolute;
        right: -54px;
        bottom: -66px;
        width: 150px;
        height: 150px;
        border-radius: 999px;
        opacity: .55;
    }

    .finus-stat-red::after {
        background: #ffe4e6;
    }

    .finus-stat-green::after {
        background: #dcfce7;
    }

    .finus-stat-blue::after {
        background: #dbeafe;
    }

    .finus-stat-yellow::after {
        background: #fef9c3;
    }

    .finus-stat-top {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 22px;
    }

    .finus-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }

    .finus-stat-icon-red {
        background: #ffe4e6;
        color: #dc2626;
    }

    .finus-stat-icon-green {
        background: #dcfce7;
        color: #16a34a;
    }

    .finus-stat-icon-blue {
        background: #dbeafe;
        color: #2563eb;
    }

    .finus-stat-icon-yellow {
        background: #fef9c3;
        color: #ca8a04;
    }

    .finus-stat-badge {
        border-radius: 12px;
        padding: 8px 12px;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .finus-stat-badge::before {
        content: "";
        display: inline-block;
        width: 7px;
        height: 7px;
        margin-right: 7px;
        border-radius: 999px;
        vertical-align: middle;
    }

    .finus-stat-badge-red::before {
        background: #ef4444;
    }

    .finus-stat-badge-green::before {
        background: #16a34a;
    }

    .finus-stat-badge-blue::before {
        background: #2563eb;
    }

    .finus-stat-badge-yellow::before {
        background: #ca8a04;
    }

    .finus-stat-content {
        position: relative;
        z-index: 2;
    }

    .finus-stat-label {
        margin-bottom: 10px;
        color: #475569;
        font-size: 14px;
        font-weight: 600;
    }

    .finus-stat-value {
        margin-bottom: 6px;
        color: #0f172a;
        font-size: 25px;
        font-weight: 800;
    }

    .finus-stat-desc {
        margin-bottom: 0;
        color: #94a3b8;
        font-size: 13px;
    }

    .badge-soft-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-soft-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-soft-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .table thead th {
        border-top: 0;
        color: #475569;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .agenda-card {
        border: 0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .agenda-item {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 14px;
    }

    .agenda-badge {
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 12px;
        white-space: nowrap;
    }

    .agenda-note {
        border-radius: 16px;
        background: #ecfdf5;
        color: #047857;
        padding: 14px 16px;
        font-size: 14px;
    }

    /* Live Digital Clock styling */
    .live-clock-wrapper {
        font-size: 13px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.95);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(0, 0, 0, 0.18);
        padding: 6px 16px;
        border-radius: 30px;
        margin-bottom: 14px;
        backdrop-filter: blur(4px);
    }

    /* Quick Actions */
    .quick-actions-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .btn-quick-action {
        border: 0;
        border-radius: 16px;
        padding: 16px 10px;
        text-align: center;
        background: rgba(255, 255, 255, 0.12);
        color: white !important;
        font-weight: 700;
        font-size: 13px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(8px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .btn-quick-action:hover {
        background: #ffffff;
        color: #065f46 !important;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .btn-quick-action i {
        font-size: 20px;
    }

    /* Prayer Times Widget */
    .prayer-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        overflow: hidden;
    }

    .prayer-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        margin-top: 16px;
    }

    .prayer-time-item {
        background: #f8fafc;
        border: 1.5px solid #f1f5f9;
        border-radius: 14px;
        padding: 12px 8px;
        text-align: center;
        transition: all 0.25s ease;
    }

    .prayer-time-item.next-prayer {
        border-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.22);
        position: relative;
        transform: translateY(-4px) scale(1.05);
        z-index: 10;
    }

    .prayer-time-item.next-prayer::before {
        content: "SEKARANG/NANTI";
        position: absolute;
        top: -8px;
        left: 50%;
        transform: translateX(-50%);
        background: #10b981;
        color: white;
        font-size: 8px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 30px;
        letter-spacing: 0.05em;
    }

    .prayer-name {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .prayer-time-item.next-prayer .prayer-name {
        color: #047857;
    }

    .prayer-val {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .prayer-time-item.next-prayer .prayer-val {
        color: #065f46;
    }

    /* Enhanced Next Prayer Badge */
    .next-prayer-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #059669, #10b981);
        color: #ffffff !important;
        font-weight: 800;
        font-size: 14.5px;
        padding: 10px 20px;
        border-radius: 50px;
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.25);
        border: 2px solid rgba(255, 255, 255, 0.15);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        animation: pulse-badge 2.5s infinite ease-in-out;
    }

    .next-prayer-badge:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 12px 25px rgba(16, 185, 129, 0.4);
    }

    .next-prayer-badge::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.25),
            transparent
        );
        transition: 0.5s;
        animation: shimmers 3s infinite;
    }

    @keyframes shimmers {
        0% {
            left: -100%;
        }
        100% {
            left: 100%;
        }
    }

    @keyframes pulse-badge {
        0% {
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
        50% {
            box-shadow: 0 4px 22px rgba(16, 185, 129, 0.55);
        }
        100% {
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
    }

    /* Pulsing status indicator dot */
    .pulse-dot {
        width: 10px;
        height: 10px;
        background-color: #ffffff;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
        animation: pulse-dot-key 1.5s infinite cubic-bezier(0.66, 0, 0, 1);
    }

    @keyframes pulse-dot-key {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(255, 255, 255, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
        }
    }

    @media (max-width: 767px) {
        .prayer-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
    }

    /* Agenda & Activities */
    .agenda-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 16px;
        margin-top: 10px;
    }
    .agenda-grid.owl-carousel {
        display: block !important;
        position: relative;
        padding: 10px 42px !important; /* Memberi ruang di kiri-kanan agar tombol panah aman di dalam */
        margin-top: 10px;
    }
    .agenda-grid.owl-carousel .owl-item {
        padding: 5px 0;
    }
    .agenda-grid.owl-carousel .owl-nav {
        position: absolute;
        top: 40% !important;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        pointer-events: none;
        z-index: 10;
        height: 0 !important; /* Mencegah tinggi navigasi mengganggu layout */
    }
    .agenda-grid.owl-carousel .owl-nav button.owl-prev,
    .agenda-grid.owl-carousel .owl-nav button.owl-next {
        width: 44px;
        height: 44px;
        border-radius: 50% !important;
        background: #ffffff !important;
        color: #0e5423 !important;
        border: 1.5px solid #e2e8f0 !important;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.12) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        pointer-events: auto;
        position: static !important; /* Gunakan static agar sejajar otomatis lewat flexbox */
        margin: 0 !important;
        padding: 0 !important;
    }
    .agenda-grid.owl-carousel .owl-nav button.owl-prev:hover,
    .agenda-grid.owl-carousel .owl-nav button.owl-next:hover {
        background: #0e5423 !important;
        color: #ffffff !important;
        border-color: #0e5423 !important;
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(14, 84, 35, 0.25) !important;
    }
    .agenda-grid.owl-carousel .owl-nav button.disabled {
        opacity: 0 !important;
        pointer-events: none !important;
    }
    .agenda-grid.owl-carousel .owl-dots {
        text-align: center;
        margin-top: 15px;
        display: flex;
        justify-content: center;
        gap: 6px;
    }
    .agenda-grid.owl-carousel .owl-dots .owl-dot span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1 !important;
        display: block;
        transition: all 0.2s ease;
    }
    .agenda-grid.owl-carousel .owl-dots .owl-dot.active span {
        background: #10b981 !important;
        width: 20px;
        border-radius: 4px;
    }
    @media (max-width: 991px) {
        .agenda-grid.owl-carousel .owl-nav {
            display: none !important;
        }
    }
    .agenda-card-item {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .agenda-card-item:hover {
        transform: translateY(-3px);
        border-color: #10b981;
        background: #ffffff;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.06);
    }
    .agenda-badge-new {
        background: #d1fae5;
        color: #065f46;
        font-size: 10.5px;
        font-weight: 800;
        padding: 4px 11px;
        border-radius: 30px;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        border: 1.5px solid #6ee7b7;
        box-shadow: 0 1px 4px rgba(16,185,129,.15);
    }

    /* Expenses Summary */
    .expense-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 16px;
    }
    .expense-card-item {
        background: #f8fafc;
        border: 1.5px solid #f1f5f9;
        border-radius: 16px;
        padding: 16px;
        transition: all 0.2s ease;
    }
    .expense-card-item:hover {
        border-color: #fca5a5;
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.05);
    }
    .expense-progress-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 10px;
    }
    .expense-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #ef4444, #f87171);
        border-radius: 999px;
        transition: width 0.4s ease;
    }

    /* ZISWAF Summary Cards */
    .ziswaf-sum-card {
        border-radius: 20px;
        padding: 24px;
        color: #ffffff !important;
        position: relative;
        overflow: hidden;
        border: 0;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        transition: all 0.25s ease;
    }
    .ziswaf-sum-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.1);
    }
    .ziswaf-sum-zakat {
        background: linear-gradient(135deg, #047857, #10b981);
    }
    .ziswaf-sum-wakaf {
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    }
    .ziswaf-sum-card::after {
        content: "";
        position: absolute;
        width: 140px;
        height: 140px;
        right: -30px;
        bottom: -30px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }
    .ziswaf-sum-card i {
        font-size: 42px;
        position: absolute;
        right: 24px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.18;
        pointer-events: none;
    }
    .ziswaf-sum-card span {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        opacity: 0.9;
        display: block;
        margin-bottom: 6px;
    }
    .ziswaf-sum-card h4 {
        font-size: 24px;
        font-weight: 800;
        margin: 0;
        color: white;
    }
</style>

<div class="jamaah-hero mb-4">
    <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-7">
            <div class="live-clock-wrapper">
                <i class="fa fa-clock"></i> <span id="live_clock_time">--:--:--</span>
            </div>
            <p class="mb-1 text-white-50">Assalamu'alaikum,</p>
            <h2 class="text-white font-weight-bold mb-2">{{ $jamaah->name }}</h2>
            <p class="mb-0 text-white-50 small">
                Selamat datang di sistem FINUS Pusdai Jabar. Di sini Anda dapat memantau riwayat donasi, 
                arus transparansi pengeluaran masjid, serta melihat agenda kegiatan terupdate.
            </p>
        </div>
        <div class="col-lg-5 mt-4 mt-lg-0">
            <div class="quick-actions-row">
                <a href="{{ route('jamaah.transaksi.create', 'zakat') }}" class="btn-quick-action">
                    <i class="fa-solid fa-hand-holding-heart"></i>
                    Bayar Zakat
                </a>
                <a href="{{ route('jamaah.transaksi.create', 'infak') }}" class="btn-quick-action">
                    <i class="fa-solid fa-circle-dollar-to-slot"></i>
                    Bayar Infak
                </a>
                <a href="{{ route('jamaah.transaksi.create', 'wakaf') }}" class="btn-quick-action">
                    <i class="fa-solid fa-mosque"></i>
                    Wakaf
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card prayer-card mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <h5 class="mb-1 font-weight-bold text-dark"><i class="fa-solid fa-location-dot text-success mr-1"></i> Jadwal Salat Hari Ini</h5>
                <small class="text-muted" id="prayer_location_text">Mendeteksi lokasi...</small>
            </div>
            <div class="text-md-right mt-2 mt-md-0">
                <div class="next-prayer-badge">
                    <span class="pulse-dot"></span>
                    <span id="next_prayer_countdown">Mendeteksi Jadwal Salat...</span>
                </div>
            </div>
        </div>
        <div class="prayer-grid" id="prayer_grid_container">
            <div class="prayer-time-item" id="prayer_Imsak">
                <div class="prayer-name">Imsak</div>
                <div class="prayer-val" id="val_Imsak">--:--</div>
            </div>
            <div class="prayer-time-item" id="prayer_Fajr">
                <div class="prayer-name">Subuh</div>
                <div class="prayer-val" id="val_Fajr">--:--</div>
            </div>
            <div class="prayer-time-item" id="prayer_Dhuhr">
                <div class="prayer-name">Dzuhur</div>
                <div class="prayer-val" id="val_Dhuhr">--:--</div>
            </div>
            <div class="prayer-time-item" id="prayer_Asr">
                <div class="prayer-name">Ashar</div>
                <div class="prayer-val" id="val_Asr">--:--</div>
            </div>
            <div class="prayer-time-item" id="prayer_Maghrib">
                <div class="prayer-name">Maghrib</div>
                <div class="prayer-val" id="val_Maghrib">--:--</div>
            </div>
            <div class="prayer-time-item" id="prayer_Isha">
                <div class="prayer-name">Isya</div>
                <div class="prayer-val" id="val_Isha">--:--</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Card 1: Total Transaksi -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="finus-stat-card finus-stat-red">
            <div class="finus-stat-top">
                <div class="finus-stat-icon finus-stat-icon-red">
                    <i class="fa fa-receipt"></i>
                </div>
                <span class="finus-stat-badge finus-stat-badge-red">Semua</span>
            </div>
            <div class="finus-stat-content">
                <p class="finus-stat-label">Transaksi Saya</p>
                <h4 class="finus-stat-value">{{ $rupiah($totalTransaksiSaya) }}</h4>
                <p class="finus-stat-desc">
                    {{ $jumlahTransaksiSaya }} transaksi yang telah Anda input.
                </p>
            </div>
        </div>
    </div>

    <!-- Card 2: Zakat Saya -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="finus-stat-card finus-stat-green">
            <div class="finus-stat-top">
                <div class="finus-stat-icon finus-stat-icon-green">
                    <i class="fa-solid fa-hand-holding-heart"></i>
                </div>
                <span class="finus-stat-badge finus-stat-badge-green">Zakat</span>
            </div>
            <div class="finus-stat-content">
                <p class="finus-stat-label">Zakat Saya</p>
                <h4 class="finus-stat-value">{{ $rupiah($totalZakatSaya) }}</h4>
                <p class="finus-stat-desc">
                    Akumulasi zakat yang telah Anda tunaikan.
                </p>
            </div>
        </div>
    </div>

    <!-- Card 3: Infak Saya -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="finus-stat-card finus-stat-blue">
            <div class="finus-stat-top">
                <div class="finus-stat-icon finus-stat-icon-blue">
                    <i class="fa-solid fa-circle-dollar-to-slot"></i>
                </div>
                <span class="finus-stat-badge finus-stat-badge-blue">Infak</span>
            </div>
            <div class="finus-stat-content">
                <p class="finus-stat-label">Infak Saya</p>
                <h4 class="finus-stat-value">{{ $rupiah($totalInfakSaya) }}</h4>
                <p class="finus-stat-desc">
                    Akumulasi infak yang telah Anda salurkan.
                </p>
            </div>
        </div>
    </div>

    <!-- Card 4: Wakaf Saya -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="finus-stat-card finus-stat-yellow">
            <div class="finus-stat-top">
                <div class="finus-stat-icon finus-stat-icon-yellow">
                    <i class="fa-solid fa-mosque"></i>
                </div>
                <span class="finus-stat-badge finus-stat-badge-yellow">Wakaf</span>
            </div>
            <div class="finus-stat-content">
                <p class="finus-stat-label">Wakaf Saya</p>
                <h4 class="finus-stat-value">{{ $rupiah($totalWakafSaya) }}</h4>
                <p class="finus-stat-desc">
                    Akumulasi wakaf yang telah Anda salurkan.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card agenda-card mb-4">
            <div class="card-body p-4">
                <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1 font-weight-bold text-dark">Agenda & Kegiatan Masjid</h5>
                        <p class="mb-0 text-muted small">Ikuti berbagai kegiatan kajian, sosial, dan ibadah di Masjid Pusdai.</p>
                    </div>
                    <span style="
                        background: linear-gradient(135deg, #059669, #10b981);
                        color: #ffffff;
                        font-size: 11.5px;
                        font-weight: 800;
                        padding: 6px 16px;
                        border-radius: 30px;
                        letter-spacing: 0.06em;
                        text-transform: uppercase;
                        box-shadow: 0 3px 10px rgba(16,185,129,.35);
                        border: 1.5px solid #34d399;
                        text-shadow: 0 1px 3px rgba(0,0,0,.15);
                    ">Terjadwal</span>
                </div>
                <div class="agenda-grid owl-carousel owl-theme">
                    @forelse(($agendaKegiatan ?? []) as $agenda)
                        <div class="agenda-card-item">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="mb-0 font-weight-bold text-dark" style="font-size: 15px;">{{ $agenda['judul'] }}</h6>
                                <span class="agenda-badge-new">{{ $agenda['kategori'] }}</span>
                            </div>
                            <p class="mb-1 text-muted small"><i class="fa fa-calendar mr-2 text-success"></i> {{ $agenda['hari'] }}</p>
                            <p class="mb-1 text-muted small"><i class="fa fa-clock mr-2 text-success"></i> {{ $agenda['waktu'] }}</p>
                            <p class="mb-2 text-muted small"><i class="fa fa-map-marker-alt mr-2 text-success"></i> {{ $agenda['lokasi'] }}</p>
                            <hr class="my-2" style="border-top: 1px solid #f1f5f9;">
                            <p class="mb-0 text-secondary small" style="line-height: 1.4;">{{ $agenda['deskripsi'] }}</p>
                        </div>
                    @empty
                        <div class="py-5 text-center w-100">
                            <i class="fa-regular fa-calendar mb-3 text-muted" style="font-size: 34px;"></i>
                            <p class="mb-0 text-muted">Belum ada agenda kegiatan saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(() => {

    // Deteksi Zona Waktu Lokal
    const getTimezoneName = () => {
        const offset = new Date().getTimezoneOffset();
        if (offset === -420) return 'WIB';
        if (offset === -480) return 'WITA';
        if (offset === -540) return 'WIT';
        return ''; // Untuk zona waktu di luar Indonesia
    };

    const timezoneStr = getTimezoneName();

    // Live Clock
    const clockSpan = document.getElementById('live_clock_time');
    if (clockSpan) {
        setInterval(() => {
            const now = new Date();
            clockSpan.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + (timezoneStr ? ` ${timezoneStr}` : '');
        }, 1000);
    }

    // Geolocation & Prayer Times
    const locationText = document.getElementById('prayer_location_text');
    const defaultLat = -6.9025; // Bandung (Pusdai)
    const defaultLng = 107.6256; // Bandung (Pusdai)

    function getPrayerTimes(lat, lng, isCustomLoc = false) {
        if (locationText) {
            locationText.textContent = isCustomLoc ? 'Mendeteksi nama lokasi...' : 'Lokasi: Bandung (Pusdai)';
        }

        const date = new Date().toISOString().split('T')[0];
        fetch(`https://api.aladhan.com/v1/timings/${date}?latitude=${lat}&longitude=${lng}&method=20`)
            .then(res => res.json())
            .then(data => {
                const timings = data.data.timings;
                const prayers = ['Imsak', 'Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
                
                prayers.forEach(p => {
                    const cellVal = document.getElementById(`val_${p}`);
                    if (cellVal) {
                        cellVal.textContent = timings[p] || '--:--';
                    }
                });


                // Find next prayer
                updateNextPrayer(timings);
            })
            .catch(err => {
                console.error(err);
                if (locationText) locationText.textContent = 'Gagal memuat jadwal salat';
            });

        if (isCustomLoc && locationText) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`, {
                headers: {
                    'Accept-Language': 'id-ID,id;q=0.9,en;q=0.8'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.address) {
                    const addr = data.address;
                    const district = addr.village || addr.suburb || addr.neighbourhood || addr.hamlet || addr.city_district || '';
                    const city = addr.city || addr.town || addr.municipality || addr.county || '';
                    
                    let locationName = '';
                    if (district && city) {
                        locationName = `${district}, ${city}`;
                    } else if (district) {
                        locationName = district;
                    } else if (city) {
                        locationName = city;
                    } else {
                        locationName = data.name || (data.display_name ? data.display_name.split(',')[0] : `${lat.toFixed(4)}, ${lng.toFixed(4)}`);
                    }
                    locationText.textContent = `Lokasi Anda: ${locationName}`;
                } else {
                    locationText.textContent = `Lokasi Anda: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                }
            })
            .catch(err => {
                console.error("Geocoding error:", err);
                locationText.textContent = `Lokasi Anda: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            });
        }
    }

    function updateNextPrayer(timings) {
        const now = new Date();
        const currentMinutes = now.getHours() * 60 + now.getMinutes();

        const prayerMinutes = {
            'Imsak': parseMinutes(timings['Imsak']),
            'Subuh': parseMinutes(timings['Fajr']),
            'Dzuhur': parseMinutes(timings['Dhuhr']),
            'Ashar': parseMinutes(timings['Asr']),
            'Maghrib': parseMinutes(timings['Maghrib']),
            'Isya': parseMinutes(timings['Isha'])
        };

        const prayerIds = {
            'Imsak': 'Imsak',
            'Subuh': 'Fajr',
            'Dzuhur': 'Dhuhr',
            'Ashar': 'Asr',
            'Maghrib': 'Maghrib',
            'Isya': 'Isha'
        };

        // Reset highlight
        document.querySelectorAll('.prayer-time-item').forEach(el => el.classList.remove('next-prayer'));

        let nextName = 'Imsak';
        let nextTime = timings['Imsak'];
        let found = false;

        for (const [name, mins] of Object.entries(prayerMinutes)) {
            if (mins > currentMinutes) {
                nextName = name;
                nextTime = timings[prayerIds[name]];
                const targetId = `prayer_${prayerIds[name]}`;
                const targetEl = document.getElementById(targetId);
                if (targetEl) targetEl.classList.add('next-prayer');
                found = true;
                break;
            }
        }

        if (!found) {
            nextName = 'Imsak';
            const targetEl = document.getElementById('prayer_Imsak');
            if (targetEl) targetEl.classList.add('next-prayer');
        }

        const countdownEl = document.getElementById('next_prayer_countdown');
        if (countdownEl) {
            countdownEl.textContent = `Salat Berikutnya: ${nextName} (${nextTime})`;
        }
    }

    function parseMinutes(timeStr) {
        if (!timeStr) return 0;
        const [h, m] = timeStr.split(':').map(Number);
        return h * 60 + m;
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                getPrayerTimes(pos.coords.latitude, pos.coords.longitude, true);
            },
            (err) => {
                console.warn("Geolocation blocked/failed, using default: ", err);
                getPrayerTimes(defaultLat, defaultLng, false);
            }
        );
    } else {
        getPrayerTimes(defaultLat, defaultLng, false);
    }
})();
</script>
<script src="{{ asset('assets/js/lib/owl-carousel/owl.carousel.min.js') }}"></script>
<script>
$(document).ready(function() {
    const agendaCarousel = $('.agenda-grid');
    if (agendaCarousel.length && agendaCarousel.find('.agenda-card-item').length > 0) {
        agendaCarousel.owlCarousel({
            loop: true,
            margin: 16,
            nav: true,
            dots: true,
            navText: [
                '<i class="fa-solid fa-arrow-left"></i>',
                '<i class="fa-solid fa-arrow-right"></i>'
            ],
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                992: {
                    items: 3
                }
            }
        });

        // Trigger refresh setelah animasi reveal selesai agar kalkulasi lebar tepat
        setTimeout(function() {
            agendaCarousel.trigger('refresh.owl.carousel');
        }, 350);
    }
});
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: dashboard/jamaah.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="dashboard/jamaah.blade.php">
html[data-finus-theme="dark"] body .jamaah-hero { border:1px solid rgba(126,255,135,.09) !important; box-shadow:0 18px 38px rgba(0,0,0,.22) !important; }
html[data-finus-theme="dark"] body :where(.finus-stat-card,.agenda-card,.prayer-card,.agenda-card-item,.expense-card-item) { border-color:#293D31 !important; background:linear-gradient(155deg,#15211A,#111A15) !important; color:#F1F6F3 !important; box-shadow:0 13px 30px rgba(0,0,0,.19) !important; }
html[data-finus-theme="dark"] body :where(.finus-stat-value,.prayer-name,.prayer-val,.agenda-card h1,.agenda-card h2,.agenda-card h3,.prayer-card h1,.prayer-card h2,.prayer-card h3) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.finus-stat-label,.finus-stat-desc,.agenda-card .text-muted,.prayer-card .text-muted,.agenda-card-item .text-muted) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body .prayer-time-item { border-color:#293D31 !important; background:#101C15 !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .btn-quick-action { border-color:#304A39 !important; background:#14211A !important; color:#DCE9E0 !important; }
html[data-finus-theme="dark"] body .btn-quick-action:hover { border-color:#3D5D48 !important; background:#192A20 !important; color:#C9F5D3 !important; }
html[data-finus-theme="dark"] body .agenda-grid.owl-carousel .owl-nav button.owl-prev,
html[data-finus-theme="dark"] body .agenda-grid.owl-carousel .owl-nav button.owl-next { border-color:#293D31 !important; background:#14211A !important; color:#DCE9E0 !important; }
</style>
@endpush


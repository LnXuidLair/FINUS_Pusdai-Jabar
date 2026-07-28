@extends('layouts.app')

@section('title', 'Laporan Arus Kas')
@section('hide-page-header', '1')

@php
    $pemasukan = (float) ($totalPemasukan ?? 0);
    $pengeluaran = (float) ($totalPengeluaran ?? 0);
    $saldoBersih = (float) ($saldo ?? ($pemasukan - $pengeluaran));
    $nilaiTerbesar = max(abs($pemasukan), abs($pengeluaran), 1);
    $persentasePemasukan = min(100, (abs($pemasukan) / $nilaiTerbesar) * 100);
    $persentasePengeluaran = min(100, (abs($pengeluaran) / $nilaiTerbesar) * 100);

    $statusArusKas = $saldoBersih > 0
        ? ['label' => 'Surplus', 'icon' => 'fa-arrow-trend-up', 'color' => '#179B40']
        : ($saldoBersih < 0
            ? ['label' => 'Defisit', 'icon' => 'fa-arrow-trend-down', 'color' => '#DC2626']
            : ['label' => 'Seimbang', 'icon' => 'fa-scale-balanced', 'color' => '#D97706']);

    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp

@include('layouts.partials.finus-ui')

@section('content')
<div class="fr-page">
    <section class="fr-hero fr-reveal">
        <div class="fr-hero-main">
            <span class="fr-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </span>
            <div>
                <h1 class="fr-hero-title">Laporan Arus Kas</h1>
                <p class="fr-hero-subtitle">
                    Ringkasan pemasukan, pengeluaran, dan perubahan saldo kas FINUS.
                </p>
            </div>
        </div>

        <div class="fr-hero-actions">
            <span class="fr-hero-badge">
                <i class="fa-solid fa-calendar-days"></i>
                Diperbarui {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </section>

    <section class="fr-summary" style="--summary-columns:3">
        <article class="fr-stat fr-stat-green fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-arrow-down"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Total Pemasukan</span>
                <strong class="fr-stat-value">{{ $rupiah($pemasukan) }}</strong>
                <span class="fr-stat-note">Seluruh dana yang diterima</span>
            </span>
        </article>

        <article class="fr-stat fr-stat-red fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-arrow-up"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Total Pengeluaran</span>
                <strong class="fr-stat-value">{{ $rupiah($pengeluaran) }}</strong>
                <span class="fr-stat-note">Seluruh dana yang dikeluarkan</span>
            </span>
        </article>

        <article class="fr-stat {{ $saldoBersih >= 0 ? 'fr-stat-blue' : 'fr-stat-amber' }} fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-wallet"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Saldo Bersih</span>
                <strong class="fr-stat-value">{{ $rupiah($saldoBersih) }}</strong>
                <span class="fr-stat-note">Pemasukan dikurangi pengeluaran</span>
            </span>
        </article>
    </section>

    <section class="fr-grid-main-aside">
        <article class="fr-card fr-reveal">
            <header class="fr-card-head">
                <div class="fr-card-title-row">
                    <span class="fr-card-icon"><i class="fa-solid fa-chart-simple"></i></span>
                    <div>
                        <h2 class="fr-card-title">Perbandingan Arus Kas</h2>
                        <p class="fr-card-subtitle">Visualisasi proporsi pemasukan dan pengeluaran.</p>
                    </div>
                </div>
            </header>

            <div class="fr-card-body">
                <div class="fr-breakdown">
                    <div class="fr-breakdown-item" style="--bar-color:#179B40">
                        <div class="fr-breakdown-top">
                            <span class="fr-breakdown-label">
                                <span class="fr-breakdown-dot"></span>
                                Pemasukan
                            </span>
                            <strong class="fr-breakdown-value">{{ $rupiah($pemasukan) }}</strong>
                        </div>
                        <div class="fr-progress" aria-label="Proporsi pemasukan">
                            <div class="fr-progress-bar" style="--bar-width:{{ number_format($persentasePemasukan, 2, '.', '') }}%"></div>
                        </div>
                    </div>

                    <div class="fr-breakdown-item" style="--bar-color:#DC2626">
                        <div class="fr-breakdown-top">
                            <span class="fr-breakdown-label">
                                <span class="fr-breakdown-dot"></span>
                                Pengeluaran
                            </span>
                            <strong class="fr-breakdown-value">{{ $rupiah($pengeluaran) }}</strong>
                        </div>
                        <div class="fr-progress" aria-label="Proporsi pengeluaran">
                            <div class="fr-progress-bar" style="--bar-width:{{ number_format($persentasePengeluaran, 2, '.', '') }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </article>

        <aside class="fr-status-panel fr-reveal" style="--status-color:{{ $statusArusKas['color'] }}">
            <span class="fr-status-icon">
                <i class="fa-solid {{ $statusArusKas['icon'] }}"></i>
            </span>
            <span class="fr-status-label">Kondisi Arus Kas</span>
            <h2 class="fr-status-title">{{ $statusArusKas['label'] }}</h2>
            <p class="fr-status-copy">
                @if($saldoBersih > 0)
                    Pemasukan lebih besar daripada pengeluaran dengan selisih {{ $rupiah(abs($saldoBersih)) }}.
                @elseif($saldoBersih < 0)
                    Pengeluaran lebih besar daripada pemasukan dengan selisih {{ $rupiah(abs($saldoBersih)) }}.
                @else
                    Jumlah pemasukan dan pengeluaran saat ini berada pada nilai yang sama.
                @endif
            </p>
        </aside>
    </section>
</div>
@endsection
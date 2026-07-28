@extends('layouts.app')

@section('title', 'Arus Kas PSAK')
@section('hide-page-header', '1')

@php
    $operasi = (float) data_get($arusKas ?? [], 'operasi.bersih', 0);
    $investasi = (float) data_get($arusKas ?? [], 'investasi.bersih', 0);
    $pendanaan = (float) data_get($arusKas ?? [], 'pendanaan.bersih', 0);
    $total = (float) ($totalBersih ?? ($operasi + $investasi + $pendanaan));
    $nilaiTerbesar = max(abs($operasi), abs($investasi), abs($pendanaan), 1);

    $kategoriArus = [
        [
            'nama' => 'Aktivitas Operasi',
            'nilai' => $operasi,
            'warna' => '#179B40',
            'kelas' => 'fr-stat-green',
            'ikon' => 'fa-building-columns',
            'keterangan' => 'Arus kas dari kegiatan utama dan operasional masjid.',
        ],
        [
            'nama' => 'Aktivitas Investasi',
            'nilai' => $investasi,
            'warna' => '#2563EB',
            'kelas' => 'fr-stat-blue',
            'ikon' => 'fa-chart-line',
            'keterangan' => 'Arus kas terkait perolehan atau pelepasan aset jangka panjang.',
        ],
        [
            'nama' => 'Aktivitas Pendanaan',
            'nilai' => $pendanaan,
            'warna' => '#7C3AED',
            'kelas' => 'fr-stat-purple',
            'ikon' => 'fa-hand-holding-dollar',
            'keterangan' => 'Arus kas yang memengaruhi sumber dan struktur pendanaan.',
        ],
    ];

    $statusTotal = $total > 0
        ? ['label' => 'Kenaikan Kas Bersih', 'icon' => 'fa-arrow-trend-up', 'color' => '#179B40']
        : ($total < 0
            ? ['label' => 'Penurunan Kas Bersih', 'icon' => 'fa-arrow-trend-down', 'color' => '#DC2626']
            : ['label' => 'Tidak Ada Perubahan', 'icon' => 'fa-minus', 'color' => '#D97706']);

    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp

@include('layouts.partials.finus-ui')

@section('content')
<div class="fr-page">
    <section class="fr-hero fr-reveal">
        <div class="fr-hero-main">
            <span class="fr-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-chart-pie"></i>
            </span>
            <div>
                <h1 class="fr-hero-title">Laporan Arus Kas PSAK</h1>
                <p class="fr-hero-subtitle">
                    Pengelompokan arus kas berdasarkan aktivitas operasi, investasi, dan pendanaan.
                </p>
            </div>
        </div>

        <div class="fr-hero-actions">
            <span class="fr-hero-badge">
                <i class="fa-solid fa-layer-group"></i>
                3 klasifikasi aktivitas
            </span>
        </div>
    </section>

    <section class="fr-summary" style="--summary-columns:4">
        @foreach($kategoriArus as $kategori)
            <article class="fr-stat {{ $kategori['kelas'] }} fr-reveal">
                <span class="fr-stat-icon"><i class="fa-solid {{ $kategori['ikon'] }}"></i></span>
                <span class="fr-stat-copy">
                    <span class="fr-stat-label">{{ $kategori['nama'] }}</span>
                    <strong class="fr-stat-value">{{ $rupiah($kategori['nilai']) }}</strong>
                    <span class="fr-stat-note">Nilai bersih aktivitas</span>
                </span>
            </article>
        @endforeach

        <article class="fr-stat {{ $total >= 0 ? 'fr-stat-amber' : 'fr-stat-red' }} fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-scale-balanced"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Total Bersih</span>
                <strong class="fr-stat-value">{{ $rupiah($total) }}</strong>
                <span class="fr-stat-note">Gabungan seluruh aktivitas</span>
            </span>
        </article>
    </section>

    <section class="fr-grid-main-aside">
        <article class="fr-card fr-reveal">
            <header class="fr-card-head">
                <div class="fr-card-title-row">
                    <span class="fr-card-icon"><i class="fa-solid fa-list-check"></i></span>
                    <div>
                        <h2 class="fr-card-title">Rincian Aktivitas</h2>
                        <p class="fr-card-subtitle">Perbandingan nilai bersih setiap klasifikasi arus kas.</p>
                    </div>
                </div>
            </header>

            <div class="fr-card-body">
                <div class="fr-breakdown">
                    @foreach($kategoriArus as $kategori)
                        @php
                            $lebar = min(100, (abs($kategori['nilai']) / $nilaiTerbesar) * 100);
                        @endphp
                        <div class="fr-breakdown-item" style="--bar-color:{{ $kategori['warna'] }}">
                            <div class="fr-breakdown-top">
                                <span class="fr-breakdown-label">
                                    <span class="fr-breakdown-dot"></span>
                                    {{ $kategori['nama'] }}
                                </span>
                                <strong class="fr-breakdown-value">{{ $rupiah($kategori['nilai']) }}</strong>
                            </div>
                            <div class="fr-progress" aria-label="Proporsi {{ $kategori['nama'] }}">
                                <div class="fr-progress-bar" style="--bar-width:{{ number_format($lebar, 2, '.', '') }}%"></div>
                            </div>
                            <p class="fr-card-subtitle" style="margin-top:10px">{{ $kategori['keterangan'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <aside class="fr-status-panel fr-reveal" style="--status-color:{{ $statusTotal['color'] }}">
            <span class="fr-status-icon">
                <i class="fa-solid {{ $statusTotal['icon'] }}"></i>
            </span>
            <span class="fr-status-label">Perubahan Kas Bersih</span>
            <h2 class="fr-status-title">{{ $statusTotal['label'] }}</h2>
            <p class="fr-status-copy">
                Total perubahan kas bersih dari seluruh aktivitas adalah
                <strong>{{ $rupiah($total) }}</strong>.
                Nilai positif menunjukkan kenaikan kas, sedangkan nilai negatif menunjukkan penurunan kas.
            </p>
        </aside>
    </section>
</div>
@endsection
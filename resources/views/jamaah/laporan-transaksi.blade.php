@extends('layouts.app')

@section('title', 'Laporan Transaksi Jamaah')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');

    $laporanJenisFilterLabels = collect($jenisLabels)
        ->except(['shadaqah', 'fidyah'])
        ->all();

    $summaryCards = [
        [
            'label' => 'Total Pengajuan',
            'value' => $rupiah($summary['total']),
            'icon' => 'fa-wallet',
            'color' => '#2563EB',
            'soft' => '#EDF4FF',
        ],
        [
            'label' => 'Dana Diterima',
            'value' => $rupiah($summary['diterima']),
            'icon' => 'fa-circle-check',
            'color' => '#117A32',
            'soft' => '#EAF8EE',
        ],
        [
            'label' => 'Menunggu Verifikasi',
            'value' => $rupiah($summary['pending']),
            'icon' => 'fa-clock',
            'color' => '#B86B00',
            'soft' => '#FFF7E8',
        ],
        [
            'label' => 'Jumlah Transaksi',
            'value' => number_format($summary['jumlah'], 0, ',', '.'),
            'icon' => 'fa-receipt',
            'color' => '#7C3AED',
            'soft' => '#F4EEFF',
        ],
    ];
@endphp

@include('jamaah.partials.transaction-page-styles')

<div class="jt-page">
    <section class="jt-heading">
        <div class="jt-heading-main">
            <span class="jt-icon"><i class="fa-solid fa-chart-column"></i></span>
            <div>
                <div class="jt-eyebrow">Laporan Pribadi</div>
                <h1>Laporan Transaksi Jamaah</h1>
                <p>
                    Periode
                    {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->translatedFormat('d F Y') }}
                    sampai
                    {{ \Carbon\Carbon::parse($filters['tanggal_selesai'])->translatedFormat('d F Y') }}.
                </p>
            </div>
        </div>

        <div class="jt-heading-actions">
            <a class="jt-btn" href="{{ route('jamaah.riwayat.index') }}">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Riwayat
            </a>

            <a
                class="jt-btn jt-btn-soft"
                href="{{ route('jamaah.laporan.export', request()->query()) }}"
            >
                <i class="fa-solid fa-file-csv"></i>
                Export CSV
            </a>

            <button
                type="button"
                class="jt-btn jt-btn-primary"
                onclick="window.print()"
            >
                <i class="fa-solid fa-print"></i>
                Cetak
            </button>
        </div>
    </section>

    <section class="jt-card jt-filter-card">
        <header class="jt-card-head">
            <div class="jt-title-row">
                <span class="jt-icon"><i class="fa-solid fa-calendar-days"></i></span>
                <div>
                    <h2>Periode Laporan</h2>
                    <p>Pilih rentang tanggal, jenis transaksi, dan status verifikasi.</p>
                </div>
            </div>
        </header>

        <div class="jt-card-body">
            <form method="GET" action="{{ route('jamaah.laporan.index') }}" class="jt-filter">
                <div class="jt-field">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        class="jt-control"
                        value="{{ $filters['tanggal_mulai'] }}"
                        required
                    >
                </div>

                <div class="jt-field">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input
                        type="date"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        class="jt-control"
                        value="{{ $filters['tanggal_selesai'] }}"
                        required
                    >
                </div>

                <div class="jt-field">
                    <label for="jenis">Jenis</label>
                    <select id="jenis" name="jenis" class="jt-control">
                        <option value="">Semua jenis</option>
                        @foreach($laporanJenisFilterLabels as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['jenis'] ?? '') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="jt-field">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="jt-control">
                        <option value="">Semua status</option>

                        @foreach($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="jt-filter-actions">
                    <button type="submit" class="jt-btn jt-btn-primary">
                        <i class="fa-solid fa-chart-line"></i>
                        Tampilkan
                    </button>

                    <a href="{{ route('jamaah.laporan.index') }}" class="jt-btn">
                        <i class="fa-solid fa-rotate-left"></i>
                        Tahun Ini
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="jt-grid jt-grid-4">
        @foreach($summaryCards as $card)
            <article
                class="jt-card jt-stat"
                style="--stat-color:{{ $card['color'] }};--stat-soft:{{ $card['soft'] }}"
            >
                <div class="jt-stat-top">
                    <span class="jt-icon">
                        <i class="fa-solid {{ $card['icon'] }}"></i>
                    </span>
                </div>

                <div class="jt-stat-body">
                    <span>{{ $card['label'] }}</span>
                    <strong>{{ $card['value'] }}</strong>
                </div>
            </article>
        @endforeach
    </section>

    <section class="jt-grid jt-grid-2">
        <article class="jt-card">
            <header class="jt-card-head">
                <div class="jt-title-row">
                    <span class="jt-icon"><i class="fa-solid fa-chart-area"></i></span>
                    <div>
                        <h2>Perkembangan Bulanan</h2>
                        <p>Nominal transaksi sesuai periode laporan.</p>
                    </div>
                </div>
            </header>

            <div class="jt-card-body">
                <div class="jt-chart">
                    <canvas id="jtMonthlyChart"></canvas>
                </div>
            </div>
        </article>

        <article class="jt-card">
            <header class="jt-card-head">
                <div class="jt-title-row">
                    <span class="jt-icon"><i class="fa-solid fa-chart-pie"></i></span>
                    <div>
                        <h2>Komposisi Jenis</h2>
                        <p>Perbandingan nominal berdasarkan jenis ZISWAF.</p>
                    </div>
                </div>
            </header>

            <div class="jt-card-body">
                <div class="jt-chart-small">
                    <canvas id="jtTypeChart"></canvas>
                </div>
            </div>
        </article>
    </section>

    <section class="jt-card">
        <header class="jt-card-head">
            <div class="jt-title-row">
                <span class="jt-icon"><i class="fa-solid fa-layer-group"></i></span>
                <div>
                    <h2>Ringkasan per Jenis</h2>
                    <p>Jumlah transaksi dan nominal pada setiap kategori.</p>
                </div>
            </div>
        </header>

        <div class="jt-card-body">
            <div class="jt-breakdown">
                @forelse($perJenis as $item)
                    <div class="jt-breakdown-item">
                        <div>
                            <strong>
                                {{ $jenisLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf }}
                            </strong>
                            <small>{{ number_format($item->jumlah_transaksi, 0, ',', '.') }} transaksi</small>
                        </div>

                        <div class="jt-money">{{ $rupiah($item->total) }}</div>
                    </div>
                @empty
                    <div class="jt-empty">Belum ada data pada periode ini.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="jt-card">
        <header class="jt-card-head">
            <div class="jt-title-row">
                <span class="jt-icon"><i class="fa-solid fa-table-list"></i></span>
                <div>
                    <h2>Rincian Laporan</h2>
                    <p>Daftar lengkap transaksi yang masuk ke laporan ini.</p>
                </div>
            </div>
        </header>

        <div class="jt-table-wrap">
            <table class="jt-table">
                <thead>
                    <tr>
                        <th>Referensi</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th class="text-right">Nominal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transaksiLaporan as $item)
                        @php
                            $status = $item->status_verifikasi ?: 'pending';
                        @endphp

                        <tr>
                            <td class="jt-reference">ZISWAF-{{ $item->id }}</td>

                            <td>{{ $item->tanggal?->format('d/m/Y') }}</td>

                            <td>
                                <span class="jt-type">
                                    {{ $jenisLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf }}
                                </span>
                            </td>

                            <td>
                                {{ $metodeLabels[$item->metode_pembayaran]
                                    ?? strtoupper(str_replace('_', ' ', $item->metode_pembayaran)) }}
                            </td>

                            <td>
                                <span class="jt-badge jt-badge-{{ $status }}">
                                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                                </span>
                            </td>

                            <td class="jt-note">{{ $item->keterangan ?? '-' }}</td>

                            <td class="jt-money text-right">{{ $rupiah($item->nominal) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="jt-empty">
                                Tidak ada transaksi dalam periode laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if($transaksiLaporan->isNotEmpty())
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">Total</th>
                            <th class="text-right jt-money">{{ $rupiah($summary['total']) }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const money = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');

    const monthlyCanvas = document.getElementById('jtMonthlyChart');

    if (monthlyCanvas) {
        new Chart(monthlyCanvas, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Nominal Transaksi',
                    data: @json($chartData),
                    backgroundColor: 'rgba(23, 155, 64, .72)',
                    borderColor: '#0E5423',
                    borderWidth: 1,
                    borderRadius: 7,
                    maxBarThickness: 45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#64748B',
                            usePointStyle: true,
                            boxWidth: 7,
                            font: { size: 10, weight: '600' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: context => money(context.raw)
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748B', font: { size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(100,116,139,.14)' },
                        ticks: {
                            color: '#64748B',
                            font: { size: 10 },
                            callback: money
                        }
                    }
                }
            }
        });
    }

    const typeCanvas = document.getElementById('jtTypeChart');

    if (typeCanvas) {
        new Chart(typeCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($jenisChartLabels),
                datasets: [{
                    data: @json($jenisChartData),
                    backgroundColor: [
                        '#179B40',
                        '#2563EB',
                        '#0891B2',
                        '#7C3AED',
                        '#EA8B22',
                        '#E5484D'
                    ],
                    borderColor: '#FFFFFF',
                    borderWidth: 4,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '67%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#64748B',
                            usePointStyle: true,
                            boxWidth: 7,
                            font: { size: 10, weight: '600' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: context => `${context.label}: ${money(context.raw)}`
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush

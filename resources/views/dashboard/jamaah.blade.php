@extends('layouts.app')
@section('title', 'Dashboard Jamaah')
@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
@endphp

<style>
    .jamaah-hero {
        border-radius: 24px;
        background: linear-gradient(135deg, #065f46, #16a34a);
        color: white;
        overflow: hidden;
        position: relative;
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
</style>

<div class="jamaah-hero p-4 p-md-5 mb-4">
    <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-12">
            <p class="mb-2 text-white-50">Assalamu'alaikum,</p>
            <h2 class="text-white font-weight-bold mb-2">{{ $jamaah->name }}</h2>
            <p class="mb-0 text-white-50">
                Dashboard jamaah untuk melihat riwayat transaksi, grafik pemasukan jamaah,
                agenda kegiatan, dan transparansi pengeluaran masjid.
            </p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="finus-stat-card finus-stat-red mb-4">
            <div class="finus-stat-top">
                <div class="finus-stat-icon finus-stat-icon-red">
                    <i class="fa fa-receipt"></i>
                </div>

                <span class="finus-stat-badge finus-stat-badge-red">
                    Bulan Ini
                </span>
            </div>

            <div class="finus-stat-content">
                <p class="finus-stat-label">Transaksi Saya</p>
                <h4 class="finus-stat-value">{{ $rupiah($totalTransaksiSaya) }}</h4>
                <p class="finus-stat-desc">
                    {{ $jumlahTransaksiSaya }} transaksi yang telah kamu input.
                </p>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="finus-stat-card finus-stat-green mb-4">
            <div class="finus-stat-top">
                <div class="finus-stat-icon finus-stat-icon-green">
                    <i class="fa fa-wallet"></i>
                </div>

                <span class="finus-stat-badge finus-stat-badge-green">
                    Bulan Ini
                </span>
            </div>

            <div class="finus-stat-content">
                <p class="finus-stat-label">Donasi Jamaah</p>
                <h4 class="finus-stat-value">{{ $rupiah($totalPemasukanJamaah) }}</h4>
                <p class="finus-stat-desc">
                    Donasi dari akumulasi keseluruhan jamaah.
                </p>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="finus-stat-card finus-stat-blue mb-4">
            <div class="finus-stat-top">
                <div class="finus-stat-icon finus-stat-icon-blue">
                    <i class="fa fa-book-open"></i>
                </div>

                <span class="finus-stat-badge finus-stat-badge-blue">
                    Bulan Ini
                </span>
            </div>

            <div class="finus-stat-content">
                <p class="finus-stat-label">Pengeluaran Masjid</p>
                <h4 class="finus-stat-value">{{ $rupiah($totalPengeluaran) }}</h4>
                <p class="finus-stat-desc">
                    Pengeluaran masjid yang telah dicatat admin.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card agenda-card mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h5 class="mb-1 font-weight-bold text-dark">Agenda & Kegiatan</h5>
                    <p class="mb-0 text-muted">Informasi kegiatan masjid untuk jamaah.</p>
                </div>
                <div class="d-flex flex-wrap">
                    @forelse(($agendaKegiatan ?? []) as $agenda)
                        <div class="agenda-item me-3 mb-3" style="flex: 1 1 30%; min-width: 250px;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1 font-weight-bold text-dark">{{ $agenda['judul'] }}</h6>
                                    <p class="mb-0 text-muted small"><i class="fa fa-calendar mr-1 text-success"></i> {{ $agenda['hari'] }}</p>
                                </div>
                                <span class="agenda-badge">{{ $agenda['kategori'] }}</span>
                            </div>
                            <p class="mb-1 text-muted small"><i class="fa fa-clock mr-1 text-success"></i> {{ $agenda['waktu'] }}</p>
                            <p class="mb-2 text-muted small"><i class="fa fa-map-marker-alt mr-1 text-success"></i> {{ $agenda['lokasi'] }}</p>
                            <p class="mb-0 text-secondary small">{{ $agenda['deskripsi'] }}</p>
                        </div>
                    @empty
                        <div class="py-5 text-center w-100">
                            <i class="fa fa-calendar mb-3 text-muted" style="font-size: 34px;"></i>
                            <p class="mb-0 text-muted">Belum ada agenda kegiatan.</p>
                        </div>
                    @endforelse
                </div>
                <div class="agenda-note mt-4">Untuk transaksi ZISWAF, gunakan menu di sidebar.</div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card finus-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 font-weight-bold">Ringkasan Pengeluaran Masjid</h5>
                    <small class="text-muted">Pengeluaran ditampilkan sederhana berdasarkan kelompok kategori.</small>
                </div>
                <span class="badge badge-soft-danger px-3 py-2">Transparansi</span>
            </div>
            <div class="card-body px-4">
                <div class="row">
                    @forelse($pengeluaranKategori as $item)
                        @php
                            $persen = $totalPengeluaran > 0 ? round(($item->total / $totalPengeluaran) * 100) : 0;
                        @endphp
                        <div class="col-md-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <h6 class="mb-0 font-weight-bold">{{ $item->kategori_nama ?: 'Lainnya' }}</h6>
                                    <small class="text-muted">{{ $item->jumlah_transaksi }} transaksi</small>
                                </div>
                                <div class="text-right">
                                    <strong class="text-danger">{{ $rupiah($item->total) }}</strong>
                                    <small class="d-block text-muted">{{ $persen }}%</small>
                                </div>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 999px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $persen }}%; border-radius: 999px;" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 w-100">
                            <i class="fa fa-wallet text-muted mb-3" style="font-size: 32px;"></i>
                            <p class="text-muted mb-0">Belum ada data pengeluaran.</p>
                        </div>
                    @endforelse
                </div>
                @if($totalPengeluaran > 0)
                    <div class="border-top pt-3 mt-2 d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold">Total Pengeluaran</span>
                        <h5 class="mb-0 text-danger font-weight-bold">{{ $rupiah($totalPengeluaran) }}</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card finus-card mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="mb-1 font-weight-bold">Ringkasan Dana ZISWAF</h5>
        <small class="text-muted">Informasi sederhana untuk membantu jamaah memahami arus dana.</small>
    </div>

    <div class="card-body px-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="p-3 rounded bg-light">
                    <small class="text-muted">Total Zakat</small>
                    <h5 class="mb-0 text-success">{{ $rupiah($totalZakat) }}</h5>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="p-3 rounded bg-light">
                    <small class="text-muted">Total Wakaf</small>
                    <h5 class="mb-0 text-primary">{{ $rupiah($totalWakaf) }}</h5>
                </div>
            </div>
        </div>

        <p class="mb-0 text-muted">
            Catatan: informasi ini menampilkan ringkasan dana ZISWAF yang tercatat pada sistem.
            Untuk laporan resmi, tetap gunakan laporan keuangan admin.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
    const rupiah = value => 'Rp ' + Number(value || 0).toLocaleString('id-ID');

    const lineCanvas = document.getElementById('grafikPemasukanJamaah');

    if (lineCanvas) {
        new Chart(lineCanvas, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Pemasukan Jamaah',
                    data: @json($chartData),
                    tension: 0.35,
                    fill: true,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => rupiah(context.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => rupiah(value)
                        }
                    }
                }
            }
        });
    }

    const doughnutCanvas = document.getElementById('grafikKomposisiZiswaf');

    if (doughnutCanvas) {
        new Chart(doughnutCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($komposisiLabels),
                datasets: [{
                    data: @json($komposisiData),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => `${context.label}: ${rupiah(context.raw)}`
                        }
                    }
                }
            }
        });
    }
})();
</script>
@endpush
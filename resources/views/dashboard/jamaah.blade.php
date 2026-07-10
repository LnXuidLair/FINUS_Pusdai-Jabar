@extends('layouts.app')

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

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    .quick-action {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px;
        display: block;
        color: #334155;
        text-decoration: none;
        transition: .2s;
        background: #fff;
    }

    .quick-action:hover {
        transform: translateY(-2px);
        color: #047857;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .08);
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
    <div class="col-xl-3 col-md-6">
        <div class="card finus-card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Transaksi Saya</p>
                        <h4 class="mb-0 text-success">{{ $rupiah($totalTransaksiSaya) }}</h4>
                        <small>{{ $jumlahTransaksiSaya }} transaksi tercatat</small>
                    </div>
                    <span class="stat-icon bg-success">
                        <i class="fa fa-hand-holding-heart"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card finus-card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Pemasukan Jamaah</p>
                        <h4 class="mb-0 text-primary">{{ $rupiah($totalPemasukanJamaah) }}</h4>
                        <small>Total seluruh jamaah</small>
                    </div>
                    <span class="stat-icon bg-primary">
                        <i class="fa fa-chart-line"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card finus-card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Infak Terkumpul</p>
                        <h4 class="mb-0 text-info">{{ $rupiah($totalInfak) }}</h4>
                        <small>Khusus jenis infak</small>
                    </div>
                    <span class="stat-icon bg-info">
                        <i class="fa fa-mosque"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card finus-card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Pengeluaran</p>
                        <h4 class="mb-0 text-danger">{{ $rupiah($totalPengeluaran) }}</h4>
                        <small>Data dari admin</small>
                    </div>
                    <span class="stat-icon bg-danger">
                        <i class="fa fa-wallet"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card finus-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-1 font-weight-bold">Grafik Pemasukan Jamaah</h5>
                <small class="text-muted">Perkembangan pemasukan ZISWAF dan infak enam bulan terakhir.</small>
            </div>
            <div class="card-body px-4">
                <canvas id="grafikPemasukanJamaah" height="115"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card finus-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-1 font-weight-bold">Komposisi ZISWAF</h5>
                <small class="text-muted">Pembagian pemasukan berdasarkan jenis transaksi.</small>
            </div>
            <div class="card-body px-4">
                <canvas id="grafikKomposisiZiswaf" height="185"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="mb-4 rounded-2xl bg-white p-6 shadow-[0_10px_28px_rgba(15,23,42,.07)]">
            <div class="mb-5">
                <h5 class="mb-1 text-xl font-bold text-slate-800">Agenda & Kegiatan</h5>
                <p class="mb-0 text-sm text-slate-500">
                    Informasi kegiatan masjid untuk jamaah.
                </p>
            </div>

            <div class="space-y-4">
                @forelse(($agendaKegiatan ?? []) as $agenda)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="mb-2 flex items-start justify-between gap-3">
                            <div>
                                <h6 class="mb-1 font-bold text-slate-800">
                                    {{ $agenda['judul'] }}
                                </h6>

                                <p class="mb-0 text-sm text-slate-500">
                                    <i class="fa fa-calendar mr-1 text-emerald-600"></i>
                                    {{ $agenda['hari'] }}
                                </p>
                            </div>

                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ $agenda['kategori'] }}
                            </span>
                        </div>

                        <p class="mb-1 text-sm text-slate-500">
                            <i class="fa fa-clock mr-1 text-emerald-600"></i>
                            {{ $agenda['waktu'] }}
                        </p>

                        <p class="mb-2 text-sm text-slate-500">
                            <i class="fa fa-map-marker-alt mr-1 text-emerald-600"></i>
                            {{ $agenda['lokasi'] }}
                        </p>

                        <p class="mb-0 text-sm text-slate-600">
                            {{ $agenda['deskripsi'] }}
                        </p>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <i class="fa fa-calendar mb-3 text-3xl text-slate-400"></i>
                        <p class="mb-0 text-sm text-slate-500">
                            Belum ada agenda kegiatan.
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700">
                Untuk transaksi ZISWAF, gunakan menu di sidebar.
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card finus-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 font-weight-bold">Riwayat Transaksi Saya</h5>
                    <small class="text-muted">Daftar transaksi ZISWAF dan infak yang kamu input.</small>
                </div>
                <span class="badge badge-soft-success px-3 py-2">Jamaah</span>
            </div>

            <div class="card-body table-responsive px-4">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Metode</th>
                            <th>Keterangan</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatSaya as $transaksi)
                            <tr>
                                <td>{{ $transaksi->tanggal?->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-soft-info px-2 py-1">
                                        {{ $jenisLabels[$transaksi->jenis_ziswaf] ?? $transaksi->jenis_ziswaf }}
                                    </span>
                                </td>
                                <td>{{ strtoupper(str_replace('_', ' ', $transaksi->metode_pembayaran)) }}</td>
                                <td>{{ $transaksi->keterangan ?? '-' }}</td>
                                <td class="text-right font-weight-bold text-success">
                                    {{ $rupiah($transaksi->nominal) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card finus-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-1 font-weight-bold">Transaksi Jamaah Terbaru</h5>
                <small class="text-muted">Aktivitas transaksi terbaru dari jamaah di aplikasi.</small>
            </div>

            <div class="card-body table-responsive px-4">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Jamaah</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksiTerbaruJamaah as $item)
                            <tr>
                                <td>{{ $item->muzakki?->name ?? 'Jamaah' }}</td>
                                <td>{{ $item->tanggal?->format('d/m/Y') }}</td>
                                <td>{{ $jenisLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf }}</td>
                                <td class="text-right font-weight-bold text-success">
                                    {{ $rupiah($item->nominal) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada transaksi jamaah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card finus-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 font-weight-bold">Ringkasan Pengeluaran Masjid</h5>
                    <small class="text-muted">Pengeluaran ditampilkan sederhana berdasarkan kelompok kategori.</small>
                </div>
                <span class="badge badge-soft-danger px-3 py-2">Transparansi</span>
            </div>

            <div class="card-body px-4">
                @forelse($pengeluaranKategori as $item)
                    @php
                        $persen = $totalPengeluaran > 0
                            ? round(($item->total / $totalPengeluaran) * 100)
                            : 0;
                    @endphp

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <h6 class="mb-0 font-weight-bold">
                                    {{ $item->kategori_nama ?: 'Lainnya' }}
                                </h6>
                                <small class="text-muted">
                                    {{ $item->jumlah_transaksi }} transaksi
                                </small>
                            </div>

                            <div class="text-right">
                                <strong class="text-danger">
                                    {{ $rupiah($item->total) }}
                                </strong>
                                <small class="d-block text-muted">{{ $persen }}%</small>
                            </div>
                        </div>

                        <div class="progress" style="height: 8px; border-radius: 999px;">
                            <div class="progress-bar bg-danger"
                                role="progressbar"
                                style="width: {{ $persen }}%; border-radius: 999px;"
                                aria-valuenow="{{ $persen }}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fa fa-wallet text-muted mb-3" style="font-size: 32px;"></i>
                        <p class="text-muted mb-0">Belum ada data pengeluaran.</p>
                    </div>
                @endforelse

                @if($totalPengeluaran > 0)
                    <div class="border-top pt-3 mt-2 d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold">Total Pengeluaran</span>
                        <h5 class="mb-0 text-danger font-weight-bold">
                            {{ $rupiah($totalPengeluaran) }}
                        </h5>
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
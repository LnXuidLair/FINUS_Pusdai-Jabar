@extends('layouts.app')

@section('content')
<style>
    .finus-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .finus-card {
        background: #fff;
        border: 1px solid #e6ece8;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(17, 72, 39, .07);
        padding: 20px;
    }
    .finus-card small { color: #6b7280; }
    .finus-value { color: #14532d; font-size: 24px; font-weight: 700; margin-top: 6px; }
    .finus-chart { height: 330px; }
    @media (max-width: 991px) {
        .finus-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 575px) {
        .finus-summary { grid-template-columns: 1fr; }
    }
</style>

<div class="finus-summary">
    <div class="finus-card">
        <small>Pengeluaran bulan ini</small>
        <div class="finus-value">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</div>
    </div>
    <div class="finus-card">
        <small>Penggajian bulan ini</small>
        <div class="finus-value">Rp {{ number_format($gajiBulanIni, 0, ',', '.') }}</div>
    </div>
    <div class="finus-card">
        <small>Jurnal bulan ini</small>
        <div class="finus-value">{{ number_format($jurnalBulanIni) }} transaksi</div>
    </div>
    <div class="finus-card">
        <small>Pegawai masjid</small>
        <div class="finus-value">{{ number_format($jumlahPegawai) }} orang</div>
    </div>
    <div class="finus-card">
        <small>Jamaah terdaftar</small>
        <div class="finus-value">{{ number_format($jumlahJamaah) }} akun</div>
    </div>
    <div class="finus-card">
        <small>Presensi hari ini</small>
        <div class="finus-value">{{ number_format($presensiHariIni) }} catatan</div>
    </div>
</div>

<div class="finus-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1">Operasional Tahunan</h5>
            <small>Perbandingan pengeluaran dan penggajian {{ now()->year }}</small>
        </div>
    </div>
    <div class="finus-chart">
        <canvas id="operasionalChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const fillMonths = data => Array.from({ length: 12 }, (_, index) => Number(data[index + 1] || 0));

    new Chart(document.getElementById('operasionalChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Pengeluaran',
                    data: fillMonths(@json($pengeluaranBulanan)),
                    backgroundColor: '#ef4444'
                },
                {
                    label: 'Penggajian',
                    data: fillMonths(@json($penggajianBulanan)),
                    backgroundColor: '#16a34a'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => 'Rp ' + Number(value).toLocaleString('id-ID')
                    }
                }
            }
        }
    });
</script>
@endpush

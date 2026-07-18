@extends('layouts.app')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
@endphp

<style>
    .finus-report-hero {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background: linear-gradient(135deg, #065f22 0%, #16a34a 100%);
        color: #ffffff;
        padding: 30px;
        margin-bottom: 24px;
        box-shadow: 0 18px 38px rgba(22, 163, 74, .20);
    }

    .finus-report-hero::before {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        right: -80px;
        top: -95px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .14);
    }

    .finus-report-hero::after {
        content: "";
        position: absolute;
        width: 170px;
        height: 170px;
        left: 55%;
        bottom: -100px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .09);
    }

    .finus-report-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .finus-report-hero h3 {
        margin-bottom: 8px;
        color: #ffffff;
        font-size: 28px;
        font-weight: 800;
    }

    .finus-report-hero p {
        margin-bottom: 0;
        color: rgba(255, 255, 255, .84);
    }

    .finus-report-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border-radius: 14px;
        background: rgba(255, 255, 255, .16);
        color: #ffffff;
        font-weight: 800;
        white-space: nowrap;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .16);
    }

    .finus-summary-card {
        border: 0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        overflow: hidden;
    }

    .finus-summary-card .card-body {
        padding: 24px;
    }

    .finus-summary-label {
        margin-bottom: 8px;
        color: #64748b;
        font-size: 14px;
        font-weight: 700;
    }

    .finus-summary-value {
        margin-bottom: 0;
        color: #065f22;
        font-size: 24px;
        font-weight: 900;
    }

    .finus-filter {
        border: 0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        padding: 22px;
        margin-bottom: 24px;
    }

    .finus-filter label {
        color: #0f172a;
        font-weight: 700;
    }

    .finus-input {
        height: 44px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        box-shadow: none;
    }

    .finus-input:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 4px rgba(22, 163, 74, .12);
    }

    .finus-btn-primary {
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #047857, #16a34a);
        color: #ffffff;
        font-weight: 700;
        padding: 10px 18px;
    }

    .finus-btn-primary:hover {
        color: #ffffff;
        background: linear-gradient(135deg, #065f46, #15803d);
    }

    .finus-table-card {
        border: 0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        overflow: hidden;
    }

    .finus-table-card .card-header {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 22px 24px;
    }

    .finus-table-card .card-header h5 {
        margin-bottom: 4px;
        color: #0f172a;
        font-weight: 800;
    }

    .finus-table-card .card-header small {
        color: #64748b;
    }

    .table thead th {
        border-top: 0;
        color: #475569;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .badge-gaji {
        display: inline-block;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 800;
    }

    .badge-dibayar,
    .badge-selesai {
        background: #dcfce7;
        color: #166534;
    }

    .badge-pending,
    .badge-diproses,
    .badge-belum-dibayar {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-gagal,
    .badge-ditolak {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 991.98px) {
        .finus-report-hero-content {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="finus-report-hero">
    <div class="finus-report-hero-content">
        <div>
            <h3>Laporan Gaji Pegawai</h3>
            <p>
                Riwayat penggajian milik {{ $pegawai->nama_pegawai ?? auth()->user()->name }}
                berdasarkan data yang diproses admin.
            </p>
        </div>

        <div class="finus-report-badge">
            <i class="ti-user"></i>
            {{ $pegawai->jabatan ?? 'Pegawai' }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card finus-summary-card">
            <div class="card-body">
                <div class="finus-summary-label">Total Gaji Tercatat</div>
                <h4 class="finus-summary-value">{{ $rupiah($totalGaji ?? 0) }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card finus-summary-card">
            <div class="card-body">
                <div class="finus-summary-label">Gaji Terakhir</div>
                <h4 class="finus-summary-value">
                    {{ $rupiah($gajiTerakhir->total_gaji ?? 0) }}
                </h4>
            </div>
        </div>
    </div>
</div>

<div class="finus-filter">
    <form method="GET" action="{{ route('pegawai.laporan-gaji.index') }}">
        <div class="row align-items-end">
            <div class="col-md-4 mb-3 mb-md-0">
                <label for="bulan">Filter Bulan</label>
                <input
                    type="month"
                    name="bulan"
                    id="bulan"
                    value="{{ $bulan }}"
                    class="form-control finus-input"
                >
            </div>

            <div class="col-md-8">
                <button type="submit" class="finus-btn-primary">
                    <i class="ti-search mr-1"></i>
                    Tampilkan
                </button>

                <a href="{{ route('pegawai.laporan-gaji.index') }}" class="btn btn-light ml-2">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card finus-table-card mb-4">
    <div class="card-header">
        <h5>Riwayat Penggajian</h5>
        <small>Daftar gaji pegawai yang sudah tercatat pada sistem FINUS.</small>
    </div>

    <div class="card-body table-responsive px-4">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th class="text-right">Total Gaji</th>
                </tr>
            </thead>

            <tbody>
                @forelse($laporanGaji as $gaji)
                    @php
                        $status = strtolower($gaji->status_penggajian ?? 'pending');
                        $statusClass = str_replace(' ', '-', $status);
                    @endphp

                    <tr>
                        <td>
                            {{ $gaji->tanggal ? \Carbon\Carbon::parse($gaji->tanggal)->format('d/m/Y') : '-' }}
                        </td>

                        <td>
                            {{ $gaji->bulan ?? '-' }}
                            {{ $gaji->tahun ?? '' }}
                        </td>

                        <td>
                            <span class="badge-gaji badge-{{ $statusClass }}">
                                {{ ucfirst($gaji->status_penggajian ?? 'Pending') }}
                            </span>
                        </td>

                        <td class="text-right font-weight-bold text-success">
                            {{ $rupiah($gaji->total_gaji ?? 0) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Belum ada data penggajian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 pb-4">
        {{ $laporanGaji->links() }}
    </div>
</div>
@endsection
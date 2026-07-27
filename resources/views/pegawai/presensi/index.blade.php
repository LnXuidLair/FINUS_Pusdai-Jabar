@extends('layouts.app')
@section('title', 'Presensi Saya')
@section('content')

@php
    $totalHadir = $presensis->where('status', 'hadir')->count();
    $totalIzin = $presensis->where('status', 'izin')->count();
    $totalLembur = $presensis->where('status', 'lembur')->count();
    $totalPresensi = $presensis->count();
@endphp

<style>
    .presensi-hero {
        border-radius: 20px;
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: white;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .presensi-hero h3 {
        margin: 0;
        font-weight: 800;
        font-size: 22px;
        color: white;
    }
    
    .presensi-hero p {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 13px;
    }

    .presensi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .presensi-stat-card {
        border: 0;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .presensi-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }

    .presensi-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 700;
    }

    .icon-total { background: #f1f5f9; color: #475569; }
    .icon-hadir { background: #dcfce7; color: #16a34a; }
    .icon-izin { background: #fef3c7; color: #d97706; }
    .icon-lembur { background: #dbeafe; color: #2563eb; }

    .presensi-stat-info {
        display: flex;
        flex-direction: column;
    }

    .presensi-stat-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .presensi-stat-value {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin-top: 2px;
    }

    .presensi-table-card {
        border: 0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }

    .presensi-table-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
    }

    .presensi-table-header h5 {
        margin: 0;
        font-weight: 800;
        font-size: 16px;
        color: #0f172a;
    }

    .p-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .p-table th, .p-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
    }

    .p-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
    }

    .p-table tbody tr:hover td {
        background-color: #f8fafc;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        border: 0;
    }

    .badge-status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .badge-hadir {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-izin {
        background: #fef3c7;
        color: #b45309;
    }

    .badge-lembur {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .btn-action-view {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.2s ease;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-action-view:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    @media (max-width: 768px) {
        .presensi-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
    }
</style>

<div class="presensi-hero">
    <div>
        <h3>Presensi Saya</h3>
        <p>Kelola dan pantau riwayat absensi harian Anda sebagai pegawai.</p>
    </div>
    <div>
        <a class="btn btn-success px-4 py-2 font-weight-bold" style="border-radius: 12px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);" href="{{ route('pegawai.presensi.create') }}">
            <i class="fa fa-plus mr-1"></i> Isi Presensi
        </a>
    </div>
</div>

<div class="presensi-grid">
    <div class="presensi-stat-card">
        <div class="presensi-stat-icon icon-total">
            <i class="fa fa-calendar-check"></i>
        </div>
        <div class="presensi-stat-info">
            <span class="presensi-stat-label">Total Presensi</span>
            <span class="presensi-stat-value">{{ $totalPresensi }}</span>
        </div>
    </div>
    
    <div class="presensi-stat-card">
        <div class="presensi-stat-icon icon-hadir">
            <i class="fa fa-check"></i>
        </div>
        <div class="presensi-stat-info">
            <span class="presensi-stat-label">Hadir</span>
            <span class="presensi-stat-value">{{ $totalHadir }}</span>
        </div>
    </div>
    
    <div class="presensi-stat-card">
        <div class="presensi-stat-icon icon-izin">
            <i class="fa fa-envelope"></i>
        </div>
        <div class="presensi-stat-info">
            <span class="presensi-stat-label">Izin</span>
            <span class="presensi-stat-value">{{ $totalIzin }}</span>
        </div>
    </div>
    
    <div class="presensi-stat-card">
        <div class="presensi-stat-icon icon-lembur">
            <i class="fa fa-moon"></i>
        </div>
        <div class="presensi-stat-info">
            <span class="presensi-stat-label">Lembur</span>
            <span class="presensi-stat-value">{{ $totalLembur }}</span>
        </div>
    </div>
</div>

<div class="card presensi-table-card">
    <div class="presensi-table-header">
        <h5>Daftar Riwayat Presensi</h5>
    </div>
    <div class="table-responsive">
        <table class="p-table">
            <thead>
                <tr>
                    <th>Hari / Tanggal</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Bukti Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensis as $item)
                    <tr>
                        <td class="font-weight-bold text-dark">
                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') : '-' }}
                        </td>
                        <td>
                            <span class="badge-status badge-{{ $item->status }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="text-secondary">
                            {{ $item->keterangan ?? '-' }}
                        </td>
                        <td>
                            @if($item->bukti_kehadiran)
                                <a href="{{ asset('storage/' . $item->bukti_kehadiran) }}" target="_blank" class="btn-action-view">
                                    <i class="fa fa-eye"></i> Lihat Bukti
                                </a>
                            @else
                                <span class="text-muted small">Tidak ada bukti</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="fa-regular fa-folder-open mb-3" style="font-size: 32px;"></i>
                            <p class="mb-0">Belum ada riwayat presensi.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

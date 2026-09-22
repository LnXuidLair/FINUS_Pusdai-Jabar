@extends('layouts.app')

@section('title', 'Dashboard Parkir Pusdai')
@section('hide-page-header', '1')

@php
    $rupiah = fn ($v) => 'Rp ' . number_format((float)$v, 0, ',', '.');
@endphp

@include('layouts.partials.finus-ui')

@section('content')
<div class="fr-page">

    {{-- Hero --}}
    <section class="fr-hero fr-reveal">
        <div class="fr-hero-main">
            <span class="fr-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-square-parking"></i>
            </span>
            <div>
                <h1 class="fr-hero-title">Monitor Parkir</h1>
                <p class="fr-hero-subtitle">
                    Dashboard transaksi parkir QRIS harian dengan pencatatan pendapatan otomatis ke jurnal pemasukan.
                </p>
            </div>
        </div>
        <div class="fr-hero-actions">
            <a href="{{ route('admin.parkir.loket') }}"
                style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:#0E5423;color:white;border-radius:10px;text-decoration:none;font-weight:700;font-size:14px;box-shadow:0 4px 14px rgba(14,84,35,.3);">
                <i class="fa-solid fa-motorcycle"></i> Buka Loket Parkir
            </a>
        </div>
    </section>

    {{-- Summary Cards --}}
    <section class="fr-summary" style="--summary-columns:4">
        <article class="fr-stat fr-stat-green fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-square-parking"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Kendaraan Masuk</span>
                <strong class="fr-stat-value">{{ number_format($stats['total_masuk'], 0, ',', '.') }}</strong>
                <span class="fr-stat-note">Total sesi dibuka hari ini</span>
            </span>
        </article>

        <article class="fr-stat fr-stat-emerald fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-circle-check"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Transaksi Selesai</span>
                <strong class="fr-stat-value">{{ number_format($stats['total_selesai'], 0, ',', '.') }}</strong>
                <span class="fr-stat-note">Motor: {{ $stats['total_motor'] }} • Mobil: {{ $stats['total_mobil'] }}</span>
            </span>
        </article>

        <article class="fr-stat fr-stat-amber fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-car-side"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Sedang Parkir</span>
                <strong class="fr-stat-value">{{ number_format($stats['sedang_parkir'], 0, ',', '.') }}</strong>
                <span class="fr-stat-note">Kendaraan belum keluar</span>
            </span>
        </article>

        <article class="fr-stat fr-stat-blue fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-qrcode"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Total Pendapatan</span>
                <strong class="fr-stat-value">{{ $rupiah($stats['total_pendapatan']) }}</strong>
                <span class="fr-stat-note">QRIS + Tunai (auto-jurnal)</span>
            </span>
        </article>
    </section>

    {{-- Filter Bar --}}
    <section class="fr-card fr-reveal" style="margin-bottom:24px;padding:18px 22px;">
        <form method="GET" action="{{ route('admin.parkir.index') }}" style="margin:0;">
            <div style="display:flex;flex-direction:row;align-items:flex-end;gap:12px;flex-wrap:nowrap;">
                <div style="flex:1;min-width:150px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:.03em;">
                        <i class="fa-solid fa-calendar-day" style="color:#94a3b8;margin-right:4px;"></i> Tanggal
                    </label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}"
                        style="width:100%;height:42px;padding:0 12px;font-size:13px;border:1.5px solid #cbd5e1;border-radius:10px;background:#fff;color:#1e293b;outline:none;box-sizing:border-box;">
                </div>
                <div style="flex:1;min-width:150px;">
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:.03em;">
                        <i class="fa-solid fa-filter" style="color:#94a3b8;margin-right:4px;"></i> Status
                    </label>
                    <select name="status" style="width:100%;height:42px;padding:0 12px;font-size:13px;border:1.5px solid #cbd5e1;border-radius:10px;background:#fff;color:#1e293b;outline:none;box-sizing:border-box;">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="masuk" {{ $status === 'masuk' ? 'selected' : '' }}>Sedang Parkir</option>
                        <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ $status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div style="flex:0 0 auto;display:flex;gap:8px;align-items:flex-end;">
                    <button type="submit"
                        style="height:42px;padding:0 20px;display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:700;background:#0E5423;color:white;border:none;border-radius:10px;cursor:pointer;">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- Tabel Sesi Parkir --}}
    <section class="fr-card fr-reveal">
        <header class="fr-card-head">
            <div class="fr-card-title-row">
                <span class="fr-card-icon"><i class="fa-solid fa-list-check"></i></span>
                <div>
                    <h2 class="fr-card-title">Riwayat Sesi Parkir</h2>
                    <p class="fr-card-subtitle">
                        Tarif: Motor {{ $rupiah($tarif['motor']) }} • Mobil {{ $rupiah($tarif['mobil']) }} — via QRIS Statis / Tunai
                    </p>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div style="margin:16px 24px 0;padding:12px 18px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;color:#065f46;font-size:13px;font-weight:600;">
                <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i> {{ session('success') }}
            </div>
        @endif

        <div class="fr-table-wrap">
            <table class="fr-table">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th style="width:120px">Waktu Masuk</th>
                        <th style="width:120px">Plat Nomor</th>
                        <th style="width:110px">Jenis</th>
                        <th style="width:110px">Metode</th>
                        <th style="width:110px">Status</th>
                        <th style="width:130px;text-align:right">Nominal</th>
                        <th style="width:120px">Waktu Keluar</th>
                        <th style="width:100px;text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesiList as $i => $sesi)
                        <tr>
                            <td>{{ $sesiList->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $sesi->waktu_masuk ? $sesi->waktu_masuk->format('H:i') : '-' }}</strong>
                                <div style="font-size:11px;color:#64748b;">{{ $sesi->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td>
                                @if($sesi->plat_nomor)
                                    <span style="font-family:monospace;font-weight:700;font-size:13px;background:#f1f5f9;padding:3px 8px;border-radius:6px;border:1px solid #e2e8f0;">
                                        {{ $sesi->plat_nomor }}
                                    </span>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">Tidak dicatat</span>
                                @endif
                            </td>
                            <td>
                                @if($sesi->jenis_kendaraan === 'motor')
                                    <span style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:600;">
                                        <i class="fa-solid fa-motorcycle"></i> Motor
                                    </span>
                                @elseif($sesi->jenis_kendaraan === 'mobil')
                                    <span style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:600;">
                                        <i class="fa-solid fa-car-side"></i> Mobil
                                    </span>
                                @else
                                    <span style="background:#f8fafc;color:#475569;border:1px solid #e2e8f0;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:600;">
                                        Lainnya
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($sesi->metode_pembayaran === 'qris')
                                    <span style="background:#faf5ff;color:#6d28d9;border:1px solid #ddd6fe;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:600;">
                                        <i class="fa-solid fa-qrcode"></i> QRIS
                                    </span>
                                @else
                                    <span style="background:#fef9c3;color:#854d0e;border:1px solid #fde68a;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:600;">
                                        <i class="fa-solid fa-money-bill"></i> Tunai
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($sesi->status === 'masuk')
                                    <span style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:600;">
                                        <i class="fa-solid fa-circle-dot"></i> Aktif
                                    </span>
                                @elseif($sesi->status === 'selesai')
                                    <span style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:600;">
                                        <i class="fa-solid fa-circle-check"></i> Selesai
                                    </span>
                                @else
                                    <span style="background:#f8fafc;color:#94a3b8;border:1px solid #e2e8f0;padding:3px 8px;border-radius:10px;font-size:11px;font-weight:600;">
                                        <i class="fa-solid fa-ban"></i> Batal
                                    </span>
                                @endif
                            </td>
                            <td style="text-align:right;font-weight:700;color:{{ $sesi->status === 'selesai' ? '#047857' : '#94a3b8' }};">
                                {{ $rupiah($sesi->nominal) }}
                            </td>
                            <td style="text-align:center;">
                                @if($sesi->waktu_keluar)
                                    <strong style="font-size:12px;color:#475569;">{{ $sesi->waktu_keluar->format('H:i') }}</strong>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">—</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($sesi->status === 'masuk')
                                    <div style="display:flex;gap:4px;justify-content:center;">
                                        <form method="POST" action="{{ route('admin.parkir.selesai', $sesi) }}" style="margin:0;">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="Konfirmasi Lunas & Selesai"
                                                style="height:32px;width:32px;display:inline-flex;align-items:center;justify-content:center;background:#059669;color:white;border:none;border-radius:8px;cursor:pointer;font-size:13px;">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.parkir.batal', $sesi) }}" style="margin:0;"
                                            onsubmit="return confirm('Batalkan sesi parkir ini?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="Batalkan"
                                                style="height:32px;width:32px;display:inline-flex;align-items:center;justify-content:center;background:#dc2626;color:white;border:none;border-radius:8px;cursor:pointer;font-size:13px;">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span style="color:#cbd5e1;font-size:12px;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:40px;color:#64748b;">
                                <i class="fa-solid fa-square-parking" style="font-size:36px;color:#cbd5e1;display:block;margin-bottom:10px;"></i>
                                Belum ada transaksi parkir untuk tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sesiList->hasPages())
            <div style="padding:16px 24px;border-top:1px solid #e2e8f0;">
                {{ $sesiList->links() }}
            </div>
        @endif
    </section>
</div>
@endsection

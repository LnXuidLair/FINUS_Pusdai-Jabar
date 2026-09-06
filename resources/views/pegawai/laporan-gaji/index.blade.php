@extends('layouts.app')
@section('title', 'Laporan Gaji')
@section('hide-page-header', '1')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
    $gajiItems = $laporanGaji instanceof \Illuminate\Contracts\Pagination\Paginator ? collect($laporanGaji->items()) : collect($laporanGaji);
    $jumlahData = method_exists($laporanGaji, 'total') ? $laporanGaji->total() : $gajiItems->count();
    $sudahDibayar = $gajiItems->where('status_penggajian', 'sudah_dibayar')->count();
@endphp
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
            <div><h1>Laporan Gaji Pegawai</h1><p>Informasi gaji {{ $pegawai->nama_pegawai ?? auth()->user()->name }} berdasarkan presensi yang telah disetujui admin.</p></div>
        </div>
        <div class="fmu-hero-actions"><span class="fmu-hero-badge"><i class="fa-solid fa-briefcase"></i>{{ $pegawai->jabatan ?? 'Pegawai' }}</span></div>
    </section>

    <section class="fmu-grid fmu-grid-3 mb-3">
        <article class="fmu-stat" style="--fmu-stat-color:#2563EB;--fmu-stat-soft:#EEF4FF"><span class="fmu-stat-icon"><i class="fa-solid fa-wallet"></i></span><div class="fmu-stat-copy"><small>Total Gaji Sudah Dibayar</small><strong>{{ $rupiah($totalGaji ?? 0) }}</strong></div></article>
        <article class="fmu-stat" style="--fmu-stat-color:#179B40;--fmu-stat-soft:#EAF8EE"><span class="fmu-stat-icon"><i class="fa-solid fa-money-bill-wave"></i></span><div class="fmu-stat-copy"><small>Gaji Harian Terakhir</small><strong>{{ $rupiah($gajiTerakhir?->gaji_perhari ?? 0) }}</strong></div></article>
        <article class="fmu-stat" style="--fmu-stat-color:#7C3AED;--fmu-stat-soft:#F5F0FF"><span class="fmu-stat-icon"><i class="fa-solid fa-calendar-check"></i></span><div class="fmu-stat-copy"><small>Kehadiran ACC Terakhir</small><strong>{{ number_format($gajiTerakhir?->jumlah_kehadiran ?? 0,0,',','.') }} hari</strong></div></article>
    </section>

    <section class="fmu-card mb-3">
        <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-filter"></i></span><div><h2>Filter Periode</h2><p>Pilih bulan tertentu atau tampilkan seluruh riwayat penggajian.</p></div></div></div>
        <form method="GET" action="{{ route('pegawai.laporan-gaji.index') }}">
            <div class="fmu-card-body"><div class="fmu-form-grid"><div class="fmu-field"><label class="fmu-label" for="bulan">Bulan laporan</label><div class="fmu-input-icon-wrap"><i class="fa-solid fa-calendar-days"></i><input type="month" name="bulan" id="bulan" value="{{ $bulan }}" class="fmu-control"></div></div></div></div>
            <div class="fmu-actions"><a href="{{ route('pegawai.laporan-gaji.index') }}" class="fmu-btn"><i class="fa-solid fa-rotate-left"></i>Reset</a><button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-magnifying-glass"></i>Tampilkan</button></div>
        </form>
    </section>

    <section class="fmu-card">
        <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-clock-rotate-left"></i></span><div><h2>Riwayat Penggajian</h2><p>Sebelum dibayar, yang ditampilkan hanya gaji harian dan kehadiran ACC. Slip tersedia setelah pembayaran.</p></div></div></div>
        <div class="fmu-table-wrap">
            <table class="fmu-table">
                <thead><tr><th>Periode</th><th>Gaji / Hari</th><th>Kehadiran ACC</th><th>Total Gaji</th><th>Status</th><th>Tanggal Bayar</th><th>Slip</th></tr></thead>
                <tbody>
                @forelse($laporanGaji as $gaji)
                    @php
                        $isPaid = $gaji->status_penggajian === 'sudah_dibayar';
                        $periodeText = \Carbon\Carbon::createFromFormat('Y-m-d', $gaji->periode . '-01')->translatedFormat('F Y');
                    @endphp
                    <tr>
                        <td class="font-weight-bold">{{ $periodeText }}</td>
                        <td>{{ $rupiah($gaji->gaji_perhari ?? 0) }}</td>
                        <td><strong>{{ number_format($gaji->jumlah_kehadiran ?? 0,0,',','.') }} hari</strong></td>
                        <td>
                            @if($isPaid)<strong style="color:#179B40">{{ $rupiah($gaji->total_gaji ?? 0) }}</strong>
                            @else<span class="text-muted">Tersedia setelah pembayaran</span>@endif
                        </td>
                        <td><span class="fmu-badge" style="--badge-color:{{ $isPaid ? '#179B40' : '#D97706' }};--badge-soft:{{ $isPaid ? '#EAF8EE' : '#FFF7E6' }}">{{ $isPaid ? 'Sudah Dibayar' : 'Belum Dibayar' }}</span></td>
                        <td>{{ $isPaid && $gaji->tanggal ? \Carbon\Carbon::parse($gaji->tanggal)->translatedFormat('d F Y') : '-' }}</td>
                        <td>
                            @if($isPaid)
                                <a href="{{ route('pegawai.laporan-gaji.slip', $gaji) }}" class="fmu-btn fmu-btn-primary" style="min-height:36px;padding-inline:11px"><i class="fa-solid fa-download"></i>Download Slip</a>
                            @else
                                <span class="text-muted" style="font-size:12px">Slip belum tersedia</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="fmu-empty"><i class="fa-regular fa-folder-open"></i>Belum ada data penggajian.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($laporanGaji, 'links'))<div class="fmu-card-body pt-3">{{ $laporanGaji->links() }}</div>@endif
    </section>
</div>
@endsection

{{-- FINUS DARK MODE LOCAL: pegawai/laporan-gaji/index.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="pegawai/laporan-gaji/index.blade.php">
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-body,.fr-card-body) { background:transparent !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-icon,.fr-card-icon,.fmu-stat-icon,.fr-stat-icon) { box-shadow:inset 0 1px 0 rgba(255,255,255,.025) !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-hero-badge,.fr-hero-badge) { border-color:rgba(255,255,255,.18) !important; background:rgba(4,35,15,.36) !important; color:#F5FFF7 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-side-note,.fr-tip-item,.fr-breakdown-item) { border-color:#293D31 !important; background:#101B14 !important; }
</style>
@endpush


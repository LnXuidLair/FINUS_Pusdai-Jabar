@extends('layouts.app')
@section('title', 'Laporan Gaji')
@section('hide-page-header', '1')

@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
    $gajiItems = $laporanGaji instanceof \Illuminate\Contracts\Pagination\Paginator
        ? collect($laporanGaji->items())
        : collect($laporanGaji);
    $jumlahData = method_exists($laporanGaji, 'total') ? $laporanGaji->total() : $gajiItems->count();
    $sudahDibayar = $gajiItems->filter(fn ($item) => in_array(
        strtolower(str_replace([' ', '-'], '_', (string) ($item->status_penggajian ?? ''))),
        ['sudah_dibayar', 'dibayar', 'selesai'], true
    ))->count();
@endphp

@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
            <div>
                <h1>Laporan Gaji Pegawai</h1>
                <p>Riwayat penggajian {{ $pegawai->nama_pegawai ?? auth()->user()->name }} yang telah diproses melalui FINUS.</p>
            </div>
        </div>
        <div class="fmu-hero-actions">
            <span class="fmu-hero-badge"><i class="fa-solid fa-briefcase"></i>{{ $pegawai->jabatan ?? 'Pegawai' }}</span>
        </div>
    </section>

    <section class="fmu-grid fmu-grid-3 mb-3">
        <article class="fmu-stat" style="--fmu-stat-color:#2563EB;--fmu-stat-soft:#EEF4FF">
            <span class="fmu-stat-icon"><i class="fa-solid fa-wallet"></i></span>
            <div class="fmu-stat-copy"><small>Total Gaji Tercatat</small><strong>{{ $rupiah($totalGaji ?? 0) }}</strong></div>
        </article>
        <article class="fmu-stat" style="--fmu-stat-color:#179B40;--fmu-stat-soft:#EAF8EE">
            <span class="fmu-stat-icon"><i class="fa-solid fa-money-check-dollar"></i></span>
            <div class="fmu-stat-copy"><small>Gaji Terakhir</small><strong>{{ $rupiah($gajiTerakhir->total_gaji ?? 0) }}</strong></div>
        </article>
        <article class="fmu-stat" style="--fmu-stat-color:#7C3AED;--fmu-stat-soft:#F5F0FF">
            <span class="fmu-stat-icon"><i class="fa-solid fa-receipt"></i></span>
            <div class="fmu-stat-copy"><small>Data / Sudah Dibayar</small><strong>{{ number_format($jumlahData, 0, ',', '.') }} / {{ number_format($sudahDibayar, 0, ',', '.') }}</strong></div>
        </article>
    </section>

    <section class="fmu-card mb-3">
        <div class="fmu-card-head">
            <div class="fmu-card-head-main">
                <span class="fmu-card-icon"><i class="fa-solid fa-filter"></i></span>
                <div><h2>Filter Periode</h2><p>Pilih bulan tertentu atau tampilkan seluruh riwayat penggajian.</p></div>
            </div>
        </div>
        <form method="GET" action="{{ route('pegawai.laporan-gaji.index') }}">
            <div class="fmu-card-body">
                <div class="fmu-form-grid">
                    <div class="fmu-field">
                        <label class="fmu-label" for="bulan">Bulan laporan</label>
                        <div class="fmu-input-icon-wrap">
                            <i class="fa-solid fa-calendar-days"></i>
                            <input type="month" name="bulan" id="bulan" value="{{ $bulan }}" class="fmu-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="fmu-actions">
                <a href="{{ route('pegawai.laporan-gaji.index') }}" class="fmu-btn"><i class="fa-solid fa-rotate-left"></i>Reset</a>
                <button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-magnifying-glass"></i>Tampilkan</button>
            </div>
        </form>
    </section>

    <section class="fmu-card">
        <div class="fmu-card-head">
            <div class="fmu-card-head-main">
                <span class="fmu-card-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                <div><h2>Riwayat Penggajian</h2><p>Daftar gaji yang tercatat pada akun pegawai.</p></div>
            </div>
        </div>
        <div class="fmu-table-wrap">
            <table class="fmu-table">
                <thead><tr><th>Tanggal</th><th>Periode</th><th>Status</th><th class="text-right">Total Gaji</th></tr></thead>
                <tbody>
                @forelse($laporanGaji as $gaji)
                    @php
                        $statusRaw = strtolower(str_replace([' ', '-'], '_', (string) ($gaji->status_penggajian ?? 'pending')));
                        $isPaid = in_array($statusRaw, ['sudah_dibayar','dibayar','selesai'], true);
                    @endphp
                    <tr>
                        <td>{{ $gaji->tanggal ? \Carbon\Carbon::parse($gaji->tanggal)->translatedFormat('d F Y') : '-' }}</td>
                        <td>{{ trim(($gaji->bulan ?? '-') . ' ' . ($gaji->tahun ?? '')) }}</td>
                        <td><span class="fmu-badge" style="--badge-color:{{ $isPaid ? '#179B40' : '#D97706' }};--badge-soft:{{ $isPaid ? '#EAF8EE' : '#FFF7E6' }}">{{ ucwords(str_replace('_',' ', $statusRaw)) }}</span></td>
                        <td class="text-right font-weight-bold">{{ $rupiah($gaji->total_gaji ?? 0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="fmu-empty"><i class="fa-regular fa-folder-open"></i>Belum ada data penggajian.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($laporanGaji, 'links'))
            <div class="fmu-card-body pt-3">{{ $laporanGaji->links() }}</div>
        @endif
    </section>
</div>
@endsection
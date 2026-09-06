@extends('layouts.app')
@section('title', 'Presensi Saya')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-calendar-check"></i></span>
            <div><h1>Presensi Saya</h1><p>Pantau riwayat kehadiran dan status persetujuan admin.</p></div>
        </div>
        <div class="fmu-hero-actions"><a href="{{ route('pegawai.presensi.create') }}" class="fmu-btn" style="background:#fff;color:#0E5423!important"><i class="fa-solid fa-plus"></i>Isi Presensi</a></div>
    </section>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <section class="fmu-grid fmu-grid-4 mb-3">
        <article class="fmu-stat" style="--fmu-stat-color:#2563EB;--fmu-stat-soft:#EEF4FF"><span class="fmu-stat-icon"><i class="fa-solid fa-list-check"></i></span><div class="fmu-stat-copy"><small>Total Presensi</small><strong>{{ number_format($totalPresensi,0,',','.') }}</strong></div></article>
        <article class="fmu-stat" style="--fmu-stat-color:#179B40;--fmu-stat-soft:#EAF8EE"><span class="fmu-stat-icon"><i class="fa-solid fa-calendar-check"></i></span><div class="fmu-stat-copy"><small>Hari Hadir Di-ACC</small><strong>{{ number_format($totalHadirDisetujui,0,',','.') }}</strong></div></article>
        <article class="fmu-stat" style="--fmu-stat-color:#7C3AED;--fmu-stat-soft:#F5F0FF"><span class="fmu-stat-icon"><i class="fa-solid fa-check-double"></i></span><div class="fmu-stat-copy"><small>Total Disetujui</small><strong>{{ number_format($totalDisetujui,0,',','.') }}</strong></div></article>
        <article class="fmu-stat" style="--fmu-stat-color:#D97706;--fmu-stat-soft:#FFF7E6"><span class="fmu-stat-icon"><i class="fa-solid fa-clock"></i></span><div class="fmu-stat-copy"><small>Menunggu Approval</small><strong>{{ number_format($totalMenunggu,0,',','.') }}</strong></div></article>
    </section>

    <section class="fmu-card">
        <div class="fmu-card-head">
            <div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-clock-rotate-left"></i></span><div><h2>Riwayat Presensi</h2><p>Hanya status Hadir yang sudah disetujui yang dihitung sebagai hari gaji.</p></div></div>
            <div style="width:min(100%,310px)"><div class="fmu-input-icon-wrap"><i class="fa-solid fa-magnifying-glass"></i><input id="staffAttendanceSearch" type="search" class="fmu-control" placeholder="Cari tanggal, status, approval..."></div></div>
        </div>
        <div class="fmu-table-wrap">
            <table class="fmu-table">
                <thead><tr><th style="width:65px">No</th><th>Hari / Tanggal</th><th>Status</th><th>Approval</th><th>Keterangan</th><th>Bukti</th></tr></thead>
                <tbody>
                @forelse($presensis as $item)
                    @php
                        $status = strtolower(trim((string) $item->status));
                        $approved = (bool) $item->is_approved;
                        $tanggalText = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') : '-';
                        $badgeColor = $status === 'hadir' ? '#179B40' : ($status === 'izin' ? '#D97706' : '#7C3AED');
                        $badgeSoft = $status === 'hadir' ? '#EAF8EE' : ($status === 'izin' ? '#FFF7E6' : '#F5F0FF');
                    @endphp
                    <tr data-attendance-row data-search="{{ $tanggalText }}|{{ $status }}|{{ $approved ? 'disetujui' : 'menunggu' }}|{{ $item->keterangan ?? '-' }}">
                        <td data-row-number>{{ $loop->iteration }}</td>
                        <td class="font-weight-bold">{{ $tanggalText }}</td>
                        <td><span class="fmu-badge" style="--badge-color:{{ $badgeColor }};--badge-soft:{{ $badgeSoft }}">{{ ucfirst($status) }}</span></td>
                        <td>
                            <span class="fmu-badge" style="--badge-color:{{ $approved ? '#179B40' : '#D97706' }};--badge-soft:{{ $approved ? '#EAF8EE' : '#FFF7E6' }}">
                                <i class="fa-solid {{ $approved ? 'fa-check' : 'fa-clock' }}"></i>{{ $approved ? 'Disetujui' : 'Menunggu' }}
                            </span>
                        </td>
                        <td>{{ $item->keterangan ?: '-' }}</td>
                        <td>
                            @if($item->bukti_kehadiran)
                                <a href="{{ asset('storage/' . $item->bukti_kehadiran) }}" target="_blank" rel="noopener" class="fmu-btn" style="min-height:36px;padding-inline:11px"><i class="fa-solid fa-eye"></i>Lihat</a>
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="fmu-empty"><i class="fa-regular fa-folder-open"></i>Belum ada riwayat presensi.</td></tr>
                @endforelse
                <tr id="staffAttendanceEmpty" style="display:none"><td colspan="6" class="fmu-empty"><i class="fa-solid fa-magnifying-glass"></i>Data tidak ditemukan.</td></tr>
                </tbody>
            </table>
        </div>
        @if(method_exists($presensis, 'links'))<div class="fmu-card-body pt-3">{{ $presensis->links() }}</div>@endif
    </section>
</div>
@endsection
@push('scripts')
<script>
(() => {
    const input = document.getElementById('staffAttendanceSearch');
    const rows = Array.from(document.querySelectorAll('[data-attendance-row]'));
    const empty = document.getElementById('staffAttendanceEmpty');
    const normalize = value => (value || '').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim();
    const filter = () => {
        const keyword = normalize(input?.value);
        let visible = 0;
        rows.forEach(row => {
            const values = (row.dataset.search || '').split('|').map(normalize);
            const match = !keyword || values.some(value => value.startsWith(keyword));
            row.style.display = match ? '' : 'none';
            if (match) { visible++; const number = row.querySelector('[data-row-number]'); if (number) number.textContent = visible; }
        });
        if (empty) empty.style.display = rows.length && !visible ? '' : 'none';
    };
    input?.addEventListener('input', filter);
    filter();
})();
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: pegawai/presensi/index.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="pegawai/presensi/index.blade.php">
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-body,.fr-card-body) { background:transparent !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-icon,.fr-card-icon,.fmu-stat-icon,.fr-stat-icon) { box-shadow:inset 0 1px 0 rgba(255,255,255,.025) !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-hero-badge,.fr-hero-badge) { border-color:rgba(255,255,255,.18) !important; background:rgba(4,35,15,.36) !important; color:#F5FFF7 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-side-note,.fr-tip-item,.fr-breakdown-item) { border-color:#293D31 !important; background:#101B14 !important; }
</style>
@endpush


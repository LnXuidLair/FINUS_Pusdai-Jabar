@extends('layouts.app')
@section('title', 'Presensi Saya')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-calendar-check"></i></span>
            <div><h1>Presensi Saya</h1><p>Pantau presensi datang–pulang, kondisi kehadiran, dan status persetujuan admin.</p></div>
        </div>
        <div class="fmu-hero-actions">
            <span class="fmu-hero-badge"><i class="fa-solid fa-calendar-day"></i>{{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </section>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="alert alert-info" style="border-radius:14px;font-size:13px">
        <i class="fa-solid fa-circle-info"></i>
        <strong>Jadwal:</strong>
        Datang {{ $schedule['datang']['start'] }}–{{ $schedule['datang']['end'] }} •
        Pulang normal {{ $schedule['pulang']['start'] }}–{{ $schedule['pulang']['end'] }} •
        Lembur {{ $schedule['lembur']['start'] }}–{{ $schedule['lembur']['end'] }}.
        Jika harus pulang lebih awal atau meninggalkan lokasi karena tugas/panggilan resmi, gunakan aksi khusus setelah presensi datang.
    </div>

    @if($todayPresensi)
        <section class="fmu-card mb-3">
            <div class="fmu-card-head">
                <div class="fmu-card-head-main">
                    <span class="fmu-card-icon"><i class="fa-solid fa-calendar-day"></i></span>
                    <div><h2>Status Hari Ini</h2><p>{{ now()->translatedFormat('l, d F Y') }}</p></div>
                </div>
            </div>
            <div class="fmu-card-body">
                <div class="fmu-grid fmu-grid-4">
                    <article class="fmu-stat" style="--fmu-stat-color:#2563EB;--fmu-stat-soft:#EEF4FF">
                        <span class="fmu-stat-icon"><i class="fa-solid fa-user-check"></i></span>
                        <div class="fmu-stat-copy"><small>Status</small><strong>{{ ucfirst($todayPresensi->status) }}</strong></div>
                    </article>
                    <article class="fmu-stat" style="--fmu-stat-color:#179B40;--fmu-stat-soft:#EAF8EE">
                        <span class="fmu-stat-icon"><i class="fa-solid fa-right-to-bracket"></i></span>
                        <div class="fmu-stat-copy"><small>Datang</small><strong>{{ $todayPresensi->jam_datang ? substr($todayPresensi->jam_datang,0,5) : '-' }}</strong></div>
                    </article>
                    <article class="fmu-stat" style="--fmu-stat-color:#7C3AED;--fmu-stat-soft:#F5F0FF">
                        <span class="fmu-stat-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                        <div class="fmu-stat-copy"><small>Pulang</small><strong>{{ $todayPresensi->jam_pulang ? substr($todayPresensi->jam_pulang,0,5) : '-' }}</strong></div>
                    </article>
                    <article class="fmu-stat" style="--fmu-stat-color:#D97706;--fmu-stat-soft:#FFF7E6">
                        <span class="fmu-stat-icon"><i class="fa-solid fa-circle-check"></i></span>
                        <div class="fmu-stat-copy">
                            <small>Approval</small>
                            <strong>{{ $todayPresensi->is_approved ? 'Di-ACC' : ($todayPresensi->isComplete() ? 'Menunggu' : 'Belum Lengkap') }}</strong>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    @endif

    <section class="fmu-card mb-3">
        <div class="fmu-card-head">
            <div class="fmu-card-head-main">
                <span class="fmu-card-icon"><i class="fa-solid fa-bolt"></i></span>
                <div><h2>Aksi Presensi Saat Ini</h2><p>Aksi hanya muncul jika sesuai dengan waktu dan kondisi presensi hari ini.</p></div>
            </div>
        </div>
        <div class="fmu-card-body">
            @if(count($availableActions))
                <div class="fmu-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px">
                    @foreach($availableActions as $key => $action)
                        <a href="{{ route('pegawai.presensi.create', ['aksi' => $key]) }}" class="fmu-btn" style="min-height:74px;justify-content:flex-start;text-align:left;text-decoration:none">
                            <i class="fa-solid {{ $action['icon'] }}" style="font-size:18px"></i>
                            <span>
                                <strong style="display:block">{{ $action['label'] }}</strong>
                                <small style="white-space:normal">{{ $action['description'] }}</small>
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="fmu-empty" style="padding:24px">
                    <i class="fa-solid fa-lock"></i>
                    Tidak ada aksi presensi yang tersedia saat ini. Jika lupa presensi, serahkan bukti kepada Admin untuk dikoreksi.
                </div>
            @endif
        </div>
    </section>

    <section class="fmu-grid fmu-grid-4 mb-3">
        <article class="fmu-stat" style="--fmu-stat-color:#2563EB;--fmu-stat-soft:#EEF4FF"><span class="fmu-stat-icon"><i class="fa-solid fa-list-check"></i></span><div class="fmu-stat-copy"><small>Total Hari Tercatat</small><strong>{{ number_format($totalPresensi,0,',','.') }}</strong></div></article>
        <article class="fmu-stat" style="--fmu-stat-color:#179B40;--fmu-stat-soft:#EAF8EE"><span class="fmu-stat-icon"><i class="fa-solid fa-calendar-check"></i></span><div class="fmu-stat-copy"><small>Hadir Terhitung Gaji</small><strong>{{ number_format($totalHadirDisetujui,0,',','.') }}</strong></div></article>
        <article class="fmu-stat" style="--fmu-stat-color:#7C3AED;--fmu-stat-soft:#F5F0FF"><span class="fmu-stat-icon"><i class="fa-solid fa-check-double"></i></span><div class="fmu-stat-copy"><small>Total Disetujui</small><strong>{{ number_format($totalDisetujui,0,',','.') }}</strong></div></article>
        <article class="fmu-stat" style="--fmu-stat-color:#D97706;--fmu-stat-soft:#FFF7E6"><span class="fmu-stat-icon"><i class="fa-solid fa-clock"></i></span><div class="fmu-stat-copy"><small>Menunggu Approval</small><strong>{{ number_format($totalMenunggu,0,',','.') }}</strong></div></article>
    </section>

    <section class="fmu-card">
        <div class="fmu-card-head">
            <div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-clock-rotate-left"></i></span><div><h2>Riwayat Presensi</h2><p>Presensi Hadir baru dihitung ke gaji setelah Datang dan Pulang lengkap serta sudah di-ACC.</p></div></div>
            <div style="width:min(100%,310px)"><div class="fmu-input-icon-wrap"><i class="fa-solid fa-magnifying-glass"></i><input id="staffAttendanceSearch" type="search" class="fmu-control" placeholder="Cari tanggal, status, kondisi..."></div></div>
        </div>
        <div class="fmu-table-wrap">
            <table class="fmu-table" style="min-width:1050px">
                <thead>
                    <tr>
                        <th style="width:65px">No</th>
                        <th>Hari / Tanggal</th>
                        <th>Status</th>
                        <th>Kondisi</th>
                        <th>Datang</th>
                        <th>Pulang</th>
                        <th>Approval</th>
                        <th>Keterangan</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($presensis as $item)
                    @php
                        $status = strtolower(trim((string) $item->status));
                        $approved = (bool) $item->is_approved;
                        $complete = $item->isComplete();
                        $tanggalText = $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') : '-';
                        $badgeColor = $status === 'hadir' ? '#179B40' : ($status === 'izin' ? '#2563EB' : ($status === 'sakit' ? '#D97706' : '#DC2626'));
                        $badgeSoft = $status === 'hadir' ? '#EAF8EE' : ($status === 'izin' ? '#EEF4FF' : ($status === 'sakit' ? '#FFF7E6' : '#FFF1F2'));
                        $approvalText = $approved ? 'Disetujui' : ($complete ? 'Menunggu' : 'Belum Lengkap');
                    @endphp
                    <tr data-attendance-row data-search="{{ $tanggalText }}|{{ $status }}|{{ $item->kondisiLabel() }}|{{ $approvalText }}|{{ $item->keterangan ?? '-' }}">
                        <td data-row-number>{{ $loop->iteration }}</td>
                        <td class="font-weight-bold">{{ $tanggalText }}</td>
                        <td><span class="fmu-badge" style="--badge-color:{{ $badgeColor }};--badge-soft:{{ $badgeSoft }}">{{ ucfirst($status) }}</span></td>
                        <td>{{ $status === 'hadir' ? $item->kondisiLabel() : '-' }}</td>
                        <td>
                            @if($status === 'hadir')
                                <strong>{{ $item->jam_datang ? substr($item->jam_datang,0,5) : '-' }}</strong>
                                @if($item->input_datang_role)<br><small>{{ ucfirst($item->input_datang_role) }}</small>@endif
                            @else - @endif
                        </td>
                        <td>
                            @if($status === 'hadir')
                                <strong>{{ $item->jam_pulang ? substr($item->jam_pulang,0,5) : '-' }}</strong>
                                @if($item->input_pulang_role)<br><small>{{ ucfirst($item->input_pulang_role) }}</small>@endif
                            @else - @endif
                        </td>
                        <td>
                            <span class="fmu-badge" style="--badge-color:{{ $approved ? '#179B40' : ($complete ? '#D97706' : '#64748B') }};--badge-soft:{{ $approved ? '#EAF8EE' : ($complete ? '#FFF7E6' : '#F1F5F9') }}">
                                <i class="fa-solid {{ $approved ? 'fa-check' : ($complete ? 'fa-clock' : 'fa-circle-exclamation') }}"></i>{{ $approvalText }}
                            </span>
                        </td>
                        <td>{{ $item->keterangan ?: '-' }}</td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:center">
                                @if($item->bukti_datang)
                                    <button type="button" class="fmu-btn" style="min-height:34px;padding-inline:9px" data-proof-preview data-proof-url="{{ route('pegawai.presensi.bukti', [$item,'datang']) }}" data-proof-title="Bukti Presensi Datang"><i class="fa-solid fa-eye"></i>Datang</button>
                                @endif
                                @if($item->bukti_pulang)
                                    <button type="button" class="fmu-btn" style="min-height:34px;padding-inline:9px" data-proof-preview data-proof-url="{{ route('pegawai.presensi.bukti', [$item,'pulang']) }}" data-proof-title="Bukti Presensi Pulang"><i class="fa-solid fa-eye"></i>Pulang</button>
                                @endif
                                @if($item->bukti_status)
                                    <button type="button" class="fmu-btn" style="min-height:34px;padding-inline:9px" data-proof-preview data-proof-url="{{ route('pegawai.presensi.bukti', [$item,'status']) }}" data-proof-title="Bukti Presensi"><i class="fa-solid fa-eye"></i>Bukti</button>
                                @endif
                                @if($item->bukti_kehadiran && !$item->bukti_datang && !$item->bukti_pulang && !$item->bukti_status)
                                    <button type="button" class="fmu-btn" style="min-height:34px;padding-inline:9px" data-proof-preview data-proof-url="{{ route('pegawai.presensi.bukti', [$item,'status']) }}" data-proof-title="Bukti Presensi Lama"><i class="fa-solid fa-clock-rotate-left"></i>Lama</button>
                                @endif
                                @if(!$item->bukti_datang && !$item->bukti_pulang && !$item->bukti_status && !$item->bukti_kehadiran)
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="fmu-empty"><i class="fa-regular fa-folder-open"></i>Belum ada riwayat presensi.</td></tr>
                @endforelse
                <tr id="staffAttendanceEmpty" style="display:none"><td colspan="9" class="fmu-empty"><i class="fa-solid fa-magnifying-glass"></i>Data tidak ditemukan.</td></tr>
                </tbody>
            </table>
        </div>
        @if(method_exists($presensis, 'links'))<div class="fmu-card-body pt-3">{{ $presensis->links() }}</div>@endif
    </section>
</div>

<div id="staffProofModal" class="finus-proof-modal" aria-hidden="true">
    <div class="finus-proof-modal__backdrop" data-proof-close></div>
    <div class="finus-proof-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="staffProofModalTitle">
        <div class="finus-proof-modal__header">
            <div>
                <h3 id="staffProofModalTitle">Bukti Presensi</h3>
                <p>Pratinjau bukti presensi tanpa meninggalkan halaman.</p>
            </div>
            <button type="button" class="finus-proof-modal__close" data-proof-close aria-label="Tutup pratinjau">&times;</button>
        </div>
        <div class="finus-proof-modal__body">
            <div id="staffProofLoading" class="finus-proof-modal__loading">
                <i class="fa-solid fa-spinner fa-spin"></i> Memuat bukti...
            </div>
            <img id="staffProofImage" src="" alt="Bukti presensi" hidden>
            <div id="staffProofError" class="finus-proof-modal__error" hidden>
                <i class="fa-solid fa-triangle-exclamation"></i> Bukti tidak dapat ditampilkan.
            </div>
        </div>
    </div>
</div>

<style>
.finus-proof-modal{position:fixed;inset:0;z-index:1300;display:none;align-items:center;justify-content:center;padding:20px}
.finus-proof-modal.is-open{display:flex}
.finus-proof-modal__backdrop{position:absolute;inset:0;background:rgba(3,20,9,.68);backdrop-filter:blur(5px)}
.finus-proof-modal__dialog{position:relative;z-index:1;width:min(760px,100%);max-height:min(88vh,900px);display:flex;flex-direction:column;overflow:hidden;border:1px solid #DDE8E0;border-radius:18px;background:#fff;box-shadow:0 24px 70px rgba(0,0,0,.28)}
.finus-proof-modal__header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:17px 18px;border-bottom:1px solid #E6EEE8}
.finus-proof-modal__header h3{margin:0;color:#123D20;font-size:18px;font-weight:800}
.finus-proof-modal__header p{margin:4px 0 0;color:#64748B;font-size:12px}
.finus-proof-modal__close{flex:0 0 auto;width:38px;height:38px;border:1px solid #D8E5DC;border-radius:11px;background:#F7FAF8;color:#31543A;font-size:26px;line-height:1;cursor:pointer}
.finus-proof-modal__body{min-height:240px;padding:16px;overflow:auto;background:#F6F9F7;text-align:center}
.finus-proof-modal__body img{display:block;max-width:100%;max-height:70vh;margin:auto;border-radius:12px;object-fit:contain;box-shadow:0 8px 24px rgba(0,0,0,.10)}
.finus-proof-modal__loading,.finus-proof-modal__error{display:flex;min-height:220px;align-items:center;justify-content:center;gap:9px;color:#52635A;font-size:14px}
.finus-proof-modal__error{color:#B42318}
body.finus-proof-modal-open{overflow:hidden}
html[data-finus-theme="dark"] .finus-proof-modal__dialog{border-color:#294032;background:#101B14;color:#F1F6F3}
html[data-finus-theme="dark"] .finus-proof-modal__header{border-color:#294032}
html[data-finus-theme="dark"] .finus-proof-modal__header h3{color:#F1F6F3}
html[data-finus-theme="dark"] .finus-proof-modal__header p{color:#9FB2A6}
html[data-finus-theme="dark"] .finus-proof-modal__close{border-color:#314A39;background:#18271D;color:#E8F5EC}
html[data-finus-theme="dark"] .finus-proof-modal__body{background:#0C1510}
@media(max-width:575.98px){.finus-proof-modal{padding:10px}.finus-proof-modal__dialog{max-height:92vh;border-radius:15px}.finus-proof-modal__header{padding:14px}.finus-proof-modal__body{padding:10px}.finus-proof-modal__body img{max-height:72vh}}
</style>

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
            if (match) {
                visible++;
                const number = row.querySelector('[data-row-number]');
                if (number) number.textContent = visible;
            }
        });
        if (empty) empty.style.display = rows.length && !visible ? '' : 'none';
    };
    input?.addEventListener('input', filter);
    filter();
})();

(() => {
    const modal = document.getElementById('staffProofModal');
    const image = document.getElementById('staffProofImage');
    const title = document.getElementById('staffProofModalTitle');
    const loading = document.getElementById('staffProofLoading');
    const error = document.getElementById('staffProofError');
    const previewButtons = document.querySelectorAll('[data-proof-preview]');
    const closeButtons = modal?.querySelectorAll('[data-proof-close]') || [];

    if (!modal || !image || !title || !loading || !error) return;

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('finus-proof-modal-open');
        image.removeAttribute('src');
        image.hidden = true;
        loading.hidden = false;
        error.hidden = true;
    };

    const openModal = button => {
        const url = button.dataset.proofUrl;
        if (!url) return;

        title.textContent = button.dataset.proofTitle || 'Bukti Presensi';
        loading.hidden = false;
        error.hidden = true;
        image.hidden = true;

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('finus-proof-modal-open');

        image.onload = () => {
            loading.hidden = true;
            error.hidden = true;
            image.hidden = false;
        };

        image.onerror = () => {
            loading.hidden = true;
            image.hidden = true;
            error.hidden = false;
        };

        image.src = url;
    };

    previewButtons.forEach(button => button.addEventListener('click', () => openModal(button)));
    closeButtons.forEach(button => button.addEventListener('click', closeModal));

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });
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
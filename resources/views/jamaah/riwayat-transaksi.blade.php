@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');

    $riwayatJenisFilterLabels = collect($jenisLabels)
        ->except(['fidyah', 'shadaqah', 'zakat_fitrah'])
        ->all();

    $summaryCards = [
        [
            'label' => 'Jumlah Transaksi',
            'value' => number_format($ringkasan['jumlah'], 0, ',', '.'),
            'icon'  => 'fa-receipt',
            'color' => '#179B40',
            'soft'  => '#EAF8EE',
        ],
        [
            'label' => 'Total Nominal',
            'value' => $rupiah($ringkasan['nominal']),
            'icon'  => 'fa-wallet',
            'color' => '#2563EB',
            'soft'  => '#EDF4FF',
        ],
    ];
@endphp

@include('layouts.partials.finus-ui')

<style>
    /* ── Status row highlight ── */
    .jt-row-diterima {
        border-left: 4px solid #22c55e;
        background: linear-gradient(90deg, #f0fdf4 0%, transparent 20%);
    }
    .jt-row-ditolak,
    .jt-row-dibatalkan {
        border-left: 4px solid #f87171;
        background: linear-gradient(90deg, #fff5f5 0%, transparent 20%);
    }
    .jt-row-pending {
        border-left: 4px solid #fbbf24;
        background: linear-gradient(90deg, #fffbeb 0%, transparent 20%);
    }
    .jt-row-pending-pay {
        border-left: 4px solid #3b82f6;
        background: linear-gradient(90deg, #eff6ff 0%, transparent 20%);
    }

    /* ── Status pill premium ── */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 11px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }
    .status-pill i { font-size: 11px; }

    .status-pill-diterima   { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .status-pill-ditolak    { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
    .status-pill-dibatalkan { background: #fce7f3; color: #9d174d; border: 1px solid #f9a8d4; }
    .status-pill-pending    { background: #fef9c3; color: #92400e; border: 1px solid #fde68a; }
    .status-pill-bayar      { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }

    /* ── Banner ringkasan berhasil / gagal ── */
    .rw-banner-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 18px;
    }
    .rw-banner {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 20px 22px;
        border-radius: 18px;
        border: 1.5px solid transparent;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease;
        cursor: pointer;
    }
    .rw-banner:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(0,0,0,.1);
        text-decoration: none;
    }
    .rw-banner-success {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-color: #86efac;
    }
    .rw-banner-danger {
        background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
        border-color: #fca5a5;
    }
    .rw-banner-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,.12);
    }
    .rw-banner-success .rw-banner-icon { background: linear-gradient(135deg, #16a34a, #22c55e); color: #fff; }
    .rw-banner-danger  .rw-banner-icon { background: linear-gradient(135deg, #dc2626, #ef4444); color: #fff; }
    .rw-banner-body { flex: 1; min-width: 0; }
    .rw-banner-label {
        font-size: 11px; font-weight: 800;
        text-transform: uppercase; letter-spacing: .07em; margin-bottom: 4px;
    }
    .rw-banner-success .rw-banner-label { color: #166534; }
    .rw-banner-danger  .rw-banner-label { color: #991b1b; }
    .rw-banner-amount { font-size: 20px; font-weight: 900; line-height: 1.1; }
    .rw-banner-success .rw-banner-amount { color: #15803d; }
    .rw-banner-danger  .rw-banner-amount { color: #b91c1c; }
    .rw-banner-meta { font-size: 11px; color: #6b7280; margin-top: 4px; }
    .rw-banner-arrow {
        font-size: 14px;
        flex-shrink: 0;
        opacity: .45;
        transition: opacity .2s, transform .2s;
    }
    .rw-banner:hover .rw-banner-arrow { opacity: 1; transform: translateX(3px); }
    .rw-banner-success .rw-banner-arrow { color: #15803d; }
    .rw-banner-danger  .rw-banner-arrow { color: #b91c1c; }

    /* ── Nominal di tabel ── */
    .rw-nominal-diterima { color: #15803d; font-weight: 800; }
    .rw-nominal-gagal    { color: #b91c1c; font-weight: 700; }
    .rw-nominal-pending  { color: #92400e; font-weight: 700; }

    /* ── Sub-keterangan di kolom nominal & referensi ── */
    .rw-sub { font-size: 10.5px; font-weight: 600; display: block; margin-top: 3px; }
    .rw-sub-ok  { color: #16a34a; }
    .rw-sub-bad { color: #ef4444; }

    /* ── Perbesar icon pada kartu stat riwayat ── */
    .jt-grid-2 .jt-stat .jt-icon {
        width: 64px;
        min-width: 64px;
        height: 64px;
        border-radius: 18px;
        font-size: 28px;
    }

    /* ── Alignment Header & Kolom Aksi ── */
    .jt-table thead tr th.jt-th-center,
    .jt-table thead tr th:last-child {
        text-align: center !important;
        vertical-align: middle !important;
    }
    .jt-table tbody tr td.jt-actions,
    .jt-table tbody tr td:last-child {
        text-align: center !important;
        vertical-align: middle !important;
    }
    .jt-action-group {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
    }
    .jt-action-none {
        display: inline-block;
        color: #9ca3af;
        font-size: 14px;
        text-align: center;
    }

    @media (max-width: 640px) {
        .rw-banner-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="jt-page">
    <section class="jt-heading">
        <div class="jt-heading-main">
            <span class="jt-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
            <div>
                <div class="jt-eyebrow">Transaksi Pribadi</div>
                <h1>Riwayat Transaksi</h1>
                <p>Lihat status dan seluruh transaksi ZISWAF milik {{ $jamaah->name }}.</p>
            </div>
        </div>

        <div class="jt-heading-actions">
            <a class="jt-btn jt-btn-soft" href="{{ route('jamaah.laporan.index') }}">
                <i class="fa-solid fa-chart-column"></i>
                Buka Laporan
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="jt-alert">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="jt-alert jt-alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ session('warning') }}
        </div>
    @endif

    @if($errors->any())
        <div class="jt-alert jt-alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <section class="jt-grid jt-grid-2">
        @foreach($summaryCards as $card)
            <article
                class="jt-card jt-stat"
                style="--stat-color:{{ $card['color'] }};--stat-soft:{{ $card['soft'] }}"
            >
                <div class="jt-stat-top">
                    <span class="jt-icon">
                        <i class="fa-solid {{ $card['icon'] }}"></i>
                    </span>
                </div>

                <div class="jt-stat-body">
                    <span>{{ $card['label'] }}</span>
                    <strong>{{ $card['value'] }}</strong>
                </div>
            </article>
        @endforeach
    </section>

    @php
        $jumlahDiterima = $transaksi->where('status_verifikasi', 'diterima')->count();
    @endphp

    <div class="rw-banner-grid">
        <a href="{{ route('jamaah.riwayat.index', array_merge(request()->query(), ['status' => 'diterima'])) }}"
           class="rw-banner rw-banner-success">
            <div class="rw-banner-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="rw-banner-body">
                <div class="rw-banner-label">Transaksi Berhasil</div>
                <div class="rw-banner-amount">{{ $rupiah($ringkasan['diterima']) }}</div>
                <div class="rw-banner-meta">{{ $jumlahDiterima }} transaksi diterima &middot; Klik untuk filter</div>
            </div>
            <i class="fa-solid fa-arrow-right rw-banner-arrow"></i>
        </a>

        <a href="{{ route('jamaah.riwayat.index', array_merge(request()->query(), ['status' => 'dibatalkan'])) }}"
           class="rw-banner rw-banner-danger">
            <div class="rw-banner-icon">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="rw-banner-body">
                <div class="rw-banner-label">Transaksi Gagal / Dibatalkan</div>
                <div class="rw-banner-amount">{{ $rupiah($ringkasan['pending']) }}</div>
                <div class="rw-banner-meta">{{ $ringkasan['jumlah_gagal'] }} transaksi gagal &middot; Klik untuk filter</div>
            </div>
            <i class="fa-solid fa-arrow-right rw-banner-arrow"></i>
        </a>
    </div>

    <section class="jt-card jt-filter-card">
        <header class="jt-card-head">
            <div class="jt-title-row">
                <span class="jt-icon"><i class="fa-solid fa-filter"></i></span>
                <div>
                    <h2>Filter Riwayat</h2>
                    <p>Cari berdasarkan referensi, jenis, status, metode, atau tanggal.</p>
                </div>
            </div>
        </header>

        <div class="jt-card-body">
            <form method="GET" action="{{ route('jamaah.riwayat.index') }}" class="jt-filter" id="filterForm">
                <div class="jt-field jt-field-wide">
                    <label for="q">Pencarian</label>
                    <div class="jt-search-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input
                            id="q"
                            name="q"
                            class="jt-control"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="Cari referensi atau keterangan..."
                        >
                    </div>
                </div>

                <div class="jt-field">
                    <label for="jenis">Jenis ZISWAF</label>
                    <select id="jenis" name="jenis" class="jt-control">
                        <option value="">Semua jenis</option>
                        @foreach($riwayatJenisFilterLabels as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['jenis'] ?? '') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="jt-field">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="jt-control">
                        <option value="">Semua status</option>
                        @foreach($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="jt-field">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        class="jt-control"
                        value="{{ $filters['tanggal_mulai'] ?? '' }}"
                    >
                </div>

                <div class="jt-field">
                    <label for="tanggal_selesai">Tanggal Selesai</label>
                    <input
                        type="date"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        class="jt-control"
                        value="{{ $filters['tanggal_selesai'] ?? '' }}"
                    >
                </div>

                <div class="jt-filter-actions">
                    <button type="submit" class="jt-btn jt-btn-primary">
                        <i class="fa-solid fa-filter"></i>
                        Filter
                    </button>

                    <a href="{{ route('jamaah.riwayat.index') }}" class="jt-btn">
                        <i class="fa-solid fa-rotate-left"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="jt-card">
        <header class="jt-card-head">
            <div class="jt-title-row">
                <span class="jt-icon"><i class="fa-solid fa-list-check"></i></span>
                <div>
                    <h2>Daftar Transaksi</h2>
                    <p>Hanya transaksi dari akun jamaah yang sedang login.</p>
                </div>
            </div>
        </header>

        <div class="jt-table-wrap">
            <table class="jt-table">
                <thead>
                    <tr>
                        <th style="width:6px;padding:0;"></th>
                        <th>Referensi</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th class="jt-th-center" style="text-align: center !important;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transaksi as $item)
                        @php
                            $status     = $item->status_verifikasi ?: 'pending';
                            $referensi  = $item->order_id ?: 'ZSF-' . $item->id;
                            $isGagal    = in_array($status, ['ditolak', 'dibatalkan']);
                            $isBerhasil = $status === 'diterima';

                            $bisaBayar = $status === 'pending'
                                && $item->payment_gateway === 'midtrans'
                                && !empty($item->snap_token)
                                && in_array($item->payment_status, ['pending', null, ''], true);

                            $rowClass = match(true) {
                                $bisaBayar  => 'jt-row-pending-pay',
                                $isBerhasil => 'jt-row-diterima',
                                $isGagal    => 'jt-row-ditolak',
                                default     => 'jt-row-pending',
                            };

                            $pillConfig = match($status) {
                                'diterima'   => ['class' => 'status-pill-diterima',   'icon' => 'fa-circle-check',   'label' => 'Berhasil'],
                                'ditolak'    => ['class' => 'status-pill-ditolak',    'icon' => 'fa-circle-xmark',   'label' => 'Ditolak'],
                                'dibatalkan' => ['class' => 'status-pill-dibatalkan', 'icon' => 'fa-ban',            'label' => 'Dibatalkan'],
                                default      => $bisaBayar
                                    ? ['class' => 'status-pill-bayar',   'icon' => 'fa-clock',          'label' => 'Belum Dibayar']
                                    : ['class' => 'status-pill-pending', 'icon' => 'fa-hourglass-half', 'label' => 'Menunggu'],
                            };

                            $nominalClass = match(true) {
                                $isBerhasil => 'rw-nominal-diterima',
                                $isGagal    => 'rw-nominal-gagal',
                                default     => 'rw-nominal-pending',
                            };
                        @endphp

                        <tr class="{{ $rowClass }}" @if($bisaBayar) data-poll-row data-poll-id="{{ $item->id }}" data-poll-url="{{ route('jamaah.pembayaran.poll-status', $item) }}" @endif>
                            <td style="padding:0;width:6px;"></td>

                            <td class="jt-reference">
                                <span style="font-weight:700;font-size:12.5px;">{{ $referensi }}</span>
                                @if($item->catatan_verifikasi)
                                    <span class="rw-sub" style="color:#6b7280;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                                          title="{{ $item->catatan_verifikasi }}">
                                        {{ Str::limit($item->catatan_verifikasi, 40) }}
                                    </span>
                                @endif
                            </td>

                            <td style="white-space:nowrap;font-size:13px;">
                                {{ $item->tanggal?->format('d/m/Y') }}
                            </td>

                            <td>
                                <span class="jt-type">
                                    {{ $jenisLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf }}
                                </span>
                            </td>

                            <td class="jt-money {{ $nominalClass }}">
                                {{ $rupiah($item->nominal) }}
                                @if($isBerhasil)
                                    <span class="rw-sub rw-sub-ok">
                                        <i class="fa-solid fa-check" style="margin-right:2px;"></i>Tersalurkan
                                    </span>
                                @elseif($isGagal)
                                    <span class="rw-sub rw-sub-bad">
                                        <i class="fa-solid fa-triangle-exclamation" style="margin-right:2px;"></i>Tidak Tersalurkan
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="status-pill {{ $pillConfig['class'] }}" @if($bisaBayar) id="pill-{{ $item->id }}" @endif>
                                    <i class="fa-solid {{ $pillConfig['icon'] }}" @if($bisaBayar) id="pill-icon-{{ $item->id }}" @endif></i>
                                    <span @if($bisaBayar) id="pill-text-{{ $item->id }}" @endif>{{ $pillConfig['label'] }}</span>
                                </span>
                                @if($item->verified_at && $isBerhasil)
                                    <span class="rw-sub" style="color:#6b7280;">
                                        {{ $item->verified_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </td>

                            <td class="jt-actions">
                                @if($bisaBayar)
                                    <div class="jt-action-group">
                                        <a
                                            href="{{ route('jamaah.pembayaran.show', $item) }}"
                                            class="jt-btn jt-btn-pay-action"
                                            title="Lanjutkan Pembayaran"
                                        >
                                            <i class="fa-solid fa-credit-card"></i>
                                            <span>Bayar</span>
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('jamaah.pembayaran.batal', $item) }}"
                                            style="display:inline"
                                            onsubmit="return confirm('Yakin ingin membatalkan transaksi ini? Tindakan ini tidak dapat dibatalkan.')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="jt-btn jt-btn-cancel-action"
                                                title="Batalkan Pembayaran"
                                            >
                                                <i class="fa-solid fa-xmark"></i>
                                                <span>Batal</span>
                                            </button>
                                        </form>
                                    </div>
                                @elseif($isBerhasil)
                                    <div class="jt-action-group">
                                        <button
                                            type="button"
                                            class="jt-btn jt-btn-invoice"
                                            title="Lihat Invoice"
                                            data-invoice-url="{{ route('jamaah.riwayat.invoice', $item) }}"
                                            onclick="bukaInvoice(this)"
                                        >
                                            <i class="fa-solid fa-file-invoice"></i>
                                            <span>Invoice</span>
                                        </button>

                                        <a
                                            href="{{ route('jamaah.riwayat.invoice.cetak', $item) }}"
                                            target="_blank"
                                            class="jt-btn jt-btn-print"
                                            title="Cetak / Download PDF"
                                        >
                                            <i class="fa-solid fa-print"></i>
                                            <span>Cetak</span>
                                        </a>
                                    </div>
                                @else
                                    <span class="jt-action-none">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="jt-empty">
                                <i class="fa-regular fa-folder-open"></i>
                                Tidak ada transaksi yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksi->hasPages() || $transaksi->total() > 0)
            <div class="jt-pagination">
                <div class="jt-pagination-info">
                    <i class="fa-solid fa-circle-info"></i>
                    Menampilkan {{ $transaksi->firstItem() ?? 0 }}–{{ $transaksi->lastItem() ?? 0 }}
                    dari {{ $transaksi->total() }} transaksi.
                </div>

                {{ $transaksi->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </section>
</div>

{{-- ═══════════════════════════════════════════════
     INVOICE MODAL
═══════════════════════════════════════════════ --}}
<style>
    /* ── Invoice button styles ── */
    .jt-btn-invoice {
        background: linear-gradient(135deg, #065f46, #059669);
        color: #fff;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        transition: all .2s ease;
        text-decoration: none;
    }
    .jt-btn-invoice:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(6,95,70,.3);
        color: #fff;
    }
    .jt-btn-print {
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
        color: #fff;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 700;
        transition: all .2s ease;
        text-decoration: none;
    }
    .jt-btn-print:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29,78,216,.3);
        color: #fff;
        text-decoration: none;
    }

    /* ── Modal Overlay ── */
    .inv-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.55);
        backdrop-filter: blur(6px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .inv-overlay.active { display: flex; }

    /* ── Modal Box ── */
    .inv-modal {
        background: #fff;
        border-radius: 24px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 24px 64px rgba(0,0,0,.25);
        animation: invSlide .3s cubic-bezier(.16,1,.3,1) both;
        overflow: hidden;
        position: relative;
    }
    @keyframes invSlide {
        from { opacity:0; transform:translateY(24px) scale(.97); }
        to   { opacity:1; transform:translateY(0)   scale(1); }
    }

    /* ── Modal Header ── */
    .inv-head {
        background: linear-gradient(135deg, #065f46 0%, #059669 100%);
        padding: 28px 28px 20px;
        color: #fff;
        position: relative;
    }
    .inv-head-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .inv-lembaga-name {
        font-size: 16px;
        font-weight: 800;
        letter-spacing: .01em;
        margin: 0;
    }
    .inv-lembaga-sub {
        font-size: 11px;
        opacity: .8;
        margin-top: 2px;
    }
    .inv-close-btn {
        background: rgba(255,255,255,.18);
        border: none;
        color: #fff;
        width: 32px; height: 32px;
        border-radius: 50%;
        font-size: 16px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .2s;
        flex-shrink: 0;
    }
    .inv-close-btn:hover { background: rgba(255,255,255,.32); }
    .inv-ref-row {
        margin-top: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .inv-ref-badge {
        background: rgba(255,255,255,.15);
        border: 1.5px solid rgba(255,255,255,.3);
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .03em;
        font-family: monospace;
    }
    .inv-status-badge {
        background: #dcfce7;
        color: #15803d;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    /* ── Modal Body ── */
    .inv-body {
        padding: 24px 28px;
    }
    .inv-section-title {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 10px;
        margin-top: 18px;
    }
    .inv-section-title:first-child { margin-top: 0; }
    .inv-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        gap: 12px;
    }
    .inv-row:last-child { border-bottom: 0; }
    .inv-row-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        flex-shrink: 0;
    }
    .inv-row-value {
        font-size: 13px;
        color: #0f172a;
        font-weight: 700;
        text-align: right;
    }
    .inv-nominal-big {
        font-size: 28px;
        font-weight: 900;
        color: #065f46;
        display: block;
        margin: 12px 0 4px;
        letter-spacing: -.02em;
    }
    .inv-nominal-sub {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .inv-nominal-box {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1.5px solid #86efac;
        border-radius: 16px;
        padding: 16px 20px;
        text-align: center;
    }

    /* ── Modal Footer ── */
    .inv-footer {
        padding: 16px 28px 24px;
        display: flex;
        gap: 10px;
    }
    .inv-btn-print {
        flex: 1;
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: all .2s;
        text-decoration: none;
    }
    .inv-btn-print:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(29,78,216,.3);
        color: #fff;
        text-decoration: none;
    }
    .inv-btn-close {
        flex: 1;
        background: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: background .2s;
    }
    .inv-btn-close:hover { background: #e2e8f0; }

    /* ── Loading state ── */
    .inv-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        gap: 16px;
        color: #64748b;
    }
    .inv-loading i { font-size: 32px; color: #059669; }
    .inv-keterangan-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 12.5px;
        color: #475569;
        margin-top: 4px;
        font-style: italic;
    }
</style>

{{-- Overlay --}}
<div class="inv-overlay" id="invOverlay" onclick="tutupInvoice(event)">
    <div class="inv-modal" id="invModal">
        {{-- Konten akan diisi oleh JS --}}
        <div class="inv-loading" id="invLoading">
            <i class="fa-solid fa-circle-notch fa-spin"></i>
            <span>Memuat invoice...</span>
        </div>
        <div id="invContent" style="display:none;"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Kumpulkan semua baris transaksi yang masih menunggu pembayaran (bisaBayar)
    const pollRows = document.querySelectorAll('[data-poll-row]');

    if (pollRows.length === 0) return; // Tidak ada transaksi pending, tidak perlu polling

    const POLL_INTERVAL_MS = 7000; // Cek setiap 7 detik
    const MAX_POLLS = 60;          // Berhenti setelah ~7 menit
    let pollCount = 0;
    let activePolls = new Set();

    // Tandai semua pill sebagai "sedang dicek" dengan ikon spin
    pollRows.forEach(function (row) {
        const id = row.getAttribute('data-poll-id');
        const icon = document.getElementById('pill-icon-' + id);
        if (icon) {
            icon.classList.remove('fa-clock');
            icon.classList.add('fa-rotate', 'fa-spin');
        }
        activePolls.add(id);
    });

    function pollAll() {
        if (activePolls.size === 0) return;

        pollCount++;
        if (pollCount > MAX_POLLS) {
            // Kembalikan ikon ke semula, hentikan polling
            activePolls.forEach(function (id) {
                const icon = document.getElementById('pill-icon-' + id);
                if (icon) {
                    icon.classList.remove('fa-rotate', 'fa-spin');
                    icon.classList.add('fa-clock');
                }
            });
            clearInterval(timer);
            return;
        }

        activePolls.forEach(function (id) {
            const row = document.querySelector('[data-poll-id="' + id + '"]');
            if (!row) return;

            const url = row.getAttribute('data-poll-url');

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                if (data.status === 'paid') {
                    // Pembayaran berhasil — update pill langsung di halaman
                    activePolls.delete(id);

                    const pill  = document.getElementById('pill-' + id);
                    const icon  = document.getElementById('pill-icon-' + id);
                    const text  = document.getElementById('pill-text-' + id);

                    if (pill) {
                        pill.className = 'status-pill status-pill-diterima';
                    }
                    if (icon) {
                        icon.className = 'fa-solid fa-circle-check';
                    }
                    if (text) {
                        text.textContent = 'Berhasil';
                    }

                    // Update warna baris
                    if (row) {
                        row.classList.remove('jt-row-pending-pay', 'jt-row-pending');
                        row.classList.add('jt-row-diterima');
                    }

                    // Sembunyikan tombol Bayar & Batal di baris tersebut
                    const actionGroup = row.querySelector('.jt-action-group');
                    if (actionGroup) {
                        actionGroup.innerHTML = '<span class="jt-action-none">&mdash;</span>';
                    }

                    // Jika semua sudah selesai, hentikan polling
                    if (activePolls.size === 0) clearInterval(timer);

                } else if (data.status === 'failed') {
                    activePolls.delete(id);

                    const pill = document.getElementById('pill-' + id);
                    const icon = document.getElementById('pill-icon-' + id);
                    const text = document.getElementById('pill-text-' + id);

                    if (pill) pill.className = 'status-pill status-pill-ditolak';
                    if (icon) icon.className = 'fa-solid fa-circle-xmark';
                    if (text) text.textContent = 'Ditolak';

                    if (row) {
                        row.classList.remove('jt-row-pending-pay', 'jt-row-pending');
                        row.classList.add('jt-row-ditolak');
                    }

                    const actionGroup = row.querySelector('.jt-action-group');
                    if (actionGroup) {
                        actionGroup.innerHTML = '<span class="jt-action-none">&mdash;</span>';
                    }

                    if (activePolls.size === 0) clearInterval(timer);
                }
                // status 'pending' atau 'error' — biarkan tetap polling
            })
            .catch(function () {
                // Network error — biarkan coba lagi di interval berikutnya
            });
        });
    }

    // Poll pertama 3 detik setelah halaman dimuat\r
    setTimeout(pollAll, 3000);\r
    var timer = setInterval(pollAll, POLL_INTERVAL_MS);\r
});\r
</script>

{{-- ── Invoice Modal JS ── --}}
<script>
function bukaInvoice(btn) {
    var url = btn.getAttribute('data-invoice-url');
    var overlay = document.getElementById('invOverlay');
    var loading  = document.getElementById('invLoading');
    var content  = document.getElementById('invContent');

    // Reset state
    loading.style.display = 'flex';
    content.style.display = 'none';
    content.innerHTML = '';

    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        credentials: 'same-origin',
    })
    .then(function(res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(function(d) {
        var keteranganHtml = '';
        if (d.keterangan) {
            keteranganHtml = '<div class="inv-keterangan-box">' +
                '<i class="fa-solid fa-comment-dots" style="margin-right:6px;color:#059669;"></i>' +
                escHtml(d.keterangan) +
            '</div>';
        }
        content.innerHTML =
            '<div class="inv-head">' +
                '<div class="inv-head-top">' +
                    '<div>' +
                        '<p class="inv-lembaga-name">&#x1F54C;&nbsp; Pusdai Jawa Barat</p>' +
                        '<p class="inv-lembaga-sub">FINUS &mdash; Sistem Informasi Keuangan Masjid</p>' +
                    '</div>' +
                    '<button class="inv-close-btn" onclick="tutupInvoice()" title="Tutup">' +
                        '<i class="fa-solid fa-xmark"></i>' +
                    '</button>' +
                '</div>' +
                '<div class="inv-ref-row">' +
                    '<span class="inv-ref-badge">' + escHtml(d.referensi) + '</span>' +
                    '<span class="inv-status-badge"><i class="fa-solid fa-circle-check" style="margin-right:4px;"></i>Pembayaran Berhasil</span>' +
                '</div>' +
            '</div>' +

            '<div class="inv-body">' +
                '<div class="inv-nominal-box">' +
                    '<div class="inv-nominal-sub">Total Pembayaran</div>' +
                    '<span class="inv-nominal-big">' + escHtml(d.nominal_fmt) + '</span>' +
                    '<div class="inv-nominal-sub">Jenis: <strong>' + escHtml(d.jenis) + '</strong></div>' +
                '</div>' +

                '<div class="inv-section-title">Informasi Muzakki</div>' +
                '<div class="inv-row">' +
                    '<span class="inv-row-label">Nama</span>' +
                    '<span class="inv-row-value">' + escHtml(d.jamaah_nama) + '</span>' +
                '</div>' +
                '<div class="inv-row">' +
                    '<span class="inv-row-label">Email</span>' +
                    '<span class="inv-row-value">' + escHtml(d.jamaah_email) + '</span>' +
                '</div>' +

                '<div class="inv-section-title">Detail Transaksi</div>' +
                '<div class="inv-row">' +
                    '<span class="inv-row-label">No. Referensi</span>' +
                    '<span class="inv-row-value" style="font-family:monospace;">' + escHtml(d.referensi) + '</span>' +
                '</div>' +
                '<div class="inv-row">' +
                    '<span class="inv-row-label">Tanggal Transaksi</span>' +
                    '<span class="inv-row-value">' + escHtml(d.tanggal) + '</span>' +
                '</div>' +
                '<div class="inv-row">' +
                    '<span class="inv-row-label">Metode Pembayaran</span>' +
                    '<span class="inv-row-value">' + escHtml(d.metode) + '</span>' +
                '</div>' +
                '<div class="inv-row">' +
                    '<span class="inv-row-label">Diverifikasi Pada</span>' +
                    '<span class="inv-row-value">' + escHtml(d.verified_at || '-') + '</span>' +
                '</div>' +
                (keteranganHtml ? '<div class="inv-section-title" style="margin-top:16px;">Keterangan</div>' + keteranganHtml : '') +
            '</div>' +

            '<div class="inv-footer">' +
                '<a href="' + d.url_cetak + '" target="_blank" class="inv-btn-print">' +
                    '<i class="fa-solid fa-print"></i> Cetak / Download PDF' +
                '</a>' +
                '<button class="inv-btn-close" onclick="tutupInvoice()">Tutup</button>' +
            '</div>';

        loading.style.display = 'none';
        content.style.display = 'block';
    })
    .catch(function() {
        content.innerHTML =
            '<div class="inv-loading">' +
                '<i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;"></i>' +
                '<span>Gagal memuat invoice. Silakan coba lagi.</span>' +
                '<button class="inv-btn-close" onclick="tutupInvoice()" style="margin-top:8px;padding:8px 20px;border-radius:10px;">Tutup</button>' +
            '</div>';
        loading.style.display = 'none';
        content.style.display = 'block';
    });
}

function tutupInvoice(event) {
    // Tutup hanya jika klik di overlay (bukan modal itu sendiri)
    if (event && event.target !== document.getElementById('invOverlay')) return;
    var overlay = document.getElementById('invOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// Tutup dengan tombol Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        var overlay = document.getElementById('invOverlay');
        if (overlay && overlay.classList.contains('active')) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
});

function escHtml(str) {
    if (!str && str !== 0) return '-';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: jamaah/riwayat-transaksi.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="jamaah/riwayat-transaksi.blade.php">
html[data-finus-theme="dark"] body .jt-page :where(.rw-banner,.rw-sub) { border-color:#293D31 !important; background:linear-gradient(155deg,#15211A,#111A15) !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .jt-page .rw-banner-success { border-color:#2D6140 !important; background:#15331E !important; color:#A9F0B9 !important; }
html[data-finus-theme="dark"] body .jt-page .rw-banner-danger { border-color:#704044 !important; background:#371E22 !important; color:#F5B1B5 !important; }
html[data-finus-theme="dark"] body .jt-page .status-pill-pending { border-color:#6A552D !important; background:#342817 !important; color:#F4D38B !important; }
html[data-finus-theme="dark"] body .jt-page :where(.status-pill-ditolak,.status-pill-dibatalkan) { border-color:#704044 !important; background:#371E22 !important; color:#F5B1B5 !important; }
</style>
@endpush


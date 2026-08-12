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
                        <th class="jt-th-center">Aksi</th>
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

    // Poll pertama 3 detik setelah halaman dimuat
    setTimeout(pollAll, 3000);
    var timer = setInterval(pollAll, POLL_INTERVAL_MS);
});
</script>
@endpush
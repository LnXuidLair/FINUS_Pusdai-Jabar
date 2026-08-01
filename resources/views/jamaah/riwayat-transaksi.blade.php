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
            'icon' => 'fa-receipt',
            'color' => '#179B40',
            'soft' => '#EAF8EE',
        ],
        [
            'label' => 'Total Nominal',
            'value' => $rupiah($ringkasan['nominal']),
            'icon' => 'fa-wallet',
            'color' => '#2563EB',
            'soft' => '#EDF4FF',
        ],
        
    ];
@endphp

@include('layouts.partials.finus-ui')

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

    <section class="jt-grid jt-grid-4">
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
                            $status = $item->status_verifikasi ?: 'pending';
                            $referensi = $item->order_id ?: 'ZSF-' . $item->id;

                            // Cek apakah transaksi bisa dilanjutkan pembayarannya
                            $bisaBayar = $status === 'pending'
                                && $item->payment_gateway === 'midtrans'
                                && !empty($item->snap_token)
                                && in_array($item->payment_status, ['pending', null, ''], true);
                        @endphp

                        <tr class="{{ $bisaBayar ? 'jt-row-pending-pay' : '' }}">
                            <td class="jt-reference">
                                {{ $referensi }}
                                @if($bisaBayar)
                                    <span class="jt-badge-pay-indicator">Belum Dibayar</span>
                                @endif
                            </td>

                            <td>{{ $item->tanggal?->format('d/m/Y') }}</td>

                            <td>
                                <span class="jt-type">
                                    {{ $jenisLabels[$item->jenis_ziswaf] ?? $item->jenis_ziswaf }}
                                </span>
                            </td>

                            <td class="jt-money">{{ $rupiah($item->nominal) }}</td>

                            <td>
                                <span class="jt-badge jt-badge-{{ $status }}">
                                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                                </span>
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
                            <td colspan="6" class="jt-empty">
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
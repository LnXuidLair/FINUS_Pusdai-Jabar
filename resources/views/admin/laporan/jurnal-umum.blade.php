@extends('layouts.app')

@section('title', 'Jurnal Umum')
@section('hide-page-header', '1')

@php
    $isPaginated = $jurnals instanceof \Illuminate\Contracts\Pagination\Paginator;
    $jurnalItems = $isPaginated ? collect($jurnals->items()) : collect($jurnals ?? []);
    $totalTransaksi = method_exists($jurnals, 'total') ? $jurnals->total() : $jurnalItems->count();
    $totalDebit = (float) $jurnalItems->where('tipe', 'debit')->sum('jumlah');
    $totalKredit = (float) $jurnalItems->where('tipe', 'kredit')->sum('jumlah');
    $selisih = $totalDebit - $totalKredit;
    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp

@include('layouts.partials.finus-ui')

@section('content')
<div class="fr-page">
    <section class="fr-hero fr-reveal">
        <div class="fr-hero-main">
            <span class="fr-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-book-open"></i>
            </span>
            <div>
                <h1 class="fr-hero-title">Jurnal Umum</h1>
                <p class="fr-hero-subtitle">
                    Daftar pencatatan debit dan kredit dari seluruh transaksi keuangan FINUS.
                </p>
            </div>
        </div>

        <div class="fr-hero-actions">
            <span class="fr-hero-badge">
                <i class="fa-solid fa-database"></i>
                {{ number_format($totalTransaksi, 0, ',', '.') }} transaksi
            </span>
        </div>
    </section>

    <section class="fr-summary" style="--summary-columns:4">
        <article class="fr-stat fr-stat-green fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-list-ol"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Total Transaksi</span>
                <strong class="fr-stat-value">{{ number_format($totalTransaksi, 0, ',', '.') }}</strong>
                <span class="fr-stat-note">Seluruh data jurnal</span>
            </span>
        </article>

        <article class="fr-stat fr-stat-blue fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-arrow-down"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Total Debit Halaman Ini</span>
                <strong class="fr-stat-value">{{ $rupiah($totalDebit) }}</strong>
                <span class="fr-stat-note">Berdasarkan data yang tampil</span>
            </span>
        </article>

        <article class="fr-stat fr-stat-amber fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-arrow-up"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Total Kredit Halaman Ini</span>
                <strong class="fr-stat-value">{{ $rupiah($totalKredit) }}</strong>
                <span class="fr-stat-note">Berdasarkan data yang tampil</span>
            </span>
        </article>

        <article class="fr-stat {{ abs($selisih) < 0.5 ? 'fr-stat-purple' : 'fr-stat-red' }} fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-scale-balanced"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Selisih Debit/Kredit</span>
                <strong class="fr-stat-value">{{ $rupiah($selisih) }}</strong>
                <span class="fr-stat-note">Idealnya bernilai seimbang</span>
            </span>
        </article>
    </section>

    @if(session('success'))
        <div class="fr-alert fr-alert-success fr-reveal">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="fr-alert fr-alert-danger fr-reveal">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Terjadi kesalahan.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <section class="fr-card fr-reveal">
        <header class="fr-card-head">
            <div class="fr-card-title-row">
                <span class="fr-card-icon"><i class="fa-solid fa-table-list"></i></span>
                <div>
                    <h2 class="fr-card-title">Daftar Jurnal</h2>
                    <p class="fr-card-subtitle" id="recordCount" data-label="transaksi">
                        Menampilkan {{ $jurnalItems->count() }} dari {{ $totalTransaksi }} transaksi
                    </p>
                </div>
            </div>

            <div class="fr-search-wrap">
                <i class="fa-solid fa-magnifying-glass fr-search-icon"></i>
                <input
                    id="searchInput"
                    type="search"
                    class="fr-search"
                    placeholder="Cari dari karakter awal..."
                    autocomplete="off"
                    aria-label="Cari jurnal umum"
                >
                <button type="button" class="fr-search-clear" id="clearSearch" aria-label="Hapus pencarian">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </header>

        <div class="fr-table-wrap">
            <table id="searchTable" class="fr-table">
                <thead>
                    <tr>
                        <th style="width:70px">No</th>
                        <th>Tanggal</th>
                        <th>Akun</th>
                        <th>Keterangan</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurnalItems as $item)
                        @php
                            $debitValue = $item->tipe === 'debit' ? (float) $item->jumlah : 0;
                            $kreditValue = $item->tipe === 'kredit' ? (float) $item->jumlah : 0;
                            $debitText = $debitValue > 0 ? $rupiah($debitValue) : '-';
                            $kreditText = $kreditValue > 0 ? $rupiah($kreditValue) : '-';
                            $nomorAwal = $isPaginated && method_exists($jurnals, 'firstItem')
                                ? (($jurnals->firstItem() ?? 1) + $loop->index)
                                : $loop->iteration;
                        @endphp
                        <tr
                            data-search-row
                            data-search-start="{{ $item->tanggal }}|{{ $item->akun }}|{{ $item->keterangan }}|{{ $debitText }}|{{ $kreditText }}"
                        >
                            <td data-row-number>{{ $nomorAwal }}</td>
                            <td>
                                <span class="fr-chip">
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ $item->tanggal }}
                                </span>
                            </td>
                            <td><strong>{{ $item->akun }}</strong></td>
                            <td>{{ $item->keterangan ?: '-' }}</td>
                            <td>
                                <span class="fr-money {{ $debitValue > 0 ? 'is-debit' : 'is-muted' }}">
                                    {{ $debitText }}
                                </span>
                            </td>
                            <td>
                                <span class="fr-money {{ $kreditValue > 0 ? 'is-credit' : 'is-muted' }}">
                                    {{ $kreditText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="fr-empty">
                                <span class="fr-empty-icon"><i class="fa-solid fa-book-open"></i></span>
                                <strong>Belum ada data jurnal umum</strong>
                                Data transaksi akan muncul setelah jurnal berhasil dicatat.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="emptySearchRow" style="display:none">
                        <td colspan="6" class="fr-empty">
                            <span class="fr-empty-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <strong>Data tidak ditemukan</strong>
                            Coba gunakan karakter awal dari tanggal, akun, keterangan, debit, atau kredit.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($isPaginated && method_exists($jurnals, 'links'))
            <div class="fr-card-body" style="padding-top:14px;padding-bottom:14px;border-top:1px solid var(--fr-border)">
                {{ $jurnals->links() }}
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const input = document.getElementById('searchInput');
        const clearButton = document.getElementById('clearSearch');
        const rows = Array.from(document.querySelectorAll('[data-search-row]'));
        const emptyRow = document.getElementById('emptySearchRow');
        const countElement = document.getElementById('recordCount');

        if (!input) return;

        const normalize = value => (value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();

        const filterRows = () => {
            const keyword = normalize(input.value);
            let visible = 0;

            rows.forEach(row => {
                const values = (row.dataset.searchStart || '')
                    .split('|')
                    .map(normalize)
                    .filter(Boolean);

                const match = keyword === '' || values.some(value => value.startsWith(keyword));
                row.style.display = match ? '' : 'none';

                if (match) {
                    visible += 1;
                    const numberCell = row.querySelector('[data-row-number]');
                    if (numberCell) numberCell.textContent = visible;
                }
            });

            if (emptyRow) {
                emptyRow.style.display = visible === 0 && rows.length > 0 ? '' : 'none';
            }

            if (countElement) {
                countElement.textContent = keyword
                    ? `Ditemukan ${visible} transaksi pada halaman ini`
                    : `Menampilkan ${visible} transaksi pada halaman ini`;
            }

            clearButton?.classList.toggle('is-visible', keyword !== '');
        };

        input.addEventListener('input', filterRows);
        clearButton?.addEventListener('click', () => {
            input.value = '';
            filterRows();
            input.focus();
        });

        filterRows();
    })();
</script>
@endpush
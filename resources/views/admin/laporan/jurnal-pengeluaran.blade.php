@extends('layouts.app')

@section('title', 'Jurnal Pengeluaran')
@section('hide-page-header', '1')

@php
    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $totalTransaksi = $jurnals->total();
@endphp

@include('layouts.partials.finus-ui')

@section('content')
<div class="fr-page">
    <section class="fr-hero fr-reveal">
        <div class="fr-hero-main">
            <span class="fr-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-arrow-up-long"></i>
            </span>
            <div>
                <h1 class="fr-hero-title">Jurnal Pengeluaran</h1>
                <p class="fr-hero-subtitle">
                    Penyaluran ZIS, manfaat wakaf, operasional pengelola, dan pengembalian wakaf temporer dicatat menurut sumber dananya.
                </p>
            </div>
        </div>

        <div class="fr-hero-actions">
            <span class="fr-hero-badge">
                <i class="fa-solid fa-building-columns"></i>
                1 Rekening Kas/Bank Terpadu
            </span>
            <span class="fr-hero-badge">
                <i class="fa-solid fa-receipt"></i>
                {{ number_format($totalTransaksi, 0, ',', '.') }} transaksi
            </span>
        </div>
    </section>

    <!-- Ringkasan statistik pengeluaran -->
    <section class="fr-summary" style="--summary-columns:5">
        <article class="fr-stat fr-stat-red fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-money-bill-transfer"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Total Kas Keluar</span>
                <strong class="fr-stat-value">{{ $rupiah($summary['total_pengeluaran']) }}</strong>
                <span class="fr-stat-note">Pengeluaran fisik kas/bank</span>
            </span>
        </article>

        <article class="fr-stat fr-stat-emerald fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Penyaluran Zakat</span>
                <strong class="fr-stat-value">{{ $rupiah($summary['total_penyaluran_zakat']) }}</strong>
                <span class="fr-stat-note">Beban Dana Zakat (Asnaf)</span>
            </span>
        </article>

        <article class="fr-stat fr-stat-blue fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-hands-holding-child"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Penyaluran Infak</span>
                <strong class="fr-stat-value">{{ $rupiah($summary['total_penyaluran_infak']) }}</strong>
                <span class="fr-stat-note">Beban Dana Infak (Dakwah/Sosial)</span>
            </span>
        </article>

        <article class="fr-stat fr-stat-amber fr-reveal">
            <span class="fr-stat-icon"><i class="fa-solid fa-briefcase"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label">Beban Amil & Gaji</span>
                <strong class="fr-stat-value">{{ $rupiah($summary['total_beban_amil']) }}</strong>
                <span class="fr-stat-note">Beban Dana Amil (Operasional DKM)</span>
            </span>
        </article>

        <article class="fr-stat fr-reveal" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border: 1px solid #fed7aa;">
            <span class="fr-stat-icon" style="background: rgba(194,65,12,.12); color: #c2410c;"><i class="fa-solid fa-landmark-dome"></i></span>
            <span class="fr-stat-copy">
                <span class="fr-stat-label" style="color: #9a3412;">Penyaluran Wakaf</span>
                <strong class="fr-stat-value" style="color: #7c2d12;">{{ $rupiah($summary['total_penyaluran_wakaf']) }}</strong>
                <span class="fr-stat-note" style="color: #c2410c;">Mushaf, Pembangunan & Pendidikan</span>
            </span>
        </article>
    </section>

    <!-- Filter Bar (1 Baris) -->
    <section class="fr-card fr-reveal" style="margin-bottom: 24px; padding: 18px 22px;">
        <form method="GET" action="{{ url()->current() }}" style="margin: 0;">
            <div style="display: flex; flex-direction: row; align-items: flex-end; gap: 10px; width: 100%; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 2px;">
                <div style="flex: 1.6; min-width: 150px;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap;">
                        <i class="fa-solid fa-magnifying-glass" style="margin-right: 4px; color: #94a3b8;"></i> Pencarian
                    </label>
                    <input type="text" name="q" value="{{ $search }}" placeholder="No. bukti, deskripsi..." 
                        style="width: 100%; height: 42px; padding: 0 12px; font-size: 13px; border: 1.5px solid #cbd5e1; border-radius: 10px; background: #fff; color: #1e293b; outline: none; box-sizing: border-box;">
                </div>

                <div style="flex: 1.3; min-width: 145px;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap;">
                        <i class="fa-solid fa-tags" style="margin-right: 4px; color: #94a3b8;"></i> Tipe Pengeluaran
                    </label>
                    <select name="tipe" style="width: 100%; height: 42px; padding: 0 10px; font-size: 13px; border: 1.5px solid #cbd5e1; border-radius: 10px; background: #fff; color: #1e293b; outline: none; cursor: pointer; box-sizing: border-box;">
                        <option value="all" {{ $tipeFilter === 'all' || !$tipeFilter ? 'selected' : '' }}>Semua Pengeluaran</option>
                        <option value="penyaluran" {{ $tipeFilter === 'penyaluran' ? 'selected' : '' }}>Penyaluran Mustahik</option>
                        <option value="operasional" {{ $tipeFilter === 'operasional' ? 'selected' : '' }}>Operasional & Gaji</option>
                    </select>
                </div>

                <div style="flex: 1.2; min-width: 140px;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap;">
                        <i class="fa-solid fa-layer-group" style="margin-right: 4px; color: #94a3b8;"></i> Golongan Dana
                    </label>
                    <select name="dana" style="width: 100%; height: 42px; padding: 0 10px; font-size: 13px; border: 1.5px solid #cbd5e1; border-radius: 10px; background: #fff; color: #1e293b; outline: none; cursor: pointer; box-sizing: border-box;">
                        <option value="all" {{ $danaFilter === 'all' || !$danaFilter ? 'selected' : '' }}>Semua Dana</option>
                        <option value="zakat" {{ $danaFilter === 'zakat' ? 'selected' : '' }}>Dana Zakat</option>
                        <option value="infak_sedekah" {{ in_array($danaFilter, ['infak', 'infak_sedekah']) ? 'selected' : '' }}>Dana Infak</option>
                        <option value="amil" {{ $danaFilter === 'amil' ? 'selected' : '' }}>Dana Amil</option>
                        <option value="wakaf" {{ $danaFilter === 'wakaf' ? 'selected' : '' }}>Dana Wakaf</option>
                        <option value="wakaf_temporer" {{ $danaFilter === 'wakaf_temporer' ? 'selected' : '' }}>Wakaf Temporer</option>
                    </select>
                </div>

                <div style="flex: 1; min-width: 120px;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap;">
                        <i class="fa-solid fa-calendar-day" style="margin-right: 4px; color: #94a3b8;"></i> Dari Tanggal
                    </label>
                    <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}" 
                        style="width: 100%; height: 42px; padding: 0 8px; font-size: 13px; border: 1.5px solid #cbd5e1; border-radius: 10px; background: #fff; color: #1e293b; outline: none; box-sizing: border-box;">
                </div>

                <div style="flex: 1; min-width: 120px;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap;">
                        <i class="fa-solid fa-calendar-check" style="margin-right: 4px; color: #94a3b8;"></i> Sampai Tanggal
                    </label>
                    <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" 
                        style="width: 100%; height: 42px; padding: 0 8px; font-size: 13px; border: 1.5px solid #cbd5e1; border-radius: 10px; background: #fff; color: #1e293b; outline: none; box-sizing: border-box;">
                </div>

                <div style="flex: 0 0 auto; display: flex; gap: 8px; align-items: flex-end;">
                    <button type="submit" 
                        style="height: 42px; padding: 0 18px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 13px; font-weight: 700; background: #0E5423; color: white; border: none; border-radius: 10px; cursor: pointer; white-space: nowrap; box-shadow: 0 4px 12px rgba(14, 84, 35, .2);">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    @if($search || $danaFilter || $tipeFilter || $tanggalDari || $tanggalSampai)
                        <a href="{{ url()->current() }}" 
                            style="height: 42px; padding: 0 14px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; font-size: 13px; font-weight: 600; background: #f1f5f9; color: #475569; border: 1.5px solid #cbd5e1; border-radius: 10px; text-decoration: none; cursor: pointer; white-space: nowrap;">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </section>

    <!-- Tabel Jurnal Pengeluaran -->
    <section class="fr-card fr-reveal">
        <header class="fr-card-head">
            <div class="fr-card-title-row">
                <span class="fr-card-icon"><i class="fa-solid fa-receipt"></i></span>
                <div>
                    <h2 class="fr-card-title">Daftar Jurnal Pengeluaran Kas</h2>
                    <p class="fr-card-subtitle">
                        Menampilkan {{ $jurnals->count() }} dari total {{ number_format($totalTransaksi, 0, ',', '.') }} transaksi jurnal keluar
                    </p>
                </div>
            </div>
        </header>

        <div class="fr-table-wrap">
            <table class="fr-table">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th style="width:140px">Tanggal & Bukti</th>
                        <th>Keterangan / Penerima Manfaat</th>
                        <th style="width:160px">Golongan Dana</th>
                        <th style="width:200px">Akun Debit</th>
                        <th style="width:200px">Akun Kredit (Kas/Bank)</th>
                        <th style="width:140px; text-align:right;">Nominal Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurnals as $index => $jurnal)
                        @php
                            $kreditKas = $jurnal->detail->first(fn($d) => (float)$d->credit > 0 && (in_array($d->coa?->kode_akun, ['1101', '1102']) || str_contains($d->coa?->nama_akun ?? '', 'Kas') || str_contains($d->coa?->nama_akun ?? '', 'Bank')));
                            $debitBeban = $jurnal->detail->first(fn($d) => (float)$d->debit > 0 && in_array((int) $d->coa?->header_akun, [2, 5, 6]));
                            $danaItem = $debitBeban?->jenis_dana ?? ($jurnal->detail->first()?->jenis_dana ?? 'amil');
                            $rincianAsnaf = $jurnal->detail->filter(fn($d) => (float)$d->debit > 0 && !empty($d->asnaf) && $d->asnaf !== 'amil');
                        @endphp
                        <tr>
                            <td>{{ $jurnals->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $jurnal->tanggal ? $jurnal->tanggal->format('d/m/Y') : '-' }}</strong><br>
                                <span style="font-size: 11px; color: #64748b; font-family: monospace;">{{ $jurnal->no_referensi }}</span>
                            </td>
                            <td>
                                <strong>{{ $jurnal->deskripsi }}</strong>
                                @if($jurnal->keterangan && $jurnal->keterangan !== $jurnal->deskripsi)
                                    <div style="font-size: 12px; color: #64748b;">{{ $jurnal->keterangan }}</div>
                                @endif
                                @if($rincianAsnaf->isNotEmpty())
                                    <div style="display:grid;gap:3px;margin-top:6px;">
                                        @foreach($rincianAsnaf as $rincian)
                                            <span style="font-size:11px;color:#166534;">
                                                <i class="fa-solid fa-user-group"></i>
                                                {{ $rincian->deskripsi }} - {{ $rupiah($rincian->debit) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                @if($danaItem === 'wakaf')
                                    @php
                                        $bidangWakaf = $debitBeban?->kategori_bidang
                                            ?? (str_contains(strtolower($jurnal->deskripsi ?? ''), 'mushaf') ? 'Mushaf Al-Quran'
                                            : (str_contains(strtolower($jurnal->deskripsi ?? ''), 'bangun') ? 'Pembangunan'
                                            : (str_contains(strtolower($jurnal->deskripsi ?? ''), 'pendidikan') ? 'Pendidikan Islam'
                                            : (str_contains(strtolower($jurnal->keterangan ?? ''), 'mushaf') ? 'Mushaf Al-Quran'
                                            : (str_contains(strtolower($jurnal->keterangan ?? ''), 'bangun') ? 'Pembangunan'
                                            : (str_contains(strtolower($jurnal->keterangan ?? ''), 'pendidikan') ? 'Pendidikan Islam'
                                            : null))))));
                                    @endphp
                                    @if($bidangWakaf)
                                        <span style="display:inline-flex; align-items:center; gap:4px; margin-top:4px; font-size:11px; background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; padding:2px 8px; border-radius:6px; font-weight:600;">
                                            <i class="fa-solid fa-{{ $bidangWakaf === 'Mushaf Al-Quran' ? 'book-quran' : ($bidangWakaf === 'Pembangunan' ? 'building' : 'graduation-cap') }}"></i>
                                            {{ $bidangWakaf }}
                                        </span>
                                    @else
                                        <span style="display:inline-flex; align-items:center; gap:4px; margin-top:4px; font-size:11px; background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; padding:2px 8px; border-radius:6px; font-weight:600;">
                                            <i class="fa-solid fa-landmark-dome"></i> Penyaluran Wakaf
                                        </span>
                                    @endif
                                @elseif($debitBeban?->kategori_bidang)
                                    <span style="display:inline-block; margin-top:4px; font-size:11px; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:6px;">
                                        Bidang: {{ $debitBeban->kategori_bidang }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($danaItem === 'zakat')
                                    <span class="fr-badge" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:600;">
                                        <i class="fa-solid fa-hand-holding-dollar"></i> Beban Dana Zakat
                                    </span>
                                @elseif(in_array($danaItem, ['infak', 'infak_sedekah']))
                                    <span class="fr-badge" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:600;">
                                        <i class="fa-solid fa-hands-holding-child"></i> Beban Dana Infak
                                    </span>
                                @elseif($danaItem === 'wakaf')
                                    <span class="fr-badge" style="background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:600;">
                                        <i class="fa-solid fa-landmark-dome"></i> Penyaluran Wakaf
                                    </span>
                                @elseif($danaItem === 'wakaf_temporer')
                                    <span class="fr-badge" style="background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:600;">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Pengembalian Wakaf Temporer
                                    </span>
                                @else
                                    <span class="fr-badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:600;">
                                        <i class="fa-solid fa-briefcase"></i> Beban Dana Amil
                                    </span>
                                @endif
                                @if($debitBeban?->restriction_type)
                                    <div style="font-size:11px;color:#64748b;margin-top:4px;">{{ $debitBeban->restriction_type === 'muqayyadah' ? 'Terikat (Muqayyadah)' : 'Tidak Terikat (Mutlaqah)' }}</div>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #0f172a;">
                                    {{ $debitBeban?->coa?->nama_akun ?? 'Beban Operasional' }}
                                </span>
                                <div style="font-size: 11px; color: #64748b;">Kode: {{ $debitBeban?->coa?->kode_akun ?? '51xx' }}</div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #0f172a;">
                                    {{ $kreditKas?->coa?->nama_akun ?? 'Kas / Bank' }}
                                </span>
                                <div style="font-size: 11px; color: #64748b;">Kode: {{ $kreditKas?->coa?->kode_akun ?? '1102' }}</div>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #dc2626;">
                                {{ $rupiah($kreditKas?->credit ?? ($debitBeban?->debit ?? 0)) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 36px; color: #64748b;">
                                <i class="fa-solid fa-folder-open" style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                                Belum ada data jurnal pengeluaran untuk kriteria yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jurnals->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $jurnals->links() }}
            </div>
        @endif
    </section>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Pembayaran ZISWAF')

@section('content')
@include('layouts.partials.finus-ui')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');

    $snapUrl = $isProduction
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';

    $jenisText = $jenisLabels[$transaksi->jenis_ziswaf]
        ?? ucwords(str_replace('_', ' ', $transaksi->jenis_ziswaf));

    $metodeText = $metodeLabels[$transaksi->metode_pembayaran]
        ?? strtoupper(str_replace('_', ' ', $transaksi->metode_pembayaran));
@endphp

<style>
    /* ─── Payment page wrapper ─── */
    .pay-page {
        padding: 8px 0 40px;
        color: var(--jt-text);
    }

    /* ─── Back link ─── */
    .pay-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 20px;
        padding: 8px 14px;
        border: 1px solid var(--jt-border);
        border-radius: 10px;
        background: #fff;
        color: var(--jt-muted);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all .2s ease;
    }

    .pay-back:hover {
        border-color: #c3d4ca;
        color: var(--jt-green-dark);
        transform: translateX(-2px);
    }

    /* ─── Grid layout ─── */
    .pay-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 22px;
        align-items: start;
    }

    /* ─── Card base ─── */
    .pay-card {
        border: 1px solid var(--jt-border);
        border-radius: 20px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .07);
    }

    /* ─── Hero banner ─── */
    .pay-hero {
        position: relative;
        overflow: hidden;
        padding: 26px 28px;
        background:
            radial-gradient(circle at 92% 12%, rgba(255,255,255,.15), transparent 30%),
            linear-gradient(135deg, #064e1a 0%, #0e5423 40%, #179b40 100%);
        color: #fff;
    }

    .pay-hero::before {
        content: "";
        position: absolute;
        top: -80px;
        right: -40px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        pointer-events: none;
    }

    .pay-hero::after {
        content: "";
        position: absolute;
        bottom: -90px;
        right: 120px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(126,255,135,.1);
        pointer-events: none;
    }

    .pay-hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .pay-hero-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        min-width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(255,255,255,.15);
        font-size: 22px;
        color: #fff;
        backdrop-filter: blur(4px);
    }

    .pay-hero-eyebrow {
        margin-bottom: 3px;
        color: rgba(255,255,255,.65);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .pay-hero h2 {
        margin: 0;
        color: #fff;
        font-size: 20px;
        font-weight: 800;
    }

    .pay-hero p {
        margin: 3px 0 0;
        color: rgba(255,255,255,.7);
        font-size: 12px;
    }

    /* ─── Amount display ─── */
    .pay-amount-block {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 22px;
        padding: 16px 18px;
        border-radius: 14px;
        background: rgba(255,255,255,.12);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,.18);
    }

    .pay-amount-label {
        color: rgba(255,255,255,.65);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .pay-amount-value {
        color: #fff;
        font-size: 28px;
        font-weight: 900;
        letter-spacing: -.5px;
    }

    /* ─── Detail rows ─── */
    .pay-details {
        padding: 22px 24px;
    }

    .pay-details-title {
        margin: 0 0 15px;
        color: #334155;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .pay-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .pay-row:last-child {
        border-bottom: none;
    }

    .pay-row-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--jt-muted);
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .pay-row-label i {
        width: 18px;
        text-align: center;
        color: var(--jt-green);
        font-size: 12px;
    }

    .pay-row-value {
        color: var(--jt-text);
        font-size: 12.5px;
        font-weight: 700;
        text-align: right;
    }

    /* ─── Status pill ─── */
    .pay-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 99px;
        background: #fef3c7;
        color: #92400e;
        font-size: 11px;
        font-weight: 700;
    }

    .pay-status-pill::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #d97706;
        animation: pay-pulse 1.8s ease-in-out infinite;
    }

    @keyframes pay-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .4; }
    }

    /* ─── Warning notice ─── */
    .pay-notice {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin: 0 24px 20px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #fde68a;
        background: #fffbeb;
        color: #92400e;
        font-size: 11.5px;
        line-height: 1.55;
    }

    .pay-notice i {
        margin-top: 1px;
        color: #d97706;
        font-size: 14px;
        flex-shrink: 0;
    }

    /* ─── Sidebar sticky ─── */
    .pay-sidebar {
        position: sticky;
        top: 24px;
    }

    /* ─── CTA card ─── */
    .pay-cta {
        padding: 24px;
    }

    .pay-cta-title {
        margin: 0 0 6px;
        font-size: 15px;
        font-weight: 800;
        color: var(--jt-text);
    }

    .pay-cta-sub {
        margin: 0 0 20px;
        color: var(--jt-muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .pay-btn-main {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        width: 100%;
        padding: 14px 20px;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, #047857, #16a34a, #22c55e);
        color: #fff;
        font-size: 14.5px;
        font-weight: 800;
        cursor: pointer;
        transition: all .25s ease;
        box-shadow: 0 6px 18px rgba(22, 163, 74, .38);
        letter-spacing: .01em;
    }

    .pay-btn-main:hover {
        background: linear-gradient(135deg, #065f46, #15803d, #16a34a);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(22, 163, 74, .45);
    }

    .pay-btn-main:active {
        transform: translateY(0);
    }

    .pay-btn-main:disabled {
        opacity: .65;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .pay-btn-secondary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        margin-top: 10px;
        padding: 11px 20px;
        border: 1.5px solid var(--jt-border);
        border-radius: 12px;
        background: #fff;
        color: var(--jt-muted);
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        transition: all .2s ease;
    }

    .pay-btn-secondary:hover {
        border-color: #c3d4ca;
        color: var(--jt-text);
    }

    .pay-btn-cancel {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        margin-top: 8px;
        padding: 10px 20px;
        border: 1.5px solid #fca5a5;
        border-radius: 12px;
        background: #fff1f2;
        color: #b91c1c;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s ease;
    }

    .pay-btn-cancel:hover {
        background: #fee2e2;
        border-color: #f87171;
        color: #991b1b;
    }

    /* ── Cek Status Button ── */
    .pay-btn-check {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        margin-top: 10px;
        padding: 11px 20px;
        border: 1.5px solid #a5b4fc;
        border-radius: 12px;
        background: #eef2ff;
        color: #4338ca;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all .2s ease;
    }

    .pay-btn-check:hover {
        background: #e0e7ff;
        border-color: #818cf8;
        color: #3730a3;
        transform: translateY(-1px);
    }


    /* ─── Guarantee list ─── */
    .pay-guarantees {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid #f1f5f9;
    }

    .pay-guarantee-item {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 9px;
        color: var(--jt-muted);
        font-size: 11.5px;
    }

    .pay-guarantee-item:last-child {
        margin-bottom: 0;
    }

    .pay-guarantee-item i {
        color: var(--jt-green);
        font-size: 13px;
        flex-shrink: 0;
    }

    /* ─── Sandbox badge ─── */
    .pay-sandbox-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 14px;
        padding: 5px 10px;
        border-radius: 8px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    /* ─── Responsive ─── */
    @media (max-width: 991px) {
        .pay-grid {
            grid-template-columns: 1fr;
        }
        .pay-sidebar {
            position: static;
        }
    }

    @media (max-width: 576px) {
        .pay-hero {
            padding: 20px 18px;
        }
        .pay-details, .pay-cta {
            padding: 18px;
        }
        .pay-notice {
            margin: 0 18px 16px;
        }
        .pay-amount-value {
            font-size: 22px;
        }
    }
</style>

<div class="pay-page">
    {{-- Back link --}}
    <a href="{{ route('jamaah.riwayat.index') }}" class="pay-back">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali ke Riwayat
    </a>

    <div class="pay-grid">
        {{-- ══════════════════════════════
             KOLOM KIRI — Detail Transaksi
        ══════════════════════════════ --}}
        <div>
            <div class="pay-card">
                {{-- Hero --}}
                <div class="pay-hero">
                    <div class="pay-hero-inner">
                        <div class="pay-hero-icon">
                            <i class="fa-solid fa-mosque"></i>
                        </div>
                        <div>
                            <div class="pay-hero-eyebrow">Pembayaran Digital</div>
                            <h2>Pembayaran ZISWAF</h2>
                            <p>Selesaikan pembayaran untuk melengkapi transaksi Anda.</p>
                        </div>
                    </div>

                    {{-- Amount block --}}
                    <div class="pay-amount-block">
                        <div>
                            <div class="pay-amount-label">Total Pembayaran</div>
                            <div class="pay-amount-value">{{ $rupiah($transaksi->nominal) }}</div>
                        </div>
                        <div style="text-align:right">
                            <div class="pay-amount-label">Jenis</div>
                            <div style="color:#fff;font-weight:800;font-size:14px;margin-top:4px;">{{ $jenisText }}</div>
                        </div>
                    </div>
                </div>

                {{-- Detail rows --}}
                <div class="pay-details">
                    <p class="pay-details-title">Ringkasan Transaksi</p>

                    <div class="pay-row">
                        <span class="pay-row-label">
                            <i class="fa-solid fa-hashtag"></i>
                            Referensi
                        </span>
                        <span class="pay-row-value" style="font-family: monospace; font-size: 12px; color: var(--jt-green-dark);">
                            {{ $transaksi->order_id }}
                        </span>
                    </div>

                    <div class="pay-row">
                        <span class="pay-row-label">
                            <i class="fa-solid fa-hand-holding-heart"></i>
                            Jenis ZISWAF
                        </span>
                        <span class="pay-row-value">
                            <span style="display:inline-flex;padding:4px 10px;border-radius:8px;background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:700;">
                                {{ $jenisText }}
                            </span>
                        </span>
                    </div>

                    <div class="pay-row">
                        <span class="pay-row-label">
                            <i class="fa-solid fa-credit-card"></i>
                            Metode
                        </span>
                        <span class="pay-row-value">{{ $metodeText }}</span>
                    </div>

                    <div class="pay-row">
                        <span class="pay-row-label">
                            <i class="fa-solid fa-calendar-day"></i>
                            Tanggal
                        </span>
                        <span class="pay-row-value">
                            {{ optional($transaksi->tanggal)->format('d M Y') }}
                        </span>
                    </div>

                    <div class="pay-row">
                        <span class="pay-row-label">
                            <i class="fa-solid fa-circle-dot"></i>
                            Status Pembayaran
                        </span>
                        <span class="pay-row-value">
                            <span class="pay-status-pill">
                                Menunggu Pembayaran
                            </span>
                        </span>
                    </div>

                    @if($transaksi->keterangan)
                        <div class="pay-row">
                            <span class="pay-row-label">
                                <i class="fa-solid fa-note-sticky"></i>
                                Keterangan
                            </span>
                            <span class="pay-row-value" style="max-width:220px;text-align:right;font-weight:600;color:var(--jt-muted);">
                                {{ $transaksi->keterangan }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Warning notice --}}
                <div class="pay-notice">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        <strong>Penting:</strong> Transaksi akan <strong>otomatis kadaluarsa</strong> jika tidak diselesaikan.
                        Klik tombol <em>"Bayar Sekarang"</em> di sebelah kanan untuk membuka jendela pembayaran Midtrans.
                        Jika ingin melanjutkan nanti, kamu bisa kembali melalui menu <strong>Riwayat Transaksi</strong>.
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════
             KOLOM KANAN — CTA Bayar
        ══════════════════════════════ --}}
        <div class="pay-sidebar">
            <div class="pay-card">
                <div class="pay-cta">
                    <p class="pay-cta-title">Selesaikan Pembayaran</p>
                    <p class="pay-cta-sub">
                        Klik tombol di bawah untuk membuka halaman pembayaran Midtrans.
                        Anda dapat memilih metode yang tersedia.
                    </p>

                    <button
                        type="button"
                        id="pay-button"
                        class="pay-btn-main"
                    >
                        <i class="fa-solid fa-credit-card"></i>
                        Bayar Sekarang
                    </button>


                    {{-- Cek Status: fallback jika webhook tidak terkirim --}}
                    <a
                        href="{{ route('jamaah.pembayaran.cek-status', $transaksi) }}"
                        class="pay-btn-check"
                        id="check-status-btn"
                        onclick="this.innerHTML='<i class=\'fa-solid fa-circle-notch fa-spin\'></i> Mengecek status...'; this.style.pointerEvents='none';"
                    >
                        <i class="fa-solid fa-rotate"></i>
                        Sudah Bayar? Cek Status
                    </a>

                    {{-- Batalkan transaksi --}}
                    <form
                        method="POST"
                        action="{{ route('jamaah.pembayaran.batal', $transaksi) }}"
                        onsubmit="return confirm('Yakin ingin membatalkan transaksi ini? Tindakan ini tidak dapat dibatalkan.')"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="pay-btn-cancel">
                            <i class="fa-solid fa-xmark"></i>
                            Batalkan Transaksi
                        </button>
                    </form>

                    {{-- Security guarantees --}}
                    <div class="pay-guarantees">
                        <div class="pay-guarantee-item">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Pembayaran diproses dengan enkripsi SSL</span>
                        </div>
                        <div class="pay-guarantee-item">
                            <i class="fa-solid fa-rotate"></i>
                            <span>Status diperbarui otomatis setelah pembayaran</span>
                        </div>
                        <div class="pay-guarantee-item">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                            <span>Dana tersalurkan langsung ke Pusdai Jabar</span>
                        </div>
                    </div>

                    @if(!$isProduction)
                        <div style="text-align:center">
                            <span class="pay-sandbox-badge">
                                <i class="fa-solid fa-flask"></i>
                                Mode Sandbox / Pengujian
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Nominal summary box --}}
            <div class="pay-card" style="margin-top: 16px;">
                <div style="padding: 18px 22px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:12px;color:var(--jt-muted);font-weight:600;">Nominal yang dibayar</span>
                        <span style="font-size:18px;font-weight:900;color:var(--jt-green-dark);">
                            {{ $rupiah($transaksi->nominal) }}
                        </span>
                    </div>
                    <div style="margin-top:10px;padding-top:10px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:11px;color:var(--jt-muted);font-weight:600;">Diproses oleh</span>
                        <span style="font-size:12px;font-weight:800;color:#334155;">
                            <i class="fa-solid fa-circle" style="font-size:8px;color:#22c55e;margin-right:5px;"></i>
                            Midtrans
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const payButton = document.getElementById('pay-button');

    if (!payButton) {
        return;
    }

    payButton.addEventListener('click', function () {
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Membuka halaman bayar...';

        window.snap.pay('{{ $transaksi->snap_token }}', {
            onSuccess: function () {
                window.location.href = "{{ route('jamaah.riwayat.index') }}";
            },
            onPending: function () {
                window.location.href = "{{ route('jamaah.riwayat.index') }}";
            },
            onError: function () {
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fa-solid fa-credit-card"></i> Bayar Sekarang';
                alert('Pembayaran gagal. Silakan coba lagi atau hubungi admin.');
            },
            onClose: function () {
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fa-solid fa-credit-card"></i> Bayar Sekarang';
            }
        });
    });
});
</script>
@endpush
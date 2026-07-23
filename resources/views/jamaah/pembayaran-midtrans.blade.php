@extends('layouts.app')

@section('content')
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
    .payment-card {
        border: 0;
        border-radius: 22px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        overflow: hidden;
    }

    .payment-hero {
        background: linear-gradient(135deg, #065f22, #16a34a);
        color: #ffffff;
        padding: 30px;
    }

    .payment-hero h3 {
        color: #ffffff;
        font-weight: 800;
    }

    .payment-summary {
        padding: 28px;
        background: #ffffff;
    }

    .payment-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 13px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .payment-row span:first-child {
        color: #64748b;
        font-weight: 700;
    }

    .payment-row span:last-child {
        color: #0f172a;
        font-weight: 800;
        text-align: right;
    }

    .status-pill {
        display: inline-block;
        padding: 7px 12px;
        border-radius: 999px;
        background: #fef3c7;
        color: #92400e;
        font-size: 12px;
        font-weight: 800;
    }

    .btn-pay {
        border: 0;
        border-radius: 14px;
        background: linear-gradient(135deg, #047857, #16a34a);
        color: #ffffff;
        font-weight: 800;
        padding: 13px 20px;
    }

    .btn-pay:hover {
        color: #ffffff;
        background: linear-gradient(135deg, #065f46, #15803d);
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card payment-card">
            <div class="payment-hero">
                <h3 class="mb-2">Pembayaran ZISWAF</h3>
                <p class="mb-0 text-white-50">
                    Selesaikan pembayaran agar transaksi otomatis terhubung ke admin.
                </p>
            </div>

            <div class="payment-summary">
                <div class="payment-row">
                    <span>Referensi</span>
                    <span>{{ $transaksi->order_id }}</span>
                </div>

                <div class="payment-row">
                    <span>Jenis</span>
                    <span>{{ $jenisText }}</span>
                </div>

                <div class="payment-row">
                    <span>Metode</span>
                    <span>{{ $metodeText }}</span>
                </div>

                <div class="payment-row">
                    <span>Nominal</span>
                    <span>{{ $rupiah($transaksi->nominal) }}</span>
                </div>

                <div class="payment-row">
                    <span>Status</span>
                    <span>
                        <span class="status-pill">
                            {{ ucfirst($transaksi->payment_status ?? 'pending') }}
                        </span>
                    </span>
                </div>

                <button type="button" id="pay-button" class="btn btn-pay btn-block mt-4">
                    <i class="fa fa-credit-card mr-1"></i>
                    Bayar Sekarang
                </button>

                <a href="{{ route('jamaah.riwayat.index') }}" class="btn btn-light btn-block mt-2">
                    Lihat Riwayat Transaksi
                </a>
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
        window.snap.pay('{{ $transaksi->snap_token }}', {
            onSuccess: function () {
                window.location.href = "{{ route('jamaah.riwayat.index') }}";
            },
            onPending: function () {
                window.location.href = "{{ route('jamaah.riwayat.index') }}";
            },
            onError: function () {
                alert('Pembayaran gagal. Silakan coba lagi.');
            },
            onClose: function () {
                console.log('Jamaah menutup popup pembayaran.');
            }
        });
    });
});
</script>
@endpush
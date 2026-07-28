@extends('layouts.app')

@section('content')
@include('layouts.partials.finus-ui')
<style>
    .finus-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .page-hero {
        border-radius: 22px;
        background: linear-gradient(135deg, #1d4ed8, #16a34a);
        color: white;
    }

    .qris-box {
        border: 2px dashed #d1d5db;
        border-radius: 20px;
        min-height: 340px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
    }
</style>

<div class="page-hero p-4 p-md-5 mb-4">
    <h3 class="text-white font-weight-bold mb-2">QRIS Masjid</h3>
    <p class="mb-0 text-white-50">
        Gunakan QRIS untuk pembayaran infak, sedekah, wakaf, atau transaksi ZISWAF lainnya.
    </p>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card finus-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-1 font-weight-bold">Kode QRIS</h5>
                <small class="text-muted">Scan menggunakan aplikasi pembayaran yang mendukung QRIS.</small>
            </div>

            <div class="card-body px-4 pb-4">
                <div class="qris-box">
                    @if($qrisPath)
                        <img src="{{ $qrisPath }}" alt="QRIS Masjid" class="img-fluid rounded" style="max-height: 320px;">
                    @else
                        <div class="text-center text-muted">
                            <i class="fa fa-qrcode mb-3" style="font-size: 80px;"></i>
                            <p class="mb-0">Gambar QRIS belum tersedia.</p>
                            <small>Simpan gambar ke public/images/qris-masjid.png</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card finus-card mb-4">
            <div class="card-body p-4">
                <h5 class="font-weight-bold mb-3">Cara Pembayaran</h5>

                <ol class="pl-3 text-muted">
                    <li>Buka aplikasi mobile banking atau e-wallet.</li>
                    <li>Pilih menu scan QRIS.</li>
                    <li>Scan kode QRIS masjid.</li>
                    <li>Masukkan nominal pembayaran.</li>
                    <li>Simpan bukti pembayaran jika diperlukan.</li>
                    <li>Input transaksi melalui menu Transaksi ZISWAF.</li>
                </ol>

                <a href="{{ route('jamaah.transaksi.create', 'infak') }}" class="btn btn-success btn-block mt-3">
                    <i class="fa fa-hand-holding-heart mr-1"></i>
                    Input Transaksi Infak
                </a>

                <a href="{{ route('jamaah.dashboard') }}" class="btn btn-light btn-block">
                    <i class="fa fa-arrow-left mr-1"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
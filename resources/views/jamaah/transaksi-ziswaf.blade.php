@extends('layouts.app')

@section('content')
<style>
    .finus-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .page-hero {
        border-radius: 22px;
        background: linear-gradient(135deg, #065f46, #16a34a);
        color: white;
    }

    .qris-box {
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        background: #f8fafc;
        padding: 18px;
        text-align: center;
    }
</style>

@php
    $jenisOptions = $config['jenisOptions'];
    $singleJenisKey = array_key_first($jenisOptions);
@endphp

<div class="page-hero p-4 p-md-5 mb-4">
    <h3 class="text-white font-weight-bold mb-2">{{ $config['title'] }}</h3>
    <p class="mb-0 text-white-50">
        {{ $config['subtitle'] }}
    </p>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row">
    <div class="col-lg-7">
        <div class="card finus-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-1 font-weight-bold">Form {{ $config['title'] }}</h5>
                <small class="text-muted">Isi data transaksi dengan benar.</small>
            </div>

            <div class="card-body px-4">
                <form method="POST" action="{{ route('jamaah.transaksi.store', $jenis) }}">
                    @csrf

                    <div class="form-group">
                        <label>Jenis Transaksi</label>

                        @if(count($jenisOptions) === 1)
                            <input type="hidden" name="jenis_ziswaf" value="{{ $singleJenisKey }}">

                            <input type="text"
                                class="form-control"
                                value="{{ $jenisOptions[$singleJenisKey] }}"
                                disabled>
                        @else
                            <select name="jenis_ziswaf" class="form-control" required>
                                <option value="">Pilih jenis</option>

                                @foreach($jenisOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('jenis_ziswaf') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        @error('jenis_ziswaf')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Nominal</label>

                        <input type="number"
                            name="nominal"
                            class="form-control"
                            value="{{ old('nominal') }}"
                            min="1000"
                            placeholder="Contoh: 50000"
                            required>

                        @error('nominal')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Metode Pembayaran</label>

                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                            <option value="tunai" @selected(old('metode_pembayaran') === 'tunai')>Tunai</option>
                            <option value="transfer" @selected(old('metode_pembayaran') === 'transfer')>Transfer</option>
                            <option value="qris" @selected(old('metode_pembayaran') === 'qris')>QRIS</option>
                        </select>

                        @error('metode_pembayaran')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div id="qris-info" class="qris-box mb-3" style="display: none;">
                        <h6 class="font-weight-bold mb-2">
                            <i class="fa fa-qrcode mr-1"></i>
                            Pembayaran QRIS
                        </h6>

                        <img src="{{ asset('images/qris-masjid.png') }}"
                            alt="QRIS Masjid"
                            class="img-fluid rounded mb-2"
                            style="max-height: 220px;"
                            onerror="this.style.display='none'; document.getElementById('qris-empty').style.display='block';">

                        <div id="qris-empty" class="text-muted" style="display: none;">
                            <i class="fa fa-qrcode mb-2" style="font-size: 48px;"></i>
                            <p class="mb-0">Gambar QRIS belum tersedia.</p>
                            <small>Simpan gambar di public/images/qris-masjid.png</small>
                        </div>

                        <small class="text-muted d-block mt-2">
                            Scan QRIS, lalu simpan transaksi ini agar admin dapat memverifikasi pembayaran.
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>

                        <textarea name="keterangan"
                            class="form-control"
                            rows="3"
                            placeholder="Opsional">{{ old('keterangan') }}</textarea>

                        @error('keterangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fa fa-paper-plane mr-1"></i>
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card finus-card mb-4">
            <div class="card-body p-4">
                <h5 class="font-weight-bold mb-2">Catatan Pembayaran</h5>

                <p class="text-muted mb-3">
                    Pilih jenis transaksi sesuai menu yang dibuka. QRIS hanya digunakan sebagai metode pembayaran,
                    bukan sebagai jenis transaksi.
                </p>

                <div class="bg-light rounded p-3 mb-3">
                    <small class="text-muted d-block">Menu saat ini</small>
                    <h5 class="mb-0 font-weight-bold text-success">
                        {{ $config['title'] }}
                    </h5>
                </div>

                <ul class="text-muted pl-3 mb-4">
                    <li>Transaksi akan masuk sebagai data jamaah.</li>
                    <li>Status awal transaksi menunggu verifikasi admin.</li>
                    <li>Setelah diverifikasi, transaksi dapat dihubungkan ke jurnal keuangan.</li>
                </ul>

                <a href="{{ route('jamaah.dashboard') }}" class="btn btn-light btn-block">
                    <i class="fa fa-arrow-left mr-1"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const metodePembayaran = document.getElementById('metode_pembayaran');
    const qrisInfo = document.getElementById('qris-info');

    function toggleQrisInfo() {
        if (!metodePembayaran || !qrisInfo) {
            return;
        }

        qrisInfo.style.display = metodePembayaran.value === 'qris' ? 'block' : 'none';
    }

    toggleQrisInfo();

    if (metodePembayaran) {
        metodePembayaran.addEventListener('change', toggleQrisInfo);
    }
});
</script>
@endpush
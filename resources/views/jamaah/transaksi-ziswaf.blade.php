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
</style>

<div class="page-hero p-4 p-md-5 mb-4">
    <h3 class="text-white font-weight-bold mb-2">Transaksi ZISWAF</h3>
    <p class="mb-0 text-white-50">
        Catat transaksi zakat, infak, sedekah, wakaf, atau fidyah secara mandiri.
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
                <h5 class="mb-1 font-weight-bold">Form Transaksi</h5>
                <small class="text-muted">Isi data transaksi dengan benar.</small>
            </div>

            <div class="card-body px-4">
                <form method="POST" action="{{ route('jamaah.ziswaf.store') }}">
                    @csrf

                    <div class="form-group">
                        <label>Jenis Transaksi</label>
                        <select name="jenis_ziswaf" class="form-control" required>
                            <option value="">Pilih jenis</option>
                            @foreach($jenisLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('jenis_ziswaf') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
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
                        <select name="metode_pembayaran" class="form-control" required>
                            <option value="tunai" @selected(old('metode_pembayaran') === 'tunai')>Tunai</option>
                            <option value="transfer" @selected(old('metode_pembayaran') === 'transfer')>Transfer</option>
                            <option value="qris" @selected(old('metode_pembayaran') === 'qris')>QRIS</option>
                        </select>

                        @error('metode_pembayaran')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
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
                    Setelah transaksi disimpan, admin dapat melakukan verifikasi dan menghubungkannya ke jurnal keuangan.
                </p>

                <a href="{{ route('jamaah.qris') }}" class="btn btn-outline-success btn-block">
                    <i class="fa fa-qrcode mr-1"></i>
                    Lihat QRIS Masjid
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
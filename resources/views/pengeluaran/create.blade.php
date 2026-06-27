@extends('layouts.app')
@section('content')
<form method="POST" action="{{ route('admin.pengeluaran.store') }}" enctype="multipart/form-data" class="card card-body">@csrf<h3>Tambah Pengeluaran</h3><input name="kategori" class="form-control mb-2" placeholder="Kategori" required><input name="deskripsi" class="form-control mb-2" placeholder="Deskripsi" required><input type="number" min="0" name="jumlah" class="form-control mb-2" required><input type="date" name="tanggal" class="form-control mb-2" required><input type="file" name="bukti_pembayaran" class="form-control mb-3"><button class="btn btn-primary">Simpan</button></form>
@endsection

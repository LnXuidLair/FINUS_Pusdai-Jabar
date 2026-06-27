@extends('layouts.app')
@section('content')
<div class="card card-body"><div class="d-flex justify-content-between"><h3>Pengeluaran</h3><a class="btn btn-primary" href="{{ route('admin.pengeluaran.create') }}">Tambah</a></div>
<table class="table mt-3"><tr><th>Tanggal</th><th>Kategori</th><th>Deskripsi</th><th>Jumlah</th></tr>@foreach($allPengeluaran as $item)<tr><td>{{ $item->tanggal }}</td><td>{{ $item->kategori }}</td><td>{{ $item->deskripsi }}</td><td>Rp {{ number_format($item->jumlah) }}</td></tr>@endforeach</table></div>
@endsection

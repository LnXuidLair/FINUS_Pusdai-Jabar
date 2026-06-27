@extends('layouts.app')
@section('content')
<div class="card card-body"><div class="d-flex justify-content-between"><h3>Penggajian</h3><a class="btn btn-primary" href="{{ route('admin.penggajian.create') }}">Proses Gaji</a></div>
<table class="table mt-3"><tr><th>Pegawai</th><th>Periode</th><th>Kehadiran</th><th>Total</th><th>Status</th></tr>@foreach($penggajians as $item)<tr><td>{{ $item->pegawai->nama_pegawai }}</td><td>{{ $item->periode }}</td><td>{{ $item->jumlah_kehadiran }}</td><td>Rp {{ number_format($item->total_gaji) }}</td><td>{{ $item->status_penggajian }}</td></tr>@endforeach</table></div>
@endsection

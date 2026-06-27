@extends('layouts.app')
@section('content')
<div class="card card-body"><div class="d-flex justify-content-between"><h3>Presensi</h3><a class="btn btn-primary" href="{{ route('admin.presensi.create') }}">Tambah</a></div>
<table class="table mt-3"><tr><th>Tanggal</th><th>Pegawai</th><th>Status</th></tr>@foreach($presensis as $item)<tr><td>{{ $item->tanggal }}</td><td>{{ $item->pegawai->nama_pegawai }}</td><td>{{ $item->status }}</td></tr>@endforeach</table></div>
@endsection

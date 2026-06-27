@extends('layouts.app')
@section('content')
<div class="card card-body"><div class="d-flex justify-content-between"><h3>Presensi Saya</h3><a class="btn btn-primary" href="{{ route('pegawai.presensi.create') }}">Isi Presensi</a></div>
<table class="table mt-3"><tr><th>Tanggal</th><th>Status</th><th>Keterangan</th></tr>@foreach($presensis as $item)<tr><td>{{ $item->tanggal }}</td><td>{{ $item->status }}</td><td>{{ $item->keterangan }}</td></tr>@endforeach</table></div>
@endsection

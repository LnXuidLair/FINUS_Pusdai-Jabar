@extends('layouts.app')
@section('content')
<div class="card card-body"><div class="d-flex justify-content-between"><h3>Gaji Jabatan</h3><a class="btn btn-primary" href="{{ route('admin.gaji-jabatan.create') }}">Tambah</a></div>
<table class="table mt-3"><tr><th>Jabatan</th><th>Gaji per Hari</th><th>Aksi</th></tr>@foreach($gajiJabatans as $item)<tr><td>{{ $item->jabatan }}</td><td>Rp {{ number_format($item->gaji_perhari) }}</td><td><a href="{{ route('admin.gaji-jabatan.edit',$item) }}">Edit</a></td></tr>@endforeach</table></div>
@endsection

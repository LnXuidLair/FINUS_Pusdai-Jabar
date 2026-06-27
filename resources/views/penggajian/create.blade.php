@extends('layouts.app')
@section('content')
<form method="POST" action="{{ route('admin.penggajian.store') }}" class="card card-body">@csrf<h3>Proses Penggajian</h3><select name="id_pegawai" class="form-control mb-2" required>@foreach($pegawais as $pegawai)<option value="{{ $pegawai->id }}">{{ $pegawai->nama_pegawai }} - {{ $pegawai->jabatan }}</option>@endforeach</select><input type="month" name="periode" class="form-control mb-2" required><select name="status_penggajian" class="form-control mb-3"><option value="belum_dibayar">Belum Dibayar</option><option value="sudah_dibayar">Sudah Dibayar</option></select><button class="btn btn-primary">Proses</button></form>
@endsection

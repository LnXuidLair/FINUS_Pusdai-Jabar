@extends('layouts.app')
@section('content')
<form method="POST" action="{{ route('admin.presensi.store') }}" class="card card-body">@csrf<h3>Tambah Presensi</h3><select name="id_pegawai" class="form-control mb-2">@foreach($pegawais as $pegawai)<option value="{{ $pegawai->id }}">{{ $pegawai->nama_pegawai }}</option>@endforeach</select><input type="date" name="tanggal" class="form-control mb-2" required><select name="status" class="form-control mb-3"><option value="hadir">Hadir</option><option value="izin">Izin</option><option value="lembur">Lembur</option><option value="tidak hadir">Tidak Hadir</option></select><button class="btn btn-primary">Simpan</button></form>
@endsection

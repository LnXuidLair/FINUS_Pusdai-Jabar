@extends('layouts.app')
@section('content')
<form method="POST" action="{{ route('admin.gaji-jabatan.store') }}" class="card card-body">@csrf<h3>Tambah Gaji Jabatan</h3><input class="form-control mb-2" name="jabatan" required><input class="form-control mb-3" type="number" min="0" name="gaji_perhari" required><button class="btn btn-primary">Simpan</button></form>
@endsection

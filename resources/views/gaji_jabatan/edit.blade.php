@extends('layouts.app')
@section('content')
<form method="POST" action="{{ route('admin.gaji-jabatan.update',$gajiJabatan) }}" class="card card-body">@csrf @method('PUT')<h3>Edit Gaji Jabatan</h3><input class="form-control mb-2" name="jabatan" value="{{ $gajiJabatan->jabatan }}" required><input class="form-control mb-3" type="number" min="0" name="gaji_perhari" value="{{ $gajiJabatan->gaji_perhari }}" required><button class="btn btn-primary">Perbarui</button></form>
@endsection

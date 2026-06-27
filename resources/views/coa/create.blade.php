@extends('layouts.app')
@section('content')
<form method="POST" action="{{ route('admin.coa.store') }}" class="card card-body">@csrf
    <h3>Tambah Akun</h3>
    <input class="form-control mb-2" name="kode_akun" placeholder="Kode akun" required>
    <input class="form-control mb-2" name="nama_akun" placeholder="Nama akun" required>
    <select class="form-control mb-3" name="header_akun" required>@foreach([1=>'Aset',2=>'Kewajiban',3=>'Ekuitas',4=>'Pendapatan',5=>'Beban'] as $key=>$label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select>
    <button class="btn btn-primary">Simpan</button>
</form>
@endsection

@extends('layouts.app')
@section('content')
<form method="POST" action="{{ route('admin.coa.update', $coa) }}" class="card card-body">@csrf @method('PUT')
    <h3>Edit Akun</h3>
    <input class="form-control mb-2" name="kode_akun" value="{{ old('kode_akun', $coa->kode_akun) }}" required>
    <input class="form-control mb-2" name="nama_akun" value="{{ old('nama_akun', $coa->nama_akun) }}" required>
    <select class="form-control mb-3" name="header_akun" required>@foreach([1=>'Aset',2=>'Kewajiban',3=>'Ekuitas',4=>'Pendapatan',5=>'Beban'] as $key=>$label)<option value="{{ $key }}" @selected($coa->header_akun==$key)>{{ $label }}</option>@endforeach</select>
    <button class="btn btn-primary">Perbarui</button>
</form>
@endsection

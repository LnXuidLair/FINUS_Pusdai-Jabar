@extends('layouts.app')
@section('content')
<form method="POST" action="{{ route('admin.presensi.update',$presensi) }}" class="card card-body">@csrf @method('PUT')<h3>Edit Presensi</h3><select name="id_pegawai" class="form-control mb-2">@foreach($pegawais as $pegawai)<option value="{{ $pegawai->id }}" @selected($presensi->id_pegawai==$pegawai->id)>{{ $pegawai->nama_pegawai }}</option>@endforeach</select><input type="date" name="tanggal" value="{{ $presensi->tanggal->format('Y-m-d') }}" class="form-control mb-2"><input name="status" value="{{ $presensi->status }}" class="form-control mb-3"><button class="btn btn-primary">Perbarui</button></form>
@endsection

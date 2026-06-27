@extends('layouts.app')
@section('content')
<form method="POST" action="{{ route('pegawai.presensi.store') }}" enctype="multipart/form-data" class="card card-body">@csrf<h3>Isi Presensi</h3><select name="status" class="form-control mb-2"><option value="hadir">Hadir</option><option value="izin">Izin</option><option value="lembur">Lembur</option></select><textarea name="keterangan" class="form-control mb-2" placeholder="Keterangan"></textarea><input type="file" name="bukti_kehadiran" class="form-control mb-3" required><button class="btn btn-primary">Kirim</button></form>
@endsection

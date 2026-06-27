@extends('layouts.app')
@section('content')
<div class="card card-body">
    <h3>{{ $pegawai->nama_pegawai }}</h3>
    <p>NIP: {{ $pegawai->nip }}</p>
    <p>Jabatan: {{ $pegawai->jabatan }}</p>
    <p>Email: {{ $pegawai->email }}</p>
    <p>Telepon: {{ $pegawai->no_telp ?: '-' }}</p>
</div>
@endsection

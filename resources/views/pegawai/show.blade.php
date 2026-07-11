@extends('layouts.app')

@section('content')
<div class="card card-body">
    <h3>{{ $pegawai->nama_pegawai }}</h3>

    <p><strong>NIP:</strong> {{ $pegawai->nip }}</p>
    <p><strong>Jabatan:</strong> {{ $pegawai->jabatan }}</p>
    <p><strong>Email:</strong> {{ $pegawai->email ?: '-' }}</p>
    <p><strong>Telepon:</strong> {{ $pegawai->no_telp ?: '-' }}</p>
    <p><strong>Alamat:</strong> {{ $pegawai->alamat ?: '-' }}</p>

    <p>
        <strong>Status:</strong>
        @if($pegawai->is_verified)
            <span class="badge badge-success">Verified</span>
        @else
            <span class="badge badge-warning text-dark">Unverified</span>
        @endif
    </p>

    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
</div>
@endsection
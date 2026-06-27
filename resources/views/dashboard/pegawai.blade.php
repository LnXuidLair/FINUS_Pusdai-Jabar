@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $pegawai->nama_pegawai }}</h4>
                <p class="mb-1">{{ $pegawai->jabatan }}</p>
                <small class="text-muted">NIP: {{ $pegawai->nip }}</small>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Presensi Bulan Ini</h4>
                <h2>{{ $presensiBulanIni->where('status', 'hadir')->count() }}</h2>
                <small class="text-muted">hari hadir</small>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Penggajian Terakhir</h4>
                <h2>Rp {{ number_format($penggajianTerakhir->total_gaji ?? 0, 0, ',', '.') }}</h2>
                <small class="text-muted">data penggajian terbaru</small>
            </div>
        </div>
    </div>
</div>
@endsection

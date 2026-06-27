@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Dashboard Jamaah</h4>
                <p class="mb-1">Assalamu'alaikum, {{ $jamaah->name }}.</p>
                <p class="text-muted mb-0">
                    Halaman ini menjadi pusat informasi keuangan dan layanan masjid untuk jamaah.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

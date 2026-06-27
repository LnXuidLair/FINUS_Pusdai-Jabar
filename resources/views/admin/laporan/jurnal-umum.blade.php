@extends('layouts.app')
@section('content')
<div class="card card-body"><h3>Jurnal Umum</h3><table class="table"><tr><th>Tanggal</th><th>Akun</th><th>Keterangan</th><th>Debit</th><th>Kredit</th></tr>@foreach($jurnals as $item)<tr><td>{{ $item->tanggal }}</td><td>{{ $item->akun }}</td><td>{{ $item->keterangan }}</td><td>{{ $item->tipe==='debit' ? number_format($item->jumlah) : '-' }}</td><td>{{ $item->tipe==='kredit' ? number_format($item->jumlah) : '-' }}</td></tr>@endforeach</table></div>
@endsection

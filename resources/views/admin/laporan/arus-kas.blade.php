@extends('layouts.app')
@section('content')
<div class="card card-body"><h3>Arus Kas</h3><p>Total pemasukan: Rp {{ number_format($totalPemasukan) }}</p><p>Total pengeluaran: Rp {{ number_format($totalPengeluaran) }}</p><p><strong>Saldo: Rp {{ number_format($saldo) }}</strong></p></div>
@endsection

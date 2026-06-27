@extends('layouts.app')
@section('content')
<div class="card card-body"><h3>Arus Kas PSAK</h3><p>Operasi: Rp {{ number_format($arusKas['operasi']['bersih']) }}</p><p>Investasi: Rp {{ number_format($arusKas['investasi']['bersih']) }}</p><p>Pendanaan: Rp {{ number_format($arusKas['pendanaan']['bersih']) }}</p><p><strong>Total bersih: Rp {{ number_format($totalBersih) }}</strong></p></div>
@endsection

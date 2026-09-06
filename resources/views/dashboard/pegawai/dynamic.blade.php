@extends('layouts.app')

@section('content')
@include('layouts.partials.finus-ui')
@include('dashboard.pegawai._dashboard-content')
@endsection

{{-- FINUS DARK MODE LOCAL: dashboard/pegawai/dynamic.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="dashboard/pegawai/dynamic.blade.php">
html[data-finus-theme="dark"] body .main { color:#F1F6F3 !important; }
</style>
@endpush


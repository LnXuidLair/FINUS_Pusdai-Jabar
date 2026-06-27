@extends('layouts.app')
@section('content')
<div class="card"><div class="card-body">
    <div class="d-flex justify-content-between"><h3>Chart of Accounts</h3><a class="btn btn-primary" href="{{ route('admin.coa.create') }}">Tambah Akun</a></div>
    <table class="table mt-3"><thead><tr><th>Kode</th><th>Nama</th><th>Kelompok</th><th>Aksi</th></tr></thead><tbody>
    @forelse($coa as $item)<tr><td>{{ $item->kode_akun }}</td><td>{{ $item->nama_akun }}</td><td>{{ $item->label_header_akun }}</td><td><a href="{{ route('admin.coa.edit', $item) }}">Edit</a></td></tr>
    @empty<tr><td colspan="4">Belum ada akun.</td></tr>@endforelse
    </tbody></table>
</div></div>
@endsection

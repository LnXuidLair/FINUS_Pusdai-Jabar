@extends('layouts.app')
@section('content')
<style>
    .finus-table-card {
        border-radius: 10px;
        border: 1px solid #e6eef9;
        box-shadow: 0 10px 30px rgba(15,23,42,.06);
        overflow: hidden;
        background: linear-gradient(180deg,#fff 0%,#fbfcff 100%);
    }
    .finus-header-gradient {
        position: relative;
        background: linear-gradient(to left,#15582A 0%,#179B40 25%,#19BB4B 50%,#22BA51 75%,#0E5423 100%) !important;
        padding: 14px 18px;
        overflow: hidden;
        border-radius: 10px 10px 0 0;
    }
    .finus-header-gradient::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,.15);
        pointer-events: none;
    }
    .finus-header-content {
        position: relative;
        z-index: 2;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .finus-header-left {
        display: flex;
        align-items: center;
    }
    .finus-header-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: rgba(255,255,255,.14);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }
    .finus-header-title {
        font-weight: 800;
        text-shadow: 0 3px 8px rgba(0,0,0,.35);
    }
    .finus-header-subtitle {
        font-size: 13px;
        color: rgba(255,255,255,.95);
    }
    .finus-search {
        min-width: 250px;
        background: rgba(255,255,255,.96);
    }
    .table thead th {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: #4a5568;
        background: #fbfdff;
        border-bottom: 2px solid #f1f5f9;
        vertical-align: middle;
    }
    .table td {
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background: rgba(0,0,0,.015);
    }
    .btn-outline-info {
        color: #0b7285;
        border-color: rgba(11,114,133,.12);
    }
    .btn-outline-warning {
        color: #b77a00;
        border-color: rgba(183,122,0,.08);
    }
    .btn-outline-danger {
        color: #b91c1c;
        border-color: rgba(185,28,28,.08);
    }
    .badge-success {
        background: linear-gradient(180deg,#10b981,#059669);
    }
    .badge-warning {
        background: linear-gradient(180deg,#f59e0b,#d97706);
    }
    .btn-group form {
        display: inline-flex;
        margin: 0;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card finus-table-card">
            <div class="finus-header-gradient">
                <div class="finus-header-content">
                    <div class="finus-header-left">
                        <div class="finus-header-icon">
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <div class="finus-header-title">Data Pegawai</div>
                            <div class="finus-header-subtitle" id="recordCount" data-label="pegawai">
                                Total: {{ is_countable($pegawais) ? count($pegawais) : 0 }} pegawai
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <input id="searchInput" type="search" class="form-control form-control-sm mr-2 finus-search" placeholder="Cari NIP, nama, atau jabatan">
                        <a href="{{ route('admin.pegawai.create') }}" class="btn btn-light btn-sm shadow-sm">
                            <i class="fa fa-plus"></i> Tambah Data
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="table-responsive">
                    <table id="pegawaiTable" class="table table-hover table-bordered table-sm">
                        <thead>
                            <tr class="text-center">
                                <th width="60">No</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Jabatan</th>
                                <th width="120">Status</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pegawais as $pegawai)
                                <tr class="text-center" data-search-row data-search-start="{{ $pegawai->nip }}|{{ $pegawai->nama_pegawai }}|{{ $pegawai->email }}|{{ $pegawai->jabatan }}">
                                    <td data-row-number>{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold">{{ $pegawai->nip }}</td>
                                    <td>{{ $pegawai->nama_pegawai }}</td>
                                    <td>{{ $pegawai->email ?: '-' }}</td>
                                    <td>{{ $pegawai->jabatan }}</td> 
                                    <td>
                                        @if($pegawai->is_verified)
                                            <span class="badge badge-success px-2 py-1">
                                                <i class="fa fa-check mr-1"></i> Verified
                                            </span>
                                        @else
                                            <span class="badge badge-warning px-2 py-1 text-dark">
                                                <i class="fa fa-clock mr-1"></i> Unverified
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.pegawai.show', $pegawai->id) }}" class="btn btn-outline-info btn-sm" title="Detail">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.pegawai.edit', $pegawai->id)}}" class="btn btn-outline-warning btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.pegawai.destroy', $pegawai->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus {{ $pegawai->nama_pegawai }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">Tidak ada data pegawai saat ini.</td>
                                </tr>
                            @endforelse
                            <tr id="emptySearchRow" style="display:none;">
                                <td colspan="7" class="text-center text-muted py-5">Data tidak ditemukan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    const input = document.getElementById('searchInput');
    const rows = Array.from(document.querySelectorAll('[data-search-row]'));
    const emptyRow = document.getElementById('emptySearchRow');
    const countEl = document.getElementById('recordCount');
    function normalize(value) {
        return (value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }
    function filterRows() {
        const keyword = normalize(input.value);
        let visible = 0;
        rows.forEach(row => {
            const values = (row.dataset.searchStart || '')
                .split('|')
                .map(normalize)
                .filter(Boolean);
            const match = keyword === '' || values.some(value => value.startsWith(keyword));
            row.style.display = match ? '' : 'none';
            if (match) {
                visible++;
                const numberCell = row.querySelector('[data-row-number]');
                if (numberCell) {
                    numberCell.textContent = visible;
                }
            }
        });
        if (emptyRow) {
            emptyRow.style.display = visible === 0 && rows.length > 0 ? '' : 'none';
        }
        if (countEl) {
            const label = countEl.dataset.label || 'data';
            countEl.textContent = `Total: ${visible} ${label}`;
        }
    }
    if (input) {
        input.addEventListener('input', filterRows);
        filterRows();
    }
})();
</script>
@endsection
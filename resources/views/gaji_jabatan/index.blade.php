@extends('layouts.app')
@section('content')
<style>
    .finus-table-card{ 
        border-radius:10px; 
        border:1px solid #e6eef9; 
        box-shadow:0 10px 30px rgba(15,23,42,.06); 
        overflow:hidden; 
        background:linear-gradient(180deg,#fff 0%,#fbfcff 100%);
    }
    .finus-header-gradient{ 
        position:relative; 
        background:linear-gradient(to left,#15582A 0%,#179B40 25%,#19BB4B 50%,#22BA51 75%,#0E5423 100%) !important; 
        padding:14px 18px; 
        overflow:hidden; 
        border-radius:10px 10px 0 0;
    }
    .finus-header-gradient::after{ 
        content:""; 
        position:absolute; 
        inset:0; 
        background:rgba(0,0,0,.15); 
        pointer-events:none;
    }
    .finus-header-content{ 
        position:relative; 
        z-index:2; 
        color:#fff; 
        display:flex; 
        justify-content:space-between; 
        align-items:center; 
        gap:16px; 
        flex-wrap:wrap;
    }
    .finus-header-left{ 
        display:flex; 
        align-items:center;
    }
    .finus-header-icon{ 
        width:42px; 
        height:42px; 
        border-radius:10px; 
        background:rgba(255,255,255,.14); 
        display:flex; 
        align-items:center; 
        justify-content:center; 
        margin-right:12px;
    }
    .finus-header-title{ 
        font-weight:800; 
        text-shadow:0 3px 8px rgba(0,0,0,.35);
    }
    .finus-header-subtitle{ 
        font-size:13px; color:rgba(255,255,255,.95);
    }
    .finus-search{ 
        min-width:250px; background:rgba(255,255,255,.96);
    }
    .table thead th{ 
        font-size:13px; 
        font-weight:700; 
        text-transform:uppercase; 
        color:#4a5568; 
        background:#fbfdff; 
        border-bottom:2px solid #f1f5f9; vertical-align:middle;
    }
    .table td{ 
        vertical-align:middle; 
    }
    .table tbody tr:hover{ 
        background:rgba(0,0,0,.015); 
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card finus-table-card">
            <div class="finus-header-gradient">
                <div class="finus-header-content">
                    <div class="finus-header-left">
                        <div class="finus-header-icon">
                            <i class="fa fa-briefcase text-white"></i>
                        </div>
                        <div>
                            <div class="finus-header-title">Gaji Jabatan</div>
                            <div class="finus-header-subtitle" id="recordCount" data-label="data">
                                Total: {{ is_countable($gajiJabatans) ? count($gajiJabatans) : 0 }} data
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <input id="searchInput" type="search" class="form-control form-control-sm mr-2 finus-search" placeholder="Cari nama jabatan atau gaji">
                        <a class="btn btn-light btn-sm shadow-sm" href="{{ route('admin.gaji-jabatan.create') }}">
                            <i class="fa fa-plus"></i> Tambah
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table id="searchTable" class="table table-hover table-bordered table-sm">
                        <thead>
                            <tr class="text-center">
                                <th width="60" class="text-center">No</th>
                                <th>Jabatan</th>
                                <th>Gaji per Hari</th>
                                <th width="120" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gajiJabatans as $item)
                                @php
                                    $gajiFormatted = 'Rp '.number_format($item->gaji_perhari, 0, ',', '.');
                                    $gajiRaw = preg_replace('/\D+/', '', (string) $item->gaji_perhari);
                                @endphp
                                <tr data-search-row data-search-start="{{ $item->jabatan }}|{{ $gajiFormatted }}|{{ $gajiRaw }}" class="text-center">
                                    <td class="text-center" data-row-number>{{ $loop->iteration }}</td>
                                    <td>{{ $item->jabatan }}</td>
                                    <td>{{ $gajiFormatted }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.gaji-jabatan.edit', $item) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        Belum ada data gaji jabatan.
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="emptySearchRow" style="display:none;">
                                <td colspan="4" class="text-center text-muted py-5">
                                    Data tidak ditemukan.
                                </td>
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
    function onlyDigits(value) {
        return (value || '').replace(/\D/g, '');
    }
    function filterRows() {
        const keyword = normalize(input.value);
        const keywordDigits = onlyDigits(keyword);
        let visible = 0;
        rows.forEach(row => {
            const values = (row.dataset.searchStart || '')
                .split('|')
                .map(normalize)
                .filter(Boolean);
            const matchText = keyword === '' || values.some(value => value.startsWith(keyword));
            const matchNumber = keywordDigits !== '' && values.some(value => {
                const digitValue = onlyDigits(value);
                return digitValue.startsWith(keywordDigits);
            });
            const match = keyword === '' || matchText || matchNumber;
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
@extends('layouts.app')

@section('content')
<style>
    .finus-table-card { border-radius:10px; border:1px solid #e6eef9; box-shadow:0 10px 30px rgba(15,23,42,.06); overflow:hidden; background:linear-gradient(180deg,#fff 0%,#fbfcff 100%); }
    .finus-header-gradient { position:relative; background:linear-gradient(to left,#15582A 0%,#179B40 25%,#19BB4B 50%,#22BA51 75%,#0E5423 100%) !important; padding:14px 18px; overflow:hidden; border-radius:10px 10px 0 0; }
    .finus-header-gradient::after { content:""; position:absolute; inset:0; background:rgba(0,0,0,.15); pointer-events:none; }
    .finus-header-content { position:relative; z-index:2; color:#fff; display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; }
    .finus-header-left { display:flex; align-items:center; }
    .finus-header-icon { width:42px; height:42px; border-radius:10px; background:rgba(255,255,255,.14); display:flex; align-items:center; justify-content:center; margin-right:12px; }
    .finus-header-title { font-weight:800; text-shadow:0 3px 8px rgba(0,0,0,.35); }
    .finus-header-subtitle { font-size:13px; color:rgba(255,255,255,.95); }
    .finus-search { min-width:250px; background:rgba(255,255,255,.96); }
    .table thead th { font-size:13px; font-weight:700; text-transform:uppercase; color:#4a5568; background:#fbfdff; border-bottom:2px solid #f1f5f9; vertical-align:middle; }
    .table td { vertical-align:middle; }
    .table tbody tr:hover { background:rgba(0,0,0,.015); }
</style>

<div class="row">
    <div class="col-12">
        <div class="card finus-table-card">
            <div class="finus-header-gradient">
                <div class="finus-header-content">
                    <div class="finus-header-left">
                        <div class="finus-header-icon">
                            <i class="fa fa-receipt text-white"></i>
                        </div>
                        <div>
                            <div class="finus-header-title">Pengeluaran</div>
                            <div class="finus-header-subtitle" id="recordCount">
                                Total: {{ is_countable($allPengeluaran) ? count($allPengeluaran) : 0 }} data
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <input id="searchInput" type="search" class="form-control form-control-sm mr-2 finus-search" placeholder="Cari tanggal, kategori, deskripsi, atau jumlah...">
                        <a class="btn btn-light btn-sm shadow-sm" href="{{ route('admin.pengeluaran.create') }}">
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
                            <tr>
                                <th width="60" class="text-center">No</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allPengeluaran as $item)
                                @php
                                    $jumlahFormatted = 'Rp '.number_format($item->jumlah, 0, ',', '.');
                                @endphp
                                <tr
                                    data-search-row
                                    data-search-start="{{ $item->tanggal }}|{{ $item->kategori }}|{{ $item->deskripsi }}|{{ $jumlahFormatted }}"
                                >
                                    <td class="text-center" data-row-number>{{ $loop->iteration }}</td>
                                    <td>{{ $item->tanggal }}</td>
                                    <td>{{ $item->kategori }}</td>
                                    <td>{{ $item->deskripsi }}</td>
                                    <td class="text-right">{{ $jumlahFormatted }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        Tidak ada data pengeluaran.
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="emptySearchRow" style="display:none;">
                                <td colspan="5" class="text-center text-muted py-5">
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
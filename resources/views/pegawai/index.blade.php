@extends('layouts.app')

@section('content')
<style>
    .table-card {
        border-radius: 10px;
        border: 1px solid #e6eef9;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .table thead th {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        color: #4a5568;
        background: #fbfdff;
        border-bottom: 2px solid #f1f5f9;
    }
    .search-input {
        min-width: 260px;
    }
    .no-data-illustration {
        opacity: .8;
    }
    .table tbody tr:hover {
        background: rgba(0,0,0,0.01);
    }
    .small-muted {
        font-size: 12px;
        color: #6b7280;
    }
    /* Premium tweaks */
    .card.table-card { background: linear-gradient(180deg,#ffffff 0%, #fbfcff 100%); }
    .table td, .table th { vertical-align: middle; }
    .btn-outline-info { color: #0b7285; border-color: rgba(11,114,133,.12); }
    .btn-outline-warning { color: #b77a00; border-color: rgba(183,122,0,.08); }
    .btn-outline-danger { color: #b91c1c; border-color: rgba(185,28,28,.08); }
    .badge-success { background: linear-gradient(180deg,#10b981,#059669); }
    .badge-warning { background: linear-gradient(180deg,#f59e0b,#d97706); }
    .pagination { margin: 0; }
    .page-link { color: #334155; }
    /* Header gradient (scoped to this card) */
    .table-card .header-gradient{
        position:relative;
        background:linear-gradient(135deg,#f97316,#6366f1,#0b7285);
        border-radius:10px 10px 0 0;
        overflow:hidden;
        padding:14px 18px;
    }
    .table-card .header-gradient::after{
        content:"";
        position:absolute;
        inset:0;
        background:rgba(0,0,0,.18);
    }
    .table-card .header-content{ position:relative; z-index:2; color:#fff; display:flex; align-items:center; }
    .table-card .header-icon{ width:40px; height:40px; border-radius:10px; background:rgba(255,255,255,.12); display:flex; align-items:center; justify-content:center; margin-right:12px; }
    .table-card .header-title{ font-weight:700; letter-spacing:.2px; text-shadow:0 3px 8px rgba(0,0,0,.45); }
</style>

<div class="row">
    <div class="col-12">
        <div class="card table-card">

            <div class="header-gradient">
                <div class="header-content d-flex justify-content-between align-items-center w-100">
                    <div class="d-flex align-items-center">
                        <div class="header-icon">
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <div class="header-title">Data Pegawai</div>
                            <div class="small-muted" id="recordCount" style="color:rgba(255,255,255,0.95); font-size:13px;">Total: {{ method_exists($pegawais, 'total') ? $pegawais->total() : (is_countable($pegawais) ? count($pegawais) : 0) }} pegawai</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <input id="search" type="search" class="form-control form-control-sm mr-2" placeholder="Cari nama, NIP, email, atau jabatan..." style="min-width:240px; background:rgba(255,255,255,0.95);">
                        <a href="{{ route('admin.pegawai.create') }}" class="btn btn-light btn-sm shadow-sm">
                            <i class="fa fa-plus"></i> Tambah Data
                        </a>
                    </div>
                </div>
            </div>

            {{-- BODY --}}
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table id="pegawaiTable" class="table table-hover table-bordered table-sm align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Jabatan</th>
                                <th width="120" class="text-center">Status</th>
                                <th width="140" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($pegawais as $i => $pegawai)
                            <tr>
                                <td class="font-weight-bold">{{ $pegawai->nip }}</td>
                                <td>{{ $pegawai->nama_pegawai }}</td>
                                <td>{{ $pegawai->email ?: '-' }}</td>
                                <td>{{ $pegawai->jabatan }}</td>
                                <td class="text-center">
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
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.pegawai.show', $pegawai->id) }}" class="btn btn-outline-info btn-sm" title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.pegawai.edit', $pegawai->id) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.pegawai.destroy', $pegawai->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus {{ $pegawai->nama_pegawai }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="mb-3">
                                        <i class="fa fa-folder-open fa-2x no-data-illustration"></i>
                                    </div>
                                    <div>Tidak ada data pegawai saat ini.</div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    <!-- Pagination controls -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="d-flex align-items-center">
                            <label class="mb-0 small-muted mr-2">Rows per page</label>
                            <select id="perPage" class="form-control form-control-sm" style="width:90px">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="mr-3 small-muted" id="pageInfo">Menampilkan 0–0 dari 0</div>
                            <nav>
                                <ul id="pager" class="pagination mb-0">
                                    <!-- page buttons injected here -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    (function(){
        const search = document.getElementById('search');
        const table = document.getElementById('pegawaiTable');
        const tbody = table ? table.tBodies[0] : null;
        const allRows = tbody ? Array.from(tbody.rows).filter(r => !r.textContent.includes('Tidak ada data')): [];
        const countEl = document.getElementById('recordCount');
        const pager = document.getElementById('pager');
        const perPageEl = document.getElementById('perPage');
        const pageInfo = document.getElementById('pageInfo');

        let filtered = allRows.slice();
        let currentPage = 1;

        function updateCount(total, showingFrom, showingTo){
            if(countEl) countEl.textContent = `Total: ${total} pegawai`;
            if(pageInfo) pageInfo.textContent = `Menampilkan ${showingFrom}–${showingTo} dari ${total}`;
        }

        function renderPager(){
            if(!pager) return;
            pager.innerHTML = '';
            const perPage = parseInt(perPageEl.value, 10);
            const total = filtered.length;
            const totalPages = Math.max(1, Math.ceil(total / perPage));
            const createPageItem = (p, label = null, disabled = false, active = false) => {
                const li = document.createElement('li');
                li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
                const a = document.createElement('a');
                a.className = 'page-link';
                a.href = '#';
                a.dataset.page = p;
                a.textContent = label || p;
                li.appendChild(a);
                return li;
            };

            // prev
            pager.appendChild(createPageItem(Math.max(1, currentPage-1), '‹', currentPage===1, false));

            // pages (show up to 7, centered)
            const maxButtons = 7;
            let start = Math.max(1, currentPage - Math.floor(maxButtons/2));
            let end = Math.min(totalPages, start + maxButtons - 1);
            if(end - start < maxButtons - 1) start = Math.max(1, end - maxButtons + 1);
            for(let p = start; p <= end; p++){
                pager.appendChild(createPageItem(p, null, false, p===currentPage));
            }

            // next
            pager.appendChild(createPageItem(Math.min(totalPages, currentPage+1), '›', currentPage===totalPages, false));
        }

        function showPage(page){
            const perPage = parseInt(perPageEl.value, 10);
            const total = filtered.length;
            const totalPages = Math.max(1, Math.ceil(total / perPage));
            currentPage = Math.min(Math.max(1, page), totalPages);
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;

            // hide all, then show slice
            allRows.forEach(r => r.style.display = 'none');
            const slice = filtered.slice(start, end);
            slice.forEach(r => r.style.display = '');

            // empty state handling
            if(total === 0){
                // insert a single row showing no results
                if(!tbody.querySelector('.no-results-row')){
                    const tr = document.createElement('tr');
                    tr.className = 'no-results-row';
                    tbody.appendChild(tr);
                }
            } else {
                const nr = tbody.querySelector('.no-results-row');
                if(nr) nr.remove();
            }

            const showingFrom = total === 0 ? 0 : start + 1;
            const showingTo = Math.min(total, end);
            updateCount(total, showingFrom, showingTo);
            renderPager();
        }

        function applyFilter(term){
            const q = term.trim().toLowerCase();
            filtered = allRows.filter(r => {
                const cells = Array.from(r.cells).slice(1,5); // NIP, Nama, Email, Jabatan
                const text = cells.map(c => c.textContent.toLowerCase()).join(' ');
                return q === '' || text.indexOf(q) !== -1;
            });
            currentPage = 1;
            showPage(currentPage);
        }

        // events
        if(perPageEl) perPageEl.addEventListener('change', () => showPage(1));
        if(pager) pager.addEventListener('click', function(e){
            e.preventDefault();
            const a = e.target.closest('a.page-link');
            if(!a) return;
            const p = parseInt(a.dataset.page, 10);
            showPage(p);
        });
        if(search) search.addEventListener('input', function(e){ applyFilter(e.target.value); });

        // initial render
        applyFilter('');
    })();
</script>
@endsection
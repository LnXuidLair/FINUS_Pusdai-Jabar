@extends('layouts.app')

@section('title', 'Agenda Kegiatan Masjid')

@section('content')
@include('layouts.partials.finus-ui')

<style>
    .ag-page { padding: 8px 0 40px; }

    .ag-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .ag-heading-title h1 {
        font-size: 22px;
        font-weight: 800;
        color: var(--jt-text);
        margin: 0 0 4px;
    }

    .ag-heading-title p {
        font-size: 13px;
        color: var(--jt-muted);
        margin: 0;
    }

    .ag-btn-add {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #064e1a, #179b40);
        color: #fff;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: transform .2s, box-shadow .2s;
        white-space: nowrap;
    }

    .ag-btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(23,155,64,.35);
        color: #fff;
        text-decoration: none;
    }

    .ag-card {
        background: #fff;
        border: 1px solid var(--jt-border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15,23,42,.06);
    }

    .ag-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ag-table thead th {
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--jt-muted);
        border-bottom: 1px solid var(--jt-border);
        text-align: left;
    }

    .ag-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background .15s;
    }

    .ag-table tbody tr:last-child { border-bottom: none; }
    .ag-table tbody tr:hover { background: #f8fafc; }

    .ag-table td {
        padding: 14px 16px;
        font-size: 13px;
        color: var(--jt-text);
        vertical-align: middle;
    }

    .ag-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }

    .ag-badge-kajian     { background: #fef3c7; color: #92400e; }
    .ag-badge-sosial     { background: #dbeafe; color: #1e40af; }
    .ag-badge-pendidikan { background: #dcfce7; color: #15803d; }
    .ag-badge-ibadah     { background: #ede9fe; color: #6d28d9; }

    .ag-status-on  { color: #15803d; font-weight: 700; font-size: 12px; }
    .ag-status-off { color: #9ca3af; font-weight: 700; font-size: 12px; }

    .ag-actions { display: flex; gap: 8px; align-items: center; }

    .ag-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all .15s;
    }

    .ag-btn-edit    { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .ag-btn-edit:hover { background: #dcfce7; color: #15803d; text-decoration: none; }

    .ag-btn-toggle-on  { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .ag-btn-toggle-off { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .ag-btn-toggle-on:hover, .ag-btn-toggle-off:hover { opacity: .8; }

    .ag-btn-delete { background: #fff5f5; color: #b91c1c; border: 1px solid #fecaca; }
    .ag-btn-delete:hover { background: #fee2e2; }

    .ag-empty {
        text-align: center;
        padding: 48px 16px;
        color: var(--jt-muted);
    }

    .ag-empty i { font-size: 40px; margin-bottom: 12px; display: block; }
</style>

<div class="jt-page ag-page">

    {{-- Alert --}}
    @if(session('success'))
        <div class="jt-alert" style="margin-bottom:18px;">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="ag-heading">
        <div class="ag-heading-title">
            <h1><i class="fa-solid fa-calendar-days" style="color:#179b40;margin-right:10px;"></i>Agenda Kegiatan Masjid</h1>
            <p>Kelola daftar agenda dan kegiatan yang ditampilkan di dashboard jamaah.</p>
        </div>
        <a href="{{ route('admin.agenda-kegiatan.create') }}" class="ag-btn-add">
            <i class="fa-solid fa-plus"></i>
            Tambah Agenda
        </a>
    </div>

    <div class="ag-card">
        <table class="ag-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul Kegiatan</th>
                    <th>Kategori</th>
                    <th>Jadwal</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agenda as $item)
                    <tr>
                        <td style="color:var(--jt-muted);font-size:12px;">{{ $item->urutan ?: $loop->iteration }}</td>
                        <td>
                            <div style="font-weight:700;">{{ $item->judul }}</div>
                            @if($item->deskripsi)
                                <div style="font-size:11px;color:var(--jt-muted);margin-top:2px;">{{ Str::limit($item->deskripsi, 60) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="ag-badge ag-badge-{{ $item->kategori }}">
                                {{ $kategoriLabels[$item->kategori] ?? $item->kategori }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:12px;">{{ $item->hari }}</div>
                            <div style="font-size:11px;color:var(--jt-muted);">{{ $item->waktu }}</div>
                        </td>
                        <td style="font-size:12px;">{{ $item->lokasi }}</td>
                        <td>
                            @if($item->is_aktif)
                                <span class="ag-status-on"><i class="fa-solid fa-circle" style="font-size:8px;"></i> Aktif</span>
                            @else
                                <span class="ag-status-off"><i class="fa-regular fa-circle" style="font-size:8px;"></i> Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="ag-actions">
                                <a href="{{ route('admin.agenda-kegiatan.edit', $item) }}" class="ag-btn ag-btn-edit">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>

                                <form method="POST" action="{{ route('admin.agenda-kegiatan.toggle', $item) }}" style="margin:0;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="ag-btn {{ $item->is_aktif ? 'ag-btn-toggle-on' : 'ag-btn-toggle-off' }}">
                                        <i class="fa-solid {{ $item->is_aktif ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        {{ $item->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.agenda-kegiatan.destroy', $item) }}" style="margin:0;"
                                      onsubmit="return confirm('Yakin ingin menghapus agenda \"{{ $item->judul }}\"? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ag-btn ag-btn-delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="ag-empty">
                            <i class="fa-regular fa-calendar-xmark"></i>
                            Belum ada agenda kegiatan. Klik <strong>Tambah Agenda</strong> untuk memulai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

{{-- FINUS DARK MODE LOCAL: admin/agenda-kegiatan/index.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="admin/agenda-kegiatan/index.blade.php">
html[data-finus-theme="dark"] body .ag-page { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .ag-page .ag-heading-title { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body .ag-page .ag-card { border-color:#293D31 !important; background:#111A15 !important; color:#F1F6F3 !important; box-shadow:0 16px 38px rgba(0,0,0,.22) !important; }
html[data-finus-theme="dark"] body .ag-page .ag-table thead th { border-color:#293D31 !important; background:#17261D !important; color:#D2E1D6 !important; }
html[data-finus-theme="dark"] body .ag-page .ag-table tbody td { border-color:#24372B !important; background:#111A15 !important; color:#DCE7E0 !important; }
html[data-finus-theme="dark"] body .ag-page .ag-table tbody tr:hover td { background:#17251D !important; }
html[data-finus-theme="dark"] body .ag-page .ag-btn-edit { border-color:#66542B !important; background:#332817 !important; color:#F2CE7E !important; }
html[data-finus-theme="dark"] body .ag-page .ag-btn-delete { border-color:#693B40 !important; background:#341C20 !important; color:#F39DA3 !important; }
html[data-finus-theme="dark"] body .ag-page .ag-status-on { border-color:#2D6140 !important; background:#15331E !important; color:#A9F0B9 !important; }
html[data-finus-theme="dark"] body .ag-page .ag-status-off { border-color:#4B554E !important; background:#1B241E !important; color:#B8C4BB !important; }
html[data-finus-theme="dark"] body .ag-page .ag-empty { color:#9EAEA4 !important; }
</style>
@endpush


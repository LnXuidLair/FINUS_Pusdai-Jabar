@extends('layouts.app')
@section('title', 'Kategori Transaksi')
@section('hide-page-header', '1')

@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <nav class="tc-tabs" aria-label="Master pencatatan">
        <a href="{{ route('admin.coa.index') }}"><i class="fa-solid fa-book"></i>Daftar Akun</a>
        <a href="{{ route('admin.transaction-categories.index') }}" class="active"><i class="fa-solid fa-tags"></i>Kategori Transaksi</a>
    </nav>

    <section class="fmu-hero">
        <div class="fmu-hero-main"><span class="fmu-hero-icon"><i class="fa-solid fa-tags"></i></span><div><h1>Kategori Transaksi</h1><p>Pemetaan kegiatan ke akun dan sumber dana pengeluaran.</p></div></div>
        <div class="fmu-hero-actions"><a href="{{ route('admin.transaction-categories.create') }}" class="fmu-btn"><i class="fa-solid fa-plus"></i>Tambah Kategori</a></div>
    </section>

    <section class="fmu-card">
        <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-table-list"></i></span><div><h2>Daftar Kategori</h2><p>{{ $categories->count() }} kategori tersedia untuk form transaksi.</p></div></div></div>
        <div class="tc-table-wrap">
            <table class="tc-table">
                <thead><tr><th>Kode & Kategori</th><th>Sumber Dana</th><th>Akun Tujuan</th><th>Form</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($categories as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong><small>{{ $item->code }}</small></td>
                        <td><span class="tc-badge">{{ $fundLabels[$item->resolvedFundType()] ?? 'Belum dipetakan' }}</span></td>
                        <td><strong>{{ $item->coa?->nama_akun ?? 'Belum dipetakan' }}</strong><small>{{ $item->coa?->kode_akun }}</small></td>
                        <td>{{ $formLabels[$item->form_type] ?? $item->form_type }}</td>
                        <td><span class="tc-status {{ $item->is_active ? 'on' : 'off' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="tc-actions">
                            <a href="{{ route('admin.transaction-categories.edit', $item) }}" title="Ubah kategori"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.transaction-categories.destroy', $item) }}" onsubmit="return confirm('Hapus kategori {{ e($item->name) }}?')">@csrf @method('DELETE')<button type="submit" title="Hapus kategori"><i class="fa-solid fa-trash"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="tc-empty">Belum ada kategori transaksi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
.tc-tabs{display:flex;gap:8px;margin-bottom:14px}.tc-tabs a{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:44px;padding:0 15px;border:1px solid #D6E9DC;border-radius:12px;background:#EAF7EE;color:#0E5423!important;font-size:12px;font-weight:800;text-decoration:none!important;transition:transform .2s ease,box-shadow .2s ease,background .2s ease}.tc-tabs a:hover{background:#DFF1E4;transform:translateY(-1px);box-shadow:0 8px 16px rgba(14,84,35,.11)}.tc-tabs a.active{border-color:rgba(23,155,64,.45);background:linear-gradient(135deg,#0E5423,#179B40);color:#fff!important}.tc-tabs a.active:hover{background:linear-gradient(135deg,#0A431C,#128334)}html[data-finus-theme="dark"] body .tc-tabs a:not(.active){border-color:#34523F!important;background:#14261B!important;color:#C9EFD2!important}.tc-table-wrap{overflow-x:auto}.tc-table{width:100%;border-collapse:collapse}.tc-table th,.tc-table td{padding:14px 16px;border-bottom:1px solid #e5ece7;text-align:left;vertical-align:middle}.tc-table th{background:#f6faf7;color:#526458;font-size:11px;text-transform:uppercase}.tc-table td{color:#24352a;font-size:13px}.tc-table td strong,.tc-table td small{display:block}.tc-table td small{margin-top:3px;color:#718078}.tc-badge,.tc-status{display:inline-flex;margin:2px;padding:5px 8px;border-radius:999px;background:#eef5f0;color:#31513c;font-size:11px;font-weight:700}.tc-status.on{background:#e6f8eb;color:#08752f}.tc-status.off{background:#f1f3f2;color:#6b7280}.tc-actions{display:flex;gap:7px}.tc-actions a,.tc-actions button{display:grid;place-items:center;width:34px;height:34px;border:1px solid #dce7df;border-radius:6px;background:#fff;color:#176d35}.tc-actions button{color:#bf3434;cursor:pointer}.tc-empty{text-align:center!important;color:#718078!important}
</style>
@endpush

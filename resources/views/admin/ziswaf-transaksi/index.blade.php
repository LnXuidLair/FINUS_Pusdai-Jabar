@extends('layouts.app')
@section('title', 'Transaksi ZISWAF — Admin FINUS')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
<style>
    .fzt-page { padding: 4px 0 45px; color: var(--finus-text,#193329); }
    .fzt-hero { display:flex; align-items:center; gap:16px; padding:25px 28px; border-radius:19px; margin-bottom:20px; background:linear-gradient(118deg,#0E5423,#179B40 65%,#20AA54); box-shadow:0 15px 28px rgba(14,84,35,.15); color:white; }
    .fzt-hero-icon { width:49px; height:49px; display:grid; place-items:center; border-radius:14px; border:1px solid rgba(255,255,255,.25); background:rgba(255,255,255,.13); font-size:21px; }
    .fzt-hero h1 { margin:0 0 3px; color:#fff; font-size:23px; font-weight:800; }
    .fzt-hero p {margin:0; color:rgba(255,255,255,.87); font-size:13px;}
    .fzt-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin-bottom:19px; }
    .fzt-stat,.fzt-panel { border:1px solid var(--finus-border,#e1eae4); background:var(--finus-surface,#fff); border-radius:17px; box-shadow:0 9px 22px rgba(15,23,42,.045); }
    .fzt-stat {padding:18px 19px; min-width:0;}
    .fzt-stat small {display:block; color:var(--finus-muted,#667e70); font-size:12px; margin-bottom:8px;}
    .fzt-stat strong {font-size:clamp(16px,1.7vw,22px); font-weight:850; overflow-wrap:anywhere;}
    .fzt-panel { padding:19px; margin-bottom:18px; }
    .fzt-filter {display:grid; grid-template-columns:minmax(140px,2fr) repeat(3,minmax(110px,1fr)) auto; gap:10px; align-items:end;}
    .fzt-field {display:grid; gap:5px; min-width:0;}
    .fzt-field label {font-size:12px; color:var(--finus-muted,#64748b); font-weight:700;}
    .fzt-control {width:100%; min-height:40px; padding:8px 10px; color:var(--finus-text,#172033); background:var(--finus-surface,#fff); border:1px solid var(--finus-border,#dbe5df); border-radius:10px; font-size:13px;}
    .fzt-button {display:inline-flex; align-items:center; justify-content:center; gap:6px; border:0; border-radius:10px; padding:10px 14px; font-size:12px; font-weight:750; cursor:pointer; text-decoration:none; white-space:nowrap; min-height:38px;}
    .fzt-button--green {background:#13843a;color:#fff;}.fzt-button--green:hover{background:#0e6b30;color:#fff;}
    .fzt-button--ghost {background:rgba(22,163,74,.08);color:#137436;}.fzt-button--ghost:hover{background:rgba(22,163,74,.15);color:#137436;}
    .fzt-button--red {background:#fee2e2;color:#b91c1c;}
    .fzt-table-wrap {overflow-x:auto;}
    .fzt-table {width:100%; border-collapse:collapse; white-space:nowrap; font-size:12.5px;}
    .fzt-table th {background:rgba(22,163,74,.065); text-transform:uppercase; letter-spacing:.04em; color:var(--finus-muted,#64748b); font-size:10px; font-weight:800; padding:13px 11px; text-align:left;}
    .fzt-table td {padding:14px 11px;border-top:1px solid var(--finus-border,#e1eae4); vertical-align:middle;}
    .fzt-table td.fzt-wrap {white-space:normal;min-width:150px;}
    .fzt-muted {color:var(--finus-muted,#64748b); font-size:11px;}
    .fzt-status {display:inline-flex; padding:4px 9px; border-radius:999px;background:#eaf7ed;color:#12662e;font-size:11px;font-weight:750;}
    .fzt-status--pending {background:#fff5db;color:#986500;}.fzt-status--rejected{background:#fee2e2;color:#b91c1c;}
    .fzt-actions {display:flex; gap:6px; flex-wrap:wrap; align-items:center;}
    .fzt-actions form{margin:0;}.fzt-actions input {width:150px;max-width:170px; padding:7px 8px; font-size:11px; border:1px solid var(--finus-border,#cbd5e1);border-radius:7px;}
    .fzt-empty {padding:50px 15px; text-align:center; color:var(--finus-muted,#64748b);}
    @media(max-width:1050px){.fzt-summary{grid-template-columns:repeat(2,minmax(0,1fr));}.fzt-filter{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:560px){.fzt-summary,.fzt-filter{grid-template-columns:1fr;}.fzt-hero{padding:18px;}.fzt-hero h1{font-size:19px;}}
</style>
<div class="fzt-page">
    <div class="fzt-hero"><span class="fzt-hero-icon"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i></span>
        <div><h1>Transaksi ZISWAF</h1><p>Pantau dan verifikasi transaksi zakat, infak, sedekah, dan wakaf masjid.</p></div>
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
    <div class="fzt-summary">
        @foreach(['total' => 'Total Transaksi', 'diterima' => 'Diterima', 'pending' => 'Menunggu Verifikasi', 'ditolak' => 'Ditolak'] as $key => $label)
            <div class="fzt-stat"><small>{{ $label }}</small><strong>Rp {{ number_format((int) ($summary[$key] ?? 0),0,',','.') }}</strong></div>
        @endforeach
    </div>
    <section class="fzt-panel" aria-label="Filter transaksi">
        <form class="fzt-filter" method="GET" action="{{ route('admin.ziswaf.transaksi.index') }}">
            <div class="fzt-field"><label for="fzt-search">Cari transaksi</label><input class="fzt-control" id="fzt-search" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Order ID, nama, keterangan"></div>
            <div class="fzt-field"><label for="fzt-jenis">Jenis</label><select class="fzt-control" id="fzt-jenis" name="jenis"><option value="">Semua jenis</option>@foreach($jenisLabels as $value=>$label)<option value="{{ $value }}" @selected(($filters['jenis'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="fzt-field"><label for="fzt-payment">Pembayaran</label><select class="fzt-control" id="fzt-payment" name="payment_status"><option value="">Semua pembayaran</option>@foreach($paymentStatusLabels as $value=>$label)<option value="{{ $value }}" @selected(($filters['payment_status'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="fzt-field"><label for="fzt-verify">Verifikasi</label><select class="fzt-control" id="fzt-verify" name="status_verifikasi"><option value="">Semua status</option>@foreach($statusVerifikasiLabels as $value=>$label)<option value="{{ $value }}" @selected(($filters['status_verifikasi'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></div>
            <button class="fzt-button fzt-button--green" type="submit"><i class="fa-solid fa-filter" aria-hidden="true"></i> Filter</button>
        </form>
    </section>
    <section class="fzt-panel" aria-label="Daftar transaksi">
        <div class="fzt-table-wrap">
            <table class="fzt-table"><thead><tr><th>Tanggal</th><th>Order ID</th><th>Jamaah</th><th>Jenis</th><th>Nominal</th><th>Metode</th><th>Pembayaran</th><th>Verifikasi</th><th>Tindakan</th></tr></thead><tbody>
                @forelse($transaksi as $row)
                    @php
                        $status = $row->status_verifikasi ?: 'pending';
                        $paymentStatus = (string) ($row->payment_status ?? '');
                        $isManual = in_array($paymentStatus, ['', 'manual_pending', 'manual_paid'], true);
                    @endphp
                    <tr>
                        <td>{{ $row->tanggal?->format('d/m/Y') ?? '—' }}</td>
                        <td><strong>{{ $row->order_id ?: ('#'.$row->id) }}</strong></td>
                        <td class="fzt-wrap">{{ $row->muzakki?->name ?? 'Jamaah / Tidak diketahui' }}</td>
                        <td>{{ $jenisLabels[$row->jenis_ziswaf] ?? ucwords(str_replace('_',' ',(string) $row->jenis_ziswaf)) }}</td>
                        <td><strong>Rp {{ number_format((int)$row->nominal,0,',','.') }}</strong></td>
                        <td class="fzt-wrap">{{ $metodeLabels[$row->metode_pembayaran] ?? ($row->metode_pembayaran ?: '—') }}</td>
                        <td class="fzt-wrap"><span class="fzt-muted">{{ $paymentStatusLabels[$paymentStatus] ?? ($paymentStatus ?: 'Belum tercatat') }}</span></td>
                        <td><span class="fzt-status {{ $status==='pending'?'fzt-status--pending':($status==='ditolak'?'fzt-status--rejected':'') }}">{{ $statusVerifikasiLabels[$status] ?? ucfirst($status) }}</span></td>
                        <td>
                          @if($status==='pending')
                            <div class="fzt-actions">
                              @if($isManual)
                                <form action="{{ route('admin.ziswaf.transaksi.terima',$row->id) }}" method="POST" onsubmit="return confirm('Yakin verifikasi dan jurnal transaksi ini? Pastikan dana sudah diterima.');">@csrf @method('PATCH')<button class="fzt-button fzt-button--green" type="submit">Terima</button></form>
                              @endif
                              <form action="{{ route('admin.ziswaf.transaksi.tolak',$row->id) }}" method="POST" onsubmit="return confirm('Yakin menolak transaksi ini?');">@csrf @method('PATCH')<input type="text" name="catatan_verifikasi" maxlength="1000" placeholder="Alasan penolakan" required aria-label="Alasan penolakan"><button type="submit" class="fzt-button fzt-button--red">Tolak</button></form>
                            </div>
                          @else
                            <span class="fzt-muted">—</span>
                          @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="fzt-empty">Belum ada transaksi yang sesuai filter.</td></tr>
                @endforelse
            </tbody></table>
        </div>
        <div class="mt-3">{{ $transaksi->links() }}</div>
    </section>
</div>
@endsection
@extends('layouts.app')
@section('title', 'Saldo Kas & Bank')
@section('hide-page-header', '1')
@section('content')
@php
    $transferRoute = request()->routeIs('pegawai.*') ? 'pegawai.keuangan.cash-transfers' : 'admin.cash-transfers';
    $allocationBalances = array_intersect_key($balances, $labels);
    $totalKas = collect($balances)->sum(fn ($row) => $row['1101'] ?? 0);
    $totalBank = collect($balances)->sum(fn ($row) => $row['1102'] ?? 0);
@endphp
@include('layouts.partials.finus-ui')
<div class="fmu-page cash-bank-page">
<section class="fmu-hero"><div class="fmu-hero-main"><span class="fmu-hero-icon"><i class="fa-solid fa-wallet"></i></span><div><h1>Saldo Kas & Bank</h1><p>Pantau saldo terkini dan lokasi dana berdasarkan jurnal keuangan masjid.</p></div></div><button type="button" class="fmu-btn" id="openTransferModal" aria-haspopup="dialog" aria-controls="transferModal"><i class="fa-solid fa-right-left"></i> Pindahkan Saldo</button></section>
<section class="fmu-card" style="padding:24px"><h2>Ringkasan Saldo</h2><p>Saldo Kas, Bank, dan rincian per golongan dana. Gunakan tombol Pindahkan Saldo untuk memindahkan dana antara Kas dan Bank.</p>
@if(session('success'))<div role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<div class="transfer-balances" aria-label="Saldo Kas dan Bank">
@foreach(['Saldo Kas' => $totalKas, 'Saldo Bank' => $totalBank, 'Total Kas dan Bank' => $totalKas + $totalBank] as $label => $balance)
<div><span>{{ $label }}</span><strong>Rp {{ number_format($balance, 0, ',', '.') }}</strong></div>
@endforeach
</div>
<h2 class="fund-balance-title">Saldo dana berdasarkan akun COA</h2>
<div class="fund-balance-scroll"><table class="fund-balance-table" aria-label="Saldo golongan dana per akun"><colgroup><col style="width:34%"><col style="width:22%"><col style="width:22%"><col style="width:22%"></colgroup><thead><tr><th scope="col">Golongan Dana</th><th scope="col">Kas <span>1101</span></th><th scope="col">Bank <span>1102</span></th><th scope="col">Total</th></tr></thead><tbody>
@foreach($labels as $fund => $label)
<tr><th scope="row">{{ $label }}</th><td>Rp {{ number_format($balances[$fund]['1101'] ?? 0, 0, ',', '.') }}</td><td>Rp {{ number_format($balances[$fund]['1102'] ?? 0, 0, ',', '.') }}</td><td>Rp {{ number_format(array_sum($balances[$fund] ?? []), 0, ',', '.') }}</td></tr>
@endforeach
</tbody><tfoot><tr><th scope="row">Total tiga golongan</th><th>Rp {{ number_format(collect($allocationBalances)->sum(fn($row) => $row['1101'] ?? 0), 0, ',', '.') }}</th><th>Rp {{ number_format(collect($allocationBalances)->sum(fn($row) => $row['1102'] ?? 0), 0, ',', '.') }}</th><th>Rp {{ number_format(collect($allocationBalances)->sum(fn($row) => array_sum($row)), 0, ',', '.') }}</th></tr>
</tfoot></table></div><p class="fund-balance-note">Tabel mencakup Operasional, Wakaf, dan Zakat. Ringkasan saldo di atas mencakup seluruh golongan, termasuk saldo lama.</p><p class="fund-balance-note">Saldo dihitung dari jurnal. Saldo negatif perlu diperiksa terhadap transaksi atau alokasi lama.</p>
</section>

<section class="fmu-card" style="padding:24px"><h2>Riwayat dan jurnal pemindahan</h2><div style="overflow-x:auto"><table class="table"><thead><tr><th>Tanggal / Referensi</th><th>Keterangan</th><th>Golongan</th><th>Akun COA</th><th>Debit</th><th>Kredit</th><th>Status</th></tr></thead><tbody>
@forelse($journals as $journal)
@foreach($journal->detail as $line)<tr><td>{{ $journal->tanggal?->format('d/m/Y') }}<br>{{ $journal->no_referensi }}</td><td>{{ $journal->deskripsi }}</td><td>{{ $labels[$line->jenis_dana === 'zakat' && $line->zakat_source === 'fitrah' ? 'zakat_fitrah' : $line->jenis_dana] ?? $line->jenis_dana }}</td><td>{{ $line->coa?->kode_akun }} · {{ $line->coa?->nama_akun }}</td><td>{{ number_format($line->debit, 0, ',', '.') }}</td><td>{{ number_format($line->credit, 0, ',', '.') }}</td><td>@if(in_array($journal->id, $cancelled))Dibatalkan @elseif($loop->first)<form method="POST" action="{{ route($transferRoute.'.destroy', $journal->id) }}" onsubmit="return confirm('Batalkan pemindahan melalui jurnal balik?')">@csrf @method('DELETE')<button class="btn btn-outline-danger" type="submit">Batalkan</button></form>@endif</td></tr>@endforeach
@empty<tr><td colspan="7">Belum ada pemindahan.</td></tr>@endforelse
</tbody></table></div>{{ $journals->links() }}<p>Pembatalan hanya dapat dilakukan jika saldo pada tujuan masih mencukupi. Jurnal asli tetap tersimpan.</p></section>
</div>
<dialog id="transferModal" class="transfer-modal" aria-labelledby="transferModalTitle" data-reopen="{{ $errors->any() && old('fund') !== null ? '1' : '0' }}">
<form id="pemindahan-saldo" method="POST" action="{{ route($transferRoute.'.store') }}" class="fmu-card" style="padding:24px">@csrf
<div class="transfer-modal-heading"><h2 id="transferModalTitle">Pemindahan Saldo Kas–Bank</h2><button type="button" class="transfer-modal-close" data-close-transfer aria-label="Tutup form pemindahan">&times;</button></div><p>Pindahkan lokasi uang sesuai golongan dana. Total dana tetap; pemindahan tidak menambah pemasukan atau pengeluaran.</p>
@if($errors->any() && old('fund') !== null)<div class="transfer-modal-errors" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<div class="fmu-field"><label for="fund">Golongan dana</label><select class="fmu-control" id="fund" name="fund" required>@foreach($labels as $fund => $label)<option value="{{ $fund }}" @selected(old('fund') === $fund)>{{ $label }}</option>@endforeach</select></div>
@foreach(['source' => 'Dari akun', 'target' => 'Ke akun'] as $key => $label)
<div class="fmu-field"><label for="{{ $key }}">{{ $label }}</label><select class="fmu-control" id="{{ $key }}" name="{{ $key }}" required><option value="">Pilih akun</option>@foreach($accounts as $account)<option value="{{ $account->id }}" @selected((string) old($key) === (string) $account->id)>{{ $account->kode_akun }} · {{ $account->nama_akun }}</option>@endforeach</select></div>
@endforeach
<div class="fmu-field"><label for="amount">Nominal (Rp)</label><input class="fmu-control" id="amount" name="amount" type="number" min="1" step="1" value="{{ old('amount') }}" required></div>
<div class="fmu-field"><label for="date">Tanggal</label><input class="fmu-control" id="date" name="date" type="date" value="{{ old('date', today()->toDateString()) }}" required></div>
<div class="fmu-field"><label for="description">Keterangan / referensi bukti</label><input class="fmu-control" id="description" name="description" maxlength="255" value="{{ old('description') }}" required></div>
<div class="transfer-modal-actions"><button type="button" class="fmu-btn" data-close-transfer>Batal</button><button class="fmu-btn fmu-btn-primary" type="submit">Catat Pemindahan</button></div>
</form>
</dialog>
@endsection

@push('styles')
<style>
.cash-bank-page .fund-balance-title{margin:4px 0 16px;font-size:20px;font-weight:700;line-height:1.4}
.fund-balance-scroll{overflow-x:auto;border:1px solid #e1ebe5;border-radius:14px;margin-bottom:14px}
.fund-balance-table{width:100%;min-width:620px;border-collapse:collapse;table-layout:fixed;font-size:14px;font-variant-numeric:tabular-nums}
.fund-balance-table th,.fund-balance-table td{padding:18px 22px;border-bottom:1px solid #e8efea;text-align:right;white-space:nowrap;vertical-align:middle}
.fund-balance-table th:first-child{text-align:left}
.cash-bank-page .fund-balance-table thead tr th:last-child,
.cash-bank-page .fund-balance-table tbody tr td:last-child,
.cash-bank-page .fund-balance-table tfoot tr th:last-child{text-align:right!important}
.fund-balance-table thead th{background:#f4f8f5;color:#52665a;font-size:12px;font-weight:700}
.fund-balance-table thead span{display:inline-block;margin-left:6px;font-size:11px;font-weight:500;color:#718478}
.fund-balance-table tbody th{font-weight:600;color:#334b3d}
.fund-balance-table tbody td{color:#52665a}
.fund-balance-table tbody td:last-child{font-weight:700;color:#1c5535}
.fund-balance-table tbody tr:hover{background:#fafcfb}
.fund-balance-table tfoot th{background:#eef7f1;color:#145b35;font-weight:750;border-bottom:0;border-top:2px solid #d6e8dc}
.cash-bank-page .fund-balance-note{margin:5px 0 0;font-size:12px;line-height:1.65;color:#718078}
html[data-finus-theme="dark"] .fund-balance-scroll{border-color:#31493a}
html[data-finus-theme="dark"] .fund-balance-table th,html[data-finus-theme="dark"] .fund-balance-table td{border-color:#31493a}
html[data-finus-theme="dark"] .fund-balance-table thead th{background:#172a1e;color:#c3d7ca}
html[data-finus-theme="dark"] .fund-balance-table tbody th,html[data-finus-theme="dark"] .fund-balance-table tbody td{color:#d2dfd6}
html[data-finus-theme="dark"] .fund-balance-table tbody td:last-child{color:#86efac}
html[data-finus-theme="dark"] .fund-balance-table tbody tr:hover{background:#192d20}
html[data-finus-theme="dark"] .fund-balance-table tfoot th{background:#193924;color:#a3efbb}
html[data-finus-theme="dark"] .cash-bank-page .fund-balance-note{color:#a6b9ad}
@media(max-width:760px){.fund-balance-table th,.fund-balance-table td{padding:15px 16px}.cash-bank-page .fund-balance-title{font-size:18px}}
</style>
<style>.cash-bank-page{display:grid;gap:24px}#pemindahan-saldo{scroll-margin-top:110px}#pemindahan-saldo .fmu-field{margin-bottom:16px}.transfer-balances{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin:24px 0}.transfer-balances>div{padding:20px;border:1px solid #dce8df;border-radius:14px;background:#f4fbf6}.transfer-balances span{display:block;font-size:13px;color:#64748b}.transfer-balances strong{display:block;margin-top:8px;font-size:24px;color:#087543;font-variant-numeric:tabular-nums}html[data-finus-theme="dark"] .transfer-balances>div{background:#14281c;border-color:#31493a}html[data-finus-theme="dark"] .transfer-balances strong{color:#86efac}@media(max-width:760px){.transfer-balances{grid-template-columns:1fr}}</style>
@endpush

@push('scripts')
<script>
(() => {
    const dialog = document.getElementById('transferModal');
    const openButton = document.getElementById('openTransferModal');
    if (!dialog || !openButton) return;
    const open = () => { if (!dialog.open) { dialog.showModal(); document.body.classList.add('transfer-modal-open'); } };
    openButton.addEventListener('click', open);
    dialog.querySelectorAll('[data-close-transfer]').forEach(button => button.addEventListener('click', () => dialog.close()));
    dialog.addEventListener('close', () => { document.body.classList.remove('transfer-modal-open'); openButton.focus(); });
    dialog.addEventListener('click', event => {
        const rect = dialog.getBoundingClientRect();
        if (event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) dialog.close();
    });
    if (dialog.dataset.reopen === '1') open();
})();
</script>
@endpush
@push('styles')
<style>
.transfer-modal{border:0;padding:0;border-radius:20px;width:min(620px,calc(100% - 32px));max-height:calc(100dvh - 48px);overflow-y:auto;color:#24352a;background:#fff;box-shadow:0 24px 80px #0004}.transfer-modal::backdrop{background:rgba(10,30,20,.55);backdrop-filter:blur(3px)}.transfer-modal #pemindahan-saldo{margin:0;border:0;box-shadow:none;border-radius:0}.transfer-modal-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:12px}.transfer-modal-heading h2{font-size:21px;margin:0;line-height:1.5}.transfer-modal-close{background:#f0f5f2;border:0;border-radius:10px;width:36px;height:36px;flex-shrink:0;font-size:25px;color:#52685a;cursor:pointer}.transfer-modal p{font-size:13px;color:#64748b;line-height:1.7}.transfer-modal-actions{display:flex;justify-content:flex-end;gap:12px;margin-top:24px;padding-top:18px;border-top:1px solid #e3ece6}.transfer-modal-errors{padding:10px 14px;background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;margin-bottom:18px}.transfer-modal-errors p{color:#b91c1c;margin:4px 0}body.transfer-modal-open{overflow:hidden}html[data-finus-theme="dark"] .transfer-modal,html[data-finus-theme="dark"] .transfer-modal #pemindahan-saldo{background:#111b15;color:#f1f6f3}html[data-finus-theme="dark"] .transfer-modal-close{background:#24392b;color:#e5f0e8}@media(max-width:600px){.transfer-modal-actions{flex-wrap:wrap}.transfer-modal-actions button{flex:1}}
</style>
@endpush

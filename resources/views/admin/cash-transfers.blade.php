@extends('layouts.app')
@section('title', 'Saldo Kas & Bank')
@section('hide-page-header', '1')
@section('content')
@php
    $transferRoute = request()->routeIs('pegawai.*') ? 'pegawai.keuangan.cash-transfers' : 'admin.cash-transfers';
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
<h2>Saldo dana berdasarkan akun COA</h2>
<div style="overflow-x:auto"><table class="table"><thead><tr><th>Golongan</th><th>Kas · 1101</th><th>Bank · 1102</th><th>Total</th></tr></thead><tbody>
@foreach($labels + array_fill_keys(array_keys($balances), 'Golongan lainnya') as $fund => $label)
<tr><td>{{ $label }}</td><td>Rp {{ number_format($balances[$fund]['1101'] ?? 0, 0, ',', '.') }}</td><td>Rp {{ number_format($balances[$fund]['1102'] ?? 0, 0, ',', '.') }}</td><td>Rp {{ number_format(array_sum($balances[$fund] ?? []), 0, ',', '.') }}</td></tr>
@endforeach
<tr><th>Total</th><th>Rp {{ number_format(collect($balances)->sum(fn($row) => $row['1101'] ?? 0), 0, ',', '.') }}</th><th>Rp {{ number_format(collect($balances)->sum(fn($row) => $row['1102'] ?? 0), 0, ',', '.') }}</th><th>Rp {{ number_format(collect($balances)->sum(fn($row) => array_sum($row)), 0, ',', '.') }}</th></tr>
</tbody></table></div><p>Saldo lokasi dihitung dari penanda golongan pada jurnal. Saldo negatif perlu diperiksa terhadap transaksi atau alokasi lama.</p>
</section>

<section class="fmu-card" style="padding:24px"><h2>Riwayat dan jurnal pemindahan</h2><div style="overflow-x:auto"><table class="table"><thead><tr><th>Tanggal / Referensi</th><th>Keterangan</th><th>Golongan</th><th>Akun COA</th><th>Debit</th><th>Kredit</th><th>Status</th></tr></thead><tbody>
@forelse($journals as $journal)
@foreach($journal->detail as $line)<tr><td>{{ $journal->tanggal?->format('d/m/Y') }}<br>{{ $journal->no_referensi }}</td><td>{{ $journal->deskripsi }}</td><td>{{ $labels[$line->jenis_dana] ?? $line->jenis_dana }}</td><td>{{ $line->coa?->kode_akun }} · {{ $line->coa?->nama_akun }}</td><td>{{ number_format($line->debit, 0, ',', '.') }}</td><td>{{ number_format($line->credit, 0, ',', '.') }}</td><td>@if(in_array($journal->id, $cancelled))Dibatalkan @elseif($loop->first)<form method="POST" action="{{ route($transferRoute.'.destroy', $journal->id) }}" onsubmit="return confirm('Batalkan pemindahan melalui jurnal balik?')">@csrf @method('DELETE')<button class="btn btn-outline-danger" type="submit">Batalkan</button></form>@endif</td></tr>@endforeach
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

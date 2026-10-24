@extends('layouts.app')
@section('title', 'Saldo Kas & Bank')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
@php
    $allocationBalances = array_intersect_key($balances, $labels);
    $allocationCash = collect($allocationBalances)->sum(fn ($row) => $row['1101'] ?? 0);
    $allocationBank = collect($allocationBalances)->sum(fn ($row) => $row['1102'] ?? 0);
    $rupiah = fn ($value) => 'Rp '.number_format((float) $value, 0, ',', '.');
    $cash = collect($balances)->sum(fn ($row) => $row['1101'] ?? 0);
    $bank = collect($balances)->sum(fn ($row) => $row['1102'] ?? 0);
@endphp
<div class="fmu-page balance-page">
    <section class="fmu-hero"><div class="fmu-hero-main"><span class="fmu-hero-icon"><i class="fa-solid fa-wallet"></i></span><div><h1>Saldo Kas & Bank</h1><p>Pantau saldo terkini dan lokasi dana berdasarkan jurnal keuangan masjid.</p></div></div></section>
    <div class="balance-summary">
        @foreach(['Saldo Kas' => $cash, 'Saldo Bank' => $bank, 'Total Kas & Bank' => $cash + $bank] as $label => $value)
        <section class="fmu-card"><span>{{ $label }}</span><strong>{{ $rupiah($value) }}</strong></section>
        @endforeach
    </div>
    <section class="fmu-card balance-detail"><h2>Saldo per Golongan Dana</h2><p>Halaman ini hanya untuk pemantauan. Pemindahan dana dikelola oleh admin dan bagian Keuangan.</p>
        <div class="balance-scroll"><table><thead><tr><th>Golongan Dana</th><th>Kas · 1101</th><th>Bank · 1102</th><th>Total</th></tr></thead><tbody>
        @foreach($labels as $fund => $label)
            <tr><td>{{ $label }}</td><td>{{ $rupiah($balances[$fund]['1101'] ?? 0) }}</td><td>{{ $rupiah($balances[$fund]['1102'] ?? 0) }}</td><td><strong>{{ $rupiah(array_sum($balances[$fund] ?? [])) }}</strong></td></tr>
        @endforeach
        </tbody><tfoot><tr><th>Total tiga golongan</th><th>{{ $rupiah($allocationCash) }}</th><th>{{ $rupiah($allocationBank) }}</th><th>{{ $rupiah($allocationCash + $allocationBank) }}</th></tr></tfoot></table></div>
        <small>Tabel menampilkan Operasional, Wakaf, dan Zakat. Ringkasan saldo di atas tetap mencakup seluruh golongan dana. Saldo mencakup seluruh jurnal tercatat, termasuk pemindahan dan jurnal balik. Pemindahan Kas–Bank mengubah lokasi dana; total dana tetap.</small>
    </section>
</div>
@endsection
@push('styles')
<style>.balance-page{display:grid;gap:24px}.balance-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.balance-summary section{padding:24px}.balance-summary span{display:block;font-size:13px;color:#64748b}.balance-summary strong{display:block;margin-top:12px;font-size:26px;color:#087543;font-variant-numeric:tabular-nums}.balance-detail{padding:24px}.balance-detail h2{font-size:20px;margin:0 0 10px}.balance-detail p,.balance-detail small{color:#64748b;line-height:1.7}.balance-scroll{overflow-x:auto;margin:20px 0}.balance-detail table{width:100%;border-collapse:collapse;font-size:13px}.balance-detail th,.balance-detail td{padding:16px;border-bottom:1px solid #e4ece7;text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums}.balance-detail th:first-child,.balance-detail td:first-child{text-align:left}.balance-detail thead,.balance-detail tfoot{background:#f0f8f3}html[data-finus-theme="dark"] .balance-detail thead,html[data-finus-theme="dark"] .balance-detail tfoot{background:#14281c}html[data-finus-theme="dark"] .balance-detail td,html[data-finus-theme="dark"] .balance-detail th{border-color:#31493a}html[data-finus-theme="dark"] .balance-summary strong{color:#86efac}@media(max-width:760px){.balance-summary{grid-template-columns:1fr}.balance-detail{padding:18px}}</style>
@endpush
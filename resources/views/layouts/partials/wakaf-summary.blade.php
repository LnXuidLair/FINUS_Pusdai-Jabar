@php($rekapWakaf = app(\App\Services\Accounting\Psak109PostingService::class)->getRekapWakaf())
<section style="margin:16px 0;padding:20px;background:#fff;border:1px solid #dce8df;border-radius:12px;overflow-x:auto">
    <h3 style="font-size:18px;margin:0 0 12px">Rekap Dana Wakaf per Peruntukan</h3>
    <table style="width:100%;text-align:left;border-collapse:collapse">
        <thead><tr><th>Peruntukan</th><th>Penerimaan</th><th>Pengeluaran</th><th>Sisa Saldo</th></tr></thead>
        <tbody>@foreach($rekapWakaf as $row)
        <tr>@foreach(['label', 'penerimaan', 'pengeluaran', 'saldo'] as $column)
            <td style="padding:8px 12px 8px 0">{{ $column === 'label' ? $row[$column] : 'Rp '.number_format($row[$column], 0, ',', '.') }}</td>
        @endforeach</tr>
        @endforeach</tbody>
    </table>
    <small>Transaksi lama tanpa peruntukan ditampilkan sebagai Belum ditentukan.</small>
</section>

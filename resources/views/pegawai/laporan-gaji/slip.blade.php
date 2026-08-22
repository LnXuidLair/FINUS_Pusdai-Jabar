<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji {{ $pegawai->nama_pegawai }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .header { text-align: center; margin-bottom: 28px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header h2 { margin: 7px 0 0; font-size: 15px; }
        .info, .gaji { width: 100%; border-collapse: collapse; }
        .info { margin-bottom: 24px; }
        .info td { padding: 5px 0; }
        .info td:first-child { width: 180px; font-weight: bold; }
        .gaji th, .gaji td { border: 1px solid #d1d5db; padding: 10px; }
        .gaji th { background: #f3f8f4; }
        .total { font-weight: bold; font-size: 14px; }
        .status { margin-top: 22px; padding: 12px; border: 1px solid #bde5c7; background: #effaf2; color: #166534; text-align: center; font-weight: bold; }
        .footer { margin-top: 35px; padding-top: 12px; border-top: 1px solid #ddd; text-align: center; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((int) $value, 0, ',', '.');
    $periode = \Carbon\Carbon::createFromFormat('Y-m-d', $penggajian->periode . '-01')->translatedFormat('F Y');
@endphp
<div class="header"><h1>FINUS - PUSDAI JAWA BARAT</h1><h2>SLIP GAJI PEGAWAI</h2></div>
<table class="info">
    <tr><td>Nama Pegawai</td><td>: {{ $pegawai->nama_pegawai }}</td></tr>
    <tr><td>NIP</td><td>: {{ $pegawai->nip }}</td></tr>
    <tr><td>Jabatan</td><td>: {{ $pegawai->jabatan }}</td></tr>
    <tr><td>Periode</td><td>: {{ $periode }}</td></tr>
    <tr><td>Tanggal Pembayaran</td><td>: {{ \Carbon\Carbon::parse($penggajian->tanggal)->translatedFormat('d F Y') }}</td></tr>
</table>
<table class="gaji">
    <thead><tr><th>Keterangan</th><th>Nilai</th></tr></thead>
    <tbody>
        <tr><td>Gaji per Hari</td><td>{{ $rupiah($penggajian->gaji_perhari) }}</td></tr>
        <tr><td>Jumlah Hari Hadir yang Disetujui</td><td>{{ $penggajian->jumlah_kehadiran }} Hari</td></tr>
        <tr class="total"><td>Total Gaji</td><td>{{ $rupiah($penggajian->total_gaji) }}</td></tr>
    </tbody>
</table>
<div class="status">STATUS PEMBAYARAN: SUDAH DIBAYAR</div>
<div class="footer">Slip gaji dibuat otomatis oleh FINUS berdasarkan presensi yang telah disetujui admin.</div>
</body>
</html>
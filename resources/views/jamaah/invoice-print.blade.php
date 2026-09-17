<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $referensi }} — FINUS Pusdai Jabar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 32px 16px 48px;
        }

        /* Action Bar (hidden on print) */
        .action-bar {
            width: 100%;
            max-width: 640px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 12px;
            flex-wrap: wrap;
        }
        .action-bar-title {
            font-size: 14px;
            font-weight: 700;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .action-bar-title i { color: #059669; }
        .btn-cetak {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #065f46, #059669);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
        }
        .btn-cetak:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(6,95,70,.35);
            color: #fff;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e2e8f0;
            color: #475569;
            border: none;
            border-radius: 12px;
            padding: 10px 18px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: background .2s;
        }
        .btn-back:hover { background: #cbd5e1; color: #334155; text-decoration: none; }

        /* Invoice Card */
        .inv-card {
            width: 100%;
            max-width: 640px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,.1);
            overflow: hidden;
        }

        /* Invoice Header */
        .inv-header {
            background: linear-gradient(135deg, #065f46 0%, #059669 50%, #10b981 100%);
            padding: 36px 40px 28px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .inv-header::before {
            content: '';
            position: absolute;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
            top: -80px; right: -60px;
            pointer-events: none;
        }
        .inv-header::after {
            content: '';
            position: absolute;
            width: 140px; height: 140px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
            bottom: -50px; left: 30px;
            pointer-events: none;
        }
        .inv-header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 28px;
        }
        .inv-lembaga {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .inv-lembaga-icon {
            width: 56px; height: 56px;
            background: rgba(255,255,255,.18);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }
        .inv-lembaga-name {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: -.01em;
        }
        .inv-lembaga-sub {
            font-size: 11.5px;
            opacity: .75;
            margin-top: 3px;
        }
        .inv-doc-type { text-align: right; }
        .inv-doc-label {
            font-size: 10px;
            opacity: .7;
            letter-spacing: .1em;
            text-transform: uppercase;
        }
        .inv-doc-title {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -.02em;
        }
        .inv-header-divider {
            border: 0;
            border-top: 1.5px solid rgba(255,255,255,.2);
            margin: 0 0 20px;
        }
        .inv-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .inv-ref-box {
            background: rgba(255,255,255,.15);
            border: 1.5px solid rgba(255,255,255,.25);
            border-radius: 10px;
            padding: 8px 16px;
        }
        .inv-ref-label {
            font-size: 9px;
            opacity: .75;
            letter-spacing: .1em;
            text-transform: uppercase;
        }
        .inv-ref-value {
            font-size: 14px;
            font-weight: 800;
            font-family: monospace;
            letter-spacing: .05em;
        }
        .inv-status-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dcfce7;
            color: #15803d;
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .05em;
        }

        /* Invoice Body */
        .inv-body { padding: 0 40px; }

        .inv-nominal-section {
            margin: 28px 0;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #86efac;
            border-radius: 18px;
            padding: 22px 24px;
            text-align: center;
        }
        .inv-nominal-label {
            font-size: 11px;
            font-weight: 700;
            color: #15803d;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .inv-nominal-amount {
            font-size: 36px;
            font-weight: 900;
            color: #065f46;
            letter-spacing: -.03em;
            display: block;
        }
        .inv-nominal-jenis {
            margin-top: 8px;
            font-size: 13px;
            color: #166534;
            font-weight: 600;
        }
        .inv-nominal-jenis strong {
            background: #bbf7d0;
            padding: 2px 10px;
            border-radius: 999px;
            font-weight: 800;
        }
        .inv-section { margin-bottom: 24px; }
        .inv-section-title {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
        }
        .inv-table { width: 100%; border-collapse: collapse; }
        .inv-table tr td {
            padding: 9px 0;
            border-bottom: 1px solid #f8fafc;
            vertical-align: top;
        }
        .inv-table tr:last-child td { border-bottom: 0; }
        .td-label {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
            width: 45%;
        }
        .td-value {
            font-size: 13px;
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }
        .td-mono { font-family: monospace; letter-spacing: .05em; }
        .inv-keterangan-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #475569;
            font-style: italic;
            line-height: 1.6;
        }

        /* Footer */
        .inv-footer {
            margin: 20px 40px 0;
            padding: 18px 0 28px;
            border-top: 1.5px dashed #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }
        .inv-footer-left {
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }
        .inv-footer-left strong { color: #0f172a; font-size: 12.5px; display: block; margin-bottom: 2px; }
        .inv-footer-right {
            text-align: right;
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }
        .inv-footer-right strong {
            color: #15803d;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            letter-spacing: .04em;
        }

        /* Print */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 12mm;
            }
            html, body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                width: 100% !important;
                height: auto !important;
                min-height: auto !important;
            }
            .action-bar { display: none !important; }
            .inv-card {
                max-width: 100% !important;
                width: 100% !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .inv-header {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                padding: 22px 28px 18px !important;
            }
            .inv-body {
                padding: 0 28px !important;
            }
            .inv-nominal-section {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                margin: 14px 0 !important;
                padding: 14px 18px !important;
            }
            .inv-nominal-amount {
                font-size: 30px !important;
            }
            .inv-section {
                margin-bottom: 14px !important;
            }
            .inv-section-title {
                margin-bottom: 6px !important;
                padding-bottom: 4px !important;
            }
            .inv-table tr td {
                padding: 6px 0 !important;
            }
            .inv-footer {
                margin: 14px 28px 0 !important;
                padding: 12px 0 16px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    {{-- Action bar --}}
    <div class="action-bar">
        <div class="action-bar-title">
            <i class="fa-solid fa-file-invoice"></i>
            Invoice Transaksi ZISWAF
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <a href="javascript:history.back()" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <button class="btn-cetak" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak / Download PDF
            </button>
        </div>
    </div>

    {{-- Invoice Card --}}
    <div class="inv-card">

        <div class="inv-header">
            <div class="inv-header-top">
                <div class="inv-lembaga">
                    <div class="inv-lembaga-icon">🕌</div>
                    <div>
                        <div class="inv-lembaga-name">Pusdai Jawa Barat</div>
                        <div class="inv-lembaga-sub">FINUS &mdash; Sistem Informasi Keuangan Masjid</div>
                    </div>
                </div>
                <div class="inv-doc-type">
                    <div class="inv-doc-label">Dokumen</div>
                    <div class="inv-doc-title">INVOICE</div>
                </div>
            </div>
            <hr class="inv-header-divider">
            <div class="inv-meta-row">
                <div class="inv-ref-box">
                    <div class="inv-ref-label">No. Referensi</div>
                    <div class="inv-ref-value">{{ $referensi }}</div>
                </div>
                <div class="inv-status-chip">
                    <i class="fa-solid fa-circle-check"></i>
                    PEMBAYARAN BERHASIL
                </div>
            </div>
        </div>

        <div class="inv-body">

            <div class="inv-nominal-section">
                <div class="inv-nominal-label">Total Pembayaran</div>
                <span class="inv-nominal-amount">Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}</span>
                <div class="inv-nominal-jenis">
                    Jenis Pembayaran: <strong>{{ $jenisLabel }}</strong>
                </div>
            </div>

            <div class="inv-section">
                <div class="inv-section-title">Informasi Muzakki / Pembayar</div>
                <table class="inv-table">
                    <tr>
                        <td class="td-label">Nama Lengkap</td>
                        <td class="td-value">{{ $jamaah->name }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Email</td>
                        <td class="td-value">{{ $jamaah->email ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="inv-section">
                <div class="inv-section-title">Detail Transaksi</div>
                <table class="inv-table">
                    <tr>
                        <td class="td-label">No. Referensi</td>
                        <td class="td-value td-mono">{{ $referensi }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Jenis ZISWAF</td>
                        <td class="td-value">{{ $jenisLabel }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Nominal</td>
                        <td class="td-value" style="color:#065f46;">Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Metode Pembayaran</td>
                        <td class="td-value">{{ $metodeLabel }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Tanggal Transaksi</td>
                        <td class="td-value">{{ $transaksi->tanggal?->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Diverifikasi Pada</td>
                        <td class="td-value">{{ $transaksi->verified_at?->format('d F Y, H:i') }} WIB</td>
                    </tr>
                </table>
            </div>

            @if($transaksi->keterangan)
            <div class="inv-section">
                <div class="inv-section-title">Keterangan</div>
                <div class="inv-keterangan-box">
                    <i class="fa-solid fa-comment-dots" style="margin-right:6px;color:#059669;"></i>
                    {{ $transaksi->keterangan }}
                </div>
            </div>
            @endif

        </div>

        <div class="inv-footer">
            <div class="inv-footer-left">
                <strong>Pusdai Jawa Barat</strong>
                Dokumen ini merupakan bukti resmi penerimaan pembayaran ZISWAF yang sah dan telah diverifikasi.
            </div>
            <div class="inv-footer-right">
                <strong><i class="fa-solid fa-circle-check"></i> TRANSAKSI SAH</strong>
                <div>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>
        </div>

    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi FINUS</title>
</head>
<body style="margin:0; padding:0; background-color:#F1F6F2; font-family:Arial, Helvetica, sans-serif; color:#172033;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; background-color:#F1F6F2;">
        <tr>
            <td align="center" style="padding:32px 14px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; max-width:600px;">
                    <tr>
                        <td style="overflow:hidden; border-radius:22px; background-color:#FFFFFF; box-shadow:0 18px 45px rgba(15,23,42,.10);">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" bgcolor="#0E5423" style="padding:34px 28px; background-color:#0E5423; background-image:linear-gradient(135deg,#0E5423 0%,#179B40 55%,#22BA51 100%);">
                                        <div style="display:inline-block; width:58px; height:58px; line-height:58px; border-radius:18px; background-color:rgba(255,255,255,.16); color:#FFFFFF; font-size:25px; font-weight:bold;">
                                            F
                                        </div>

                                        <h1 style="margin:15px 0 0; color:#FFFFFF; font-size:25px; line-height:1.3; letter-spacing:1px;">
                                            FINUS PUSDAI
                                        </h1>

                                        <p style="margin:7px 0 0; color:#DDF7E3; font-size:13px; line-height:1.6;">
                                            Sistem Informasi Keuangan Masjid
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:34px 34px 28px;">
                                        <p style="margin:0; color:#172033; font-size:16px; line-height:1.7;">
                                            Assalamu'alaikum,
                                            <strong>{{ $namaJamaah }}</strong>
                                        </p>

                                        <p style="margin:18px 0 0; color:#64748B; font-size:14px; line-height:1.75;">
                                            Gunakan kode berikut untuk memverifikasi akun jamaah Anda di FINUS.
                                        </p>

                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:24px;">
                                            <tr>
                                                <td align="center" style="padding:23px 16px; border:1px solid #CFE8D6; border-radius:16px; background-color:#F0FAF3;">
                                                    <div style="color:#0E5423; font-size:11px; font-weight:bold; letter-spacing:2px; text-transform:uppercase;">
                                                        Kode Verifikasi
                                                    </div>

                                                    <div style="margin-top:10px; color:#14532D; font-size:34px; font-weight:bold; line-height:1.25; letter-spacing:9px;">
                                                        {{ $kode }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>

                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:21px;">
                                            <tr>
                                                <td width="42" valign="top">
                                                    <div style="width:34px; height:34px; line-height:34px; border-radius:10px; background-color:#FFF7E6; color:#D97706; text-align:center; font-size:16px;">
                                                        ⏱
                                                    </div>
                                                </td>
                                                <td valign="middle" style="padding-left:10px; color:#64748B; font-size:13px; line-height:1.65;">
                                                    Kode ini hanya berlaku selama
                                                    <strong style="color:#92400E;">5 menit</strong>.
                                                    Jangan membagikan kode kepada siapa pun.
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="margin-top:22px; padding:15px 16px; border-left:4px solid #179B40; border-radius:0 11px 11px 0; background-color:#F5FBF7; color:#64748B; font-size:12.5px; line-height:1.65;">
                                            Jika Anda tidak pernah membuat akun di FINUS, abaikan email ini. Akun tidak akan diverifikasi tanpa memasukkan kode tersebut.
                                        </div>

                                        <p style="margin:26px 0 0; color:#64748B; font-size:13px; line-height:1.7;">
                                            Hormat kami,<br>
                                            <strong style="color:#0E5423;">Tim FINUS Pusdai Jawa Barat</strong>
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="center" style="padding:18px 24px; border-top:1px solid #E7EEE9; background-color:#F8FBF9; color:#94A3B8; font-size:11px; line-height:1.6;">
                                        Email ini dikirim otomatis oleh FINUS.<br>
                                        Mohon tidak membalas email ini.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:17px 20px 0; color:#94A3B8; font-size:10.5px; line-height:1.6;">
                            © {{ date('Y') }} FINUS Pusdai Jawa Barat
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
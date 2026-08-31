<!doctype html>
<html lang="id" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformat">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">

    <title>Kode OTP Reset Password FINUS</title>

    <style>
        html,
        body {
            width: 100% !important;
            min-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #eef6f0 !important;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #172033;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            word-spacing: normal;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            mso-table-lspace: 0;
            mso-table-rspace: 0;
        }

        td {
            border-collapse: collapse;
        }

        img {
            display: block;
            max-width: 100%;
            height: auto;
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        .preheader {
            display: none !important;
            visibility: hidden;
            overflow: hidden;
            opacity: 0;
            color: transparent;
            width: 0;
            height: 0;
            max-width: 0;
            max-height: 0;
            mso-hide: all;
        }

        .verification-digit {
            width: 42px;
            height: 54px;
            padding: 0;
            border: 1px solid #b8ddc3;
            border-radius: 10px;
            background-color: #ffffff;
            color: #0e5423;
            font-family: "Courier New", Courier, monospace;
            font-size: 28px;
            font-weight: 700;
            line-height: 54px;
            text-align: center;
        }

        @media only screen and (max-width: 640px) {
            .email-wrapper {
                padding: 16px 10px !important;
            }

            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }

            .email-card {
                border-radius: 18px !important;
            }

            .header-cell {
                padding: 28px 20px 26px !important;
            }

            .header-logo {
                width: 150px !important;
            }

            .header-title {
                font-size: 22px !important;
            }

            .content-cell {
                padding: 26px 20px 24px !important;
            }

            .verification-box {
                padding: 20px 10px !important;
            }

            .verification-digit {
                width: 34px !important;
                height: 48px !important;
                font-size: 24px !important;
                line-height: 48px !important;
            }

            .digit-gap {
                width: 5px !important;
            }

            .security-cell {
                padding: 16px !important;
            }

            .footer-cell {
                padding: 18px 16px !important;
            }
        }

        @media only screen and (max-width: 390px) {
            .content-cell {
                padding-right: 16px !important;
                padding-left: 16px !important;
            }

            .verification-box {
                padding-right: 5px !important;
                padding-left: 5px !important;
            }

            .verification-digit {
                width: 30px !important;
                height: 44px !important;
                font-size: 22px !important;
                line-height: 44px !important;
                border-radius: 8px !important;
            }

            .digit-gap {
                width: 4px !important;
            }
        }
    </style>

    <!--[if mso]>
    <style>
        body,
        table,
        td,
        p,
        a,
        span {
            font-family: Arial, Helvetica, sans-serif !important;
        }
    </style>
    <![endif]-->
</head>

<body style="margin:0; padding:0; background-color:#eef6f0;">

    {{-- Teks pratinjau yang muncul di daftar inbox --}}
    <div class="preheader">
        Kode OTP reset password FINUS Anda adalah {{ $code }}. Gunakan kode ini sebelum masa berlakunya berakhir.
    </div>

    <table
        role="presentation"
        width="100%"
        cellspacing="0"
        cellpadding="0"
        border="0"
        bgcolor="#EEF6F0"
        style="width:100%; background-color:#eef6f0;"
    >
        <tr>
            <td
                align="center"
                class="email-wrapper"
                style="padding:34px 14px;"
            >
                <table
                    role="presentation"
                    width="620"
                    cellspacing="0"
                    cellpadding="0"
                    border="0"
                    class="email-container"
                    style="width:100%; max-width:620px;"
                >
                    <tr>
                        <td
                            class="email-card"
                            style="
                                overflow:hidden;
                                border:1px solid #dbe9df;
                                border-radius:24px;
                                background-color:#ffffff;
                                box-shadow:0 18px 45px rgba(15,23,42,.09);
                            "
                        >
                            <table
                                role="presentation"
                                width="100%"
                                cellspacing="0"
                                cellpadding="0"
                                border="0"
                            >
                                {{-- Header --}}
                                <tr>
                                    <td
                                        align="center"
                                        bgcolor="#0E5423"
                                        class="header-cell"
                                        style="
                                            padding:34px 28px 31px;
                                            background-color:#0e5423;
                                            background-image:linear-gradient(
                                                135deg,
                                                #063d1a 0%,
                                                #0e5423 38%,
                                                #179b40 72%,
                                                #22ba51 100%
                                            );
                                        "
                                    >
                                        <table
                                            role="presentation"
                                            cellspacing="0"
                                            cellpadding="0"
                                            border="0"
                                        >
                                            <tr>
                                                <td
                                                    align="center"
                                                    style="
                                                        padding:6px 12px;
                                                        border:1px solid rgba(255,255,255,.24);
                                                        border-radius:999px;
                                                        background-color:rgba(255,255,255,.12);
                                                        color:#e8ffed;
                                                        font-size:10px;
                                                        font-weight:700;
                                                        line-height:1.4;
                                                        letter-spacing:1.4px;
                                                        text-transform:uppercase;
                                                    "
                                                >
                                                    Reset Password Jamaah
                                                </td>
                                            </tr>
                                        </table>

                                        <img
                                            src="{{ $message->embed(public_path('assets/images/FINUS_login.png')) }}"
                                            width="175"
                                            alt="FINUS PUSDAI Jawa Barat"
                                            class="header-logo"
                                            style="
                                                display:block;
                                                width:175px;
                                                max-width:175px;
                                                height:auto;
                                                margin:22px auto 0;
                                                border:0;
                                                outline:none;
                                                text-decoration:none;
                                            "
                                        >

                                        <h1
                                            class="header-title"
                                            style="
                                                margin:19px 0 0;
                                                color:#ffffff;
                                                font-size:25px;
                                                font-weight:700;
                                                line-height:1.3;
                                                letter-spacing:.3px;
                                            "
                                        >
                                            Reset Password FINUS
                                        </h1>

                                        <p
                                            style="
                                                margin:8px 0 0;
                                                color:#d9f7e1;
                                                font-size:13px;
                                                line-height:1.65;
                                            "
                                        >
                                            Sistem Informasi Keuangan Masjid PUSDAI
                                        </p>
                                    </td>
                                </tr>

                                {{-- Konten utama --}}
                                <tr>
                                    <td
                                        class="content-cell"
                                        style="padding:36px 36px 30px;"
                                    >
                                        <p
                                            style="
                                                margin:0;
                                                color:#172033;
                                                font-size:16px;
                                                line-height:1.75;
                                            "
                                        >
                                            Assalamu’alaikum,
                                            <strong style="color:#0e5423;">
                                                {{ $name ?? 'Jamaah' }}
                                            </strong>
                                        </p>

                                        <p
                                            style="
                                                margin:17px 0 0;
                                                color:#5f6f65;
                                                font-size:14px;
                                                line-height:1.75;
                                            "
                                        >
                                            Kami menerima permintaan untuk mereset password akun
                                            Jamaah FINUS Anda. Masukkan kode berikut pada halaman
                                            reset password untuk melanjutkan proses perubahan password.
                                        </p>

                                        {{-- Kotak kode verifikasi --}}
                                        <table
                                            role="presentation"
                                            width="100%"
                                            cellspacing="0"
                                            cellpadding="0"
                                            border="0"
                                            style="margin-top:25px;"
                                        >
                                            <tr>
                                                <td
                                                    align="center"
                                                    class="verification-box"
                                                    style="
                                                        padding:23px 14px 24px;
                                                        border:1px solid #c9e4d1;
                                                        border-radius:18px;
                                                        background-color:#f0faf3;
                                                    "
                                                >
                                                    <p
                                                        style="
                                                            margin:0;
                                                            color:#428058;
                                                            font-size:10px;
                                                            font-weight:700;
                                                            line-height:1.5;
                                                            letter-spacing:1.8px;
                                                            text-transform:uppercase;
                                                        "
                                                    >
                                                        Kode OTP Anda
                                                    </p>

                                                    <table
                                                        role="presentation"
                                                        cellspacing="0"
                                                        cellpadding="0"
                                                        border="0"
                                                        align="center"
                                                        style="margin:15px auto 0;"
                                                    >
                                                        <tr>
                                                            @foreach(str_split((string) $code) as $digit)
                                                                <td
                                                                    class="verification-digit"
                                                                    align="center"
                                                                    valign="middle"
                                                                >
                                                                    {{ $digit }}
                                                                </td>

                                                                @unless($loop->last)
                                                                    <td
                                                                        width="8"
                                                                        class="digit-gap"
                                                                        style="width:8px; font-size:1px; line-height:1px;"
                                                                    >
                                                                        &nbsp;
                                                                    </td>
                                                                @endunless
                                                            @endforeach
                                                        </tr>
                                                    </table>

                                                    <p
                                                        style="
                                                            margin:15px 0 0;
                                                            color:#5f7868;
                                                            font-size:11px;
                                                            line-height:1.6;
                                                        "
                                                    >
                                                        Masukkan kode OTP tersebut pada halaman
                                                        reset password FINUS.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Masa berlaku --}}
                                        <table
                                            role="presentation"
                                            width="100%"
                                            cellspacing="0"
                                            cellpadding="0"
                                            border="0"
                                            style="margin-top:19px;"
                                        >
                                            <tr>
                                                <td
                                                    width="44"
                                                    valign="top"
                                                    style="width:44px;"
                                                >
                                                    <table
                                                        role="presentation"
                                                        cellspacing="0"
                                                        cellpadding="0"
                                                        border="0"
                                                    >
                                                        <tr>
                                                            <td
                                                                align="center"
                                                                valign="middle"
                                                                width="36"
                                                                height="36"
                                                                style="
                                                                    width:36px;
                                                                    height:36px;
                                                                    border-radius:11px;
                                                                    background-color:#fff4d8;
                                                                    color:#b45309;
                                                                    font-size:17px;
                                                                    font-weight:700;
                                                                    line-height:36px;
                                                                "
                                                            >
                                                                !
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>

                                                <td
                                                    valign="middle"
                                                    style="
                                                        padding-left:8px;
                                                        color:#64748b;
                                                        font-size:13px;
                                                        line-height:1.65;
                                                    "
                                                >
                                                    Kode ini hanya berlaku selama
                                                    <strong style="color:#92400e;">
                                                        {{ $expiresInSeconds }} detik
                                                    </strong>
                                                    sejak email diterima.
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Informasi keamanan --}}
                                        <table
                                            role="presentation"
                                            width="100%"
                                            cellspacing="0"
                                            cellpadding="0"
                                            border="0"
                                            style="margin-top:22px;"
                                        >
                                            <tr>
                                                <td
                                                    class="security-cell"
                                                    style="
                                                        padding:18px;
                                                        border:1px solid #dcebe0;
                                                        border-radius:14px;
                                                        background-color:#f8fbf9;
                                                    "
                                                >
                                                    <p
                                                        style="
                                                            margin:0;
                                                            color:#274c34;
                                                            font-size:13px;
                                                            font-weight:700;
                                                            line-height:1.5;
                                                        "
                                                    >
                                                        Jaga keamanan akun Anda
                                                    </p>

                                                    <table
                                                        role="presentation"
                                                        width="100%"
                                                        cellspacing="0"
                                                        cellpadding="0"
                                                        border="0"
                                                        style="margin-top:11px;"
                                                    >
                                                        <tr>
                                                            <td
                                                                width="20"
                                                                valign="top"
                                                                style="
                                                                    width:20px;
                                                                    color:#179b40;
                                                                    font-size:13px;
                                                                    font-weight:700;
                                                                    line-height:1.6;
                                                                "
                                                            >
                                                                ✓
                                                            </td>

                                                            <td
                                                                style="
                                                                    color:#64748b;
                                                                    font-size:12px;
                                                                    line-height:1.65;
                                                                "
                                                            >
                                                                Jangan membagikan kode kepada siapa pun.
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td
                                                                width="20"
                                                                valign="top"
                                                                style="
                                                                    padding-top:5px;
                                                                    color:#179b40;
                                                                    font-size:13px;
                                                                    font-weight:700;
                                                                    line-height:1.6;
                                                                "
                                                            >
                                                                ✓
                                                            </td>

                                                            <td
                                                                style="
                                                                    padding-top:5px;
                                                                    color:#64748b;
                                                                    font-size:12px;
                                                                    line-height:1.65;
                                                                "
                                                            >
                                                                Tim FINUS tidak akan meminta kode melalui
                                                                telepon, pesan, atau media sosial.
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td
                                                                width="20"
                                                                valign="top"
                                                                style="
                                                                    padding-top:5px;
                                                                    color:#179b40;
                                                                    font-size:13px;
                                                                    font-weight:700;
                                                                    line-height:1.6;
                                                                "
                                                            >
                                                                ✓
                                                            </td>

                                                            <td
                                                                style="
                                                                    padding-top:5px;
                                                                    color:#64748b;
                                                                    font-size:12px;
                                                                    line-height:1.65;
                                                                "
                                                            >
                                                                Jika kode kedaluwarsa, Silahkanminta kode baru
                                                                melalui sistem.
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Peringatan --}}
                                        <table
                                            role="presentation"
                                            width="100%"
                                            cellspacing="0"
                                            cellpadding="0"
                                            border="0"
                                            style="margin-top:20px;"
                                        >
                                            <tr>
                                                <td
                                                    width="4"
                                                    bgcolor="#179B40"
                                                    style="
                                                        width:4px;
                                                        border-radius:4px 0 0 4px;
                                                        background-color:#179b40;
                                                    "
                                                >
                                                    &nbsp;
                                                </td>

                                                <td
                                                    style="
                                                        padding:14px 15px;
                                                        border-radius:0 11px 11px 0;
                                                        background-color:#f4faf6;
                                                        color:#64748b;
                                                        font-size:12px;
                                                        line-height:1.7;
                                                    "
                                                >
                                                    Jika Anda tidak pernah meminta reset password FINUS,
                                                    abaikan email ini. Password akun Anda tidak akan berubah
                                                    tanpa memasukkan kode OTP dan menyelesaikan proses reset.
                                                </td>
                                            </tr>
                                        </table>

                                        <p
                                            style="
                                                margin:27px 0 0;
                                                color:#64748b;
                                                font-size:13px;
                                                line-height:1.75;
                                            "
                                        >
                                            Wassalamu’alaikum Warahmatullahi Wabarakatuh.
                                        </p>

                                        <p
                                            style="
                                                margin:13px 0 0;
                                                color:#64748b;
                                                font-size:13px;
                                                line-height:1.7;
                                            "
                                        >
                                            Hormat kami,<br>
                                            <strong style="color:#0e5423;">
                                                Tim FINUS Pusdai Jawa Barat
                                            </strong>
                                        </p>
                                    </td>
                                </tr>

                                {{-- Footer --}}
                                <tr>
                                    <td
                                        align="center"
                                        class="footer-cell"
                                        style="
                                            padding:20px 24px;
                                            border-top:1px solid #e5eee7;
                                            background-color:#f8fbf9;
                                        "
                                    >
                                        <p
                                            style="
                                                margin:0;
                                                color:#7b8d82;
                                                font-size:11px;
                                                line-height:1.65;
                                            "
                                        >
                                            Email ini dikirim secara otomatis oleh sistem FINUS.<br>
                                            Mohon tidak membalas email ini.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Copyright --}}
                    <tr>
                        <td
                            align="center"
                            style="
                                padding:18px 20px 4px;
                                color:#91a096;
                                font-size:10.5px;
                                line-height:1.6;
                            "
                        >
                            © {{ date('Y') }} FINUS Pusdai Jawa Barat.<br>
                            Sistem Informasi Keuangan Masjid
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
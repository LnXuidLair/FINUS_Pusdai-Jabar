@extends('layouts.guest')

@section('portal', 'staff')
@section('title', 'FINUS | Aktivasi Pegawai Berhasil')
@section('header-title', 'Aktivasi Berhasil')
@section('panel-eyebrow', 'Akun Pegawai Aktif')
@section('panel-title', 'Simpan Recovery Code')
@section('panel-copy', 'Recovery Code hanya ditampilkan kepada Pegawai pada tahap ini.')
@section('hero-title', 'Akun Pegawai FINUS Siap Digunakan')
@section('hero-copy', 'Simpan Recovery Code untuk digunakan apabila Anda lupa password.')

@section('content')
<div class="activation-success">
    <div class="activation-status" role="status">
        <span class="activation-status-icon" aria-hidden="true">✓</span>
        <div class="activation-status-copy">
            <strong>Akun berhasil diaktifkan</strong>
            <span>Gunakan email Pegawai dan password yang baru dibuat untuk login.</span>
        </div>
    </div>

    <section class="activation-section" aria-labelledby="activation-account-title">
        <div class="activation-section-heading">
            <div>
                <span class="activation-kicker">Informasi akun</span>
                <h3 id="activation-account-title">Akun Pegawai</h3>
            </div>
            <span class="activation-ready-badge">
                <span aria-hidden="true"></span>
                Siap digunakan
            </span>
        </div>

        <div class="activation-info-row">
            <span class="activation-info-icon" aria-hidden="true">@</span>
            <div class="activation-info-copy">
                <span>Email Pegawai</span>
                <strong>{{ $activation['email'] }}</strong>
            </div>
        </div>
    </section>

    <section class="activation-code-card" aria-labelledby="activation-code-title">
        <div class="activation-code-heading">
            <div>
                <span class="activation-kicker">Simpan sekarang</span>
                <h3 id="activation-code-title">Recovery Code Pegawai</h3>
            </div>
            <span class="activation-code-mark" aria-hidden="true">#</span>
        </div>

        <div
            class="activation-code-field"
            role="textbox"
            aria-readonly="true"
            aria-label="Recovery Code Pegawai"
            tabindex="0"
        >{{ $activation['recovery_code'] }}</div>

        <div class="activation-notice">
            <span class="activation-notice-icon" aria-hidden="true">i</span>
            <p>
                Simpan kode ini di tempat yang aman. Setelah meninggalkan halaman ini,
                Pegawai tidak dapat melihatnya lagi dari akun sendiri. Admin tetap dapat
                melihat kode melalui Detail Pegawai. Recovery Code otomatis berubah jika
                password Pegawai diubah atau direset.
            </p>
        </div>
    </section>

    <a href="{{ route('login.staff') }}" class="activation-primary-button">
        <span>Saya Sudah Menyimpan Kode</span>
        <span class="activation-primary-arrow" aria-hidden="true">→</span>
    </a>
</div>
@endsection

@push('styles')
<style>
    .auth-card:has(.activation-success) {
        padding: clamp(18px, 3vw, 26px);
    }

    .activation-success {
        display: grid;
        gap: 15px;
    }

    .activation-status {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 15px;
        border: 1px solid #cce6d3;
        border-radius: 16px;
        background: linear-gradient(135deg, #f4fbf6 0%, #eaf8ee 100%);
    }

    .activation-status-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 13px;
        background: #dff5e5;
        color: #14933d;
        font-size: 21px;
        font-weight: 900;
    }

    .activation-status-copy {
        display: grid;
        gap: 3px;
        min-width: 0;
    }

    .activation-status-copy strong {
        color: #143d23;
        font-size: 12.5px;
        font-weight: 850;
        line-height: 1.35;
    }

    .activation-status-copy span {
        color: #65776b;
        font-size: 10.5px;
        line-height: 1.55;
    }

    .activation-section,
    .activation-code-card {
        border: 1px solid #d7e7dc;
        border-radius: 18px;
        background: #fbfdfb;
    }

    .activation-section {
        padding: 16px;
    }

    .activation-section-heading,
    .activation-code-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 13px;
    }

    .activation-kicker {
        display: block;
        margin-bottom: 3px;
        color: #1a9c45;
        font-size: 8.5px;
        font-weight: 900;
        letter-spacing: .11em;
        text-transform: uppercase;
    }

    .activation-section-heading h3,
    .activation-code-heading h3 {
        margin: 0;
        color: #183e26;
        font-size: 13.5px;
        font-weight: 850;
        line-height: 1.35;
    }

    .activation-ready-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex: 0 0 auto;
        padding: 6px 9px;
        border: 1px solid #d0ead7;
        border-radius: 999px;
        background: #eef9f1;
        color: #25733c;
        font-size: 8.5px;
        font-weight: 850;
        white-space: nowrap;
    }

    .activation-ready-badge > span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #35c55f;
        box-shadow: 0 0 0 3px rgba(53,197,95,.12);
    }

    .activation-info-row {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 0;
        padding: 12px 13px;
        border: 1px solid #d6e6db;
        border-radius: 13px;
        background: #fff;
    }

    .activation-info-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #eaf8ee;
        color: #14863a;
        font-size: 14px;
        font-weight: 900;
    }

    .activation-info-copy {
        display: grid;
        gap: 2px;
        min-width: 0;
    }

    .activation-info-copy span {
        color: #7a8a7f;
        font-size: 9px;
        font-weight: 750;
    }

    .activation-info-copy strong {
        overflow-wrap: anywhere;
        color: #1a3b26;
        font-size: 11.5px;
        font-weight: 800;
        line-height: 1.45;
    }

    .activation-code-card {
        position: relative;
        overflow: hidden;
        padding: 17px;
        background:
            radial-gradient(circle at 100% 0%, rgba(42,181,80,.07), transparent 150px),
            #f8fcf9;
    }

    .activation-code-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #e9f7ed;
        color: #15883b;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 14px;
        font-weight: 900;
    }

    .activation-code-field {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 62px;
        padding: 14px 16px;
        border: 1px solid #bddbc5;
        border-radius: 14px;
        background: #fff;
        color: #123a22;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        font-size: clamp(14px, 2.8vw, 18px);
        font-weight: 800;
        letter-spacing: .035em;
        line-height: 1.55;
        text-align: center;
        outline: none;
        cursor: text;
        box-sizing: border-box;
        overflow-wrap: anywhere;
        word-break: break-word;
        white-space: normal;
        user-select: all;
    }

    .activation-code-field:focus {
        border-color: #32b85c;
        box-shadow: 0 0 0 4px rgba(50,184,92,.12);
    }

    .activation-notice {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin-top: 12px;
        padding: 11px 12px;
        border-radius: 12px;
        background: #eff7f1;
    }

    .activation-notice-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 20px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #d9f0df;
        color: #16883c;
        font-size: 9px;
        font-weight: 900;
    }

    .activation-notice p {
        margin: 0;
        color: #617267;
        font-size: 9.7px;
        line-height: 1.58;
    }

    .activation-primary-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 52px;
        padding: 13px 18px;
        border-radius: 14px;
        background: linear-gradient(135deg, #119c40 0%, #20ba53 100%);
        color: #fff !important;
        font-size: 12px;
        font-weight: 850;
        text-decoration: none !important;
        box-shadow: 0 12px 24px rgba(11,125,47,.18);
        transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }

    .activation-primary-button:hover {
        transform: translateY(-1px);
        filter: brightness(1.04);
        box-shadow: 0 15px 28px rgba(11,125,47,.23);
    }

    .activation-primary-button:focus-visible {
        outline: 4px solid rgba(47,190,90,.18);
        outline-offset: 2px;
    }

    .activation-primary-arrow {
        font-size: 15px;
        line-height: 1;
        transition: transform .18s ease;
    }

    .activation-primary-button:hover .activation-primary-arrow {
        transform: translateX(3px);
    }

    @media (max-width: 520px) {
        .auth-card:has(.activation-success) {
            padding: 15px;
            border-radius: 19px;
        }

        .activation-success {
            gap: 12px;
        }

        .activation-status,
        .activation-section,
        .activation-code-card {
            border-radius: 14px;
        }

        .activation-status {
            padding: 12px;
        }

        .activation-status-icon {
            flex-basis: 36px;
            width: 36px;
            height: 36px;
            border-radius: 11px;
            font-size: 18px;
        }

        .activation-section,
        .activation-code-card {
            padding: 13px;
        }

        .activation-section-heading,
        .activation-code-heading {
            align-items: flex-start;
            margin-bottom: 11px;
        }

        .activation-ready-badge {
            padding: 5px 7px;
            font-size: 7.8px;
        }

        .activation-info-row {
            padding: 10px 11px;
        }

        .activation-code-field {
            min-height: 56px;
            padding: 12px 10px;
            font-size: clamp(12px, 4.1vw, 15px);
            letter-spacing: .02em;
            line-height: 1.5;
        }

        .activation-notice {
            padding: 10px;
        }

        .activation-primary-button {
            min-height: 49px;
            border-radius: 12px;
            font-size: 11px;
        }
    }
</style>
@endpush

{{-- FINUS DARK MODE LOCAL: auth/staff-activation-success.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="auth/staff-activation-success.blade.php">
html[data-finus-theme="dark"] body .auth-panel {
    background: linear-gradient(160deg,#0F1813 0%,#0B120E 100%) !important;
}

html[data-finus-theme="dark"] body .auth-panel-inner {
    color:#F1F6F3 !important;
}

html[data-finus-theme="dark"] body .auth-card:has(.activation-success) {
    border-color: #294033 !important;
    background: rgba(14, 24, 17, .96) !important;
    box-shadow: 0 24px 60px rgba(0,0,0,.30) !important;
}

html[data-finus-theme="dark"] body .activation-status {
    border-color: #294838;
    background: linear-gradient(135deg, #14241A 0%, #102018 100%);
}

html[data-finus-theme="dark"] body .activation-status-icon {
    background: #193522;
    color: #70E78B;
}

html[data-finus-theme="dark"] body .activation-status-copy strong,
html[data-finus-theme="dark"] body .activation-section-heading h3,
html[data-finus-theme="dark"] body .activation-code-heading h3,
html[data-finus-theme="dark"] body .activation-info-copy strong {
    color: #EDF7EF;
}

html[data-finus-theme="dark"] body .activation-status-copy span,
html[data-finus-theme="dark"] body .activation-info-copy span,
html[data-finus-theme="dark"] body .activation-notice p {
    color: #9EAEA4;
}

html[data-finus-theme="dark"] body .activation-section,
html[data-finus-theme="dark"] body .activation-code-card {
    border-color: #2B4434;
    background: #101C15;
}

html[data-finus-theme="dark"] body .activation-code-card {
    background:
        radial-gradient(circle at 100% 0%, rgba(64,210,99,.07), transparent 150px),
        #101C15;
}

html[data-finus-theme="dark"] body .activation-kicker {
    color: #61DE7E;
}

html[data-finus-theme="dark"] body .activation-ready-badge {
    border-color: #31583B;
    background: #142A1B;
    color: #A9E8B7;
}

html[data-finus-theme="dark"] body .activation-info-row {
    border-color: #304A38;
    background: #0D1711;
}

html[data-finus-theme="dark"] body .activation-info-icon,
html[data-finus-theme="dark"] body .activation-code-mark {
    background: #17331F;
    color: #72E58C;
}

html[data-finus-theme="dark"] body .activation-code-field {
    border-color: #385842;
    background: #09130D;
    color: #F3F8F4;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.01);
}

html[data-finus-theme="dark"] body .activation-code-field:focus {
    border-color: #58D575;
    box-shadow: 0 0 0 4px rgba(88,213,117,.12);
}

html[data-finus-theme="dark"] body .activation-notice {
    background: #14231A;
}

html[data-finus-theme="dark"] body .activation-notice-icon {
    background: #1C4027;
    color: #7DE793;
}

html[data-finus-theme="dark"] body .activation-primary-button {
    background: linear-gradient(135deg, #118D39 0%, #1EAA4A 100%);
    box-shadow: 0 12px 25px rgba(0,0,0,.20);
}
</style>
@endpush

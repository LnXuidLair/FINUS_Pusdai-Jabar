@extends('layouts.app')

@section('content')
@include('layouts.partials.finus-ui')
<style>
    :root {
        --finus-green-dark: #0E5423;
        --finus-green: #179B40;
        --finus-green-light: #22BA51;
        --finus-green-soft: #EAF9EE;
        --finus-lime: #7EFF87;

        --finus-text: #172033;
        --finus-muted: #64748B;
        --finus-border: #DDE6E1;
        --finus-bg: #F5F9F6;
        --finus-danger: #DC2626;
    }

    .finus-page {
        position: relative;
        padding: 14px 4px 30px;
    }

    .finus-page::before {
        content: "";
        position: absolute;
        top: -30px;
        right: 3%;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(34, 186, 81, .08);
        filter: blur(2px);
        pointer-events: none;
    }

    .finus-page::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 2%;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: rgba(126, 255, 135, .10);
        pointer-events: none;
    }

    .finus-form-wrapper {
        position: relative;
        z-index: 1;
    }

    .finus-form-card {
        border: 1px solid rgba(14, 84, 35, .08);
        border-radius: 24px;
        overflow: hidden;
        background: #ffffff;
        box-shadow:
            0 24px 55px rgba(15, 23, 42, .09),
            0 6px 18px rgba(14, 84, 35, .05);
    }

    .finus-form-header {
        position: relative;
        overflow: hidden;
        padding: 30px 32px;
        color: #ffffff;
        background: linear-gradient(
            135deg,
            #0E5423 0%,
            #179B40 45%,
            #22BA51 100%
        );
    }

    .finus-form-header::before {
        content: "";
        position: absolute;
        top: -70px;
        right: -45px;
        width: 210px;
        height: 210px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .10);
    }

    .finus-form-header::after {
        content: "";
        position: absolute;
        right: 90px;
        bottom: -95px;
        width: 170px;
        height: 170px;
        border-radius: 50%;
        background: rgba(126, 255, 135, .13);
    }

    .finus-header-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .finus-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 58px;
        min-width: 58px;
        height: 58px;
        border-radius: 17px;
        background: rgba(255, 255, 255, .15);
        border: 1px solid rgba(255, 255, 255, .20);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .14);
        backdrop-filter: blur(8px);
    }

    .finus-header-icon i {
        font-size: 24px;
        color: #ffffff;
    }

    .finus-form-header h4 {
        margin: 0;
        color: #ffffff;
        font-size: 23px;
        font-weight: 800;
        line-height: 1.25;
        letter-spacing: -.02em;
    }

    .finus-form-header p {
        margin: 7px 0 0;
        color: rgba(255, 255, 255, .82);
        font-size: 14px;
        line-height: 1.6;
    }

    .finus-form-body {
        padding: 32px;
        background:
            linear-gradient(
                180deg,
                rgba(234, 249, 238, .42) 0%,
                #ffffff 90px
            );
    }

    .finus-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #E8EFEB;
    }

    .finus-section-heading {
        margin: 0;
        color: var(--finus-text);
        font-size: 16px;
        font-weight: 800;
    }

    .finus-section-description {
        margin: 4px 0 0;
        color: var(--finus-muted);
        font-size: 13px;
    }

    .finus-required-info {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--finus-muted);
        font-size: 12px;
        white-space: nowrap;
    }

    .finus-required-info span {
        color: var(--finus-danger);
        font-weight: 800;
    }

    .finus-field {
        padding: 19px;
        border: 1px solid #E5EDE8;
        border-radius: 17px;
        background: rgba(255, 255, 255, .92);
        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            transform .2s ease;
    }

    .finus-field + .finus-field {
        margin-top: 18px;
    }

    .finus-fields-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.12fr) minmax(310px, .88fr);
        gap: 18px;
        align-items: stretch;
    }

    .finus-fields-grid .finus-field + .finus-field {
        margin-top: 0;
    }

    .finus-field:focus-within {
        border-color: rgba(23, 155, 64, .38);
        box-shadow: 0 10px 28px rgba(23, 155, 64, .08);
        transform: translateY(-1px);
    }

    .finus-label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 9px;
        color: var(--finus-text);
        font-size: 14px;
        font-weight: 800;
    }

    .finus-label-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 27px;
        height: 27px;
        border-radius: 8px;
        background: var(--finus-green-soft);
        color: var(--finus-green);
        font-size: 12px;
    }

    .finus-required {
        color: var(--finus-danger);
        margin-left: 2px;
    }

    .finus-input-group {
        position: relative;
    }

    .finus-input-prefix {
        position: absolute;
        top: 50%;
        left: 15px;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 9px;
        border-radius: 9px;
        color: #15743A;
        background: #E8F8ED;
        font-size: 13px;
        font-weight: 800;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .finus-input {
        width: 100%;
        height: 54px;
        padding: 0 16px 0 62px;
        border: 1px solid var(--finus-border);
        border-radius: 13px;
        background: #FBFDFC;
        color: var(--finus-text);
        font-size: 14px;
        font-weight: 500;
        box-shadow: none !important;
        outline: none;
        transition:
            border-color .2s ease,
            background .2s ease,
            box-shadow .2s ease;
    }

    .finus-input::placeholder {
        color: #94A3B8;
        font-weight: 400;
    }

    .finus-input:hover {
        border-color: #BFD4C6;
        background: #ffffff;
    }

    .finus-input:focus {
        border-color: var(--finus-green);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(23, 155, 64, .11) !important;
    }

    .finus-input.is-invalid {
        border-color: #EF4444;
        background: #FFFBFB;
    }

    .finus-input.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, .10) !important;
    }

    .finus-help {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 9px;
        color: var(--finus-muted);
        font-size: 12.5px;
        line-height: 1.55;
    }

    .finus-help i {
        margin-top: 3px;
        color: var(--finus-green);
        font-size: 11px;
    }

    .finus-error {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 8px;
        color: var(--finus-danger);
        font-size: 12.5px;
        font-weight: 600;
    }

    .finus-alert {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        margin-bottom: 24px;
        padding: 16px 18px;
        border: 1px solid #FECACA;
        border-radius: 14px;
        background: #FFF7F7;
        color: #991B1B;
    }

    .finus-alert-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        min-width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #FEE2E2;
        color: #DC2626;
    }

    .finus-alert strong {
        display: block;
        margin-bottom: 5px;
        color: #991B1B;
    }

    .finus-alert ul {
        margin: 0;
        padding-left: 18px;
        color: #B91C1C;
        font-size: 13px;
        line-height: 1.6;
    }

    .finus-form-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid #E7EEE9;
    }

    .finus-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 48px;
        padding: 12px 21px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 800;
        line-height: 1;
        text-decoration: none !important;
        cursor: pointer;
        transition:
            transform .2s ease,
            box-shadow .2s ease,
            background .2s ease,
            border-color .2s ease,
            color .2s ease;
    }

    .finus-btn-primary {
        border: 1px solid transparent;
        color: #ffffff !important;
        background: linear-gradient(
            135deg,
            #0E5423 0%,
            #179B40 50%,
            #22BA51 100%
        );
        box-shadow: 0 9px 20px rgba(23, 155, 64, .22);
    }

    .finus-btn-primary:hover {
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 13px 25px rgba(23, 155, 64, .30);
    }

    .finus-btn-primary:active {
        transform: translateY(0);
    }

    .finus-btn-secondary {
        border: 1px solid #CEDBD2;
        color: #334155 !important;
        background: #ffffff;
        box-shadow: 0 5px 13px rgba(15, 23, 42, .04);
    }

    .finus-btn-secondary:hover {
        border-color: #AFC6B6;
        color: var(--finus-green-dark) !important;
        background: var(--finus-green-soft);
        transform: translateY(-1px);
    }

    @media (max-width: 767px) {
        .finus-page {
            padding-top: 5px;
        }

        .finus-form-card {
            border-radius: 18px;
        }

        .finus-form-header {
            padding: 24px 21px;
        }

        .finus-header-content {
            align-items: flex-start;
        }

        .finus-header-icon {
            width: 50px;
            min-width: 50px;
            height: 50px;
            border-radius: 14px;
        }

        .finus-form-header h4 {
            font-size: 20px;
        }

        .finus-form-body {
            padding: 22px 18px;
        }

        .finus-section-title {
            align-items: flex-start;
            flex-direction: column;
        }

        .finus-fields-grid {
            grid-template-columns: 1fr;
        }

        .finus-field {
            padding: 16px;
        }

        .finus-form-footer {
            flex-direction: column-reverse;
        }

        .finus-btn {
            width: 100%;
        }
    }
</style>

<div class="finus-page">
    <div class="row justify-content-center finus-form-wrapper">
        <div class="col-xl-9 col-lg-10 col-md-12">

            <div class="card finus-form-card mb-4">
                <div class="finus-form-header">
                    <div class="finus-header-content">
                        <div class="finus-header-icon">
                            <i class="fa fa-briefcase"></i>
                        </div>

                        <div>
                            <h4>Tambah Gaji & Jabatan</h4>
                            <p>
                                Masukkan informasi jabatan pegawai dan tentukan
                                nominal gaji yang diterima setiap hari.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="finus-form-body">
                    <div class="finus-section-title">
                        <div>
                            <h5 class="finus-section-heading">
                                Informasi Jabatan
                            </h5>

                            <p class="finus-section-description">
                                Pastikan data yang dimasukkan sudah benar sebelum disimpan.
                            </p>
                        </div>

                        <div class="finus-required-info">
                            <span>*</span>
                            Wajib diisi
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="finus-alert">
                            <div class="finus-alert-icon">
                                <i class="fa fa-triangle-exclamation"></i>
                            </div>

                            <div>
                                <strong>Data belum dapat disimpan.</strong>

                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form
                        action="{{ route('admin.gaji-jabatan.store') }}"
                        method="POST"
                    >
                        @csrf

                        <div class="finus-fields-grid">
                            <div class="finus-field">
                            <label for="jabatan" class="finus-label">
                                <span class="finus-label-icon">
                                    <i class="fa fa-briefcase"></i>
                                </span>

                                Nama Jabatan
                                <span class="finus-required">*</span>
                            </label>

                            <div class="finus-input-group">
                                <span class="finus-input-prefix">
                                    <i class="fa fa-user-tie"></i>
                                </span>

                                <input
                                    type="text"
                                    name="jabatan"
                                    id="jabatan"
                                    value="{{ old('jabatan') }}"
                                    class="form-control finus-input @error('jabatan') is-invalid @enderror"
                                    placeholder="Contoh: DKM, KBIH, Security, Kebersihan"
                                    autocomplete="off"
                                    maxlength="100"
                                    required
                                >
                            </div>

                            <div class="finus-help">
                                <i class="fa fa-circle-info"></i>
                                <span>
                                    Gunakan nama jabatan yang jelas dan tidak sama
                                    dengan data yang sudah tersedia.
                                </span>
                            </div>

                            @error('jabatan')
                                <div class="finus-error">
                                    <i class="fa fa-circle-exclamation"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="finus-field">
                            <label for="gaji_perhari" class="finus-label">
                                <span class="finus-label-icon">
                                    <i class="fa fa-money-bill-wave"></i>
                                </span>

                                Gaji Per Hari
                                <span class="finus-required">*</span>
                            </label>

                            <div class="finus-input-group">
                                <span class="finus-input-prefix">
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    name="gaji_perhari"
                                    id="gaji_perhari"
                                    value="{{ old('gaji_perhari') }}"
                                    class="form-control finus-input @error('gaji_perhari') is-invalid @enderror"
                                    placeholder="Contoh: 1500 atau 100000"
                                    min="0"
                                    step="1"
                                    inputmode="numeric"
                                    required
                                >
                            </div>

                            <div class="finus-help">
                                <i class="fa fa-circle-info"></i>
                                <span>
                                    Masukkan nominal dalam angka bulat tanpa titik atau koma.
                                    Contoh: 1250, 1500, atau 100000.
                                </span>
                            </div>

                            @error('gaji_perhari')
                                <div class="finus-error">
                                    <i class="fa fa-circle-exclamation"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            </div>
                        </div>

                        <div class="finus-form-footer">
                            <a
                                href="{{ route('admin.gaji-jabatan.index') }}"
                                class="finus-btn finus-btn-secondary"
                            >
                                <i class="fa fa-arrow-left"></i>
                                Kembali
                            </a>

                            <button
                                type="submit"
                                class="finus-btn finus-btn-primary"
                            >
                                <i class="fa fa-floppy-disk"></i>
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

{{-- FINUS DARK MODE LOCAL: gaji_jabatan/create.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="gaji_jabatan/create.blade.php">
html[data-finus-theme="dark"] body .finus-form-card {
    border:1px solid #294334 !important;
    background:#101A14 !important;
    color:#F4F8F5 !important;
    box-shadow:0 26px 60px rgba(0,0,0,.32),0 0 0 1px rgba(73,180,104,.03) !important;
}
html[data-finus-theme="dark"] body .finus-form-header {
    border-color:transparent !important;
    background:linear-gradient(135deg,#0B4A20 0%,#118338 48%,#1DB64A 100%) !important;
}
html[data-finus-theme="dark"] body .finus-form-header::before { background:rgba(255,255,255,.075) !important; }
html[data-finus-theme="dark"] body .finus-form-header::after { background:rgba(126,255,135,.11) !important; }
html[data-finus-theme="dark"] body .finus-header-icon {
    border-color:rgba(255,255,255,.22) !important;
    background:rgba(5,32,14,.26) !important;
}
html[data-finus-theme="dark"] body .finus-form-header h4 { color:#FFFFFF !important; }
html[data-finus-theme="dark"] body .finus-form-header p { color:rgba(255,255,255,.82) !important; }
html[data-finus-theme="dark"] body .finus-form-body {
    background:linear-gradient(180deg,#132119 0%,#101A14 100%) !important;
}
html[data-finus-theme="dark"] body .finus-section-title { border-color:#2B4033 !important; }
html[data-finus-theme="dark"] body :where(.finus-section-heading,.finus-label) { color:#F2F7F3 !important; }
html[data-finus-theme="dark"] body :where(.finus-section-description,.finus-help,.finus-required-info) { color:#A6B7AC !important; }
html[data-finus-theme="dark"] body .finus-field {
    border-color:#2C4636 !important;
    background:linear-gradient(145deg,#17251C,#132018) !important;
    box-shadow:0 10px 24px rgba(0,0,0,.12) !important;
}
html[data-finus-theme="dark"] body .finus-field:focus-within {
    border-color:#438D57 !important;
    box-shadow:0 0 0 3px rgba(89,210,119,.08),0 14px 30px rgba(0,0,0,.16) !important;
}
html[data-finus-theme="dark"] body .finus-label-icon {
    background:#173A24 !important;
    color:#69E489 !important;
}
html[data-finus-theme="dark"] body :where(.finus-input,.form-control) {
    border-color:#35503F !important;
    background:#0B1510 !important;
    color:#F3F8F4 !important;
}
html[data-finus-theme="dark"] body :where(.finus-input,.form-control)::placeholder { color:#758A7D !important; }
html[data-finus-theme="dark"] body :where(.finus-input,.form-control):hover {
    border-color:#456451 !important;
    background:#0D1812 !important;
}
html[data-finus-theme="dark"] body :where(.finus-input,.form-control):focus {
    border-color:#64DD81 !important;
    background:#0E1A13 !important;
    box-shadow:0 0 0 4px rgba(100,221,129,.11) !important;
}
html[data-finus-theme="dark"] body .finus-input-prefix {
    border-color:#35503F !important;
    background:#173A24 !important;
    color:#BDF3C9 !important;
}
html[data-finus-theme="dark"] body .finus-form-footer { border-color:#2B4033 !important; background:transparent !important; }
html[data-finus-theme="dark"] body .finus-btn-secondary {
    border-color:#385442 !important;
    background:#16231B !important;
    color:#DCE8E0 !important;
}
html[data-finus-theme="dark"] body .finus-btn-secondary:hover {
    border-color:#4C745A !important;
    background:#1A3022 !important;
    color:#F1FAF3 !important;
}
html[data-finus-theme="dark"] body .finus-alert {
    border-color:#704044 !important;
    background:#321D21 !important;
    color:#FFC2C5 !important;
}
html[data-finus-theme="dark"] body .finus-alert strong,
html[data-finus-theme="dark"] body .finus-alert ul { color:#FFC2C5 !important; }
</style>
@endpush


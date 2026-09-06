@extends('layouts.app')

@section('content')
@include('layouts.partials.finus-ui')
<style>
    .finus-form-card {
        border: 0;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .finus-form-header {
        background: linear-gradient(135deg, #065f46, #16a34a);
        color: #ffffff;
        padding: 26px 30px;
    }

    .finus-form-header h4 {
        margin: 0;
        font-weight: 700;
        color: #ffffff;
    }

    .finus-form-header p {
        margin: 8px 0 0;
        color: rgba(255, 255, 255, .78);
        font-size: 14px;
    }

    .finus-form-body {
        padding: 30px;
        background: #ffffff;
    }

    .finus-label {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        display: block;
    }

    .finus-input-group {
        position: relative;
    }

    .finus-input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        z-index: 2;
    }

    .finus-input {
        height: 48px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding-left: 46px;
        font-size: 15px;
        color: #1e293b;
        box-shadow: none;
        transition: .2s;
    }

    .finus-input:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 4px rgba(22, 163, 74, .12);
    }

    .finus-help {
        font-size: 13px;
        color: #64748b;
        margin-top: 6px;
    }

    .finus-error {
        font-size: 13px;
        color: #dc2626;
        margin-top: 6px;
    }

    .finus-btn-primary {
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #047857, #16a34a);
        color: #ffffff;
        font-weight: 700;
        padding: 13px 24px;
        transition: .2s;
    }

    .finus-btn-primary:hover {
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(22, 163, 74, .28);
    }

    .finus-btn-secondary {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        font-weight: 700;
        padding: 12px 22px;
        text-decoration: none;
        transition: .2s;
    }

    .finus-btn-secondary:hover {
        background: #f8fafc;
        color: #0f172a;
        text-decoration: none;
    }

    .finus-alert {
        border-radius: 14px;
        border: 0;
    }
</style>

<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-10 col-md-12">
        <div class="card finus-form-card mb-4">
            <div class="finus-form-header">
                <h4>Edit Gaji & Jabatan</h4>
                <p>Perbarui data jabatan pegawai beserta nominal gaji per hari.</p>
            </div>

            <div class="finus-form-body">
                @if ($errors->any())
                    <div class="alert alert-danger finus-alert mb-4">
                        <strong>Data belum valid.</strong>
                        <ul class="mb-0 mt-2 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.gaji-jabatan.update', $gajiJabatan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-4">
                        <label for="jabatan" class="finus-label">
                            Nama Jabatan
                        </label>

                        <div class="finus-input-group">
                            <span class="finus-input-icon">
                                <i class="fa fa-briefcase"></i>
                            </span>

                            <input
                                type="text"
                                name="jabatan"
                                id="jabatan"
                                value="{{ old('jabatan', $gajiJabatan->jabatan) }}"
                                class="form-control finus-input @error('jabatan') is-invalid @enderror"
                                placeholder="Contoh: DKM, KBIH, Security, Kebersihan"
                                autocomplete="off"
                                required
                            >
                        </div>

                        <div class="finus-help">
                            Nama jabatan tidak boleh sama dengan data yang sudah ada.
                        </div>

                        @error('jabatan')
                            <div class="finus-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="gaji_perhari" class="finus-label">
                            Gaji Per Hari
                        </label>

                        <div class="finus-input-group">
                            <span class="finus-input-icon">
                                Rp
                            </span>

                            <input
                                type="number"
                                name="gaji_perhari"
                                id="gaji_perhari"
                                value="{{ old('gaji_perhari', $gajiJabatan->gaji_perhari) }}"
                                class="form-control finus-input @error('gaji_perhari') is-invalid @enderror"
                                placeholder="Contoh: 1500 atau 100000"
                                min="0"
                                step="1"
                                required
                            >
                        </div>

                        <div class="finus-help">
                            Masukkan angka bulat tanpa titik atau koma. Contoh: 1250, 1500, atau 100000.
                        </div>

                        @error('gaji_perhari')
                            <div class="finus-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('admin.gaji-jabatan.index') }}" class="finus-btn-secondary">
                            <i class="fa fa-arrow-left mr-1"></i>
                            Kembali
                        </a>

                        <button type="submit" class="finus-btn-primary">
                            <i class="fa fa-save mr-1"></i>
                            Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- FINUS DARK MODE LOCAL: gaji_jabatan/edit.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="gaji_jabatan/edit.blade.php">
html[data-finus-theme="dark"] body .finus-form-card {
    border:1px solid #294334 !important; background:#101A14 !important; color:#F4F8F5 !important;
    box-shadow:0 24px 55px rgba(0,0,0,.30) !important;
}
html[data-finus-theme="dark"] body .finus-form-header {
    background:linear-gradient(135deg,#0B4A20,#118338 50%,#1DB64A) !important; color:#fff !important;
}
html[data-finus-theme="dark"] body .finus-form-header h4 { color:#fff !important; }
html[data-finus-theme="dark"] body .finus-form-header p { color:rgba(255,255,255,.82) !important; }
html[data-finus-theme="dark"] body .finus-form-body { background:linear-gradient(180deg,#132119,#101A14) !important; }
html[data-finus-theme="dark"] body .form-group {
    padding:18px; border:1px solid #2C4636; border-radius:16px; background:linear-gradient(145deg,#17251C,#132018);
}
html[data-finus-theme="dark"] body .finus-label { color:#F2F7F3 !important; }
html[data-finus-theme="dark"] body .finus-help { color:#A6B7AC !important; }
html[data-finus-theme="dark"] body :where(.finus-input,.form-control) { border-color:#35503F !important; background:#0B1510 !important; color:#F3F8F4 !important; }
html[data-finus-theme="dark"] body :where(.finus-input,.form-control)::placeholder { color:#758A7D !important; }
html[data-finus-theme="dark"] body :where(.finus-input,.form-control):focus { border-color:#64DD81 !important; background:#0E1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.11) !important; }
html[data-finus-theme="dark"] body .finus-input-icon { color:#74DF8E !important; }
html[data-finus-theme="dark"] body .finus-btn-secondary { border-color:#385442 !important; background:#16231B !important; color:#DCE8E0 !important; }
html[data-finus-theme="dark"] body .finus-alert { border:1px solid #704044 !important; background:#321D21 !important; color:#FFC2C5 !important; }
</style>
@endpush


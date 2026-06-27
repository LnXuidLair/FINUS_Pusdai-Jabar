@extends('layouts.app')

@section('content')
<style>
.admin-card { border-radius:12px; border:1px solid #eef2ff; box-shadow:0 18px 40px rgba(2,6,23,0.06); overflow:hidden; }
.admin-card .card-body { padding:24px; }
.admin-title { font-size:18px; font-weight:700; color:#fff; }
.admin-subtitle { font-size:13px; color:rgba(255,255,255,0.9); }
.card-footer { background:#fff; border-top:0; padding:14px 22px; }
.form-group label { font-size:13px; font-weight:600; color:#334155; }
.form-control { border-radius:8px; box-shadow:none; }
.form-control:focus { box-shadow:0 6px 18px rgba(99,102,241,0.12); border-color:#6366f1; }
.header-gradient { position:relative; background:linear-gradient(135deg,#06b6d4,#6366f1); padding:16px 20px; }
.header-gradient::after{ content:""; position:absolute; inset:0; background:rgba(0,0,0,0.12); pointer-events:none; }
.header-content{ position:relative; z-index:2; display:flex; align-items:center; justify-content:space-between; }
.header-left{ display:flex; align-items:center; }
.header-icon{ width:44px; height:44px; border-radius:10px; background:rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:center; margin-right:12px; }
.btn-footer-save {
    background: linear-gradient(90deg,#6366f1,#dc2626);
    color: #fff;
    border: 0;
    border-radius: 10px;
    padding: .45rem .9rem;
    box-shadow: 0 12px 36px rgba(220,38,38,0.12);
    transition: transform .14s ease, box-shadow .14s ease, opacity .14s;
}
.btn-footer-save:hover { transform: translateY(-3px); box-shadow: 0 20px 48px rgba(2,6,23,0.14); }
.btn-footer-save i { margin-right:8px; }
.iti { width:100% !important; }
</style>

<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-9 col-md-11">
        <div class="card admin-card">

            <div class="header-gradient">
                <div class="header-content">
                    <div class="header-left">
                        <div class="header-icon"><i class="fa fa-edit text-white"></i></div>
                        <div>
                            <div class="admin-title">Edit Pegawai</div>
                            <div class="admin-subtitle">Perbarui informasi pegawai</div>
                        </div>
                    </div>
                    <div></div>
                </div>
            </div>

            {{-- BODY --}}
            <form method="POST" action="{{ route('admin.pegawai.update', $pegawai->id) }}">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>NIP <span class="text-danger">*</span></label>
                                <input type="text" name="nip"
                                    value="{{ old('nip', $pegawai->nip) }}"
                                    class="form-control @error('nip') is-invalid @enderror" required>
                                @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Pegawai <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pegawai"
                                    value="{{ old('nama_pegawai', $pegawai->nama_pegawai) }}"
                                    class="form-control @error('nama_pegawai') is-invalid @enderror" required>
                                @error('nama_pegawai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jabatan <span class="text-danger">*</span></label>
                                <select name="jabatan"
                                    class="form-control @error('jabatan') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($jabatanOptions as $jabatan)
                                        <option value="{{ $jabatan }}"
                                            {{ old('jabatan', $pegawai->jabatan) === $jabatan ? 'selected' : '' }}>
                                            {{ $jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="gender"
                                    class="form-control @error('gender') is-invalid @enderror" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('gender', $pegawai->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender', $pegawai->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    value="{{ old('email', $pegawai->email) }}"
                                    class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No Telepon</label>
                                <input type="tel" id="phone_display" class="form-control">
                                <input type="hidden" name="no_telp" id="phone_value" value="{{ $pegawai->no_telp }}">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat" rows="3"
                                    class="form-control">{{ old('alamat', $pegawai->alamat) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="card-footer">
                    @php
                        $prev = url()->previous();
                        // avoid linking to the same page (edit) — fallback to index
                        $backUrl = ($prev && $prev !== url()->current()) ? $prev : route('admin.pegawai.index');
                    @endphp
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <button class="btn-footer-save btn-sm">
                                <i class="fa fa-save"></i> Save
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const phoneInput = document.querySelector("#phone_display");
const iti = window.intlTelInput(phoneInput, {
    initialCountry: "auto",
    separateDialCode: true,
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
});

@if($pegawai->no_telp)
iti.setNumber("{{ $pegawai->no_telp }}");
@endif

phoneInput.addEventListener("blur", () => {
    if (iti.isValidNumber()) {
        document.getElementById("phone_value").value = iti.getNumber();
    }
});
</script>
@endpush

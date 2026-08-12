@extends('layouts.app')

@section('title', 'Tambah Agenda Kegiatan')

@section('content')
@include('layouts.partials.finus-ui')

<style>
    .ag-form-page { padding: 8px 0 40px; }

    .ag-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 20px;
        padding: 8px 14px;
        border: 1px solid var(--jt-border);
        border-radius: 10px;
        background: #fff;
        color: var(--jt-muted);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all .2s;
    }
    .ag-back:hover { border-color: #b6d4c1; color: var(--jt-green-dark); transform: translateX(-2px); text-decoration: none; }

    .ag-form-card {
        background: #fff;
        border: 1px solid var(--jt-border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15,23,42,.06);
    }

    .ag-form-header {
        padding: 28px 32px;
        background: linear-gradient(135deg, #064e1a 0%, #0e7a2e 50%, #179b40 100%);
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .ag-form-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,.07);
        pointer-events: none;
    }

    .ag-form-header h2 {
        font-size: 22px;
        font-weight: 900;
        margin: 0 0 6px;
        color: #ffffff;
        text-shadow: 0 2px 12px rgba(0,0,0,.25);
        letter-spacing: .01em;
    }

    .ag-form-header p { font-size: 13px; color: rgba(255,255,255,.85); margin: 0; }

    .ag-form-body { padding: 28px; }

    .ag-field { margin-bottom: 20px; }

    .ag-field label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--jt-text);
        margin-bottom: 7px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .ag-field label .req { color: #ef4444; margin-left: 2px; }

    .ag-field input,
    .ag-field select,
    .ag-field textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--jt-border);
        border-radius: 10px;
        font-size: 13px;
        color: var(--jt-text);
        background: #fff;
        outline: none;
        transition: border-color .2s;
        box-sizing: border-box;
    }

    .ag-field input:focus,
    .ag-field select:focus,
    .ag-field textarea:focus { border-color: #179b40; }

    .ag-field textarea { resize: vertical; min-height: 90px; }

    .ag-field-hint { font-size: 11px; color: var(--jt-muted); margin-top: 5px; }

    .ag-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    .ag-toggle-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border: 1.5px solid var(--jt-border);
        border-radius: 10px;
    }

    .ag-toggle-wrap input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #179b40;
        cursor: pointer;
        flex-shrink: 0;
    }

    .ag-toggle-label { font-size: 13px; color: var(--jt-text); cursor: pointer; }

    .ag-form-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 8px;
        border-top: 1px solid #f1f5f9;
        margin-top: 8px;
    }

    .ag-btn-cancel {
        padding: 10px 20px;
        background: #f8fafc;
        border: 1px solid var(--jt-border);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: var(--jt-muted);
        text-decoration: none;
        transition: all .15s;
    }
    .ag-btn-cancel:hover { background: #f1f5f9; color: var(--jt-text); text-decoration: none; }

    .ag-btn-save {
        padding: 10px 24px;
        background: linear-gradient(135deg, #064e1a, #179b40);
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
        transition: transform .2s, box-shadow .2s;
    }
    .ag-btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(23,155,64,.3); }

    .ag-error { color: #ef4444; font-size: 11px; margin-top: 4px; }
</style>

<div class="jt-page ag-form-page">

    <a href="{{ route('admin.agenda-kegiatan.index') }}" class="ag-back">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Agenda
    </a>

    <div class="ag-form-card">
        <div class="ag-form-header">
            <h2><i class="fa-solid fa-plus" style="margin-right:8px;"></i>Tambah Agenda Kegiatan</h2>
            <p>Isi form di bawah untuk menambahkan agenda baru yang akan ditampilkan di dashboard jamaah.</p>
        </div>

        <div class="ag-form-body">
            <form method="POST" action="{{ route('admin.agenda-kegiatan.store') }}">
                @csrf

                <div class="ag-field">
                    <label for="judul">Judul Kegiatan <span class="req">*</span></label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}"
                           placeholder="contoh: Kajian Rutin Subuh" required>
                    @error('judul') <div class="ag-error">{{ $message }}</div> @enderror
                </div>

                <div class="ag-field-row">
                    <div class="ag-field">
                        <label for="kategori">Kategori <span class="req">*</span></label>
                        <select id="kategori" name="kategori" required>
                            @foreach($kategoriLabels as $value => $label)
                                <option value="{{ $value }}" {{ old('kategori') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori') <div class="ag-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="ag-field">
                        <label for="urutan">Urutan Tampil</label>
                        <input type="number" id="urutan" name="urutan" value="{{ old('urutan', 0) }}"
                               min="0" max="999" placeholder="0">
                        <div class="ag-field-hint">Angka lebih kecil tampil lebih dulu</div>
                    </div>
                </div>

                <div class="ag-field-row">
                    <div class="ag-field">
                        <label for="hari">Hari / Jadwal <span class="req">*</span></label>
                        <input type="text" id="hari" name="hari" value="{{ old('hari') }}"
                               placeholder="contoh: Setiap Ahad" required>
                        @error('hari') <div class="ag-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="ag-field">
                        <label for="waktu">Waktu <span class="req">*</span></label>
                        <input type="text" id="waktu" name="waktu" value="{{ old('waktu') }}"
                               placeholder="contoh: 05.15 - 06.30 WIB" required>
                        @error('waktu') <div class="ag-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="ag-field">
                    <label for="lokasi">Lokasi <span class="req">*</span></label>
                    <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}"
                           placeholder="contoh: Aula Masjid Pusdai" required>
                    @error('lokasi') <div class="ag-error">{{ $message }}</div> @enderror
                </div>

                <div class="ag-field">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi"
                              placeholder="Keterangan singkat tentang kegiatan ini...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <div class="ag-error">{{ $message }}</div> @enderror
                </div>

                <div class="ag-field">
                    <label>Status Tampil</label>
                    <div class="ag-toggle-wrap">
                        <input type="checkbox" id="is_aktif" name="is_aktif" value="1"
                               {{ old('is_aktif', true) ? 'checked' : '' }}>
                        <label for="is_aktif" class="ag-toggle-label">
                            Aktifkan — agenda ini akan ditampilkan di dashboard jamaah
                        </label>
                    </div>
                </div>

                <div class="ag-form-footer">
                    <a href="{{ route('admin.agenda-kegiatan.index') }}" class="ag-btn-cancel">Batal</a>
                    <button type="submit" class="ag-btn-save">
                        <i class="fa-solid fa-floppy-disk" style="margin-right:6px;"></i>
                        Simpan Agenda
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

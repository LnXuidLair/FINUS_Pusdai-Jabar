@extends('layouts.app')
@section('title', 'Input / Lengkapi Presensi')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-calendar-plus"></i></span>
            <div>
                <h1>Input / Lengkapi Presensi</h1>
                <p>Koreksi presensi Pegawai yang terlupa dengan tetap menyertakan bukti dan alasan.</p>
            </div>
        </div>
        <div class="fmu-hero-actions">
            <span class="fmu-hero-badge"><i class="fa-solid fa-shield-halved"></i>Koreksi Admin</span>
        </div>
    </section>

    @if($errors->has('presensi'))
        <div class="alert alert-danger">{{ $errors->first('presensi') }}</div>
    @endif

    <div class="alert alert-info" style="border-radius:14px;font-size:13px">
        <i class="fa-solid fa-circle-info"></i>
        Admin dapat mengoreksi tanggal hari ini atau sebelumnya selama periode gaji belum dibayar.
        Jam tetap divalidasi: pulang normal <strong>{{ $schedule['pulang']['start'] }}–{{ $schedule['pulang']['end'] }}</strong>,
        pulang lebih awal sebelum <strong>{{ $schedule['pulang']['start'] }}</strong>, dan lembur
        <strong>{{ $schedule['lembur']['start'] }}–{{ $schedule['lembur']['end'] }}</strong>.
    </div>

    <form method="POST" action="{{ route('admin.presensi.store') }}" enctype="multipart/form-data" class="fmu-card" id="adminAttendanceForm">
        @csrf
        <div class="fmu-card-head">
            <div class="fmu-card-head-main">
                <span class="fmu-card-icon"><i class="fa-solid fa-calendar-plus"></i></span>
                <div><h2>Informasi Presensi</h2><p>Pilih Pegawai, tanggal, dan bagian presensi yang akan diinput atau dilengkapi.</p></div>
            </div>
        </div>

        <div class="fmu-card-body">
            <div class="fmu-form-grid">
                <div class="fmu-field fmu-field-full">
                    <label class="fmu-label" for="id_pegawai">Pegawai <span class="fmu-required">*</span></label>
                    <select id="id_pegawai" name="id_pegawai" class="fmu-select @error('id_pegawai') is-invalid @enderror" required>
                        <option value="">Pilih pegawai</option>
                        @foreach($pegawais as $pegawai)
                            <option value="{{ $pegawai->id }}" @selected((string) old('id_pegawai') === (string) $pegawai->id)>
                                {{ $pegawai->nama_pegawai }} — {{ $pegawai->jabatan ?? 'Pegawai' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_pegawai')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field">
                    <label class="fmu-label" for="tanggal">Tanggal <span class="fmu-required">*</span></label>
                    <div class="fmu-input-icon-wrap">
                        <i class="fa-solid fa-calendar-day"></i>
                        <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" class="fmu-control @error('tanggal') is-invalid @enderror" required>
                    </div>
                    @error('tanggal')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field">
                    <label class="fmu-label" for="aksi_admin">Bagian yang Diinput <span class="fmu-required">*</span></label>
                    <select id="aksi_admin" name="aksi_admin" class="fmu-select @error('aksi_admin') is-invalid @enderror" required>
                        <option value="datang" @selected(old('aksi_admin') === 'datang')>Hanya Presensi Datang</option>
                        <option value="pulang" @selected(old('aksi_admin') === 'pulang')>Hanya Presensi Pulang / Lengkapi Pulang</option>
                        <option value="lengkap" @selected(old('aksi_admin','lengkap') === 'lengkap')>Datang + Pulang Sekaligus</option>
                        <option value="izin" @selected(old('aksi_admin') === 'izin')>Izin Satu Hari</option>
                        <option value="sakit" @selected(old('aksi_admin') === 'sakit')>Sakit Satu Hari</option>
                    </select>
                    @error('aksi_admin')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field" data-field="jam_datang">
                    <label class="fmu-label" for="jam_datang">Jam Datang <span class="fmu-required">*</span></label>
                    <div class="fmu-input-icon-wrap">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <input type="time" id="jam_datang" name="jam_datang" value="{{ old('jam_datang') }}" class="fmu-control @error('jam_datang') is-invalid @enderror">
                    </div>
                    @error('jam_datang')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field" data-field="jam_pulang">
                    <label class="fmu-label" for="jam_pulang">Jam Pulang <span class="fmu-required">*</span></label>
                    <div class="fmu-input-icon-wrap">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <input type="time" id="jam_pulang" name="jam_pulang" value="{{ old('jam_pulang') }}" class="fmu-control @error('jam_pulang') is-invalid @enderror">
                    </div>
                    @error('jam_pulang')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field fmu-field-full" data-field="kondisi">
                    <label class="fmu-label" for="kondisi">Kondisi Pulang <span class="fmu-required">*</span></label>
                    <select id="kondisi" name="kondisi" class="fmu-select @error('kondisi') is-invalid @enderror">
                        <option value="normal" @selected(old('kondisi','normal') === 'normal')>Normal</option>
                        <option value="pulang_awal" @selected(old('kondisi') === 'pulang_awal')>Pulang Lebih Awal</option>
                        <option value="tugas_dinas" @selected(old('kondisi') === 'tugas_dinas')>Tugas / Panggilan Dinas</option>
                        <option value="lembur" @selected(old('kondisi') === 'lembur')>Lembur</option>
                    </select>
                    <span class="fmu-help">Pilih kondisi yang sesuai dengan jam pulang Pegawai.</span>
                    @error('kondisi')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field" data-field="bukti_datang">
                    <label class="fmu-label" for="bukti_datang">Bukti Datang <span class="fmu-required">*</span></label>
                    <label class="fmu-upload" data-upload-zone for="bukti_datang">
                        <span><i class="fa-solid fa-cloud-arrow-up"></i><strong>Klik atau seret bukti datang</strong><span>JPG, JPEG, PNG. Maks. 1 MB.</span><span class="fmu-file-name" data-file-name></span></span>
                    </label>
                    <input type="file" name="bukti_datang" id="bukti_datang" class="d-none" accept="image/jpeg,image/png">
                    @error('bukti_datang')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field" data-field="bukti_pulang">
                    <label class="fmu-label" for="bukti_pulang">Bukti Pulang <span class="fmu-required">*</span></label>
                    <label class="fmu-upload" data-upload-zone for="bukti_pulang">
                        <span><i class="fa-solid fa-cloud-arrow-up"></i><strong>Klik atau seret bukti pulang</strong><span>JPG, JPEG, PNG. Maks. 1 MB.</span><span class="fmu-file-name" data-file-name></span></span>
                    </label>
                    <input type="file" name="bukti_pulang" id="bukti_pulang" class="d-none" accept="image/jpeg,image/png">
                    @error('bukti_pulang')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field fmu-field-full" data-field="bukti_status">
                    <label class="fmu-label" for="bukti_status">Bukti Izin / Sakit <span class="fmu-required">*</span></label>
                    <label class="fmu-upload" data-upload-zone for="bukti_status">
                        <span><i class="fa-solid fa-cloud-arrow-up"></i><strong>Klik atau seret bukti izin/sakit</strong><span>JPG, JPEG, PNG. Maks. 1 MB.</span><span class="fmu-file-name" data-file-name></span></span>
                    </label>
                    <input type="file" name="bukti_status" id="bukti_status" class="d-none" accept="image/jpeg,image/png">
                    @error('bukti_status')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field fmu-field-full">
                    <label class="fmu-label" for="keterangan">Alasan / Keterangan Input Admin <span class="fmu-required">*</span></label>
                    <textarea id="keterangan" name="keterangan" class="fmu-textarea @error('keterangan') is-invalid @enderror" placeholder="Contoh: Pegawai lupa presensi pulang dan menyerahkan bukti kepada Admin." required>{{ old('keterangan') }}</textarea>
                    <span class="fmu-help">Keterangan wajib sebagai jejak audit kenapa Admin menginput atau melengkapi presensi.</span>
                    @error('keterangan')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="fmu-actions">
            <a href="{{ route('admin.presensi.index') }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a>
            <button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Presensi</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const action = document.getElementById('aksi_admin');
    const fields = {
        jam_datang: document.querySelector('[data-field="jam_datang"]'),
        jam_pulang: document.querySelector('[data-field="jam_pulang"]'),
        kondisi: document.querySelector('[data-field="kondisi"]'),
        bukti_datang: document.querySelector('[data-field="bukti_datang"]'),
        bukti_pulang: document.querySelector('[data-field="bukti_pulang"]'),
        bukti_status: document.querySelector('[data-field="bukti_status"]'),
    };
    const inputs = Object.fromEntries(Object.keys(fields).map(key => [key, document.getElementById(key)]));

    function show(keys) {
        Object.entries(fields).forEach(([key, el]) => {
            const visible = keys.includes(key);
            if (el) el.style.display = visible ? '' : 'none';
            if (inputs[key]) inputs[key].required = visible;
        });
    }

    function sync() {
        switch (action?.value) {
            case 'datang': show(['jam_datang','bukti_datang']); break;
            case 'pulang': show(['jam_pulang','kondisi','bukti_pulang']); break;
            case 'lengkap': show(['jam_datang','jam_pulang','kondisi','bukti_datang','bukti_pulang']); break;
            case 'izin':
            case 'sakit': show(['bukti_status']); break;
            default: show([]);
        }
    }

    action?.addEventListener('change', sync);
    sync();

    document.querySelectorAll('[data-upload-zone]').forEach(zone => {
        const input = document.getElementById(zone.getAttribute('for'));
        const fileName = zone.querySelector('[data-file-name]');
        const showFile = file => {
            if (!fileName) return;
            fileName.textContent = file ? file.name : '';
            fileName.classList.toggle('is-visible', Boolean(file));
        };
        input?.addEventListener('change', () => showFile(input.files?.[0]));
        ['dragenter','dragover'].forEach(name => zone.addEventListener(name, event => {
            event.preventDefault();
            zone.classList.add('is-dragging');
        }));
        ['dragleave','drop'].forEach(name => zone.addEventListener(name, event => {
            event.preventDefault();
            zone.classList.remove('is-dragging');
        }));
        zone.addEventListener('drop', event => {
            const file = event.dataTransfer?.files?.[0];
            if (!file || !input) return;
            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            showFile(file);
        });
    });
})();
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: presensi/create.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="presensi/create.blade.php">
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-body,.fr-card-body) { background:transparent !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-icon,.fr-card-icon,.fmu-stat-icon,.fr-stat-icon) { box-shadow:inset 0 1px 0 rgba(255,255,255,.025) !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-hero-badge,.fr-hero-badge) { border-color:rgba(255,255,255,.18) !important; background:rgba(4,35,15,.36) !important; color:#F5FFF7 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-side-note,.fr-tip-item,.fr-breakdown-item) { border-color:#293D31 !important; background:#101B14 !important; }
</style>
@endpush
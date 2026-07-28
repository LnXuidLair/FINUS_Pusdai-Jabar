@extends('layouts.app')
@section('title', 'Isi Presensi')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid fa-calendar-check"></i></span>
            <div><h1>Isi Presensi Harian</h1><p>Pilih status kehadiran, tambahkan keterangan, dan unggah bukti presensi.</p></div>
        </div>
        <div class="fmu-hero-actions"><span class="fmu-hero-badge"><i class="fa-solid fa-calendar-day"></i>{{ now()->translatedFormat('d F Y') }}</span></div>
    </section>

    <form method="POST" action="{{ route('pegawai.presensi.store') }}" enctype="multipart/form-data" class="fmu-card" id="staffAttendanceForm">
        @csrf
        <div class="fmu-card-head">
            <div class="fmu-card-head-main">
                <span class="fmu-card-icon"><i class="fa-solid fa-clipboard-check"></i></span>
                <div><h2>Form Presensi</h2><p>Pastikan status dan bukti yang dikirim sudah benar.</p></div>
            </div>
        </div>
        <div class="fmu-card-body">
            <div class="fmu-form-grid">
                <div class="fmu-field fmu-field-full">
                    <label class="fmu-label">Status Kehadiran <span class="fmu-required">*</span></label>
                    <select name="status" id="status_select" class="d-none" required>
                        <option value="hadir" @selected(old('status','hadir') === 'hadir')>Hadir</option>
                        <option value="izin" @selected(old('status') === 'izin')>Izin</option>
                        <option value="lembur" @selected(old('status') === 'lembur')>Lembur</option>
                    </select>
                    <div class="fmu-choice-grid" id="statusChoiceGrid">
                        <button type="button" class="fmu-choice" data-status="hadir"><span><i class="fa-solid fa-user-check"></i><strong>Hadir</strong></span></button>
                        <button type="button" class="fmu-choice" data-status="izin"><span><i class="fa-solid fa-envelope-open-text"></i><strong>Izin</strong></span></button>
                        <button type="button" class="fmu-choice" data-status="lembur"><span><i class="fa-solid fa-moon"></i><strong>Lembur</strong></span></button>
                    </div>
                    @error('status')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field">
                    <label class="fmu-label" for="keterangan">Keterangan tambahan</label>
                    <textarea name="keterangan" id="keterangan" class="fmu-textarea @error('keterangan') is-invalid @enderror" placeholder="Contoh: hadir tepat waktu, rapat divisi, atau izin sakit.">{{ old('keterangan') }}</textarea>
                    <span class="fmu-help">Berikan penjelasan singkat terutama untuk status izin atau lembur.</span>
                    @error('keterangan')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field">
                    <label class="fmu-label" for="bukti_kehadiran">Bukti kehadiran <span class="fmu-required">*</span></label>
                    <label class="fmu-upload" id="attendanceDropzone" for="bukti_kehadiran">
                        <span>
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <strong>Klik atau seret foto bukti ke sini</strong>
                            <span>JPG, JPEG, atau PNG. Maksimal mengikuti batas validasi sistem.</span>
                            <span class="fmu-file-name" id="attendanceFileName"></span>
                        </span>
                    </label>
                    <input type="file" name="bukti_kehadiran" id="bukti_kehadiran" class="d-none" accept="image/jpeg,image/png" required>
                    @error('bukti_kehadiran')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
        <div class="fmu-actions">
            <a href="{{ route('pegawai.presensi.index') }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a>
            <button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-paper-plane"></i>Kirim Presensi</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const select = document.getElementById('status_select');
    const choices = document.querySelectorAll('[data-status]');
    const syncChoices = () => choices.forEach(choice => choice.classList.toggle('is-active', choice.dataset.status === select.value));
    choices.forEach(choice => choice.addEventListener('click', () => { select.value = choice.dataset.status; syncChoices(); }));
    syncChoices();

    const input = document.getElementById('bukti_kehadiran');
    const dropzone = document.getElementById('attendanceDropzone');
    const fileName = document.getElementById('attendanceFileName');
    const showFile = file => {
        fileName.textContent = file ? file.name : '';
        fileName.classList.toggle('is-visible', Boolean(file));
    };
    input?.addEventListener('change', () => showFile(input.files?.[0]));
    ['dragenter','dragover'].forEach(name => dropzone?.addEventListener(name, event => { event.preventDefault(); dropzone.classList.add('is-dragging'); }));
    ['dragleave','drop'].forEach(name => dropzone?.addEventListener(name, event => { event.preventDefault(); dropzone.classList.remove('is-dragging'); }));
    dropzone?.addEventListener('drop', event => {
        const file = event.dataTransfer?.files?.[0];
        if (!file || !input) return;
        const transfer = new DataTransfer(); transfer.items.add(file); input.files = transfer.files; showFile(file);
    });
})();
</script>
@endpush
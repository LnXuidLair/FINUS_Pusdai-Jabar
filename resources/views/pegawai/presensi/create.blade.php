@extends('layouts.app')
@section('title', $action['label'])
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')

@php
    $needsReason = in_array($aksi, ['pulang_awal','tugas_dinas','lembur','izin','sakit'], true);
@endphp

<div class="fmu-page">
    <section class="fmu-hero">
        <div class="fmu-hero-main">
            <span class="fmu-hero-icon"><i class="fa-solid {{ $action['icon'] }}"></i></span>
            <div><h1>{{ $action['label'] }}</h1><p>{{ $action['description'] }}</p></div>
        </div>
        <div class="fmu-hero-actions"><span class="fmu-hero-badge"><i class="fa-solid fa-calendar-day"></i>{{ now()->translatedFormat('d F Y') }}</span></div>
    </section>

    @if($errors->has('presensi'))<div class="alert alert-danger">{{ $errors->first('presensi') }}</div>@endif

    <div class="alert alert-info" style="border-radius:14px;font-size:13px">
        <i class="fa-solid fa-circle-info"></i>
        @if($aksi === 'datang')
            Presensi datang hanya dapat dilakukan pukul <strong>{{ $schedule['datang']['start'] }}–{{ $schedule['datang']['end'] }}</strong>.
        @elseif($aksi === 'pulang')
            Presensi pulang normal dibuka pukul <strong>{{ $schedule['pulang']['start'] }}–{{ $schedule['pulang']['end'] }}</strong>.
        @elseif($aksi === 'pulang_awal')
            Gunakan ini jika Anda harus pulang sebelum <strong>{{ $schedule['pulang']['start'] }}</strong>. Alasan dan bukti wajib.
        @elseif($aksi === 'tugas_dinas')
            Gunakan untuk tugas/panggilan resmi yang mengharuskan Anda meninggalkan lokasi kerja. Alasan dan bukti wajib.
        @elseif($aksi === 'lembur')
            Pulang lembur tersedia pukul <strong>{{ $schedule['lembur']['start'] }}–{{ $schedule['lembur']['end'] }}</strong>.
        @else
            Izin/sakit satu hari dapat diajukan selama jam kerja sebelum Anda memiliki presensi datang pada hari yang sama.
        @endif
    </div>

    <form method="POST" action="{{ route('pegawai.presensi.store') }}" enctype="multipart/form-data" class="fmu-card" id="staffAttendanceForm">
        @csrf
        <input type="hidden" name="aksi" value="{{ $aksi }}">

        <div class="fmu-card-head">
            <div class="fmu-card-head-main">
                <span class="fmu-card-icon"><i class="fa-solid fa-clipboard-check"></i></span>
                <div><h2>Form {{ $action['label'] }}</h2><p>Pastikan bukti dan keterangan yang dikirim sudah benar.</p></div>
            </div>
        </div>

        <div class="fmu-card-body">
            @if($todayPresensi && $todayPresensi->status === 'hadir')
                <div class="alert alert-light" style="border:1px solid #dfe8e2;border-radius:12px">
                    <strong>Datang:</strong> {{ $todayPresensi->jam_datang ? substr($todayPresensi->jam_datang,0,5) : '-' }}
                    &nbsp; • &nbsp;
                    <strong>Pulang:</strong> {{ $todayPresensi->jam_pulang ? substr($todayPresensi->jam_pulang,0,5) : 'Belum tercatat' }}
                </div>
            @endif

            <div class="fmu-form-grid">
                <div class="fmu-field">
                    <label class="fmu-label" for="keterangan">
                        Keterangan @if($needsReason)<span class="fmu-required">*</span>@endif
                    </label>
                    <textarea name="keterangan" id="keterangan" class="fmu-textarea @error('keterangan') is-invalid @enderror"
                        @if($needsReason) required @endif
                        placeholder="{{ $needsReason ? 'Jelaskan alasan secara singkat dan jelas.' : 'Opsional, misalnya kegiatan yang sedang dikerjakan.' }}">{{ old('keterangan') }}</textarea>
                    <span class="fmu-help">
                        @if($needsReason)
                            Keterangan wajib untuk izin, sakit, pulang lebih awal, tugas dinas, atau lembur.
                        @else
                            Keterangan tambahan bersifat opsional.
                        @endif
                    </span>
                    @error('keterangan')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>

                <div class="fmu-field">
                    <label class="fmu-label" for="bukti">Bukti <span class="fmu-required">*</span></label>
                    <label class="fmu-upload" id="attendanceDropzone" for="bukti">
                        <span>
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <strong>Klik atau seret foto bukti ke sini</strong>
                            <span>JPG, JPEG, atau PNG. Maksimal 1 MB.</span>
                            <span class="fmu-file-name" id="attendanceFileName"></span>
                        </span>
                    </label>
                    <input type="file" name="bukti" id="bukti" class="d-none" accept="image/jpeg,image/png" required>
                    @error('bukti')<span class="fmu-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="fmu-actions">
            <a href="{{ route('pegawai.presensi.index') }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a>
            <button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-paper-plane"></i>Kirim {{ $action['label'] }}</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const input = document.getElementById('bukti');
    const dropzone = document.getElementById('attendanceDropzone');
    const fileName = document.getElementById('attendanceFileName');
    const showFile = file => {
        if (!fileName) return;
        fileName.textContent = file ? file.name : '';
        fileName.classList.toggle('is-visible', Boolean(file));
    };
    input?.addEventListener('change', () => showFile(input.files?.[0]));
    ['dragenter','dragover'].forEach(name => dropzone?.addEventListener(name, event => {
        event.preventDefault();
        dropzone.classList.add('is-dragging');
    }));
    ['dragleave','drop'].forEach(name => dropzone?.addEventListener(name, event => {
        event.preventDefault();
        dropzone.classList.remove('is-dragging');
    }));
    dropzone?.addEventListener('drop', event => {
        const file = event.dataTransfer?.files?.[0];
        if (!file || !input) return;
        const transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
        showFile(file);
    });
})();
</script>
@endpush

{{-- FINUS DARK MODE LOCAL: pegawai/presensi/create.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="pegawai/presensi/create.blade.php">
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-body,.fr-card-body) { background:transparent !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-card-icon,.fr-card-icon,.fmu-stat-icon,.fr-stat-icon) { box-shadow:inset 0 1px 0 rgba(255,255,255,.025) !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-hero-badge,.fr-hero-badge) { border-color:rgba(255,255,255,.18) !important; background:rgba(4,35,15,.36) !important; color:#F5FFF7 !important; }
html[data-finus-theme="dark"] body :where(.fmu-page,.fr-page) :where(.fmu-side-note,.fr-tip-item,.fr-breakdown-item) { border-color:#293D31 !important; background:#101B14 !important; }
</style>
@endpush
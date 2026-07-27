@extends('layouts.app')
@section('title', 'Isi Presensi')
@section('content')
<style>
    .presensi-form-card {
        width: 100%;
        border: 0;
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .presensi-form-header {
        background: linear-gradient(135deg, #065f46, #10b981);
        color: white;
        padding: 24px 30px;
        position: relative;
    }

    .presensi-form-header h3 {
        margin: 0;
        font-weight: 800;
        font-size: 20px;
        color: white;
    }

    .presensi-form-header p {
        margin: 6px 0 0;
        color: rgba(255, 255, 255, 0.85);
        font-size: 12.5px;
    }

    .presensi-form-body {
        padding: 30px;
    }

    .presensi-group {
        margin-bottom: 24px;
    }

    .presensi-group label {
        display: block;
        margin-bottom: 10px;
        color: #0f172a;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    /* Option Cards for Attendance Status */
    .status-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .status-option-card {
        border: 2px solid #f1f5f9;
        border-radius: 16px;
        padding: 20px 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .status-option-card:hover {
        border-color: #10b981;
        background: #f0fdf4;
        transform: translateY(-2px);
    }

    .status-option-card.active {
        border-color: #10b981;
        background: #e6fcf0;
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.08);
    }

    .status-option-card i {
        font-size: 24px;
        color: #64748b;
        transition: color 0.2s ease;
    }

    .status-option-card.active i {
        color: #10b981;
    }

    .status-option-card span {
        font-size: 14px;
        font-weight: 700;
        color: #334155;
    }

    .status-option-card.active span {
        color: #065f46;
    }

    /* Textarea & Inputs styling */
    .presensi-control {
        width: 100%;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px 16px;
        outline: none;
        transition: all 0.2s ease;
        font-size: 13.5px;
        background: #f8fafc;
    }

    .presensi-control:focus {
        border-color: #10b981;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.08);
    }

    /* Custom File Dropzone styling */
    .custom-dropzone {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 34px 24px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 180px;
    }

    .custom-dropzone:hover {
        border-color: #10b981;
        background: #f0fdf4;
    }

    .custom-dropzone i {
        font-size: 34px;
        color: #94a3b8;
        margin-bottom: 12px;
        transition: color 0.2s ease;
    }

    .custom-dropzone:hover i {
        color: #10b981;
    }

    .custom-dropzone p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }

    .file-badge {
        display: none;
        margin-top: 12px;
        background: #e6fcf0;
        color: #047857;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-presensi-submit {
        width: 100%;
        border: 0;
        border-radius: 14px;
        padding: 14px;
        background: linear-gradient(135deg, #065f46, #10b981);
        color: white;
        font-weight: 800;
        font-size: 15px;
        letter-spacing: 0.03em;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.15);
        transition: all 0.2s ease;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-presensi-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.22);
    }
</style>

<div class="container-fluid py-2">
    @if($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius: 14px;">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card presensi-form-card">
        <header class="presensi-form-header">
            <h3>Isi Presensi Harian</h3>
            <p>Unggah bukti dan isi keterangan absensi dengan mudah dan praktis.</p>
        </header>

        <form method="POST" action="{{ route('pegawai.presensi.store') }}" enctype="multipart/form-data" class="presensi-form-body">
            @csrf

            <div class="row">
                <!-- Left Column (Status & Notes) -->
                <div class="col-lg-6 border-right pr-lg-4">
                    <!-- Status Choice Grid -->
                    <div class="presensi-group">
                        <label>Status Kehadiran</label>
                        <select name="status" id="status_select" class="d-none">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="lembur">Lembur</option>
                        </select>

                        <div class="status-grid">
                            <div class="status-option-card active" data-value="hadir">
                                <i class="fa fa-user-check"></i>
                                <span>Hadir</span>
                            </div>
                            <div class="status-option-card" data-value="izin">
                                <i class="fa fa-envelope-open-text"></i>
                                <span>Izin</span>
                            </div>
                            <div class="status-option-card" data-value="lembur">
                                <i class="fa fa-moon"></i>
                                <span>Lembur</span>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan Textarea -->
                    <div class="presensi-group">
                        <label for="keterangan">Keterangan Tambahan</label>
                        <textarea 
                            name="keterangan" 
                            id="keterangan" 
                            rows="4" 
                            class="presensi-control" 
                            placeholder="Contoh: Hadir tepat waktu, melakukan rapat divisi, atau izin sakit..."
                        ></textarea>
                    </div>
                </div>

                <!-- Right Column (Dropzone & Submit) -->
                <div class="col-lg-6 pl-lg-4 mt-4 mt-lg-0 d-flex flex-column justify-content-between">
                    <!-- Drag and Drop Proof -->
                    <div class="presensi-group">
                        <label>Bukti Kehadiran (Foto/Dokumen)</label>
                        <div class="custom-dropzone" onclick="document.getElementById('bukti_kehadiran').click()">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <p>Klik atau Seret file bukti ke sini</p>
                            <small class="text-muted">Format: JPG, JPEG, PNG (Maks 1MB)</small>
                            <div class="file-badge" id="file_badge_info">
                                <i class="fa fa-check-circle"></i> <span id="file_name_text">bukti.png</span>
                            </div>
                        </div>
                        <input 
                            type="file" 
                            name="bukti_kehadiran" 
                            id="bukti_kehadiran" 
                            class="d-none" 
                            accept="image/*"
                            required
                        >
                    </div>

                    <div class="mt-auto">
                        <button type="submit" class="btn-presensi-submit">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Presensi Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Status Selection Cards
    const cards = document.querySelectorAll('.status-option-card');
    const select = document.getElementById('status_select');

    cards.forEach(card => {
        card.addEventListener('click', function () {
            cards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            if (select) {
                select.value = this.getAttribute('data-value');
            }
        });
    });

    // File Upload Badge Update
    const fileInput = document.getElementById('bukti_kehadiran');
    const fileBadge = document.getElementById('file_badge_info');
    const fileNameText = document.getElementById('file_name_text');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                fileNameText.textContent = this.files[0].name;
                fileBadge.style.display = 'inline-flex';
            } else {
                fileBadge.style.display = 'none';
            }
        });
    }
});
</script>
@endsection

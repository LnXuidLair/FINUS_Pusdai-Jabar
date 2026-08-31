@extends('layouts.app')
@section('title', 'Tambah Pegawai')
@section('hide-page-header', '1')
@section('content')
@include('layouts.partials.finus-ui')
<div class="fmu-page">
    <section class="fmu-hero">
    <div class="fmu-hero-main">
        <span class="fmu-hero-icon"><i class="fa-solid fa-user-plus"></i></span>
        <div>
            <h1>Tambah Pegawai</h1>
            <p>Lengkapi identitas pegawai untuk menambahkan data baru.</p>
        </div>
    </div>
    <div class="fmu-hero-actions"><span class="fmu-hero-badge"><i class="fa-solid fa-circle-info"></i>Data Master Pegawai</span></div>
</section>
    <div class="fmu-grid" style="grid-template-columns:minmax(0,1.5fr) minmax(260px,.55fr);align-items:start">
        <form method="POST" action="{{ route('admin.pegawai.store') }}" class="fmu-card">
            @csrf
            <div class="fmu-card-head"><div class="fmu-card-head-main"><span class="fmu-card-icon"><i class="fa-solid fa-user-plus"></i></span><div><h2>Informasi Pegawai</h2><p>Kolom bertanda bintang wajib diisi.</p></div></div></div>
            <div class="fmu-card-body">
                <div class="fmu-form-grid">
                    <div class="fmu-field">
                        <label class="fmu-label" for="nip">NIP <span class="fmu-required">*</span></label>
                        <div class="fmu-input-icon-wrap"><i class="fa-solid fa-id-card"></i><input type="text" id="nip" name="nip" value="{{ old('nip') }}" class="fmu-control @error('nip') is-invalid @enderror" placeholder="Masukkan NIP" required autocomplete="off"></div>
                        @error('nip')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="fmu-field">
                        <label class="fmu-label" for="nama_pegawai">Nama Pegawai <span class="fmu-required">*</span></label>
                        <div class="fmu-input-icon-wrap"><i class="fa-solid fa-user"></i><input type="text" id="nama_pegawai" name="nama_pegawai" value="{{ old('nama_pegawai') }}" class="fmu-control @error('nama_pegawai') is-invalid @enderror" placeholder="Nama lengkap pegawai" required></div>
                        @error('nama_pegawai')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="fmu-field">
                        <label class="fmu-label" for="jabatan">Jabatan <span class="fmu-required">*</span></label>
                        <select id="jabatan" name="jabatan" class="fmu-select @error('jabatan') is-invalid @enderror" required>
                            <option value="">Pilih jabatan</option>
                            @foreach($jabatanOptions as $jabatan)
                                <option value="{{ $jabatan }}" @selected(old('jabatan') === $jabatan)>{{ $jabatan }}</option>
                            @endforeach
                        </select>
                        @error('jabatan')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="fmu-field">
                        <label class="fmu-label" for="gender">Jenis Kelamin <span class="fmu-required">*</span></label>
                        <select id="gender" name="gender" class="fmu-select @error('gender') is-invalid @enderror" required>
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
                        </select>
                        @error('gender')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="fmu-field">
                        <label class="fmu-label" for="email_preview">Email Pegawai</label>
                        <div class="fmu-input-icon-wrap"><i class="fa-solid fa-envelope"></i><input type="email" id="email_preview" value="otomatis@staffpusdai.finus.id" class="fmu-control" readonly></div>
                        <span class="fmu-help">Dibuat otomatis dari dua kata pertama nama dan empat digit terakhir NIP.</span>
                        @error('email')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="fmu-field">
                        <label class="fmu-label" for="phone_display">Nomor Telepon</label>
                        <input type="tel" id="phone_display" class="fmu-control" placeholder="812 3456 7890">
                        <input type="hidden" name="no_telp" id="phone_value" value="{{ old('no_telp') }}">
                        <span class="fmu-help">Kosongkan apabila pegawai belum memiliki nomor aktif.</span>
                        @error('no_telp')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="fmu-field fmu-field-full">
                        <label class="fmu-label" for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" class="fmu-textarea @error('alamat') is-invalid @enderror" placeholder="Alamat lengkap pegawai">{{ old('alamat') }}</textarea>
                        @error('alamat')<span class="fmu-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="fmu-actions">
                <a href="{{ route('admin.pegawai.index') }}" class="fmu-btn"><i class="fa-solid fa-arrow-left"></i>Kembali</a>
                <button type="submit" class="fmu-btn fmu-btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Pegawai</button>
            </div>
        </form>
        <aside class="fmu-side-note">
            <h3><i class="fa-solid fa-shield-halved mr-2"></i>Panduan Data</h3>
            <p>Pastikan data cocok dengan identitas pegawai agar aktivasi akun dan laporan tidak keliru.</p>
            <ul><li>NIP harus unik.</li><li>Jabatan menentukan profil dashboard dan hak akses otomatis.</li><li>Jabatan DKM/Keuangan membuka menu khusus.</li><li>Email akun dibuat otomatis.</li><li>Nomor telepon disimpan dalam format internasional.</li></ul>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const nameInput = document.querySelector('input[name="nama_pegawai"]');
    const nipInput = document.querySelector('input[name="nip"]');
    const emailPreview = document.getElementById('email_preview');
    const domain = 'staffpusdai.finus.id';
    const makeEmailPreview = () => {
        if (!nameInput || !nipInput || !emailPreview) return;
        const names = nameInput.value.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim().split(/\s+/).filter(Boolean).slice(0,2).map(part => part.replace(/[^a-z0-9]/g,''));
        const local = names.join('') || 'pegawai';
        const suffix = nipInput.value.replace(/\D/g,'').slice(-4) || '0000';
        emailPreview.value = `${local}${suffix}@${domain}`;
    };
    nameInput?.addEventListener('input', makeEmailPreview);
    nipInput?.addEventListener('input', makeEmailPreview);
    makeEmailPreview();

    const phoneInput = document.getElementById('phone_display');
    const phoneValue = document.getElementById('phone_value');
    if (phoneInput && phoneValue && window.intlTelInput) {
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: 'id',
            separateDialCode: true,
            preferredCountries: ['id','my','sg'],
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js'
        });
        const initialNumber = phoneValue.value.trim();
        if (initialNumber) iti.setNumber(initialNumber);
        const syncPhone = () => { phoneValue.value = phoneInput.value.trim() ? iti.getNumber() || phoneInput.value.trim() : ''; };
        phoneInput.addEventListener('blur', syncPhone);
        phoneInput.closest('form')?.addEventListener('submit', syncPhone);
    }
})();
</script>
@endpush
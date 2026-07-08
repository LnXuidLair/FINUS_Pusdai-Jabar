@extends('layouts.app')

@section('content')
<style>
    .finus-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .page-hero {
        border-radius: 22px;
        background: linear-gradient(135deg, #065f46, #16a34a);
        color: white;
    }

    .info-box {
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        padding: 16px;
    }

    .formula-box {
        border-radius: 14px;
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        padding: 16px;
    }

    .small-muted {
        font-size: 13px;
        color: #64748b;
    }
</style>

@php
    $jenisOptions = $config['jenisOptions'];
    $metodeOptions = $config['metodeOptions'];
    $singleJenisKey = array_key_first($jenisOptions);
    $isZakatPage = ($jenis ?? null) === 'zakat';
@endphp

<div class="page-hero p-4 p-md-5 mb-4">
    <h3 class="text-white font-weight-bold mb-2">{{ $config['title'] }}</h3>
    <p class="mb-0 text-white-50">
        {{ $config['subtitle'] }}
    </p>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row">
    <div class="col-lg-7">
        <div class="card finus-card mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-1 font-weight-bold">Form {{ $config['title'] }}</h5>
                <small class="text-muted">Isi data transaksi dengan benar.</small>
            </div>

            <div class="card-body px-4">
                <form method="POST" action="{{ route('jamaah.transaksi.store', $jenis) }}">
                    @csrf

                    <div class="form-group">
                        <label>Jenis Transaksi</label>

                        @if(count($jenisOptions) === 1)
                            <input type="hidden" name="jenis_ziswaf" value="{{ $singleJenisKey }}">

                            <input type="text"
                                class="form-control"
                                value="{{ $jenisOptions[$singleJenisKey] }}"
                                disabled>
                        @else
                            <select name="jenis_ziswaf" id="jenis_ziswaf" class="form-control" required>
                                <option value="">Pilih jenis</option>

                                @foreach($jenisOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('jenis_ziswaf') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        @error('jenis_ziswaf')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    @if($isZakatPage)
                        <div id="kalkulator-zakat-maal" class="info-box mb-3" style="display: none;">
                            <h6 class="font-weight-bold mb-2">Kalkulator Zakat Maal</h6>

                            <p class="small-muted mb-3">
                                Zakat maal umumnya dihitung dari harta yang sudah mencapai nisab dan haul.
                                Perhitungan sederhananya adalah 2,5% dari total harta wajib zakat.
                            </p>

                            <div class="form-group mb-2">
                                <label>Total Harta Wajib Zakat</label>
                                <input type="number"
                                    id="harta_maal"
                                    class="form-control"
                                    placeholder="Contoh: 10000000">
                            </div>

                            <div class="formula-box">
                                <small class="d-block text-muted mb-1">Rumus</small>
                                <strong>Zakat Maal = Total Harta × 2,5%</strong>

                                <hr>

                                <small class="d-block text-muted mb-1">Hasil Perhitungan</small>
                                <h5 class="mb-0 font-weight-bold text-success" id="hasil_maal">
                                    Rp0
                                </h5>

                                <button type="button" id="pakai_hasil_maal" class="btn btn-sm btn-success mt-3">
                                    Pakai hasil ini sebagai nominal
                                </button>
                            </div>
                        </div>

                        <div id="kalkulator-zakat-fitrah" class="info-box mb-3" style="display: none;">
                            <h6 class="font-weight-bold mb-2">Kalkulator Zakat Fitrah</h6>

                            <p class="small-muted mb-3">
                                Zakat fitrah dibayarkan per jiwa. Jika dibayarkan dengan uang,
                                nominalnya mengikuti nilai makanan pokok atau ketentuan lembaga zakat setempat.
                            </p>

                            <div class="form-group mb-2">
                                <label>Jumlah Jiwa</label>
                                <input type="number"
                                    id="jumlah_jiwa"
                                    class="form-control"
                                    min="1"
                                    placeholder="Contoh: 4">
                            </div>

                            <div class="form-group mb-2">
                                <label>Nominal Zakat Fitrah per Jiwa</label>
                                <input type="number"
                                    id="nominal_per_jiwa"
                                    class="form-control"
                                    min="0"
                                    placeholder="Contoh: 45000">
                            </div>

                            <div class="formula-box">
                                <small class="d-block text-muted mb-1">Rumus</small>
                                <strong>Zakat Fitrah = Jumlah Jiwa × Nominal per Jiwa</strong>

                                <hr>

                                <small class="d-block text-muted mb-1">Hasil Perhitungan</small>
                                <h5 class="mb-0 font-weight-bold text-success" id="hasil_fitrah">
                                    Rp0
                                </h5>

                                <button type="button" id="pakai_hasil_fitrah" class="btn btn-sm btn-success mt-3">
                                    Pakai hasil ini sebagai nominal
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Nominal</label>

                        <input type="number"
                            name="nominal"
                            id="nominal"
                            class="form-control"
                            value="{{ old('nominal') }}"
                            min="1000"
                            placeholder="Contoh: 50000"
                            required>

                        @error('nominal')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Metode Pembayaran</label>

                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                            @foreach($metodeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('metode_pembayaran') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('metode_pembayaran')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div id="info-virtual-account" class="alert alert-info" style="display: none;">
                        <strong>Virtual Account</strong><br>
                        Setelah transaksi disimpan, sistem nantinya dapat menampilkan nomor virtual account
                        untuk pembayaran zakat. Untuk sementara, transaksi akan dicatat terlebih dahulu.
                    </div>

                    <div id="info-transfer-bank" class="alert alert-warning" style="display: none;">
                        <strong>Transfer Bank</strong><br>
                        Silakan transfer ke rekening masjid/lembaga, lalu simpan transaksi agar admin dapat melakukan verifikasi.
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>

                        <textarea name="keterangan"
                            class="form-control"
                            rows="3"
                            placeholder="Opsional">{{ old('keterangan') }}</textarea>

                        @error('keterangan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fa fa-paper-plane mr-1"></i>
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card finus-card mb-4">
            <div class="card-body p-4">
                <h5 class="font-weight-bold mb-3">Informasi Zakat</h5>

                @if($isZakatPage)
                    <div class="info-box mb-3">
                        <h6 class="font-weight-bold text-success mb-2">Zakat Maal</h6>

                        <p class="text-muted mb-2">
                            Zakat maal adalah zakat atas harta yang dimiliki seseorang apabila sudah memenuhi syarat,
                            seperti mencapai nisab dan dimiliki selama waktu tertentu.
                        </p>

                        <div class="formula-box">
                            <small class="d-block text-muted mb-1">Rumus sederhana</small>
                            <strong>Zakat Maal = Total Harta × 2,5%</strong>
                        </div>

                        <p class="small-muted mb-0 mt-2">
                            Contoh: jika harta wajib zakat Rp10.000.000, maka zakatnya:
                            Rp10.000.000 × 2,5% = Rp250.000.
                        </p>
                    </div>

                    <div class="info-box mb-3">
                        <h6 class="font-weight-bold text-success mb-2">Zakat Fitrah</h6>

                        <p class="text-muted mb-2">
                            Zakat fitrah adalah zakat yang dibayarkan pada bulan Ramadan menjelang Idulfitri.
                            Perhitungannya berdasarkan jumlah jiwa.
                        </p>

                        <div class="formula-box">
                            <small class="d-block text-muted mb-1">Rumus sederhana</small>
                            <strong>Zakat Fitrah = Jumlah Jiwa × Nominal per Jiwa</strong>
                        </div>

                        <p class="small-muted mb-0 mt-2">
                            Contoh: jika 4 jiwa dan nominal per jiwa Rp45.000, maka total zakat fitrah:
                            4 × Rp45.000 = Rp180.000.
                        </p>
                    </div>
                @else
                    <p class="text-muted">
                        Pilih menu transaksi zakat untuk melihat informasi zakat maal dan zakat fitrah.
                    </p>
                @endif

                <a href="{{ route('jamaah.dashboard') }}" class="btn btn-light btn-block">
                    <i class="fa fa-arrow-left mr-1"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const jenisZiswaf = document.getElementById('jenis_ziswaf');
    const nominal = document.getElementById('nominal');

    const kalkulatorMaal = document.getElementById('kalkulator-zakat-maal');
    const kalkulatorFitrah = document.getElementById('kalkulator-zakat-fitrah');

    const hartaMaal = document.getElementById('harta_maal');
    const hasilMaal = document.getElementById('hasil_maal');
    const pakaiHasilMaal = document.getElementById('pakai_hasil_maal');

    const jumlahJiwa = document.getElementById('jumlah_jiwa');
    const nominalPerJiwa = document.getElementById('nominal_per_jiwa');
    const hasilFitrah = document.getElementById('hasil_fitrah');
    const pakaiHasilFitrah = document.getElementById('pakai_hasil_fitrah');

    const metodePembayaran = document.getElementById('metode_pembayaran');
    const infoVirtualAccount = document.getElementById('info-virtual-account');
    const infoTransferBank = document.getElementById('info-transfer-bank');

    let nilaiMaal = 0;
    let nilaiFitrah = 0;

    function rupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(value || 0);
    }

    function toggleJenisZakat() {
        if (!jenisZiswaf) {
            return;
        }

        const jenis = jenisZiswaf.value;

        if (kalkulatorMaal) {
            kalkulatorMaal.style.display = jenis === 'zakat_maal' ? 'block' : 'none';
        }

        if (kalkulatorFitrah) {
            kalkulatorFitrah.style.display = jenis === 'zakat_fitrah' ? 'block' : 'none';
        }
    }

    function hitungMaal() {
        const harta = parseInt(hartaMaal?.value || 0);
        nilaiMaal = Math.floor(harta * 0.025);

        if (hasilMaal) {
            hasilMaal.textContent = rupiah(nilaiMaal);
        }
    }

    function hitungFitrah() {
        const jiwa = parseInt(jumlahJiwa?.value || 0);
        const perJiwa = parseInt(nominalPerJiwa?.value || 0);

        nilaiFitrah = jiwa * perJiwa;

        if (hasilFitrah) {
            hasilFitrah.textContent = rupiah(nilaiFitrah);
        }
    }

    function toggleMetodePembayaran() {
        if (!metodePembayaran) {
            return;
        }

        const metode = metodePembayaran.value;

        if (infoVirtualAccount) {
            infoVirtualAccount.style.display = metode === 'virtual_account' ? 'block' : 'none';
        }

        if (infoTransferBank) {
            infoTransferBank.style.display = metode === 'transfer_bank' ? 'block' : 'none';
        }
    }

    if (jenisZiswaf) {
        jenisZiswaf.addEventListener('change', toggleJenisZakat);
        toggleJenisZakat();
    }

    if (hartaMaal) {
        hartaMaal.addEventListener('input', hitungMaal);
    }

    if (pakaiHasilMaal) {
        pakaiHasilMaal.addEventListener('click', function () {
            if (nominal && nilaiMaal > 0) {
                nominal.value = nilaiMaal;
            }
        });
    }

    if (jumlahJiwa) {
        jumlahJiwa.addEventListener('input', hitungFitrah);
    }

    if (nominalPerJiwa) {
        nominalPerJiwa.addEventListener('input', hitungFitrah);
    }

    if (pakaiHasilFitrah) {
        pakaiHasilFitrah.addEventListener('click', function () {
            if (nominal && nilaiFitrah > 0) {
                nominal.value = nilaiFitrah;
            }
        });
    }

    if (metodePembayaran) {
        metodePembayaran.addEventListener('change', toggleMetodePembayaran);
        toggleMetodePembayaran();
    }
});
</script>
@endpush
@extends('layouts.app')

@section('title', 'Loket Parkir — Pusdai')
@section('hide-page-header', '1')

@php
    $rupiah = fn ($v) => 'Rp ' . number_format((float)$v, 0, ',', '.');
@endphp

@include('layouts.partials.finus-ui')

@section('content')
<div class="fr-page">

    {{-- Hero --}}
    <section class="fr-hero fr-reveal">
        <div class="fr-hero-main">
            <span class="fr-hero-icon" aria-hidden="true">
                <i class="fa-solid fa-motorcycle"></i>
            </span>
            <div>
                <h1 class="fr-hero-title">Loket Parkir</h1>
                <p class="fr-hero-subtitle">
                    Catat kendaraan masuk → konfirmasi bayar QRIS → dana otomatis masuk ke jurnal pemasukan PSAK 109.
                </p>
            </div>
        </div>
        <div class="fr-hero-actions">
            <a href="{{ route('admin.parkir.index') }}"
                style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:rgba(255,255,255,.15);color:white;border:1.5px solid rgba(255,255,255,.3);border-radius:10px;text-decoration:none;font-size:13px;font-weight:600;">
                <i class="fa-solid fa-chart-bar"></i> Dashboard Monitor
            </a>
        </div>
    </section>

    {{-- Alert --}}
    @if(session('success'))
        <div style="margin-bottom:20px;padding:14px 20px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:12px;color:#065f46;font-size:14px;font-weight:600;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-circle-check" style="font-size:18px;"></i>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="margin-bottom:20px;padding:14px 20px;background:#fef2f2;border:1px solid #fca5a5;border-radius:12px;color:#dc2626;font-size:13px;">
            @foreach($errors->all() as $e)
                <div><i class="fa-solid fa-triangle-exclamation"></i> {{ $e }}</div>
            @endforeach
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">

        {{-- FORM INPUT KENDARAAN MASUK --}}
        <section class="fr-card fr-reveal">
            <header class="fr-card-head">
                <div class="fr-card-title-row">
                    <span class="fr-card-icon" style="background:linear-gradient(135deg,#059669,#10b981);"><i class="fa-solid fa-plus"></i></span>
                    <div>
                        <h2 class="fr-card-title">Catat Kendaraan Masuk</h2>
                        <p class="fr-card-subtitle">Input saat kendaraan memasuki area parkir</p>
                    </div>
                </div>
            </header>

            <div style="padding:24px;">
                <form method="POST" action="{{ route('admin.parkir.store') }}" id="form-masuk">
                    @csrf

                    {{-- Jenis Kendaraan --}}
                    <div style="margin-bottom:20px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:10px;text-transform:uppercase;letter-spacing:.04em;">
                            Jenis Kendaraan <span style="color:#dc2626;">*</span>
                        </label>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;" id="jenis-selector">
                            @foreach($labelJenis as $key => $label)
                                <label style="cursor:pointer;">
                                    <input type="radio" name="jenis_kendaraan" value="{{ $key }}"
                                        {{ $key === 'motor' ? 'checked' : '' }}
                                        onchange="updateNominal(this.value)"
                                        style="display:none;" class="jenis-radio">
                                    <div class="jenis-card {{ $key === 'motor' ? 'jenis-card-active' : '' }}"
                                        style="border:2px solid {{ $key === 'motor' ? '#059669' : '#e2e8f0' }};border-radius:12px;padding:14px;text-align:center;background:{{ $key === 'motor' ? '#ecfdf5' : '#f8fafc' }};transition:all .2s;">
                                        <i class="fa-solid fa-{{ $key === 'motor' ? 'motorcycle' : ($key === 'mobil' ? 'car-side' : 'truck') }}"
                                            style="font-size:24px;color:{{ $key === 'motor' ? '#059669' : '#64748b' }};margin-bottom:6px;display:block;"></i>
                                        <span style="font-size:13px;font-weight:700;color:{{ $key === 'motor' ? '#065f46' : '#475569' }};">{{ $label }}</span>
                                        <span style="font-size:12px;color:{{ $key === 'motor' ? '#059669' : '#94a3b8' }};display:block;margin-top:2px;">{{ $rupiah($tarif[$key] ?? 3000) }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Plat Nomor --}}
                    <div style="margin-bottom:18px;">
                        <label for="plat_nomor" style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em;">
                            Plat Nomor <span style="font-size:11px;font-weight:400;color:#94a3b8;">(opsional)</span>
                        </label>
                        <input type="text" id="plat_nomor" name="plat_nomor"
                            value="{{ old('plat_nomor') }}"
                            placeholder="Cth: D 1234 ABC"
                            style="width:100%;height:46px;padding:0 14px;font-size:15px;font-weight:700;font-family:monospace;letter-spacing:2px;border:1.5px solid #cbd5e1;border-radius:10px;background:#fff;color:#0f172a;outline:none;box-sizing:border-box;text-transform:uppercase;">
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div style="margin-bottom:18px;">
                        <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:8px;text-transform:uppercase;letter-spacing:.04em;">
                            Metode Pembayaran <span style="color:#dc2626;">*</span>
                        </label>
                        <div style="display:flex;gap:10px;">
                            <label style="cursor:pointer;flex:1;">
                                <input type="radio" name="metode_pembayaran" value="qris" checked style="display:none;">
                                <div class="metode-card" style="border:2px solid #7c3aed;background:#faf5ff;border-radius:10px;padding:12px;text-align:center;">
                                    <i class="fa-solid fa-qrcode" style="font-size:20px;color:#7c3aed;display:block;margin-bottom:4px;"></i>
                                    <span style="font-size:13px;font-weight:700;color:#6d28d9;">QRIS</span>
                                </div>
                            </label>
                            <label style="cursor:pointer;flex:1;">
                                <input type="radio" name="metode_pembayaran" value="tunai" style="display:none;">
                                <div class="metode-card" style="border:2px solid #e2e8f0;background:#f8fafc;border-radius:10px;padding:12px;text-align:center;">
                                    <i class="fa-solid fa-money-bill" style="font-size:20px;color:#64748b;display:block;margin-bottom:4px;"></i>
                                    <span style="font-size:13px;font-weight:700;color:#475569;">Tunai</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Nominal Preview --}}
                    <div style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1px solid #a7f3d0;border-radius:12px;padding:16px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#065f46;text-transform:uppercase;letter-spacing:.04em;">Nominal Parkir</div>
                            <div id="nominal-preview" style="font-size:24px;font-weight:800;color:#059669;">Rp 3.000</div>
                        </div>
                        <i class="fa-solid fa-coins" style="font-size:32px;color:#a7f3d0;"></i>
                    </div>

                    <button type="submit"
                        style="width:100%;height:48px;display:flex;align-items:center;justify-content:center;gap:10px;font-size:15px;font-weight:700;background:linear-gradient(135deg,#0E5423,#1a7a35);color:white;border:none;border-radius:12px;cursor:pointer;box-shadow:0 4px 14px rgba(14,84,35,.35);">
                        <i class="fa-solid fa-circle-plus"></i> Catat Masuk Parkir
                    </button>
                </form>
            </div>
        </section>

        {{-- PANEL KENDARAAN AKTIF --}}
        <section class="fr-card fr-reveal">
            <header class="fr-card-head">
                <div class="fr-card-title-row">
                    <span class="fr-card-icon" style="background:linear-gradient(135deg,#d97706,#f59e0b);"><i class="fa-solid fa-car-side"></i></span>
                    <div>
                        <h2 class="fr-card-title">Kendaraan Aktif</h2>
                        <p class="fr-card-subtitle">Klik ✓ saat bayar QRIS dikonfirmasi — dana otomatis dijurnal</p>
                    </div>
                </div>
            </header>

            @if($sedangParkir->isEmpty())
                <div style="padding:40px;text-align:center;color:#64748b;">
                    <i class="fa-solid fa-square-parking" style="font-size:40px;color:#cbd5e1;display:block;margin-bottom:12px;"></i>
                    <p style="font-size:14px;font-weight:600;">Belum ada kendaraan yang sedang parkir.</p>
                    <p style="font-size:12px;color:#94a3b8;">Gunakan form di sebelah kiri untuk mencatat kendaraan masuk.</p>
                </div>
            @else
                <div style="padding:16px;display:flex;flex-direction:column;gap:10px;max-height:600px;overflow-y:auto;">
                    @foreach($sedangParkir as $sesi)
                        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                            <div style="flex:0 0 auto;width:44px;height:44px;background:white;border:1.5px solid #fde68a;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                <i class="fa-solid fa-{{ $sesi->jenis_kendaraan === 'mobil' ? 'car-side' : 'motorcycle' }}"
                                    style="font-size:20px;color:#d97706;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:700;font-size:14px;color:#0f172a;font-family:monospace;letter-spacing:1px;">
                                    {{ $sesi->plat_nomor ?: '— (Tanpa Plat)' }}
                                </div>
                                <div style="font-size:11px;color:#64748b;margin-top:2px;">
                                    Masuk {{ $sesi->waktu_masuk ? $sesi->waktu_masuk->format('H:i') : '-' }}
                                    · <strong style="color:#d97706;">{{ 'Rp ' . number_format($sesi->nominal, 0, ',', '.') }}</strong>
                                    · <span style="background:#faf5ff;color:#7c3aed;padding:1px 5px;border-radius:4px;font-size:10px;font-weight:700;">{{ strtoupper($sesi->metode_pembayaran) }}</span>
                                </div>
                            </div>
                            <div style="display:flex;gap:6px;flex-shrink:0;">
                                {{-- Tombol Selesai (Bayar Lunas) --}}
                                <form method="POST" action="{{ route('admin.parkir.selesai', $sesi) }}" style="margin:0;">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        title="Konfirmasi Pembayaran Lunas"
                                        style="height:38px;padding:0 14px;display:inline-flex;align-items:center;gap:6px;background:#059669;color:white;border:none;border-radius:8px;cursor:pointer;font-size:12px;font-weight:700;">
                                        <i class="fa-solid fa-check"></i> Lunas
                                    </button>
                                </form>
                                {{-- Tombol Batal --}}
                                <form method="POST" action="{{ route('admin.parkir.batal', $sesi) }}" style="margin:0;"
                                    onsubmit="return confirm('Batalkan sesi parkir {{ $sesi->plat_nomor ?: 'ini' }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Batalkan"
                                        style="height:38px;width:38px;display:inline-flex;align-items:center;justify-content:center;background:#f1f5f9;color:#94a3b8;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Info QRIS --}}
            <div style="padding:16px 20px;border-top:1px solid #e2e8f0;background:#fafbff;">
                <div style="display:flex;align-items:center;gap:10px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 14px;">
                    <i class="fa-solid fa-circle-info" style="color:#3b82f6;font-size:16px;flex-shrink:0;"></i>
                    <div style="font-size:12px;color:#1e40af;line-height:1.5;">
                        <strong>Alur QRIS:</strong> Pembayar scan QR statis loket → bayar → petugas klik <strong>Lunas</strong> →
                        dana <strong>otomatis masuk ke Pemasukan & Jurnal PSAK 109</strong> sebagai "Infaq Parkir & Kegiatan" (COA 4103).
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
const tarif = @json($tarif);
const rupiah = (v) => 'Rp ' + v.toLocaleString('id-ID');

function updateNominal(jenis) {
    document.getElementById('nominal-preview').textContent = rupiah(tarif[jenis] || 3000);

    // Update visual semua kartu jenis
    document.querySelectorAll('.jenis-radio').forEach(radio => {
        const card = radio.closest('label').querySelector('.jenis-card');
        const isActive = radio.value === jenis;
        card.style.border = '2px solid ' + (isActive ? '#059669' : '#e2e8f0');
        card.style.background = isActive ? '#ecfdf5' : '#f8fafc';
    });
}

// Metode pembayaran visual toggle
document.querySelectorAll('input[name="metode_pembayaran"]').forEach(radio => {
    radio.addEventListener('change', function () {
        document.querySelectorAll('.metode-card').forEach(c => {
            c.style.border = '2px solid #e2e8f0';
            c.style.background = '#f8fafc';
        });
        const activeCard = this.closest('label').querySelector('.metode-card');
        activeCard.style.border = '2px solid #7c3aed';
        activeCard.style.background = '#faf5ff';
    });
});

// Uppercase plat nomor realtime
document.getElementById('plat_nomor').addEventListener('input', function() {
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9 ]/g, '');
    this.setSelectionRange(pos, pos);
});
</script>
@endsection

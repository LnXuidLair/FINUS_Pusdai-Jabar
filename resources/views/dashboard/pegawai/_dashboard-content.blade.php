<style>
    .role-hero {
        border-radius: 8px;
        color: #fff;
        padding: 24px;
        background: linear-gradient(135deg, #065f22 0%, #16a34a 100%) !important;
        box-shadow: 0 18px 38px rgba(22, 163, 74, .20);
    }

    .role-hero * {
        color: #ffffff !important;
    }

    .role-hero small,
    .role-hero .text-muted {
        color: rgba(255, 255, 255, .85) !important;
    }

    .metric-card {
        border: 1px solid #dcfce7;
        border-radius: 8px;
        height: 100%;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .metric-card .card-body {
        background: #ffffff;
        border-radius: 8px;
    }

    .metric-card .text-muted {
        color: #166534 !important;
        font-weight: 600;
    }

    .metric-card h2,
    .metric-card h4 {
        color: #065f22 !important;
        font-weight: 800;
    }

    .metric-card small {
        color: #64748b !important;
    }

    [style*="--role-color"] {
        --role-color: #16a34a !important;
    }

    .focus-item {
        border-left: 4px solid var(--role-color);
        background: #ecfdf5;
        color: #14532d;
        padding: 12px 14px;
        border-radius: 0 8px 8px 0;
        font-weight: 600;
    }

    .card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
    }

    .card-body h4 {
        color: #065f22;
        font-weight: 700;
    }

    .btn-primary {
        background: linear-gradient(135deg, #047857, #16a34a) !important;
        border-color: #16a34a !important;
        color: #ffffff !important;
        font-weight: 700;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #065f46, #15803d) !important;
        border-color: #15803d !important;
        color: #ffffff !important;
    }

    .btn-outline-secondary {
        border-color: #16a34a !important;
        color: #16a34a !important;
        font-weight: 700;
    }

    .btn-outline-secondary:hover {
        background: #16a34a !important;
        border-color: #16a34a !important;
        color: #ffffff !important;
    }
</style>

<div class="role-hero mb-4" style="background: linear-gradient(135deg, #065f22 0%, #16a34a 100%);">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <div class="small text-uppercase" style="opacity:.8">
                Dashboard {{ $dashboardProfile['jabatan'] }}
            </div>
            <h2 class="mb-1">{{ $pegawai->nama_pegawai }}</h2>
            <p class="mb-0">{{ $dashboardProfile['subtitle'] }}</p>
        </div>

        <div class="mt-3 mt-md-0 text-md-right">
            <div>NIP {{ $pegawai->nip }}</div>
            <small>{{ $pegawai->email }}</small>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-4 mb-3">
        <div class="card metric-card">
            <div class="card-body">
                <div class="text-muted">Kehadiran Bulan Ini</div>
                <h2 class="mb-0">{{ $jumlahHadir }}</h2>
                <small>presensi hadir</small>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card metric-card">
            <div class="card-body">
                <div class="text-muted">Menunggu Persetujuan</div>
                <h2 class="mb-0">{{ $presensiMenunggu }}</h2>
                <small>pengajuan presensi</small>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card metric-card">
            <div class="card-body">
                <div class="text-muted">Penggajian Terakhir</div>
                <h4 class="mb-0">
                    Rp {{ number_format($penggajianTerakhir->total_gaji ?? 0, 0, ',', '.') }}
                </h4>
                <small>{{ $penggajianTerakhir->status_penggajian ?? 'Belum tersedia' }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-3">
        <div class="card">
            <div class="card-body" style="--role-color:#16a34a">
                <h4 class="mb-3">Fokus {{ $dashboardProfile['jabatan'] }}</h4>

                @foreach($dashboardProfile['focus'] as $focus)
                    <div class="focus-item mb-2">{{ $focus }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-3">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">Aksi Cepat</h4>

                <a href="{{ route('pegawai.presensi.create') }}" class="btn btn-primary btn-block mb-2">
                    Isi Presensi
                </a>

                <a href="{{ route('pegawai.presensi.index') }}" class="btn btn-outline-secondary btn-block">
                    Riwayat Presensi
                </a>
            </div>
        </div>
    </div>
</div>
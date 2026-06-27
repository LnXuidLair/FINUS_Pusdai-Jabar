<style>
    .role-hero { border-radius:8px; color:#fff; padding:24px; }
    .metric-card { border:1px solid #e5e7eb; border-radius:8px; height:100%; }
    .focus-item { border-left:4px solid var(--role-color); background:#f8fafc; padding:12px 14px; }
</style>

<div class="role-hero mb-4" style="background:{{ $dashboardProfile['color'] }}">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <div class="small text-uppercase" style="opacity:.8">Dashboard {{ $dashboardProfile['jabatan'] }}</div>
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
                <h4 class="mb-0">Rp {{ number_format($penggajianTerakhir->total_gaji ?? 0, 0, ',', '.') }}</h4>
                <small>{{ $penggajianTerakhir->status_penggajian ?? 'Belum tersedia' }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-3">
        <div class="card">
            <div class="card-body" style="--role-color:{{ $dashboardProfile['color'] }}">
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

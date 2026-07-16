@php
    $pegawaiSidebar = auth()->user()->pegawai;
    $profileSidebar = $pegawaiSidebar?->dashboard_profile ?? [
        'jabatan' => 'Pegawai',
        'slug' => null,
        'color' => '#063959',
    ];
@endphp
<style>
.sidebar.staff-sidebar {
    background:linear-gradient(180deg,{{ $profileSidebar['color'] }},#071033);
    color:#cbd5e1;
}
.staff-sidebar ul { list-style:none; padding:14px 10px 30px; margin:0; }
.staff-sidebar .logo { padding:22px 16px; text-align:center; }
.staff-sidebar .menu-label { padding:14px; color:#94a3b8; font-size:11px; text-transform:uppercase; letter-spacing:.14em; }
.staff-sidebar a { display:flex; align-items:center; gap:12px; padding:11px 14px; margin:5px; border-radius:10px; color:#e5e7eb; }
.staff-sidebar a:hover, .staff-sidebar a.active { background:rgba(255,255,255,.12); color:#fff; }
</style>
<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures staff-sidebar">
    <div class="nano">
        <div class="nano-content">
            <div class="logo">
                <img src="{{ asset('assets/images/pusdai_dashboard.png') }}" alt="FINUS" style="max-width:130px"
                    onerror="this.style.display='none'">
                <div class="mt-2 font-weight-bold">PEGAWAI FINUS</div>
                <small>Jabatan: {{ $profileSidebar['jabatan'] }}</small>
            </div>
            <ul>
                <li class="menu-label">Dashboard</li>
                <li>
                    <a href="{{ route('pegawai.dashboard', ['jabatan' => $profileSidebar['slug']]) }}"
                        class="{{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">
                        <i class="ti-home"></i> Dashboard {{ $profileSidebar['jabatan'] }}
                    </a>
                </li>
                <li class="menu-label">Aktivitas</li>
                <li>
                    <a href="{{ route('pegawai.presensi.index') }}"
                        class="{{ request()->routeIs('pegawai.presensi.*') ? 'active' : '' }}">
                        <i class="ti-calendar"></i> Presensi Saya
                    </a>
                </li>
                <li>
                    <a href="{{ route('pegawai.presensi.create') }}">
                        <i class="ti-check-box"></i> Isi Presensi
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@php
    $pegawaiSidebar = auth()->user()->pegawai;
    $profileSidebar = $pegawaiSidebar?->dashboard_profile ?? [
        'jabatan' => 'Pegawai',
        'slug' => null,
        'color' => '#063959',
    ];
@endphp

<style>
    :root {
        --sidebar-dark: #064e3b;
        --sidebar-green-1: #065f22;
        --sidebar-green-2: #0f8a3c;
        --sidebar-green-3: #16a34a;
        --sidebar-green-4: #22c55e;

        --sidebar-text: #f7fff9;
        --sidebar-muted: rgba(235, 255, 240, .72);

        --menu-bg: rgba(6, 95, 34, .48);
        --menu-border: rgba(255, 255, 255, .10);

        --menu-hover-bg: rgba(255, 255, 255, .15);
        --menu-hover-border: rgba(255, 255, 255, .24);

        --menu-active-start: rgba(126, 255, 135, .34);
        --menu-active-end: rgba(38, 184, 79, .34);
        --menu-active-border: rgba(168, 255, 178, .52);

        --accent-green: #91ff9b;
        --accent-light: #d9ffdd;
    }

    .sidebar.staff-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        width: 270px !important;
        min-width: 270px !important;
        height: 100vh !important;
        overflow: hidden;
        background:
            radial-gradient(
                circle at top right,
                rgba(126, 255, 135, .18),
                transparent 28%
            ),
            radial-gradient(
                circle at left 22%,
                rgba(255, 255, 255, .08),
                transparent 24%
            ),
            linear-gradient(
                180deg,
                var(--sidebar-dark) 0%,
                var(--sidebar-green-1) 28%,
                var(--sidebar-green-2) 58%,
                var(--sidebar-green-3) 100%
            ) !important;
        border-right: 1px solid rgba(255, 255, 255, .10);
        box-shadow:
            10px 0 40px rgba(0, 53, 20, .23),
            inset -1px 0 0 rgba(255, 255, 255, .06);
    }

    .sidebar.staff-sidebar .nano,
    .sidebar.staff-sidebar .nano-content {
        background: transparent !important;
    }

    .sidebar.staff-sidebar .nano-content {
        padding-bottom: 28px;
    }

    .sidebar.staff-sidebar .logo {
        position: relative;
        display: flex !important;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100% !important;
        min-height: 200px;
        padding: 26px 20px 24px !important;
        background:
            linear-gradient(
                180deg,
                rgba(2, 57, 22, .52),
                rgba(8, 91, 36, .22)
            ) !important;
        border-bottom: 1px solid rgba(255, 255, 255, .12) !important;
        box-shadow:
            inset 0 -1px 0 rgba(0, 55, 20, .18);
        overflow: hidden;
    }

    .sidebar.staff-sidebar .logo::before {
        content: "";
        position: absolute;
        top: -82px;
        right: -56px;
        width: 185px;
        height: 185px;
        border-radius: 50%;
        background: rgba(126, 255, 135, .11);
    }

    .sidebar.staff-sidebar .logo::after {
        content: "";
        position: absolute;
        bottom: -76px;
        left: -46px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .07);
    }

    .sidebar.staff-sidebar .logo img {
        position: relative;
        z-index: 2;
        display: block;
        width: auto;
        max-width: 135px !important;
        max-height: 85px;
        margin: 0 auto 12px;
        object-fit: contain;
        filter:
            brightness(0)
            invert(1)
            drop-shadow(0 6px 10px rgba(0, 0, 0, .15));
    }

    .sidebar.staff-sidebar .logo-title {
        position: relative;
        z-index: 2;
        margin: 0 !important;
        color: #ffffff !important;
        font-size: 18px !important;
        font-weight: 900 !important;
        line-height: 1.3;
        letter-spacing: 4px !important;
        text-transform: uppercase;
    }

    .sidebar.staff-sidebar .logo-sub-title {
        position: relative;
        z-index: 2;
        margin-top: 5px;
        color: rgba(255, 255, 255, .80) !important;
        font-size: 12px !important;
        font-weight: 500;
        letter-spacing: .4px;
    }

    .sidebar.staff-sidebar ul {
        margin: 0 !important;
        padding: 18px 13px 35px !important;
        list-style: none !important;
    }

    .sidebar.staff-sidebar ul li {
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
    }

    .sidebar.staff-sidebar .menu-label {
        position: relative;
        margin: 18px 10px 10px !important;
        padding-left: 18px !important;
        color: rgba(255, 255, 255, .78) !important;
        font-size: 11px !important;
        font-weight: 900 !important;
        letter-spacing: .22em !important;
        text-transform: uppercase !important;
    }

    .sidebar.staff-sidebar .menu-label::before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        width: 8px;
        height: 8px;
        transform: translateY(-50%);
        border-radius: 999px;
        background: #86efac;
        box-shadow: 0 0 0 5px rgba(134, 239, 172, .13);
    }

    .sidebar.staff-sidebar a {
        position: relative;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 12px 34px 12px 14px !important;
        margin: 6px 5px !important;
        border-radius: 14px !important;
        color: rgba(255, 255, 255, .90) !important;
        background: var(--menu-bg) !important;
        border: 1px solid var(--menu-border) !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        line-height: 1.25 !important;
        text-decoration: none !important;
        box-shadow:
            0 5px 15px rgba(0, 53, 20, .10),
            inset 0 1px 0 rgba(255, 255, 255, .05);
        transition:
            transform .22s ease,
            background .22s ease,
            border-color .22s ease,
            box-shadow .22s ease,
            color .22s ease !important;
    }

    .sidebar.staff-sidebar a i {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 34px !important;
        min-width: 34px !important;
        height: 34px;
        border-radius: 11px;
        color: var(--accent-light) !important;
        background: rgba(255, 255, 255, .10);
        font-size: 14px !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08);
        transition: color .22s ease, background .22s ease, transform .22s ease !important;
    }

    .sidebar.staff-sidebar a:hover,
    .sidebar.staff-sidebar a.active {
        background:
            linear-gradient(
                135deg,
                var(--menu-active-start),
                var(--menu-active-end)
            ) !important;
        color: #ffffff !important;
        border-color: var(--menu-active-border) !important;
        box-shadow:
            0 10px 22px rgba(0, 62, 24, .21),
            inset 4px 0 0 var(--accent-green),
            inset 0 1px 0 rgba(255, 255, 255, .12);
        transform: translateX(3px) !important;
    }

    .sidebar.staff-sidebar a:hover i,
    .sidebar.staff-sidebar a.active i {
        color: #166534 !important;
        background: #dcfce7 !important;
        transform: scale(1.05);
    }

    .sidebar.staff-sidebar a.active::after {
        content: "";
        position: absolute;
        right: 14px;
        top: 50%;
        width: 9px;
        height: 9px;
        transform: translateY(-50%);
        border-radius: 999px;
        background: var(--accent-green);
        box-shadow: 0 0 0 5px rgba(145, 255, 155, .16);
    }
</style>

<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures staff-sidebar">
    <div class="nano">
        <div class="nano-content">
            <div class="logo">
                <img src="{{ asset('assets/images/pusdai_dashboard.png') }}" alt="FINUS" style="max-width:130px"
                    onerror="this.style.display='none'">

                <div class="logo-title">Pegawai FINUS</div>
                <div class="logo-sub-title">Jabatan: {{ $profileSidebar['jabatan'] }}</div>
            </div>

            <ul>
                <li class="menu-label">Dashboard</li>

                <li>
                    <a href="{{ route('pegawai.dashboard', ['jabatan' => $profileSidebar['slug']]) }}"
                        class="{{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">
                        <i class="ti-home"></i>
                        Dashboard {{ $profileSidebar['jabatan'] }}
                    </a>
                </li>

                <li class="menu-label">Aktivitas</li>

                <li>
                    <a href="{{ route('pegawai.presensi.index') }}"
                        class="{{ request()->routeIs('pegawai.presensi.index') ? 'active' : '' }}">
                        <i class="ti-calendar"></i>
                        Presensi Saya
                    </a>
                </li>

                <li>
                    <a href="{{ route('pegawai.presensi.create') }}"
                        class="{{ request()->routeIs('pegawai.presensi.create') ? 'active' : '' }}">
                        <i class="ti-check-box"></i>
                        Isi Presensi
                    </a>
                </li>

                <li class="menu-label">Laporan</li>

                <li>
                    <a href="{{ route('pegawai.laporan-gaji.index') }}"
                        class="{{ request()->routeIs('pegawai.laporan-gaji.*') ? 'active' : '' }}">
                        <i class="ti-file"></i>
                        Laporan Gaji
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
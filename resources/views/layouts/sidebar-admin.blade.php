<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
>

<style>
    :root {
        --sidebar-dark: #0b431d;
        --sidebar-green-1: #125a28;
        --sidebar-green-2: #168b3a;
        --sidebar-green-3: #1ab64a;
        --sidebar-green-4: #20a947;

        --sidebar-text: #f7fff9;
        --sidebar-muted: rgba(235, 255, 240, .65);

        --menu-bg: rgba(5, 75, 31, .27);
        --menu-border: rgba(255, 255, 255, .09);

        --menu-hover-bg: rgba(255, 255, 255, .14);
        --menu-hover-border: rgba(255, 255, 255, .20);

        --menu-active-start: rgba(126, 255, 135, .30);
        --menu-active-end: rgba(38, 184, 79, .32);
        --menu-active-border: rgba(168, 255, 178, .42);

        --accent-green: #91ff9b;
        --accent-light: #d9ffdd;
    }

    /* =====================================================
       SIDEBAR UTAMA
    ===================================================== */
    .sidebar.sidebar-hide-to-small {
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
            linear-gradient(
                160deg,
                var(--sidebar-dark) 0%,
                var(--sidebar-green-1) 22%,
                var(--sidebar-green-2) 48%,
                var(--sidebar-green-3) 72%,
                var(--sidebar-dark) 100%
            ) !important;

        border-right: 1px solid rgba(255, 255, 255, .10);

        box-shadow:
            10px 0 40px rgba(0, 53, 20, .23),
            inset -1px 0 0 rgba(255, 255, 255, .06);
    }

    .sidebar .nano,
    .sidebar .nano-content {
        background: transparent !important;
    }

    .sidebar .nano-content {
        padding-bottom: 28px;
    }

    /* =====================================================
       AREA LOGO
    ===================================================== */
    .sidebar .logo {
        position: relative;

        display: flex !important;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        width: 100% !important;
        min-height: 190px;

        padding: 24px 20px 22px !important;

        background:
            linear-gradient(
                180deg,
                rgba(2, 57, 22, .42),
                rgba(8, 91, 36, .20)
            ) !important;

        border-bottom: 1px solid rgba(255, 255, 255, .12) !important;

        box-shadow:
            inset 0 -1px 0 rgba(0, 55, 20, .18);

        overflow: hidden;
    }

    .sidebar .logo::before {
        content: "";
        position: absolute;
        top: -80px;
        right: -55px;

        width: 180px;
        height: 180px;

        border-radius: 50%;

        background: rgba(126, 255, 135, .10);
    }

    .sidebar .logo::after {
        content: "";
        position: absolute;
        bottom: -75px;
        left: -45px;

        width: 145px;
        height: 145px;

        border-radius: 50%;

        background: rgba(255, 255, 255, .06);
    }

    .sidebar .logo img {
        position: relative;
        z-index: 2;

        display: block;

        width: auto;
        max-width: 135px !important;
        max-height: 85px;

        margin: 0 auto 10px;

        object-fit: contain;

        filter:
            brightness(0)
            invert(1)
            drop-shadow(0 6px 10px rgba(0, 0, 0, .15));
    }

    .sidebar .logo-title {
        position: relative;
        z-index: 2;

        margin: 0 !important;

        color: #ffffff !important;

        font-size: 18px !important;
        font-weight: 800 !important;
        line-height: 1.3;

        letter-spacing: 4px !important;
    }

    .sidebar .logo-sub-title {
        position: relative;
        z-index: 2;

        margin-top: 4px;

        color: rgba(255, 255, 255, .77) !important;

        font-size: 12px !important;
        font-weight: 500;
        letter-spacing: .4px;
    }

    /* =====================================================
       DAFTAR MENU
    ===================================================== */
    .sidebar ul {
        margin: 0 !important;
        padding: 17px 13px 35px !important;

        list-style: none !important;
    }

    .sidebar ul li {
        margin: 0 !important;
        padding: 0 !important;

        background: transparent !important;
    }

    /* Judul kategori */
    .sidebar ul li.label,
    .sidebar .label {
        position: relative;

        margin: 25px 13px 10px !important;
        padding: 0 0 0 13px !important;

        color: var(--sidebar-muted) !important;
        background: transparent !important;

        font-size: 10px !important;
        font-weight: 800 !important;
        line-height: 1.4 !important;

        letter-spacing: .19em !important;
        text-transform: uppercase !important;
    }

    .sidebar ul li.label::before,
    .sidebar .label::before {
        content: "";

        position: absolute;
        top: 50%;
        left: 0;

        width: 4px;
        height: 4px;

        border-radius: 50%;

        background: var(--accent-green);

        transform: translateY(-50%);

        box-shadow: 0 0 0 4px rgba(145, 255, 155, .10);
    }

    .sidebar ul li.label:first-child {
        margin-top: 11px !important;
    }

    /* =====================================================
       TOMBOL MENU
    ===================================================== */
    .sidebar ul li a {
        position: relative;

        display: flex !important;
        align-items: center !important;

        gap: 12px !important;

        width: 100% !important;
        min-height: 51px !important;

        margin: 7px 0 !important;
        padding: 9px 13px !important;

        overflow: hidden;

        color: rgba(255, 255, 255, .86) !important;
        background: var(--menu-bg) !important;

        border: 1px solid var(--menu-border) !important;
        border-radius: 14px !important;

        font-size: 14px !important;
        font-weight: 600 !important;
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

    /* Kotak ikon */
    .sidebar ul li a i {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;

        width: 34px !important;
        min-width: 34px !important;
        height: 34px;

        border-radius: 10px;

        color: var(--accent-light) !important;
        background: rgba(255, 255, 255, .09);

        font-size: 14px !important;

        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, .08);

        transition:
            color .22s ease,
            background .22s ease,
            transform .22s ease !important;
    }

    /* =====================================================
       HOVER
    ===================================================== */
    .sidebar ul li a:hover {
        color: #ffffff !important;

        background: var(--menu-hover-bg) !important;
        border-color: var(--menu-hover-border) !important;

        box-shadow:
            0 10px 20px rgba(0, 62, 24, .19),
            inset 0 1px 0 rgba(255, 255, 255, .10);

        transform: translateX(3px) !important;
    }

    .sidebar ul li a:hover i {
        color: #ffffff !important;
        background: rgba(145, 255, 155, .18);

        transform: scale(1.05);
    }

    /* =====================================================
       MENU AKTIF
    ===================================================== */
    .sidebar ul li a.active {
        color: #ffffff !important;

        background: linear-gradient(
            90deg,
            var(--menu-active-start) 0%,
            var(--menu-active-end) 100%
        ) !important;

        border-color: var(--menu-active-border) !important;

        font-weight: 800 !important;

        box-shadow:
            0 11px 22px rgba(0, 65, 24, .25),
            inset 4px 0 0 var(--accent-green),
            inset 0 1px 0 rgba(255, 255, 255, .15);

        transform: none !important;
    }

    .sidebar ul li a.active::before {
        content: "";

        position: absolute;
        top: 0;
        left: 0;

        width: 100%;
        height: 100%;

        background: linear-gradient(
            115deg,
            rgba(255, 255, 255, .08),
            transparent 55%
        );

        pointer-events: none;
    }

    .sidebar ul li a.active::after {
        content: "";

        position: absolute;
        top: 50%;
        right: 14px;

        width: 7px;
        height: 7px;

        border-radius: 50%;

        background: var(--accent-green);

        box-shadow:
            0 0 0 4px rgba(145, 255, 155, .14),
            0 0 12px rgba(145, 255, 155, .60);

        transform: translateY(-50%);
    }

    .sidebar ul li a.active i {
        color: #104d25 !important;
        background: linear-gradient(
            135deg,
            #caffcf,
            #82ff8e
        ) !important;

        box-shadow:
            0 6px 13px rgba(0, 79, 29, .18),
            inset 0 1px 0 rgba(255, 255, 255, .50);
    }

    /* =====================================================
       SCROLLBAR
    ===================================================== */
    .sidebar .nano-content::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar .nano-content::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar .nano-content::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, .20);
        border-radius: 10px;
    }

    .sidebar .nano-content::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, .35);
    }

    /* =====================================================
       RESPONSIVE
    ===================================================== */
    @media (max-width: 991px) {
        .sidebar.sidebar-hide-to-small {
            width: 260px !important;
            min-width: 260px !important;
        }
    }

    @media (max-width: 767px) {
        .sidebar.sidebar-hide-to-small {
            width: 250px !important;
            min-width: 250px !important;
        }

        .sidebar .logo {
            min-height: 170px;
            padding: 20px 16px !important;
        }

        .sidebar .logo img {
            max-width: 115px !important;
            max-height: 72px;
        }

        .sidebar .logo-title {
            font-size: 16px !important;
        }

        .sidebar ul {
            padding-left: 11px !important;
            padding-right: 11px !important;
        }

        .sidebar ul li a {
            min-height: 48px !important;
        }
    }
</style>

<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
    <div class="nano">
        <div class="nano-content">

            <div class="logo">
                <img
                    src="{{ asset('assets/images/pusdai_dashboard.png') }}"
                    alt="FINUS"
                    onerror="this.style.display='none'"
                >

                <div class="logo-title">ADMIN FINUS</div>
                <div class="logo-sub-title">Management FINUS</div>
            </div>

            <ul>
                <li class="label">Dashboard</li>

                <li>
                    <a
                        href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="label">Master Data</li>
                <li>
                    <a
                        href="{{ route('admin.coa.index') }}"
                        class="{{ request()->routeIs('admin.coa.*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-book"></i>
                        <span>COA</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('admin.gaji-jabatan.index') }}"
                        class="{{ request()->routeIs('admin.gaji-jabatan.*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-briefcase"></i>
                        <span>Gaji & Jabatan</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('admin.pegawai.index') }}"
                        class="{{ request()->routeIs('admin.pegawai.*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-users"></i>
                        <span>Pegawai</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('admin.jamaah.index') }}"
                        class="{{ request()->routeIs('admin.jamaah.*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-user-group"></i>
                        <span>Jamaah</span>
                    </a>
                </li>
                <li class="label">Transaksi</li>
                <li>
                    <a
                        href="{{ route('admin.penggajian.index') }}"
                        class="{{ request()->routeIs('admin.penggajian.*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-wallet"></i>
                        <span>Penggajian</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('admin.pengeluaran.index') }}"
                        class="{{ request()->routeIs('admin.pengeluaran.*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-receipt"></i>
                        <span>Pengeluaran</span>
                    </a>
                </li>

                <li class="label">Laporan</li>

                <li>
                    <a
                        href="{{ route('admin.presensi.index') }}"
                        class="{{ request()->routeIs('admin.presensi.*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Presensi</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('admin.laporan.jurnal-umum') }}"
                        class="{{ request()->routeIs('admin.laporan.jurnal-umum') ? 'active' : '' }}">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Jurnal Umum</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.laporan.arus-kas') }}" class="{{ request()->routeIs('admin.laporan.arus-kas') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Laporan Keuangan</span>
                    </a>
                </li>
            </ul>

        </div>
    </div>
</div>
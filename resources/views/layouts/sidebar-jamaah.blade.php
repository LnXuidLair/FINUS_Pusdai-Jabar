<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --jamaah-sidebar-dark: #0B431D;
        --jamaah-sidebar-green-1: #125A28;
        --jamaah-sidebar-green-2: #168B3A;
        --jamaah-sidebar-green-3: #1AB64A;
        --jamaah-sidebar-text: #F7FFF9;
        --jamaah-sidebar-muted: rgba(235, 255, 240, .65);
        --jamaah-menu-bg: rgba(5, 75, 31, .27);
        --jamaah-menu-border: rgba(255, 255, 255, .09);
        --jamaah-menu-hover-bg: rgba(255, 255, 255, .14);
        --jamaah-menu-hover-border: rgba(255, 255, 255, .20);
        --jamaah-menu-active-start: rgba(126, 255, 135, .30);
        --jamaah-menu-active-end: rgba(38, 184, 79, .32);
        --jamaah-menu-active-border: rgba(168, 255, 178, .42);
        --jamaah-accent-green: #91FF9B;
        --jamaah-accent-light: #D9FFDD;
    }

    .sidebar.jamaah-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1050;
        width: 270px !important;
        min-width: 270px !important;
        height: 100vh !important;
        overflow: hidden;
        color: var(--jamaah-sidebar-text);
        background:
            radial-gradient(circle at top right, rgba(126, 255, 135, .18), transparent 28%),
            linear-gradient(
                160deg,
                var(--jamaah-sidebar-dark) 0%,
                var(--jamaah-sidebar-green-1) 22%,
                var(--jamaah-sidebar-green-2) 48%,
                var(--jamaah-sidebar-green-3) 72%,
                var(--jamaah-sidebar-dark) 100%
            ) !important;
        border-right: 1px solid rgba(255, 255, 255, .10);
        box-shadow: 10px 0 40px rgba(0, 53, 20, .23), inset -1px 0 0 rgba(255, 255, 255, .06);
    }

    .jamaah-sidebar .nano,
    .jamaah-sidebar .nano-content {
        background: transparent !important;
    }

    .jamaah-sidebar .nano-content {
        padding-bottom: 28px;
    }

    .jamaah-sidebar .logo {
        position: relative;
        display: flex !important;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100% !important;
        min-height: 190px;
        padding: 24px 20px 22px !important;
        overflow: hidden;
        background: linear-gradient(180deg, rgba(2, 57, 22, .42), rgba(8, 91, 36, .20)) !important;
        border-bottom: 1px solid rgba(255, 255, 255, .12) !important;
        box-shadow: inset 0 -1px 0 rgba(0, 55, 20, .18);
    }

    .jamaah-sidebar .logo::before {
        content: "";
        position: absolute;
        top: -80px;
        right: -55px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(126, 255, 135, .10);
    }

    .jamaah-sidebar .logo::after {
        content: "";
        position: absolute;
        bottom: -75px;
        left: -45px;
        width: 145px;
        height: 145px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .06);
    }

    .jamaah-sidebar .logo img {
        position: relative;
        z-index: 2;
        display: block;
        width: auto;
        max-width: 135px !important;
        max-height: 85px;
        margin: 0 auto 10px;
        object-fit: contain;
        filter: brightness(0) invert(1) drop-shadow(0 6px 10px rgba(0, 0, 0, .15));
    }

    .jamaah-sidebar .logo-title {
        position: relative;
        z-index: 2;
        margin: 0 !important;
        color: #FFFFFF !important;
        font-size: 18px !important;
        font-weight: 800 !important;
        line-height: 1.3;
        letter-spacing: 4px !important;
    }

    .jamaah-sidebar .logo-sub-title {
        position: relative;
        z-index: 2;
        margin-top: 4px;
        color: rgba(255, 255, 255, .77) !important;
        font-size: 12px !important;
        font-weight: 500;
        letter-spacing: .4px;
    }

    .jamaah-sidebar ul {
        margin: 0 !important;
        padding: 17px 13px 35px !important;
        list-style: none !important;
    }

    .jamaah-sidebar ul li {
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
    }

    .jamaah-sidebar ul li.label {
        position: relative;
        margin: 25px 13px 10px !important;
        padding: 0 0 0 13px !important;
        color: var(--jamaah-sidebar-muted) !important;
        background: transparent !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        line-height: 1.4 !important;
        letter-spacing: .19em !important;
        text-transform: uppercase !important;
    }

    .jamaah-sidebar ul li.label::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: var(--jamaah-accent-green);
        transform: translateY(-50%);
        box-shadow: 0 0 0 4px rgba(145, 255, 155, .10);
    }

    .jamaah-sidebar ul li.label:first-child {
        margin-top: 11px !important;
    }

    .jamaah-sidebar ul li a {
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
        background: var(--jamaah-menu-bg) !important;
        border: 1px solid var(--jamaah-menu-border) !important;
        border-radius: 14px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        line-height: 1.25 !important;
        text-decoration: none !important;
        box-shadow: 0 5px 15px rgba(0, 53, 20, .10), inset 0 1px 0 rgba(255, 255, 255, .05);
        transition: transform .22s ease, background .22s ease, border-color .22s ease, box-shadow .22s ease, color .22s ease !important;
    }

    .jamaah-sidebar ul li a i {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 34px !important;
        min-width: 34px !important;
        height: 34px;
        border-radius: 10px;
        color: var(--jamaah-accent-light) !important;
        background: rgba(255, 255, 255, .09);
        font-size: 14px !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08);
        transition: color .22s ease, background .22s ease, transform .22s ease !important;
    }

    .jamaah-sidebar ul li a:hover {
        color: #FFFFFF !important;
        background: var(--jamaah-menu-hover-bg) !important;
        border-color: var(--jamaah-menu-hover-border) !important;
        box-shadow: 0 10px 20px rgba(0, 62, 24, .19), inset 0 1px 0 rgba(255, 255, 255, .10);
        transform: translateX(3px) !important;
    }

    .jamaah-sidebar ul li a:hover i {
        color: #FFFFFF !important;
        background: rgba(145, 255, 155, .18);
        transform: scale(1.05);
    }

    .jamaah-sidebar ul li a.active {
        color: #FFFFFF !important;
        background: linear-gradient(90deg, var(--jamaah-menu-active-start), var(--jamaah-menu-active-end)) !important;
        border-color: var(--jamaah-menu-active-border) !important;
        font-weight: 800 !important;
        box-shadow: 0 11px 22px rgba(0, 65, 24, .25), inset 4px 0 0 var(--jamaah-accent-green), inset 0 1px 0 rgba(255, 255, 255, .15);
        transform: none !important;
    }

    .jamaah-sidebar ul li a.active::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(115deg, rgba(255, 255, 255, .08), transparent 55%);
        pointer-events: none;
    }

    .jamaah-sidebar ul li a.active::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 14px;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--jamaah-accent-green);
        box-shadow: 0 0 0 4px rgba(145, 255, 155, .14), 0 0 12px rgba(145, 255, 155, .60);
        transform: translateY(-50%);
    }

    .jamaah-sidebar ul li a.active i {
        color: #104D25 !important;
        background: linear-gradient(135deg, #CAFFCF, #82FF8E) !important;
        box-shadow: 0 6px 13px rgba(0, 79, 29, .18), inset 0 1px 0 rgba(255, 255, 255, .50);
    }

    .jamaah-sidebar .nano-content::-webkit-scrollbar {
        width: 5px;
    }

    .jamaah-sidebar .nano-content::-webkit-scrollbar-track {
        background: transparent;
    }

    .jamaah-sidebar .nano-content::-webkit-scrollbar-thumb {
        border-radius: 10px;
        background: rgba(255, 255, 255, .20);
    }

    @media (max-width: 991px) {
        .sidebar.jamaah-sidebar {
            width: 260px !important;
            min-width: 260px !important;
        }
    }

    @media (max-width: 767px) {
        .sidebar.jamaah-sidebar {
            width: 250px !important;
            min-width: 250px !important;
        }

        .jamaah-sidebar .logo {
            min-height: 170px;
            padding: 20px 16px !important;
        }

        .jamaah-sidebar .logo img {
            max-width: 115px !important;
            max-height: 72px;
        }

        .jamaah-sidebar .logo-title {
            font-size: 16px !important;
        }

        .jamaah-sidebar ul {
            padding-left: 11px !important;
            padding-right: 11px !important;
        }

        .jamaah-sidebar ul li a {
            min-height: 48px !important;
        }
    }
</style>

<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures jamaah-sidebar">
    <div class="nano">
        <div class="nano-content">
            <div class="logo">
                <img
                    src="{{ asset('assets/images/pusdai_dashboard.png') }}"
                    alt="FINUS"
                    onerror="this.style.display='none'"
                >
                <div class="logo-title">JAMAAH FINUS</div>
                <div class="logo-sub-title">Layanan Jamaah</div>
            </div>

            <ul>
                <li class="label">Dashboard</li>
                <li>
                    <a
                        href="{{ route('jamaah.dashboard') }}"
                        class="{{ request()->routeIs('jamaah.dashboard') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-house"></i>
                        <span>Beranda</span>
                    </a>
                </li>

                <li class="label">Transaksi</li>
                <li>
                    <a
                        href="{{ route('jamaah.transaksi.create', 'zakat') }}"
                        class="{{ request()->routeIs('jamaah.transaksi.*') && request()->route('jenis') === 'zakat' ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-hand-holding-heart"></i>
                        <span>Zakat</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('jamaah.transaksi.create', 'infak') }}"
                        class="{{ request()->routeIs('jamaah.transaksi.*') && request()->route('jenis') === 'infak' ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-circle-dollar-to-slot"></i>
                        <span>Infak</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('jamaah.transaksi.create', 'wakaf') }}"
                        class="{{ request()->routeIs('jamaah.transaksi.*') && request()->route('jenis') === 'wakaf' ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-mosque"></i>
                        <span>Wakaf</span>
                    </a>
                </li>

                <li class="label">Laporan & Riwayat</li>
                <li>
                    <a
                        href="{{ route('jamaah.riwayat.index') }}"
                        class="{{ request()->routeIs('jamaah.riwayat.*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Riwayat Transaksi</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('jamaah.laporan.index') }}"
                        class="{{ request()->routeIs('jamaah.laporan.*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-chart-column"></i>
                        <span>Laporan Transaksi</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
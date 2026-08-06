@php
    $pegawaiSidebar = $currentUser->pegawai;
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
    /* FINUS SIDEBAR COMPACT UPGRADE */
    .sidebar .logo {
        min-height: 164px !important;
        padding-top: 20px !important;
        padding-bottom: 18px !important;
    }
    .sidebar .logo img {
        max-width: 118px !important;
        max-height: 72px !important;
        margin-bottom: 8px !important;
    }
    .sidebar .logo-title {
        font-size: 16px !important;
        letter-spacing: 3px !important;
    }
    .finus-sidebar-role-chip {
        position: relative;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        margin-top: 10px;
        padding: 0 9px;
        border: 1px solid rgba(255,255,255,.16);
        border-radius: 999px;
        background: rgba(255,255,255,.10);
        color: rgba(255,255,255,.84);
        font-size: 9px;
        font-weight: 850;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .sidebar ul { padding-top: 12px !important; }
    .sidebar ul li a {
        min-height: 46px !important;
        border-radius: 13px !important;
    }
    .sidebar ul li a.active {
        box-shadow: 0 9px 22px rgba(0,45,16,.18), inset 0 1px 0 rgba(255,255,255,.13) !important;
    }
    .sidebar ul li a.active::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 8px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #B7FFBE;
        box-shadow: 0 0 0 4px rgba(183,255,190,.12);
        transform: translateY(-50%);
    }
    .sidebar .nano-content { scrollbar-width: thin; scrollbar-color: rgba(183,255,190,.30) transparent; }
    @media (max-width: 991.98px) {
        .sidebar .logo { min-height: 148px !important; }
    }
</style>
<style>
    /* =====================================================
       FINUS MOBILE SIDEBAR FIX
       Menghapus efek sidebar icon-only dari template lama.
    ===================================================== */
    .finus-mobile-sidebar-close {
        display: none;
    }
    @media (max-width: 991.98px) {
        body.sidebar-open {
            overflow: hidden !important;
            overscroll-behavior: none;
        }
        /*
         * Sidebar harus berada di atas backdrop. Sebelumnya beberapa
         * sidebar memakai z-index 1000, sedangkan backdrop memakai 1045.
         */
        .sidebar.sidebar-hide-to-small {
            z-index: 1060 !important;
            width: min(84vw, 320px) !important;
            min-width: 0 !important;
            max-width: 320px !important;
            height: 100vh !important;
            height: 100dvh !important;
            max-height: 100dvh !important;
            overflow: hidden !important;
            border-radius: 0 22px 22px 0;
            box-shadow:
                18px 0 48px rgba(0, 31, 11, .34),
                inset -1px 0 0 rgba(255, 255, 255, .08) !important;
            transform: translate3d(-105%, 0, 0) !important;
            transition:
                transform .28s cubic-bezier(.22, .61, .36, 1),
                box-shadow .28s ease !important;
            will-change: transform;
            isolation: isolate;
        }
        body.sidebar-open .sidebar.sidebar-hide-to-small {
            transform: translate3d(0, 0, 0) !important;
        }
        /*
         * Memastikan backdrop tidak menutupi atau meredupkan sidebar.
         */
        .finus-sidebar-backdrop {
            z-index: 1050 !important;
            background: rgba(1, 19, 7, .47) !important;
            -webkit-backdrop-filter: blur(4px);
            backdrop-filter: blur(4px);
        }
        .sidebar.sidebar-hide-to-small .nano {
            position: relative !important;
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
        }
        .sidebar.sidebar-hide-to-small .nano-content {
            position: absolute !important;
            inset: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 100% !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            padding-bottom:
                calc(24px + env(safe-area-inset-bottom)) !important;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: contain;
            scrollbar-width: thin;
            touch-action: pan-y;
        }
        /*
         * Area logo dibuat lebih ringkas agar menu tidak terlalu turun.
         */
        .sidebar.sidebar-hide-to-small .logo {
            min-height: 146px !important;
            padding:
                calc(15px + env(safe-area-inset-top))
                44px
                15px !important;
        }
        .sidebar.sidebar-hide-to-small .logo img {
            display: block !important;
            max-width: 94px !important;
            max-height: 61px !important;
            margin: 0 auto 7px !important;
            opacity: 1 !important;
            filter:
                brightness(0)
                invert(1)
                drop-shadow(0 5px 9px rgba(0, 0, 0, .16)) !important;
        }
        .sidebar.sidebar-hide-to-small .logo-title {
            display: block !important;
            max-width: 100%;
            font-size: 14.5px !important;
            line-height: 1.25 !important;
            letter-spacing: 2.5px !important;
            text-align: center;
            white-space: nowrap;
        }
        .sidebar.sidebar-hide-to-small .logo-sub-title {
            display: block !important;
            max-width: 100%;
            margin-top: 4px !important;
            font-size: 10.5px !important;
            line-height: 1.35 !important;
            text-align: center;
            white-space: normal;
        }
        /*
         * Tombol tutup khusus perangkat seluler.
         */
        .finus-mobile-sidebar-close {
            position: absolute;
            top: calc(11px + env(safe-area-inset-top));
            right: 11px;
            z-index: 20;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            padding: 0;
            border: 1px solid rgba(255, 255, 255, .20);
            border-radius: 11px;
            background: rgba(0, 54, 20, .26);
            color: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 25px;
            font-weight: 300;
            line-height: 1;
            cursor: pointer;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, .09);
            -webkit-backdrop-filter: blur(7px);
            backdrop-filter: blur(7px);
            transition:
                background .2s ease,
                transform .2s ease,
                border-color .2s ease;
        }
        .finus-mobile-sidebar-close:hover,
        .finus-mobile-sidebar-close:focus-visible {
            border-color: rgba(255, 255, 255, .34);
            background: rgba(255, 255, 255, .16);
            outline: none;
            transform: scale(1.04);
        }
        /*
         * Mengembalikan seluruh label dan teks yang disembunyikan oleh
         * CSS .sidebar-shrink dari template lama.
         */
        .sidebar.sidebar-hide-to-small ul {
            width: 100% !important;
            padding:
                10px
                10px
                calc(28px + env(safe-area-inset-bottom)) !important;
        }
        .sidebar.sidebar-hide-to-small ul li {
            width: 100% !important;
        }
        .sidebar.sidebar-hide-to-small ul li.label,
        .sidebar.sidebar-hide-to-small .menu-label {
            display: block !important;
            width: auto !important;
            height: auto !important;
            max-height: none !important;
            margin: 17px 10px 8px !important;
            padding-left: 13px !important;
            overflow: visible !important;
            opacity: 1 !important;
            visibility: visible !important;
            font-size: 9.5px !important;
            line-height: 1.4 !important;
            letter-spacing: .17em !important;
            text-align: left !important;
            white-space: normal !important;
            transform: none !important;
        }
        .sidebar.sidebar-hide-to-small ul li.label:first-child,
        .sidebar.sidebar-hide-to-small .menu-label:first-child {
            margin-top: 8px !important;
        }
        .sidebar.sidebar-hide-to-small ul li a,
        .sidebar.sidebar-hide-to-small ul li > a {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
            min-height: 48px !important;
            margin: 6px 0 !important;
            padding: 7px 35px 7px 9px !important;
            border-radius: 13px !important;
            font-size: 13.5px !important;
            line-height: 1.3 !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
        }
        /*
         * Aturan ini adalah bagian terpenting: teks menu harus selalu
         * terlihat ketika drawer dibuka pada perangkat seluler.
         */
        .sidebar.sidebar-hide-to-small ul li a > span,
        body.sidebar-open .sidebar.sidebar-hide-to-small ul li a > span {
            position: static !important;
            display: block !important;
            flex: 1 1 auto !important;
            width: auto !important;
            min-width: 0 !important;
            max-width: none !important;
            height: auto !important;
            max-height: none !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            color: inherit !important;
            opacity: 1 !important;
            visibility: visible !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: 1.3 !important;
            text-align: left !important;
            text-indent: 0 !important;
            text-overflow: clip !important;
            white-space: normal !important;
            transform: none !important;
            clip: auto !important;
        }
        .sidebar.sidebar-hide-to-small ul li a i {
            display: inline-flex !important;
            flex: 0 0 34px !important;
            align-items: center;
            justify-content: center;
            width: 34px !important;
            min-width: 34px !important;
            height: 34px !important;
            margin: 0 !important;
            font-size: 14px !important;
            line-height: 1 !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none;
        }
        .sidebar.sidebar-hide-to-small ul li a.active {
            padding-left: 11px !important;
            box-shadow:
                0 9px 20px rgba(0, 65, 24, .24),
                inset 4px 0 0 var(--accent-green, var(--jamaah-accent-green, #91FF9B)),
                inset 0 1px 0 rgba(255, 255, 255, .14) !important;
        }
        .sidebar.sidebar-hide-to-small ul li a.active::after {
            right: 12px !important;
            width: 7px !important;
            height: 7px !important;
        }
        /*
         * Hover pada ponsel tidak boleh menggeser menu karena dapat
         * membuat drawer tampak tidak rata setelah disentuh.
         */
        .sidebar.sidebar-hide-to-small ul li a:hover {
            transform: none !important;
        }
    }
    @media (max-width: 380px) {
        .sidebar.sidebar-hide-to-small {
            width: min(87vw, 296px) !important;
        }
        .sidebar.sidebar-hide-to-small .logo {
            min-height: 138px !important;
            padding-left: 40px !important;
            padding-right: 40px !important;
        }
        .sidebar.sidebar-hide-to-small ul li a {
            min-height: 46px !important;
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            font-size: 13px !important;
        }
    }
    @media (max-width: 991.98px) and (orientation: landscape) and (max-height: 520px) {
        .sidebar.sidebar-hide-to-small .logo {
            min-height: 106px !important;
            padding-top:
                calc(9px + env(safe-area-inset-top)) !important;
            padding-bottom: 9px !important;
        }
        .sidebar.sidebar-hide-to-small .logo img {
            max-width: 72px !important;
            max-height: 42px !important;
            margin-bottom: 4px !important;
        }
        .sidebar.sidebar-hide-to-small .logo-title {
            font-size: 12.5px !important;
        }
        .sidebar.sidebar-hide-to-small .logo-sub-title {
            font-size: 9.5px !important;
        }
        .sidebar.sidebar-hide-to-small ul li.label,
        .sidebar.sidebar-hide-to-small .menu-label {
            margin-top: 12px !important;
        }
    }
</style>
<style>
    /* =====================================================
       FIX LOGO PUSDAI PADA DARK MODE HP
    ===================================================== */
    .finus-mobile-logo-mask {
        display: none;
    }
    .sidebar.sidebar-hide-to-small,
    .sidebar.sidebar-hide-to-small .logo,
    .sidebar.sidebar-hide-to-small .logo img {
        color-scheme: only light !important;
        forced-color-adjust: none !important;
    }
    .sidebar.sidebar-hide-to-small .logo img {
        opacity: 1 !important;
        background: transparent !important;
        -webkit-filter:
            brightness(0)
            saturate(100%)
            invert(100%)
            drop-shadow(0 5px 9px rgba(0, 0, 0, .16)) !important;
        filter:
            brightness(0)
            saturate(100%)
            invert(100%)
            drop-shadow(0 5px 9px rgba(0, 0, 0, .16)) !important;
        mix-blend-mode: normal !important;
        isolation: isolate;
    }
    @media (max-width: 991.98px) {
        /*
         * Mask memakai bentuk transparan PNG, lalu mengisi logo
         * dengan putih. Browser dark mode tidak dapat menggelapkannya
         * seperti ketika gambar PNG dirender secara langsung.
         */
        @supports (
            (-webkit-mask-image: url(""))
            or (mask-image: url(""))
        ) {
            .sidebar.sidebar-hide-to-small .logo img {
                display: none !important;
            }
            .finus-mobile-logo-mask {
                position: relative;
                z-index: 2;
                display: block !important;
                width: 94px;
                height: 61px;
                margin: 0 auto 7px;
                background-color: #FFFFFF !important;
                -webkit-mask-image:
                    url('{{ asset('assets/images/pusdai_dashboard.png') }}');
                -webkit-mask-repeat: no-repeat;
                -webkit-mask-position: center;
                -webkit-mask-size: contain;
                mask-image:
                    url('{{ asset('assets/images/pusdai_dashboard.png') }}');
                mask-repeat: no-repeat;
                mask-position: center;
                mask-size: contain;
                color-scheme: only light !important;
                forced-color-adjust: none !important;
                opacity: 1 !important;
                filter: drop-shadow(0 5px 9px rgba(0, 0, 0, .16));
                mix-blend-mode: normal !important;
                isolation: isolate;
            }
        }
    }
    @media (max-width: 991.98px) and (orientation: landscape) and (max-height: 520px) {
        @supports (
            (-webkit-mask-image: url(""))
            or (mask-image: url(""))
        ) {
            .finus-mobile-logo-mask {
                width: 72px;
                height: 42px;
                margin-bottom: 4px;
            }
        }
    }
</style>
<div id="finusSidebar" class="sidebar sidebar-hide-to-small staff-sidebar" role="navigation" aria-label="Navigasi Pegawai FINUS">
    <div class="nano">
        <div class="nano-content">
            <div class="logo">
                <button type="button" class="finus-mobile-sidebar-close" aria-label="Tutup menu navigasi">
                    <span aria-hidden="true">&times;</span>
                </button>
                <span class="finus-mobile-logo-mask" aria-hidden="true"></span>
                <img src="{{ asset('assets/images/pusdai_dashboard.png') }}" alt="FINUS" style="max-width:130px" onerror="this.style.display='none'">
                <div class="logo-title">Pegawai FINUS</div>
                <div class="logo-sub-title">Jabatan: {{ $profileSidebar['jabatan'] }}</div>
                <div class="finus-sidebar-role-chip">{{ $profileSidebar['jabatan'] }}</div>
            </div>
            <ul>
                <li class="menu-label">Dashboard</li>
                <li>
                    <a href="{{ route('pegawai.dashboard', ['jabatan' => $profileSidebar['slug']]) }}"
                        class="{{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">
                        <i class="ti-home"></i>
                        <span>Dashboard {{ $profileSidebar['jabatan'] }}</span>
                    </a>
                </li>
                <li class="menu-label">Aktivitas</li>
                <li>
                    <a href="{{ route('pegawai.presensi.index') }}"
                        class="{{ request()->routeIs('pegawai.presensi.index') ? 'active' : '' }}">
                        <i class="ti-calendar"></i>
                        <span>Presensi Saya</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pegawai.presensi.create') }}"
                        class="{{ request()->routeIs('pegawai.presensi.create') ? 'active' : '' }}">
                        <i class="ti-check-box"></i>
                        <span>Isi Presensi</span>
                    </a>
                </li>
                <li class="menu-label">Laporan</li>
                <li>
                    <a href="{{ route('pegawai.laporan-gaji.index') }}"
                        class="{{ request()->routeIs('pegawai.laporan-gaji.*') ? 'active' : '' }}">
                        <i class="ti-file"></i>
                        <span>Laporan Gaji</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<script>
(() => {
    const sidebar = document.querySelector('.sidebar.sidebar-hide-to-small');
    const closeButton = sidebar?.querySelector(
        '.finus-mobile-sidebar-close'
    );
    if (!sidebar || !closeButton) {
        return;
    }
    const mobileQuery = window.matchMedia(
        '(max-width: 991.98px)'
    );
    const closeSidebar = () => {
        document.body.classList.remove(
            'sidebar-open',
            'finus-sidebar-lock'
        );
        document
            .querySelectorAll('.sidebar-toggle, .hamburger')
            .forEach(toggle => {
                toggle.setAttribute(
                    'aria-expanded',
                    'false'
                );
            });
        const backdrop = document.getElementById(
            'finusSidebarBackdrop'
        );
        backdrop?.setAttribute(
            'aria-hidden',
            'true'
        );
    };
    closeButton.addEventListener('click', event => {
        event.preventDefault();
        event.stopPropagation();
        closeSidebar();
    });
    /*
     * Geser drawer ke kiri untuk menutup.
     */
    let touchStartX = 0;
    let touchStartY = 0;
    sidebar.addEventListener(
        'touchstart',
        event => {
            if (!mobileQuery.matches) {
                return;
            }
            const touch = event.changedTouches[0];
            touchStartX = touch.clientX;
            touchStartY = touch.clientY;
        },
        { passive: true }
    );
    sidebar.addEventListener(
        'touchend',
        event => {
            if (!mobileQuery.matches) {
                return;
            }
            const touch = event.changedTouches[0];
            const distanceX = touch.clientX - touchStartX;
            const distanceY = touch.clientY - touchStartY;
            if (
                distanceX < -65
                && Math.abs(distanceX) > Math.abs(distanceY)
            ) {
                closeSidebar();
            }
        },
        { passive: true }
    );
    /*
     * Saat drawer dibuka, posisi menu aktif otomatis terlihat.
     */
    const activeLink = sidebar.querySelector('a.active');
    const showActiveLink = () => {
        if (
            mobileQuery.matches
            && document.body.classList.contains('sidebar-open')
            && activeLink
        ) {
            window.setTimeout(() => {
                activeLink.scrollIntoView({
                    block: 'center',
                    behavior: 'smooth'
                });
            }, 120);
        }
    };
    const bodyObserver = new MutationObserver(showActiveLink);
    bodyObserver.observe(
        document.body,
        {
            attributes: true,
            attributeFilter: ['class']
        }
    );
})();
</script>
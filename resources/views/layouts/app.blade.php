<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0E5423">
    <meta name="color-scheme" content="light">
    <title>
        @hasSection('title')
            @yield('title') | FINUS
        @else
            FINUS | Keuangan Masjid PUSDAI
        @endif
    </title>
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=20">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?v=20">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    {{-- Bootstrap hanya dimuat satu kali --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> 
    {{-- Library template --}}
    <link href="{{ asset('assets/css/lib/weather-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/menubar/sidebar.css') }}" rel="stylesheet">
    {{-- Font Awesome 6 + kompatibilitas class Font Awesome lama --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/v4-shims.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css">
    {{-- CSS template diletakkan setelah Bootstrap agar tidak mudah tertimpa --}}
    <link href="{{ asset('assets/css/lib/helper.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom-style.css') }}" rel="stylesheet">
    {{-- CSS khusus dari halaman anak --}}
    @stack('styles')
    <style>
        :root {
            --finus-sidebar-width: 270px;
            --finus-header-height: 74px;
            --finus-green-dark: #0E5423;
            --finus-green: #179B40;
            --finus-green-light: #22BA51;
            --finus-green-soft: #EAF8EE;
            --finus-page-bg: #F4F8F5;
            --finus-surface: #FFFFFF;
            --finus-text: #172033;
            --finus-muted: #64748B;
            --finus-border: #E2EAE5;
            --finus-shadow-sm: 0 3px 12px rgba(15, 23, 42, .06);
            --finus-shadow-md: 0 14px 34px rgba(15, 23, 42, .08);
        }
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }
        html {
            min-height: 100%;
            scroll-behavior: smooth;
        }
        body.finus-layout {
            min-height: 100vh;
            margin: 0 !important;
            padding: 0 !important;
            overflow-x: hidden;
            background:
                radial-gradient(circle at 95% 5%, rgba(34, 186, 81, .055), transparent 24rem),
                var(--finus-page-bg);
            color: var(--finus-text);
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }
        body.finus-sidebar-lock {
            overflow: hidden;
        }
        a,
        button,
        input,
        select,
        textarea {
            -webkit-tap-highlight-color: transparent;
        }
        .finus-skip-link {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 3000;
            padding: 10px 14px;
            border-radius: 10px;
            background: #FFFFFF;
            color: var(--finus-green-dark);
            font-weight: 700;
            box-shadow: var(--finus-shadow-md);
            transform: translateY(-150%);
            transition: transform .2s ease;
        }
        .finus-skip-link:focus {
            color: var(--finus-green-dark);
            transform: translateY(0);
        }
        /* =====================================================
           SIDEBAR LAYOUT
        ===================================================== */
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            z-index: 1050 !important;
            width: var(--finus-sidebar-width) !important;
            min-width: var(--finus-sidebar-width) !important;
            max-width: var(--finus-sidebar-width) !important;
            height: 100vh !important;
            overflow-x: hidden;
            overflow-y: auto;
            transform: translateX(0);
            transition: transform .25s ease !important;
            will-change: transform;
        }
        .sidebar .nano,
        .sidebar .nano-content {
            min-height: 100%;
            background: transparent !important;
        }

        .sidebar .nano {
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
        }

        .sidebar .nano-content {
            height: 100vh !important;
            max-height: 100vh !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding-bottom: 45px !important;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }
        .sidebar ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .sidebar a {
            text-decoration: none;
        }
        .sidebar .logo {
            min-height: 170px;
            padding: 24px 18px 20px !important;
            display: flex !important;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .sidebar .logo img {
            width: auto;
            max-width: 125px !important;
            max-height: 84px !important;
            object-fit: contain;
        }
        body.sidebar-collapsed .sidebar {
            transform: translateX(-100%) !important;
        }
        /* =====================================================
           MOBILE SIDEBAR BACKDROP
        ===================================================== */
        .finus-sidebar-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1045;
            display: block;
            visibility: hidden;
            background: rgba(2, 16, 8, .54);
            opacity: 0;
            backdrop-filter: blur(2px);
            transition:
                opacity .25s ease,
                visibility .25s ease;
        }
        body.sidebar-open .finus-sidebar-backdrop {
            visibility: visible;
            opacity: 1;
        }
        /* =====================================================
           HEADER
        ===================================================== */
        .header {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            left: var(--finus-sidebar-width) !important;
            z-index: 1040 !important;
            width: auto !important;
            max-width: none !important;
            height: var(--finus-header-height) !important;
            min-height: var(--finus-header-height) !important;
            margin: 0 !important;
            padding: 0 28px !important;
            display: flex !important;
            align-items: center !important;
            border-bottom: 1px solid rgba(14, 84, 35, .10) !important;
            background: rgba(255, 255, 255, .96) !important;
            box-shadow: var(--finus-shadow-sm) !important;
            backdrop-filter: blur(14px);
            transition:
                left .25s ease,
                box-shadow .2s ease !important;
        }
        .header .container,
        .header .container-fluid,
        .header .row,
        .header .navbar,
        .header .navbar-header,
        .header .pull-left,
        .header .pull-right {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
        body.sidebar-collapsed .header {
            left: 0 !important;
        }
        .header-icon {
            min-height: 34px;
        }
        /* =====================================================
           HAMBURGER
        ===================================================== */
        .hamburger,
        .sidebar-toggle,
        .finus-sidebar-toggle {
            width: 42px !important;
            height: 42px !important;
            padding: 0 !important;
            display: inline-flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px;
            border: 1px solid transparent !important;
            border-radius: 11px !important;
            outline: none !important;
            background: transparent !important;
            color: var(--finus-text) !important;
            cursor: pointer;
            transition:
                background .2s ease,
                border-color .2s ease,
                box-shadow .2s ease !important;
        }
        .hamburger:hover,
        .sidebar-toggle:hover,
        .finus-sidebar-toggle:hover {
            border-color: #DBE8DF !important;
            background: var(--finus-green-soft) !important;
        }
        .hamburger:focus-visible,
        .sidebar-toggle:focus-visible,
        .finus-sidebar-toggle:focus-visible {
            border-color: rgba(23, 155, 64, .45) !important;
            box-shadow: 0 0 0 4px rgba(23, 155, 64, .12) !important;
        }
        .hamburger i,
        .sidebar-toggle i,
        .finus-sidebar-toggle i,
        .hamburger svg,
        .sidebar-toggle svg,
        .finus-sidebar-toggle svg {
            display: none !important;
        }
        .hamburger::before,
        .hamburger::after,
        .sidebar-toggle::before,
        .sidebar-toggle::after,
        .finus-sidebar-toggle::before,
        .finus-sidebar-toggle::after {
            content: none !important;
            display: none !important;
        }
        .hamburger .line,
        .sidebar-toggle .line,
        .finus-sidebar-toggle .line {
            display: block !important;
            width: 23px !important;
            height: 2px !important;
            margin: 0 !important;
            border-radius: 999px;
            background: #334155 !important;
            opacity: 1 !important;
            transform: none !important;
            transition:
                width .2s ease,
                background .2s ease !important;
        }
        .hamburger:hover .line:nth-child(2),
        .sidebar-toggle:hover .line:nth-child(2),
        .finus-sidebar-toggle:hover .line:nth-child(2) {
            width: 17px !important;
            background: var(--finus-green) !important;
        }
        /* =====================================================
           CONTENT
        ===================================================== */
        .content-wrap {
            min-height: 100vh;
            margin-left: var(--finus-sidebar-width) !important;
            padding-top: calc(var(--finus-header-height) + 18px) !important;
            transition: margin-left .25s ease !important;
        }
        body.sidebar-collapsed .content-wrap {
            margin-left: 0 !important;
        }
        .content-wrap .main {
            padding: 18px 24px 30px !important;
        }
        .finus-content-container {
            width: 100%;
            max-width: 1800px;
            margin: 0 auto;
        }
        /* =====================================================
           PAGE HEADING
        ===================================================== */
        .finus-page-heading {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
            padding: 18px 21px;
            border: 1px solid rgba(14, 84, 35, .09);
            border-radius: 17px;
            background: rgba(255, 255, 255, .88);
            box-shadow: var(--finus-shadow-sm);
            backdrop-filter: blur(8px);
        }
        .finus-page-heading::after {
            content: "";
            position: absolute;
            top: -55px;
            right: -35px;
            width: 145px;
            height: 145px;
            border-radius: 50%;
            background: rgba(34, 186, 81, .055);
            pointer-events: none;
        }
        .finus-page-heading-left {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }
        .finus-page-heading-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 43px;
            min-width: 43px;
            height: 43px;
            border-radius: 12px;
            background: var(--finus-green-soft);
            color: var(--finus-green);
            font-size: 16px;
        }
        .finus-page-kicker {
            margin-bottom: 2px;
            color: var(--finus-green);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        /*
         * Sapaan pada header dashboard yang memiliki
         * latar belakang hijau.
         */
        .finus-dashboard-greeting {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, .82);
            font-size: 11px;
            font-weight: 800;
            line-height: 1.4;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .finus-dashboard-greeting::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #A7FFAF;
            box-shadow: 0 0 0 4px rgba(167, 255, 175, .12);
        }
        .finus-page-title {
            margin: 0;
            color: var(--finus-text);
            font-size: 19px;
            font-weight: 800;
            line-height: 1.3;
        }
        .finus-page-subtitle {
            margin: 4px 0 0;
            color: var(--finus-muted);
            font-size: 12.5px;
            line-height: 1.45;
        }
        .finus-breadcrumb {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 7px;
            margin: 0;
            padding: 0;
            background: transparent;
            list-style: none;
        }
        .finus-breadcrumb li,
        .finus-breadcrumb a {
            color: var(--finus-muted);
            font-size: 12px;
            font-weight: 600;
        }
        .finus-breadcrumb a:hover {
            color: var(--finus-green);
            text-decoration: none;
        }
        .finus-breadcrumb li + li::before {
            content: "/";
            margin-right: 7px;
            color: #CBD5E1;
        }
        .finus-breadcrumb .active {
            color: var(--finus-green-dark);
            font-weight: 800;
        }
        /* =====================================================
           FLASH MESSAGE
        ===================================================== */
        .finus-flash {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
            padding: 14px 46px 14px 15px;
            border: 1px solid;
            border-radius: 14px;
            font-size: 13px;
            line-height: 1.55;
            box-shadow: var(--finus-shadow-sm);
        }
        .finus-flash-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 33px;
            min-width: 33px;
            height: 33px;
            border-radius: 9px;
        }
        .finus-flash-content {
            min-width: 0;
            flex: 1;
            padding-top: 5px;
        }
        .finus-flash-success {
            border-color: #BDE5C7;
            background: #F0FAF3;
            color: #166534;
        }
        .finus-flash-success .finus-flash-icon {
            background: #D9F4E0;
            color: #16A34A;
        }
        .finus-flash-warning {
            border-color: #F4D99B;
            background: #FFF9EA;
            color: #92400E;
        }
        .finus-flash-warning .finus-flash-icon {
            background: #FDECC8;
            color: #D97706;
        }
        .finus-flash-danger {
            border-color: #F5C2C7;
            background: #FFF4F4;
            color: #991B1B;
        }
        .finus-flash-danger .finus-flash-icon {
            background: #FEE2E2;
            color: #DC2626;
        }
        .finus-flash-info {
            border-color: #BFDBFE;
            background: #EFF6FF;
            color: #1E40AF;
        }
        .finus-flash-info .finus-flash-icon {
            background: #DBEAFE;
            color: #2563EB;
        }
        .finus-flash .close {
            position: absolute;
            top: 50%;
            right: 13px;

            padding: 5px;
            color: currentColor;
            font-size: 18px;
            line-height: 1;
            opacity: .55;
            transform: translateY(-50%);
        }
        .finus-flash .close:hover {
            opacity: 1;
        }
        .finus-flash ul {
            margin: 5px 0 0;
            padding-left: 18px;
        }
        /* =====================================================
            DROPDOWN FALLBACK
        ===================================================== */
        .dropdown {
            position: relative;
        }

        .dropdown-menu.show,
        .dropdown.open > .dropdown-menu {
            display: block !important;
        }

        .header .dropdown-menu.show,
        .header .dropdown.open > .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
            z-index: 3000 !important;
            min-width: 180px;
            margin-top: 10px;
        }
        /* =====================================================
           RESPONSIVE
        ===================================================== */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%) !important;
                box-shadow: 18px 0 45px rgba(0, 31, 11, .28) !important;
            }
            body.sidebar-open .sidebar {
                transform: translateX(0) !important;
            }
            .header,
            body.sidebar-collapsed .header {
                right: 0 !important;
                left: 0 !important;
                width: 100% !important;
                padding: 0 18px !important;
            }
            .content-wrap,
            body.sidebar-collapsed .content-wrap {
                margin-left: 0 !important;
                padding-top: calc(var(--finus-header-height) + 14px) !important;
            }
            .content-wrap .main {
                padding: 16px !important;
            }
            .finus-page-heading {
                align-items: flex-start;
                flex-direction: column;
            }
            .finus-breadcrumb {
                justify-content: flex-start;
            }
        }
        @media (min-width: 992px) {
            .finus-sidebar-backdrop {
                display: none !important;
            }
        }
        @media (max-width: 575.98px) {
            :root {
                --finus-header-height: 68px;
            }
            .header {
                padding: 0 12px !important;
            }
            .content-wrap .main {
                padding: 12px !important;
            }
            .finus-page-heading {
                margin-bottom: 15px;
                padding: 15px;
                border-radius: 14px;
            }
            .finus-page-heading-icon {
                width: 39px;
                min-width: 39px;
                height: 39px;
            }
            .finus-page-title {
                font-size: 17px;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
            }
        }
    </style>
</head>
@php
    $currentUser = auth()->user();
    $roleClass = $currentUser ? 'role-' . $currentUser->role : 'role-guest';
@endphp
<body class="finus-layout {{ $roleClass }}">
    <a href="#main-content" class="finus-skip-link">
        Lewati ke konten utama
    </a>
    @auth
        @if($currentUser->isAdmin())
            @include('layouts.sidebar-admin')
        @elseif($currentUser->isPegawai())
            @include('layouts.sidebar-staff')
        @elseif($currentUser->isJamaah())
            @include('layouts.sidebar-jamaah')
        @endif
        @include('layouts.navigation')
        <div class="finus-sidebar-backdrop" id="finusSidebarBackdrop" aria-hidden="true"></div>
    @endauth
    <div class="content-wrap">
        <main class="main" id="main-content" tabindex="-1">
            <div class="container-fluid finus-content-container">
                @auth
                    @php
                        $hidePageHeader =
                            request()->routeIs(
                                'dashboard',
                                'pegawai.dashboard',
                                'jamaah.dashboard'
                            )
                            || trim($__env->yieldContent('hide-page-header')) !== '';
                        $pageTitle = trim($__env->yieldContent('page-title'));
                        if ($pageTitle === '') {
                            $pageTitle = $currentUser->isAdmin()
                                ? 'Panel Admin'
                                : ($currentUser->isPegawai()
                                    ? 'Panel Pegawai'
                                    : 'Panel Jamaah');
                        }
                        $pageSubtitle = trim($__env->yieldContent('page-subtitle'));
                        if ($pageSubtitle === '') {
                            $pageSubtitle = $currentUser->isAdmin()
                                ? 'Kelola keuangan dan operasional masjid melalui FINUS.'
                                : ($currentUser->isPegawai()
                                    ? 'Panel ' . ($currentUser->pegawai?->jabatan ?? 'Pegawai') . ' FINUS.'
                                    : 'Akses layanan dan informasi jamaah FINUS.');
                        }
                        $breadcrumbCurrent =
                            trim($__env->yieldContent('breadcrumb-current'))
                            ?: $pageTitle;
                        $dashboardUrl = $currentUser->isAdmin()
                            ? route('dashboard')
                            : ($currentUser->isPegawai()
                                ? route('pegawai.dashboard')
                                : route('jamaah.dashboard'));
                        $dashboardLabel = $currentUser->isAdmin()
                            ? 'Dashboard Admin'
                            : ($currentUser->isPegawai()
                                ? 'Dashboard Pegawai'
                                : 'Dashboard Jamaah');
                    @endphp
                    @unless($hidePageHeader)
                        <section class="finus-page-heading" aria-labelledby="finusPageTitle">
                            <div class="finus-page-heading-left">
                                <div class="finus-page-heading-icon" aria-hidden="true">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <div>
                                    <div class="finus-page-kicker" data-finus-greeting data-user-name="{{ $currentUser->name }}">
                                        Halo, {{ $currentUser->name }}
                                    </div>
                                    <h1 class="finus-page-title" id="finusPageTitle">
                                        {{ $pageTitle }}
                                    </h1>
                                    <p class="finus-page-subtitle">
                                        {{ $pageSubtitle }}
                                    </p>
                                </div>
                            </div>
                            <nav aria-label="Breadcrumb">
                                <ol class="finus-breadcrumb">
                                    <li>
                                        <a href="{{ $dashboardUrl }}">
                                            {{ $dashboardLabel }}
                                        </a>
                                    </li>
                                    <li class="active" aria-current="page">
                                        {{ $breadcrumbCurrent }}
                                    </li>
                                </ol>
                            </nav>
                        </section>
                    @endunless
                @endauth
                @php
                    $flashMessages = [
                        'success' => [
                            'class' => 'finus-flash-success',
                            'icon' => 'fa-solid fa-circle-check',
                            'autoDismiss' => true,
                        ],
                        'warning' => [
                            'class' => 'finus-flash-warning',
                            'icon' => 'fa-solid fa-triangle-exclamation',
                            'autoDismiss' => false,
                        ],
                        'error' => [
                            'class' => 'finus-flash-danger',
                            'icon' => 'fa-solid fa-circle-xmark',
                            'autoDismiss' => false,
                        ],
                        'info' => [
                            'class' => 'finus-flash-info',
                            'icon' => 'fa-solid fa-circle-info',
                            'autoDismiss' => true,
                        ],
                    ];
                @endphp
                @foreach($flashMessages as $flashKey => $flashConfig)
                    @if(session($flashKey))
                        <div class="alert alert-dismissible fade show finus-flash {{ $flashConfig['class'] }}" role="alert" @if($flashConfig['autoDismiss']) data-auto-dismiss="true" @endif>
                            <span class="finus-flash-icon" aria-hidden="true">
                                <i class="{{ $flashConfig['icon'] }}"></i>
                            </span>
                            <div class="finus-flash-content">
                                {{ session($flashKey) }}
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup notifikasi">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                @endforeach
                @if(
                    $errors->any()
                    && trim($__env->yieldContent('hide-global-errors')) === ''
                )
                    <div class="alert alert-dismissible fade show finus-flash finus-flash-danger" role="alert">
                        <span class="finus-flash-icon" aria-hidden="true">
                            <i class="fa-solid fa-circle-exclamation"></i>
                        </span>
                        <div class="finus-flash-content">
                            <strong>Data belum dapat diproses.</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup notifikasi">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    @auth
        <form id="idle-logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
            @csrf
        </form>
    @endauth
    @stack('modals')
    {{-- Urutan JS: jQuery -> Bootstrap -> plugin/template -> script halaman --}}
    <script src="{{ asset('assets/js/lib/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/lib/jquery.nanoscroller.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/menubar/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/lib/preloader/pace.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
    <script>
        (() => {
            const alerts = document.querySelectorAll(
                '.finus-flash[data-auto-dismiss="true"]'
            );
            alerts.forEach(alertElement => {
                window.setTimeout(() => {
                    if (
                        window.jQuery
                        && typeof window.jQuery(alertElement).alert === 'function'
                    ) {
                        window.jQuery(alertElement).alert('close');
                    } else {
                        alertElement.remove();
                    }
                }, 6000);
            });
        })();
    </script>
    @auth
        <script>
            (() => {
                const userName = @json($currentUser->name);
                /*
                 * Dashboard utama memakai header miliknya sendiri dan
                 * menyembunyikan page heading dari layout. Karena itu,
                 * sapaan disisipkan otomatis ke header dashboard.
                 */
                const dashboardHeadingText = document.querySelector(
                    '.finus-dashboard-heading > div:last-child'
                );
                if (
                    dashboardHeadingText
                    && !dashboardHeadingText.querySelector(
                        '[data-finus-greeting]'
                    )
                ) {
                    const dashboardGreeting =
                        document.createElement('div');
                    dashboardGreeting.className =
                        'finus-dashboard-greeting';
                    dashboardGreeting.setAttribute(
                        'data-finus-greeting',
                        ''
                    );
                    dashboardGreeting.setAttribute(
                        'data-user-name',
                        userName
                    );
                    dashboardHeadingText.prepend(
                        dashboardGreeting
                    );
                }
                const getGreeting = hour => {
                    if (hour >= 4 && hour < 12) {
                        return 'Good morning';
                    }
                    if (hour >= 12 && hour < 17) {
                        return 'Good afternoon';
                    }
                    if (hour >= 17 && hour < 21) {
                        return 'Good evening';
                    }
                    return 'Good night';
                };
                const updateGreetings = () => {
                    const greeting = getGreeting(
                        new Date().getHours()
                    );
                    document
                        .querySelectorAll('[data-finus-greeting]')
                        .forEach(element => {
                            const name =
                                element.dataset.userName
                                || userName;
                            element.textContent =
                                `${greeting}, ${name}`;
                        });
                };
                updateGreetings();
                /*
                 * Diperbarui setiap satu menit agar sapaan otomatis
                 * berubah ketika halaman tetap terbuka melewati
                 * pergantian waktu.
                 */
                window.setInterval(
                    updateGreetings,
                    60 * 1000
                );
            })();
        </script>
        <script>
            (() => {
                const body = document.body;
                const mobileQuery = window.matchMedia('(max-width: 991.98px)');
                const sidebar = document.querySelector('.sidebar');
                const backdrop = document.getElementById('finusSidebarBackdrop');
                const toggles = Array.from(document.querySelectorAll('.sidebar-toggle, .hamburger'));
                const storageKey = 'finus:sidebar-collapsed:{{ $currentUser->id }}';
                if (!sidebar || toggles.length === 0) {
                    return;
                }
                const normalizeToggle = toggle => {
                    toggle.classList.add(
                        'finus-sidebar-toggle',
                        'hamburger'
                    );
                    toggle.setAttribute('aria-controls', 'finusSidebar');
                    toggle.setAttribute('aria-label', 'Buka atau tutup sidebar');
                    if (toggle.tagName === 'BUTTON') {
                        toggle.setAttribute('type', 'button');
                    }
                    toggle.innerHTML = `
                        <span class="line" aria-hidden="true"></span>
                        <span class="line" aria-hidden="true"></span>
                        <span class="line" aria-hidden="true"></span>
                    `;
                };
                sidebar.id = sidebar.id || 'finusSidebar';
                toggles.forEach(normalizeToggle);
                const storedCollapsed = () => localStorage.getItem(storageKey) === '1';
                const syncAccessibility = () => {
                    const sidebarVisible = mobileQuery.matches
                        ? body.classList.contains('sidebar-open')
                        : !body.classList.contains('sidebar-collapsed');
                    toggles.forEach(toggle => {
                        toggle.setAttribute(
                            'aria-expanded',
                            sidebarVisible ? 'true' : 'false'
                        );
                    });
                    if (backdrop) {
                        backdrop.setAttribute(
                            'aria-hidden',
                            body.classList.contains('sidebar-open')
                                ? 'false'
                                : 'true'
                        );
                    }
                };
                const closeMobileSidebar = () => {
                    body.classList.remove(
                        'sidebar-open',
                        'finus-sidebar-lock'
                    );
                    syncAccessibility();
                };
                const applyResponsiveState = () => {
                    if (mobileQuery.matches) {
                        body.classList.remove('sidebar-collapsed');
                        closeMobileSidebar();
                    } else {
                        body.classList.remove(
                            'sidebar-open',
                            'finus-sidebar-lock'
                        );
                        body.classList.toggle(
                            'sidebar-collapsed',
                            storedCollapsed()
                        );
                        syncAccessibility();
                    }
                };
                const toggleSidebar = () => {
                    if (mobileQuery.matches) {
                        const willOpen =
                            !body.classList.contains('sidebar-open');
                        body.classList.toggle('sidebar-open', willOpen);
                        body.classList.toggle(
                            'finus-sidebar-lock',
                            willOpen
                        );
                    } else {
                        const willCollapse =
                            !body.classList.contains('sidebar-collapsed');
                        body.classList.toggle(
                            'sidebar-collapsed',
                            willCollapse
                        );
                        localStorage.setItem(
                            storageKey,
                            willCollapse ? '1' : '0'
                        );
                    }
                    syncAccessibility();
                };
                document.addEventListener(
                    'click',
                    event => {
                        const clickedToggle = event.target.closest(
                            '.sidebar-toggle, .hamburger'
                        );
                        if (!clickedToggle) {
                            return;
                        }
                        event.preventDefault();
                        event.stopPropagation();
                        toggleSidebar();
                    },
                    true
                );
                if (backdrop) {
                    backdrop.addEventListener(
                        'click',
                        closeMobileSidebar
                    );
                }
                sidebar.addEventListener('click', event => {
                    if (!mobileQuery.matches) {
                        return;
                    }
                    const link = event.target.closest('a[href]');
                    if (
                        link
                        && link.getAttribute('href')
                        && link.getAttribute('href') !== '#'
                    ) {
                        closeMobileSidebar();
                    }
                });
                document.addEventListener('keydown', event => {
                    if (
                        event.key === 'Escape'
                        && body.classList.contains('sidebar-open')
                    ) {
                        closeMobileSidebar();

                        if (toggles[0]) {
                            toggles[0].focus();
                        }
                    }
                });
                if (typeof mobileQuery.addEventListener === 'function') {
                    mobileQuery.addEventListener(
                        'change',
                        applyResponsiveState
                    );
                } else {
                    mobileQuery.addListener(applyResponsiveState);
                }
                applyResponsiveState();
            })();
        </script>
        <script>
            (() => {
                const idleTimeout = 15 * 60 * 1000;
                const heartbeatInterval = 4 * 60 * 1000;
                const activityWriteInterval = 5000;
                const activityKey = 'finus:last-activity:{{ $currentUser->id }}';
                const logoutForm = document.getElementById('idle-logout-form');
                const csrfToken = document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content;
                let lastActivityWrite = 0;
                let loggingOut = false;
                const getLastActivity = () => {
                    const storedValue = Number(
                        localStorage.getItem(activityKey)
                    );
                    return Number.isFinite(storedValue) && storedValue > 0
                        ? storedValue
                        : Date.now();
                };
                const recordActivity = (force = false) => {
                    const now = Date.now();
                    if (
                        force
                        || now - lastActivityWrite >= activityWriteInterval
                    ) {
                        localStorage.setItem(
                            activityKey,
                            String(now)
                        );
                        lastActivityWrite = now;
                    }
                };
                const logoutBecauseIdle = () => {
                    if (loggingOut || !logoutForm) {
                        return;
                    }
                    loggingOut = true;
                    logoutForm.submit();
                };
                const checkIdle = () => {
                    const isIdle = Date.now() - getLastActivity() >= idleTimeout;
                    if (isIdle) {
                        logoutBecauseIdle();
                    }
                    return isIdle;
                };
                const sendHeartbeat = async () => {
                    if (
                        loggingOut
                        || document.hidden
                        || checkIdle()
                        || !csrfToken
                    ) {
                        return;
                    }
                    try {
                        const response = await fetch(
                            '{{ route('session.heartbeat') }}',
                            {
                                method: 'POST',
                                credentials: 'same-origin',
                                keepalive: true,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                            }
                        );
                        if (
                            response.status === 401
                            || response.status === 419
                            || response.redirected
                        ) {
                            window.location.reload();
                        }
                    } catch (error) {
                        /*
                         * Gangguan jaringan sementara tidak langsung
                         * mengeluarkan pengguna. Pemeriksaan idle tetap aktif.
                         */
                    }
                };
                const activityEvents = [
                    'pointerdown',
                    'keydown',
                    'mousemove',
                    'scroll',
                    'touchstart',
                ];
                activityEvents.forEach(eventName => {
                    window.addEventListener(
                        eventName,
                        () => recordActivity(),
                        { passive: true }
                    );
                });
                window.addEventListener('storage', event => {
                    if (event.key === activityKey) {
                        checkIdle();
                    }
                });
                document.addEventListener(
                    'visibilitychange',
                    () => {
                        if (!document.hidden) {
                            if (!checkIdle()) {
                                recordActivity(true);
                                sendHeartbeat();
                            }
                        }
                    }
                );
                recordActivity(true);
                window.setInterval(checkIdle, 15000);
                window.setInterval(
                    sendHeartbeat,
                    heartbeatInterval
                );
            })();
        </script>
    @endauth
    <script>
    (() => {
        const closeAllDropdowns = exceptMenu => {
            document
                .querySelectorAll('.dropdown-menu.show')
                .forEach(menu => {
                    if (exceptMenu && menu === exceptMenu) {
                        return;
                    }

                    menu.classList.remove('show');
                    menu.style.display = '';
                    menu.closest('.dropdown')?.classList.remove('open');
                });
            };

            document.addEventListener('click', event => {
                const toggle = event.target.closest(
                    '.dropdown-toggle, [data-toggle="dropdown"], [data-bs-toggle="dropdown"]'
                );

                if (!toggle) {
                    closeAllDropdowns(null);
                    return;
                }

                const parent = toggle.closest('.dropdown');
                const menu = parent?.querySelector('.dropdown-menu');

                if (!parent || !menu) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                const willOpen = !menu.classList.contains('show');

                closeAllDropdowns(menu);

                parent.classList.toggle('open', willOpen);
                menu.classList.toggle('show', willOpen);
                menu.style.display = willOpen ? 'block' : '';

                toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    closeAllDropdowns(null);
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0E5423">
    <meta name="color-scheme" content="light dark">
    <script>
        (() => {
            const storageKey = 'finus:appearance';
            let theme = 'light';

            try {
                theme = localStorage.getItem(storageKey) === 'dark'
                    ? 'dark'
                    : 'light';
            } catch (_) {
                theme = 'light';
            }

            document.documentElement.dataset.finusTheme = theme;
            document.documentElement.style.setProperty(
                'color-scheme',
                theme === 'dark' ? 'dark' : 'only light',
                'important'
            );

const themeColorMeta = document.querySelector(
                'meta[name="theme-color"]'
            );
            if (themeColorMeta) {
                themeColorMeta.setAttribute(
                    'content',
                    theme === 'dark' ? '#08150D' : '#0E5423'
                );
            }
        })();
    </script>
    <title>
        @hasSection('title')
            @yield('title') | FINUS
        @else
            FINUS | Keuangan Masjid PUSDAI
        @endif
    </title>
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=20">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?v=20">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
        a, button, input, select, textarea {
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
    
        /* =====================================================
           FINUS LAYOUT UPGRADE — CONSISTENT, RESPONSIVE, ACCESSIBLE
        ===================================================== */
        :root {
            --finus-sidebar-width: 264px;
            --finus-header-height: 70px;
        }
        body.finus-layout {
            font-family: "Nunito", sans-serif;
            background:
                radial-gradient(circle at 96% 3%, rgba(34,186,81,.065), transparent 23rem),
                linear-gradient(180deg, #F6FAF7 0%, #F2F7F3 100%);
        }
        body.finus-layout .header {
            background: linear-gradient(to right, #0FB442 0%, #1AAF48 39%, #118635 75%, #004716 100%) !important;
            border-bottom: 1px solid rgba(255,255,255,.18) !important;
            box-shadow: 0 10px 28px rgba(0,71,22,.20) !important;
        }
        .finus-content-container {
            width: 100%;
            max-width: 1580px;
            margin-inline: auto;
            padding: clamp(18px, 2.2vw, 30px) !important;
        }
        .finus-page-heading {
            border: 1px solid rgba(23,155,64,.10);
            border-radius: 18px;
            background: rgba(255,255,255,.92);
            box-shadow: 0 12px 30px rgba(15,23,42,.06);
            backdrop-filter: blur(10px);
        }
        .finus-page-heading-icon {
            background: linear-gradient(135deg, #EAF8EE, #DDF4E3) !important;
            color: #179B40 !important;
        }
        .content-wrap,
        .main {
            min-width: 0;
        }
        .main a:focus-visible,
        .main button:focus-visible,
        .main input:focus-visible,
        .main select:focus-visible,
        .main textarea:focus-visible {
            outline: 3px solid rgba(23,155,64,.22);
            outline-offset: 2px;
        }
        .finus-global-reveal {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity .52s ease, transform .58s cubic-bezier(.2,.72,.2,1);
        }
        .finus-global-reveal.is-visible {
            opacity: 1;
            transform: none;
        }
        ::selection {
            background: rgba(126,255,135,.45);
            color: #033D18;
        }
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(23,155,64,.55) rgba(225,234,228,.55);
        }
        *::-webkit-scrollbar { width: 9px; height: 9px; }
        *::-webkit-scrollbar-track { background: rgba(225,234,228,.55); }
        *::-webkit-scrollbar-thumb { border: 2px solid transparent; border-radius: 999px; background: rgba(23,155,64,.58); background-clip: padding-box; }
        @media (max-width: 991.98px) {
            :root { --finus-header-height: 66px; }
            .finus-content-container { padding: 16px 14px 24px !important; }
        }
        @media (max-width: 575.98px) {
            .finus-content-container { padding-inline: 12px !important; }
            .finus-page-heading { border-radius: 15px; }
        }

    
        /* =====================================================
           FINUS APPEARANCE
           Default selalu LIGHT, tidak mengikuti dark mode device.
           DARK hanya aktif jika pengguna memilihnya di FINUS.
        ===================================================== */
        html[data-finus-theme="light"] { color-scheme: only light !important; }

        html[data-finus-theme="dark"] {
            color-scheme: dark;

            --finus-page-bg: #0B130E;
            --finus-surface: #121D16;
            --finus-text: #EAF4ED;
            --finus-muted: #A8B7AD;
            --finus-border: #2B3E30;
            --finus-shadow-sm: 0 3px 14px rgba(0, 0, 0, .25);
            --finus-shadow-md: 0 18px 42px rgba(0, 0, 0, .32);
            --finus-green-soft: #16321F;
        }

        html[data-finus-theme="dark"] body.finus-layout {
            background:
                radial-gradient(circle at 95% 5%, rgba(34, 186, 81, .07), transparent 24rem),
                linear-gradient(180deg, #0B130E 0%, #0E1811 100%) !important;
            color: var(--finus-text) !important;
        }

        html[data-finus-theme="dark"] .content-wrap,
        html[data-finus-theme="dark"] .main,
        html[data-finus-theme="dark"] .finus-content-container {
            color: var(--finus-text);
        }

        html[data-finus-theme="dark"] .finus-page-heading,
        html[data-finus-theme="dark"] .card,
        html[data-finus-theme="dark"] .fmu-card,
        html[data-finus-theme="dark"] .fmu-stat,
        html[data-finus-theme="dark"] .fmu-side-note,
        html[data-finus-theme="dark"] .modal-content,
        html[data-finus-theme="dark"] .list-group-item {
            border-color: var(--finus-border) !important;
            background: #121D16 !important;
            color: var(--finus-text) !important;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .22) !important;
        }

        html[data-finus-theme="dark"] .finus-page-title,
        html[data-finus-theme="dark"] .fmu-card h1,
        html[data-finus-theme="dark"] .fmu-card h2,
        html[data-finus-theme="dark"] .fmu-card h3,
        html[data-finus-theme="dark"] .fmu-stat strong,
        html[data-finus-theme="dark"] .card-title,
        html[data-finus-theme="dark"] .modal-title {
            color: var(--finus-text) !important;
        }

        html[data-finus-theme="dark"] .finus-page-subtitle,
        html[data-finus-theme="dark"] .fmu-card p,
        html[data-finus-theme="dark"] .fmu-stat small,
        html[data-finus-theme="dark"] .text-muted {
            color: var(--finus-muted) !important;
        }

        html[data-finus-theme="dark"] .form-control,
        html[data-finus-theme="dark"] .custom-select,
        html[data-finus-theme="dark"] select,
        html[data-finus-theme="dark"] textarea,
        html[data-finus-theme="dark"] input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
        html[data-finus-theme="dark"] .fmu-control,
        html[data-finus-theme="dark"] .fmu-select,
        html[data-finus-theme="dark"] .fmu-textarea {
            border-color: #314736 !important;
            background: #0E1811 !important;
            color: #EDF7EF !important;
        }

        html[data-finus-theme="dark"] .form-control:focus,
        html[data-finus-theme="dark"] .custom-select:focus,
        html[data-finus-theme="dark"] select:focus,
        html[data-finus-theme="dark"] textarea:focus,
        html[data-finus-theme="dark"] input:focus,
        html[data-finus-theme="dark"] .fmu-control:focus,
        html[data-finus-theme="dark"] .fmu-select:focus,
        html[data-finus-theme="dark"] .fmu-textarea:focus {
            border-color: #22BA51 !important;
            background: #101C14 !important;
            color: #FFFFFF !important;
            box-shadow: 0 0 0 4px rgba(34, 186, 81, .13) !important;
        }

        html[data-finus-theme="dark"] input::placeholder,
        html[data-finus-theme="dark"] textarea::placeholder {
            color: #718379 !important;
            opacity: 1;
        }

        html[data-finus-theme="dark"] input[readonly],
        html[data-finus-theme="dark"] textarea[readonly],
        html[data-finus-theme="dark"] .form-control[readonly] {
            background: #142119 !important;
            color: #AFC0B4 !important;
        }

        html[data-finus-theme="dark"] table,
        html[data-finus-theme="dark"] .table {
            color: #E8F3EB !important;
        }

        html[data-finus-theme="dark"] .table th,
        html[data-finus-theme="dark"] .table td,
        html[data-finus-theme="dark"] table th,
        html[data-finus-theme="dark"] table td {
            border-color: #293B2E !important;
        }

        html[data-finus-theme="dark"] .table thead th,
        html[data-finus-theme="dark"] table thead th {
            background: #14221A !important;
            color: #DCEAE0 !important;
        }

        html[data-finus-theme="dark"] .table-striped tbody tr:nth-of-type(odd) {
            background: rgba(255, 255, 255, .018) !important;
        }

        html[data-finus-theme="dark"] .dropdown-divider,
        html[data-finus-theme="dark"] hr {
            border-color: #2A3C2F !important;
        }

        html[data-finus-theme="dark"] .pagination .page-link {
            border-color: #2B3F30 !important;
            background: #121D16 !important;
            color: #CFE0D3 !important;
        }

        html[data-finus-theme="dark"] .pagination .page-item.active .page-link {
            border-color: #179B40 !important;
            background: #179B40 !important;
            color: #FFFFFF !important;
        }

        html[data-finus-theme="dark"] .alert:not(.finus-flash) {
            filter: brightness(.86) saturate(.9);
        }

        html.finus-theme-changing *,
        html.finus-theme-changing *::before,
        html.finus-theme-changing *::after {
            transition:
                background-color .20s ease,
                border-color .20s ease,
                color .20s ease,
                box-shadow .20s ease !important;
        }

        @media (prefers-reduced-motion: reduce) {
            html.finus-theme-changing *,
            html.finus-theme-changing *::before,
            html.finus-theme-changing *::after {
                transition: none !important;
            }
        }

    

        /* FINUS memilih tema sendiri dan tidak mengikuti tema device. */
        html[data-finus-theme="light"] {
            color-scheme: light !important;
            color-scheme: only light !important;
        }

        html[data-finus-theme="dark"] {
            color-scheme: dark !important;
        }

        @media (prefers-color-scheme: dark) {
            html[data-finus-theme="light"] {
                color-scheme: light !important;
                color-scheme: only light !important;
            }
        }

    

        /* =====================================================
           FINUS MANUAL THEME — FINAL FORCE-LIGHT GUARD
           Mode device/OS tidak menentukan tampilan FINUS.
        ===================================================== */
        html[data-finus-theme="light"] {
            color-scheme: only light !important;
        }

        html[data-finus-theme="dark"] {
            color-scheme: dark !important;
        }

        @media (prefers-color-scheme: dark) {
            html[data-finus-theme="light"] {
                color-scheme: only light !important;
            }
        }

    

        /* =====================================================
           FINUS THEME + FORCE DARK COEXISTENCE
           -----------------------------------------------------
           - FINUS tetap memakai pilihan tema manual sendiri.
           - Meta/root menyatakan bahwa halaman memang menyediakan
             LIGHT dan DARK agar browser seperti Samsung Internet
             dapat memilih "Prefer Media Query Over Force Dark".
           - Jika FINUS sedang LIGHT, "only light" tetap digunakan
             sebagai pengaman tambahan untuk browser yang mendukungnya.
           ===================================================== */

        :root {
            color-scheme: light dark;
        }

        html[data-finus-theme="light"] {
            color-scheme: only light !important;
        }

        html[data-finus-theme="dark"] {
            color-scheme: dark !important;
        }

        html[data-finus-theme="light"] :where(
            input,
            textarea,
            select,
            button
        ) {
            color-scheme: only light !important;
        }

        html[data-finus-theme="dark"] :where(
            input,
            textarea,
            select,
            button
        ) {
            color-scheme: dark !important;
        }

        /*
         * Media query ini sengaja ADA walaupun FINUS tidak mengikuti
         * tema perangkat. Tujuannya memberi tahu browser bahwa situs
         * menyediakan penanganan dark-context sendiri.
         *
         * data-finus-theme tetap menjadi keputusan akhir:
         * - device dark + FINUS light  => tetap light
         * - device light + FINUS dark  => tetap dark
         */
        @media (prefers-color-scheme: dark) {
            html[data-finus-theme="light"] {
                color-scheme: only light !important;
            }

            html[data-finus-theme="dark"] {
                color-scheme: dark !important;
            }

            html[data-finus-theme="light"] :where(
                input,
                textarea,
                select,
                button
            ) {
                color-scheme: only light !important;
            }

            html[data-finus-theme="dark"] :where(
                input,
                textarea,
                select,
                button
            ) {
                color-scheme: dark !important;
            }
        }

        @media (prefers-color-scheme: light) {
            html[data-finus-theme="light"] {
                color-scheme: only light !important;
            }

            html[data-finus-theme="dark"] {
                color-scheme: dark !important;
            }
        }

    </style>
</head>
@php
    $routeMiddleware = collect(request()->route()?->gatherMiddleware() ?? []);
    $currentGuard = null;
    foreach (['admin', 'pegawai', 'jamaah'] as $guardName) {
        $usesGuard = $routeMiddleware->contains(function ($middleware) use ($guardName) {
            return is_string($middleware)
                && str_starts_with($middleware, 'auth:')
                && in_array($guardName, explode(',', substr($middleware, 5)), true);
        });
        if ($usesGuard) {
            $currentGuard = $guardName;
            break;
        }
    }
    if (! $currentGuard) {
        $currentGuard = match (true) {
            request()->routeIs('dashboard', 'admin.*') => 'admin',
            request()->routeIs('pegawai.*') => 'pegawai',
            request()->routeIs('jamaah.*') => 'jamaah',
            default => null,
        };
    }
    $currentUser = $currentGuard ? auth($currentGuard)->user() : null;
    $roleClass = $currentUser ? 'role-' . $currentUser->role : 'role-guest';
    $logoutRoute = $currentGuard
        ? route('logout.' . $currentGuard)
        : route('logout');
    $heartbeatRoute = $currentGuard
        ? route('session.heartbeat.' . $currentGuard)
        : null;
@endphp
<body class="finus-layout {{ $roleClass }}">
    <a href="#main-content" class="finus-skip-link">
        Lewati ke konten utama
    </a>
    @if($currentUser)
        @if($currentUser->isAdmin())
            @include('layouts.sidebar-admin')
        @elseif($currentUser->isPegawai())
            @include('layouts.sidebar-staff')
        @elseif($currentUser->isJamaah())
            @include('layouts.sidebar-jamaah')
        @endif
        @include('layouts.navigation')
        <div class="finus-sidebar-backdrop" id="finusSidebarBackdrop" aria-hidden="true"></div>
    @endif
    <div class="content-wrap">
        <main class="main" id="main-content" tabindex="-1">
            <div class="container-fluid finus-content-container">
                @if($currentUser)
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
                @endif
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
    @if($currentUser)
        <form id="idle-logout-form" method="POST" action="{{ $logoutRoute }}" class="d-none">
            @csrf
        </form>
    @endif
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
    @if($currentUser)
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
                    if (hour >= 4 && hour < 11) {
                        return 'Selamat pagi';
                    }
                    if (hour >= 11 && hour < 15) {
                        return 'Selamat siang';
                    }
                    if (hour >= 15 && hour < 18) {
                        return 'Selamat sore';
                    }
                    return 'Selamat malam';
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
                const activityKey = 'finus:last-activity:{{ $currentGuard }}:{{ $currentUser->id }}';
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
                            @json($heartbeatRoute),
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
    @endif
    <script>
        (() => {
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const candidates = Array.from(document.querySelectorAll(
                '.finus-page-heading, .finus-flash, .main > .container-fluid > section, .main > .container-fluid > .row, .main > .container-fluid > .card'
            ));
            candidates.forEach((element, index) => {
                element.classList.add('finus-global-reveal');
                element.style.transitionDelay = `${Math.min(index, 4) * 45}ms`;
            });
            if (reduceMotion || !('IntersectionObserver' in window)) {
                candidates.forEach(element => element.classList.add('is-visible'));
                return;
            }
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                });
            }, { threshold: .08 });
            candidates.forEach(element => observer.observe(element));
        })();
    </script>

    <script>
        (() => {
            const storageKey = 'finus:appearance';
            const root = document.documentElement;

            const readTheme = () => {
                try {
                    return localStorage.getItem(storageKey) === 'dark'
                        ? 'dark'
                        : 'light';
                } catch (_) {
                    return root.dataset.finusTheme === 'dark'
                        ? 'dark'
                        : 'light';
                }
            };

            const updateThemeControls = theme => {
                const dark = theme === 'dark';

                document
                    .querySelectorAll('[data-finus-theme-toggle]')
                    .forEach(button => {
                        button.setAttribute(
                            'aria-pressed',
                            dark ? 'true' : 'false'
                        );

                        button.setAttribute(
                            'title',
                            dark
                                ? 'Ubah ke mode terang'
                                : 'Ubah ke mode gelap'
                        );

                        const label = button.querySelector(
                            '[data-finus-theme-label]'
                        );

                        if (label) {
                            label.textContent = dark
                                ? 'Mode Gelap'
                                : 'Mode Terang';
                        }

                        const icon = button.querySelector(
                            '[data-finus-theme-icon]'
                        );

                        if (icon) {
                            icon.className = dark
                                ? 'fa-solid fa-moon'
                                : 'fa-solid fa-sun';
                        }
                    });
            };

            const applyTheme = (theme, persist = true) => {
                const normalized = theme === 'dark' ? 'dark' : 'light';

                root.classList.add('finus-theme-changing');
                root.dataset.finusTheme = normalized;
                root.style.setProperty(
                'color-scheme',
                normalized === 'dark' ? 'dark' : 'only light',
                'important'
            );

const themeColorMeta = document.querySelector(
                    'meta[name="theme-color"]'
                );

                if (themeColorMeta) {
                    themeColorMeta.setAttribute(
                        'content',
                        normalized === 'dark'
                            ? '#08150D'
                            : '#0E5423'
                    );
                }

                if (persist) {
                    try {
                        localStorage.setItem(
                            storageKey,
                            normalized
                        );
                    } catch (_) {}
                }

                updateThemeControls(normalized);

                window.setTimeout(() => {
                    root.classList.remove('finus-theme-changing');
                }, 230);
            };

            updateThemeControls(readTheme());

            document.addEventListener('click', event => {
                const button = event.target.closest(
                    '[data-finus-theme-toggle]'
                );

                if (!button) {
                    return;
                }

                event.preventDefault();

                applyTheme(
                    readTheme() === 'dark'
                        ? 'light'
                        : 'dark'
                );
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
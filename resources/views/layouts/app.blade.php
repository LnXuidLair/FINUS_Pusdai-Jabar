<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FINUS | Keuangan Masjid PUSDAI</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}?v=5">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}?v=5">
    <link href="{{ asset('assets/css/lib/weather-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/menubar/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lib/helper.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom-style.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css">

    <style>
        :root {
            --sidebar-width: 270px;
            --header-height: 74px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            min-height: 100%;
            background: #f4f6f9;
            color: #334155;
            overflow-x: hidden;
        }

        body {
            font-family: inherit;
        }

        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            width: var(--sidebar-width) !important;
            min-width: var(--sidebar-width) !important;
            max-width: var(--sidebar-width) !important;
            z-index: 1050 !important;
            overflow-y: auto;
            overflow-x: hidden;
            transform: translateX(0);
            transition: transform .25s ease;
        }

        .sidebar .nano,
        .sidebar .nano-content {
            min-height: 100%;
        }

        .sidebar ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sidebar a {
            text-decoration: none;
        }

        .sidebar .logo {
            min-height: 145px;
            padding: 24px 18px 20px !important;
            display: flex !important;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .sidebar .logo img {
            max-width: 120px !important;
            max-height: 84px !important;
            object-fit: contain;
        }

        .header {
            position: fixed !important;
            top: 0 !important;
            left: var(--sidebar-width) !important;
            right: 0 !important;
            width: auto !important;
            max-width: none !important;
            height: var(--header-height) !important;
            min-height: var(--header-height) !important;
            margin: 0 !important;
            padding: 0 28px !important;
            z-index: 1040 !important;
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
            box-shadow: 0 2px 12px rgba(15, 23, 42, .06) !important;
            display: flex !important;
            align-items: center !important;
            transition: left .25s ease;
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
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .content-wrap {
            min-height: 100vh;
            margin-left: var(--sidebar-width) !important;
            padding-top: calc(var(--header-height) + 18px) !important;
            transition: margin-left .25s ease;
        }

        .content-wrap .main {
            padding: 18px 24px 24px !important;
        }

        .hamburger,
        .sidebar-toggle,
        .finus-sidebar-toggle {
            width: 42px !important;
            height: 42px !important;
            padding: 0 !important;
            border: none !important;
            outline: none !important;
            background: transparent !important;
            color: #1f2937 !important;
            display: inline-flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer;
            gap: 5px;
        }

        .hamburger:hover,
        .sidebar-toggle:hover,
        .finus-sidebar-toggle:hover {
            background: #f1f5f9 !important;
            border-radius: 10px;
        }

        .hamburger:focus,
        .sidebar-toggle:focus,
        .finus-sidebar-toggle:focus {
            outline: none !important;
            box-shadow: none !important;
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
            width: 24px !important;
            height: 2px !important;
            margin: 0 !important;
            background: #334155 !important;
            border-radius: 999px;
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }

        .header-icon {
            min-height: 34px;
        }

        body.sidebar-collapsed .sidebar {
            transform: translateX(-100%) !important;
        }

        body.sidebar-collapsed .header {
            left: 0 !important;
            width: auto !important;
        }

        body.sidebar-collapsed .content-wrap {
            margin-left: 0 !important;
        }

        .page-header,
        .page_header_2 {
            margin-bottom: 18px;
        }

        .page-title h1 {
            margin: 0;
        }

        .breadcrumb {
            background: transparent !important;
            padding: 0 !important;
            margin-bottom: 0 !important;
            justify-content: flex-end;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%) !important;
            }

            .header {
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                padding: 0 18px !important;
            }

            .content-wrap {
                margin-left: 0 !important;
                padding-top: calc(var(--header-height) + 14px) !important;
            }

            .content-wrap .main {
                padding: 16px !important;
            }

            body.sidebar-open .sidebar {
                transform: translateX(0) !important;
            }

            .breadcrumb {
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>
    @auth
        @if(auth()->user()->isAdmin())
            @include('layouts.sidebar-admin')
        @elseif(auth()->user()->isPegawai())
            @include('layouts.sidebar-staff')
        @elseif(auth()->user()->isJamaah())
            @include('layouts.sidebar-jamaah')
        @endif
    @endauth

    @include('layouts.navigation')

    <div class="content-wrap overflow-hidden">
        <div class="main">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 p-r-0 title-margin-right">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>
                                    Halo, {{ auth()->user()->name }}
                                    <br>
                                    <span>
                                        @if(auth()->user()->isAdmin())
                                            Selamat datang di Panel Admin FINUS
                                        @elseif(auth()->user()->isPegawai())
                                            Panel {{ auth()->user()->pegawai?->jabatan ?? 'Pegawai' }}
                                        @else
                                            Selamat datang di Panel Jamaah
                                        @endif
                                    </span>
                                </h1>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 p-l-0 title-margin-left">
                        <div class="page-header page_header_2">
                            <div class="page-title">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('dashboard') }}">Dashboard Admin</a>
                                        @elseif(auth()->user()->isPegawai())
                                            <a href="{{ route('pegawai.dashboard') }}">Dashboard Pegawai</a>
                                        @else
                                            <a href="{{ route('jamaah.dashboard') }}">Dashboard Jamaah</a>
                                        @endif
                                    </li>
                                    <li class="breadcrumb-item active">FINUS</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <form id="idle-logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
        @csrf
    </form>

    <script src="{{ asset('assets/js/lib/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/jquery.nanoscroller.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/menubar/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/lib/preloader/pace.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>

    <script>
    (() => {
        const toggle = document.querySelector('.sidebar-toggle, .hamburger');
        const sidebar = document.querySelector('.sidebar');
        const header = document.querySelector('.header');
        const content = document.querySelector('.content-wrap');

        if (!toggle) {
            return;
        }

        const sidebarWidth = 270;

        const normalizeToggleIcon = () => {
            toggle.classList.add('finus-sidebar-toggle', 'hamburger');

            toggle.querySelectorAll('i, svg').forEach(icon => {
                icon.remove();
            });

            toggle.innerHTML = `
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            `;

            toggle.querySelectorAll('.line').forEach(line => {
                line.style.transform = 'none';
                line.style.opacity = '1';
            });
        };

        const applyLayout = () => {
            const isMobile = window.innerWidth < 992;
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');

            if (isMobile) {
                if (header) {
                    header.style.left = '0';
                    header.style.right = '0';
                    header.style.width = '100%';
                }

                if (content) {
                    content.style.marginLeft = '0';
                }

                return;
            }

            if (isCollapsed) {
                if (sidebar) {
                    sidebar.style.transform = 'translateX(-100%)';
                }

                if (header) {
                    header.style.left = '0';
                    header.style.right = '0';
                    header.style.width = 'auto';
                }

                if (content) {
                    content.style.marginLeft = '0';
                }
            } else {
                if (sidebar) {
                    sidebar.style.transform = 'translateX(0)';
                }

                if (header) {
                    header.style.left = sidebarWidth + 'px';
                    header.style.right = '0';
                    header.style.width = 'auto';
                }

                if (content) {
                    content.style.marginLeft = sidebarWidth + 'px';
                }
            }
        };

        normalizeToggleIcon();
        applyLayout();

        document.addEventListener('click', event => {
            const clickedToggle = event.target.closest('.sidebar-toggle, .hamburger');

            if (!clickedToggle) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            if (window.innerWidth < 992) {
                document.body.classList.toggle('sidebar-open');
            } else {
                document.body.classList.toggle('sidebar-collapsed');
            }

            normalizeToggleIcon();
            applyLayout();
        }, true);

        document.addEventListener('click', event => {
            if (window.innerWidth >= 992) {
                return;
            }

            if (!document.body.classList.contains('sidebar-open')) {
                return;
            }

            if (
                event.target.closest('.sidebar') ||
                event.target.closest('.sidebar-toggle') ||
                event.target.closest('.hamburger')
            ) {
                return;
            }

            document.body.classList.remove('sidebar-open');
            normalizeToggleIcon();
            applyLayout();
        });

        window.addEventListener('resize', applyLayout);
    })();

    (() => {
        const timeout = 15 * 60 * 1000;
        const key = 'finus:last-activity:{{ auth()->id() }}';

        let lastWrite = 0;
        let loggingOut = false;

        const activity = () => Number(localStorage.getItem(key)) || Date.now();

        const record = () => {
            const now = Date.now();

            if (now - lastWrite > 5000) {
                localStorage.setItem(key, String(now));
                lastWrite = now;
            }
        };

        const check = () => {
            if (!loggingOut && Date.now() - activity() >= timeout) {
                loggingOut = true;
                document.getElementById('idle-logout-form').submit();
            }
        };

        ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach(name =>
            addEventListener(name, record, { passive: true })
        );

        addEventListener('storage', check);

        record();

        setInterval(check, 15000);

        setInterval(() => {
            if (Date.now() - activity() >= timeout) {
                return check();
            }

            fetch('{{ route('session.heartbeat') }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(response => {
                if (response.status === 401 || response.redirected) {
                    location.reload();
                }
            }).catch(() => {});
        }, 4 * 60 * 1000);
    })();
    </script>

    @stack('scripts')
</body>
</html>
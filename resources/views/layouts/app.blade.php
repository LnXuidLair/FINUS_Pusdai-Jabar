<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FINUS | Keuangan Masjid PUSDAI</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
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
        body {
            margin: 0;
            min-height: 100vh;
            background: #f4f6f9;
            color: #334155;
        }
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1040;
            width: 260px;
            overflow-y: auto;
            transition: transform .25s ease;
        }
        .sidebar .nano,
        .sidebar .nano-content {
            min-height: 100%;
        }
        .sidebar ul {
            list-style: none;
            margin: 0;
        }
        .sidebar a {
            text-decoration: none;
        }
        .header {
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            z-index: 1030;
            min-height: 64px;
            padding: 14px 18px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 12px rgba(15, 23, 42, .06);
            transition: left .25s ease;
        }
        .content-wrap {
            min-height: 100vh;
            margin-left: 260px;
            padding-top: 64px;
            transition: margin-left .25s ease;
        }
        .content-wrap .main {
            padding: 18px;
        }
        .hamburger {
            width: 34px;
            padding: 5px;
            cursor: pointer;
        }
        .hamburger .line {
            display: block;
            width: 24px;
            height: 2px;
            margin: 5px 0;
            background: #334155;
        }
        .header-icon {
            min-height: 34px;
        }
        body.sidebar-collapsed .sidebar {
            transform: translateX(-260px);
        }
        body.sidebar-collapsed .header {
            left: 0;
        }
        body.sidebar-collapsed .content-wrap {
            margin-left: 0;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-260px);
            }
            .header {
                left: 0;
            }
            .content-wrap {
                margin-left: 0;
            }
            body.sidebar-open .sidebar {
                transform: translateX(0);
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

                @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                @if(session('warning'))<div class="alert alert-warning">{{ session('warning') }}</div>@endif
                @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

                @yield('content')
            </div>
        </div>
    </div>

    <form id="idle-logout-form" method="POST" action="{{ route('logout') }}" class="d-none">@csrf</form>

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
        const toggle = document.querySelector('.sidebar-toggle');
        if (!toggle) return;

        toggle.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                document.body.classList.toggle('sidebar-open');
                return;
            }
            document.body.classList.toggle('sidebar-collapsed');
        });

        document.addEventListener('click', event => {
            if (window.innerWidth >= 992 || !document.body.classList.contains('sidebar-open')) return;
            if (event.target.closest('.sidebar') || event.target.closest('.sidebar-toggle')) return;
            document.body.classList.remove('sidebar-open');
        });
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
        ['click','keydown','mousemove','scroll','touchstart'].forEach(name =>
            addEventListener(name, record, { passive:true })
        );
        addEventListener('storage', check);
        record();
        setInterval(check, 15000);
        setInterval(() => {
            if (Date.now() - activity() >= timeout) return check();
            fetch('{{ route('session.heartbeat') }}', {
                method:'POST',
                credentials:'same-origin',
                headers:{
                    'Accept':'application/json',
                    'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(response => {
                if (response.status === 401 || response.redirected) location.reload();
            }).catch(() => {});
        }, 4 * 60 * 1000);
    })();
    </script>
    @stack('scripts')
</body>
</html>
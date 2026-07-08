<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FINUS')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}?v=5">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}?v=5">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }

        body {
            font-family: "Nunito", sans-serif;
        }

        .auth-field {
            width: 100%;
            border: 1px solid rgba(5, 86, 33, .32);
            border-radius: .65rem;
            background: rgba(255, 255, 255, .92);
            padding: .72rem .9rem;
            color: #064e24;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        .auth-field::placeholder {
            color: #64806d;
        }

        .auth-field:focus {
            border-color: #04732c;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(4, 115, 44, .13);
        }

        .auth-field[readonly] {
            background: rgba(240, 253, 244, .8);
            color: #376047;
        }

        .auth-button {
            display: inline-flex;
            width: 100%;
            align-items: center;
            justify-content: center;
            border-radius: .65rem;
            background: linear-gradient(90deg, #064e24, #0b8f3e);
            padding: .75rem 1rem;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 8px 18px rgba(4, 78, 36, .18);
            transition: transform .2s, filter .2s;
        }

        .auth-button:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .auth-link {
            color: #075d2a;
            font-weight: 800;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .auth-error {
            margin-top: .3rem;
            font-size: .78rem;
            font-weight: 700;
            color: #b91c1c;
        }
    </style>
</head>
<body class="min-h-screen bg-[#eef8f0] text-slate-800">
    <header class="relative z-30 flex min-h-[64px] items-center justify-between gap-4 bg-[linear-gradient(to_right,_#004716_0%,_#118635_39%,_#1AAF48_75%,_#0FB442_100%)] px-4 py-2 shadow-md sm:px-7">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center">
            <img src="{{ asset('assets/images/FINUS_login.png') }}"
                 alt="FINUS PUSDAI"
                 class="h-11 max-w-[190px] object-contain"
                 onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.src='{{ asset('assets/images/finara1.png') }}';}else{this.style.display='none';document.getElementById('auth-brand-fallback').classList.remove('hidden');}">
            <span id="auth-brand-fallback" class="hidden text-xl font-extrabold tracking-wide text-white">
                FINUS PUSDAI
            </span>
        </a>
        <h1 class="text-right font-serif text-base font-bold text-[#063d1a] drop-shadow-sm sm:absolute sm:left-1/2 sm:-translate-x-1/2 sm:text-2xl">
            @yield('header-title', 'FINUS')
        </h1>
        <a href="{{ route('home') }}"
           class="shrink-0 rounded-md bg-white/90 px-3 py-1.5 text-sm font-bold text-[#075d2a] shadow hover:bg-white">
            Beranda
        </a>
    </header>
    <main class="grid min-h-[calc(100vh-64px)] lg:grid-cols-[minmax(0,1.65fr)_minmax(360px,.75fr)]">
        <section class="relative hidden min-h-[560px] overflow-hidden bg-cover bg-center lg:block"
            style="background-image:linear-gradient(90deg,rgba(255,255,255,.12),rgba(255,255,255,.22)),url('{{ asset('assets/images/bg-welcome.png') }}')">
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(2,74,29,.13),rgba(255,255,255,.05),rgba(2,74,29,.16))]"></div>
            <img src="{{ asset('assets/images/pusdai_batik.png') }}"
                 alt="Batik PUSDAI"
                 class="pointer-events-none absolute left-0 top-0 z-[2] h-full w-[260px] object-cover opacity-45 mix-blend-multiply"
                 onerror="this.style.display='none'">
            <div class="relative z-10 flex h-full items-center px-[7%]">
                <div class="relative max-w-xl text-[#064e24] drop-shadow-[0_1px_0_rgba(255,255,255,.8)]">
                    <p class="font-['Lobster',cursive] text-5xl leading-tight xl:text-6xl">
                        @yield('hero-title', 'Masjid PUSDAI Jawa Barat')
                    </p>

                    <p class="mt-5 max-w-lg font-serif text-xl font-bold italic leading-relaxed xl:text-2xl">
                        @yield('hero-copy', 'Sistem Informasi Keuangan Masjid yang aman, tertib, dan transparan.')
                    </p>
                </div>
            </div>
        </section>
        <section class="relative flex items-center justify-center overflow-y-auto bg-[linear-gradient(to_bottom,_#15582A_0%,_#179B40_25%,_#19BB4B_50%,_#22BA51_75%,_#0E5423_100%)] px-5 py-8 sm:px-9">
            <div class="relative w-full max-w-md">
                <div class="mb-6 flex flex-col items-center text-center text-white">
                    <img src="{{ asset('assets/images/FINUS_logo.png') }}"
                         alt="FINUS PUSDAI"
                         class="mx-auto mb-3 h-14 max-w-[210px] object-contain"
                         onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.src='{{ asset('assets/images/finara1.png') }}';}else{this.style.display='none';document.getElementById('auth-panel-logo-fallback').classList.remove('hidden');}">
                    <span id="auth-panel-logo-fallback" class="hidden mb-3 text-xl font-extrabold tracking-wide text-white">
                        FINUS PUSDAI
                    </span>
                    <h2 class="font-serif text-3xl font-bold leading-tight">
                        @yield('panel-title', 'Masuk')
                    </h2>
                    <p class="mt-1 text-sm text-green-50/90">
                        @yield('panel-copy')
                    </p>
                </div>
                <div class="rounded-2xl border border-white/35 bg-white/90 p-5 shadow-2xl backdrop-blur-sm sm:p-7">
                    @if(session('success'))
                        <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-center shadow-sm">
                            <div class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-green-700 text-white">
                                ✓
                            </div>

                            <p class="text-sm font-bold text-green-900">
                                {{ session('success') }}
                            </p>
                        </div>
                    @endif
                    @php
                        $statusMessages = [
                            'verification-code-sent' => [
                                'title' => 'Kode Verifikasi Berhasil Dikirim',
                                'message' => 'Kami sudah mengirim kode verifikasi ke alamat email Anda. Silakan masukkan kode tersebut untuk mengaktifkan akun jamaah.',
                            ],
                            'email-verified' => [
                                'title' => 'Verification Success',
                                'message' => 'Akun jamaah berhasil diverifikasi. Silakan login untuk masuk ke dashboard.',
                            ],
                            'password-reset-link-sent' => [
                                'title' => 'Tautan Reset Password Berhasil Dikirim',
                                'message' => 'Silakan periksa email Anda untuk melanjutkan proses reset password.',
                            ],
                            'password-updated' => [
                                'title' => 'Password Berhasil Diperbarui',
                                'message' => 'Silakan masuk kembali menggunakan password baru Anda.',
                            ],
                            'account-activated' => [
                                'title' => 'Akun Berhasil Diaktifkan',
                                'message' => 'Akun Anda sudah aktif. Silakan masuk menggunakan email dan password yang sudah dibuat.',
                            ],
                        ];
                        $currentStatus = session('status');
                        $statusData = $statusMessages[$currentStatus] ?? null;
                    @endphp
                    @if($statusData)
                        <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-center shadow-sm">
                            <div class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-green-700 text-white">
                                ✓
                            </div>
                            <p class="font-bold text-green-900">
                                {{ $statusData['title'] }}
                            </p>
                            <p class="mt-1 text-sm leading-relaxed text-green-800">
                                {{ $statusData['message'] }}
                            </p>
                        </div>
                    @elseif($currentStatus)
                        <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-center text-sm font-semibold text-green-800 shadow-sm">
                            {{ $currentStatus }}
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const showIcon = "{{ asset('assets/images/ShowPassword.png') }}";
            const hideIcon = "{{ asset('assets/images/HidePassword.png') }}";
            document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
                const targetSelector = button.getAttribute('data-target');
                const input = document.querySelector(targetSelector);
                const icon = button.querySelector('img');

                if (!input || !icon) return;

                button.addEventListener('click', function () {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.src = hideIcon;
                        icon.alt = 'Sembunyikan password';
                        button.setAttribute('aria-label', 'Sembunyikan password');
                    } else {
                        input.type = 'password';
                        icon.src = showIcon;
                        icon.alt = 'Tampilkan password';
                        button.setAttribute('aria-label', 'Tampilkan password');
                    }
                });
            });
        });
    </script>
</body>
</html>
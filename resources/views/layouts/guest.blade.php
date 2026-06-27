<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FINUS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: "Nunito", sans-serif; }
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
        .auth-field::placeholder { color: #64806d; }
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
        .auth-button:hover { filter: brightness(1.08); transform: translateY(-1px); }
        .auth-link { color: #075d2a; font-weight: 800; }
        .auth-link:hover { text-decoration: underline; }
        .auth-error { margin-top: .3rem; font-size: .78rem; font-weight: 700; color: #b91c1c; }
    </style>
</head>
<body class="min-h-screen bg-[#eef8f0] text-slate-800">
    <header class="relative z-30 flex min-h-[64px] items-center justify-between gap-4 bg-[linear-gradient(to_right,_#0FB442_0%,_#1AAF48_42%,_#08762B_76%,_#003F15_100%)] px-4 py-2 shadow-md sm:px-7">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center">
            <img src="{{ asset('assets/images/FINUS_Welcome.png') }}" alt="FINUS PUSDAI"
                class="h-11 max-w-[190px] object-contain"
                onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.src='{{ asset('assets/images/finara1.png') }}';}else{this.style.display='none';document.getElementById('auth-brand-fallback').classList.remove('hidden');}">
            <span id="auth-brand-fallback" class="hidden text-xl font-extrabold tracking-wide text-white">FINUS PUSDAI</span>
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
            <div class="relative flex h-full items-center px-[7%]">
                <div class="max-w-xl text-[#064e24] drop-shadow-[0_1px_0_rgba(255,255,255,.8)]">
                    <p class="font-['Lobster',cursive] text-5xl leading-tight xl:text-6xl">@yield('hero-title', 'Masjid PUSDAI Jawa Barat')</p>
                    <p class="mt-5 max-w-lg font-serif text-xl font-bold italic leading-relaxed xl:text-2xl">
                        @yield('hero-copy', 'Sistem Informasi Keuangan Masjid yang aman, tertib, dan transparan.')
                    </p>
                </div>
            </div>
        </section>

        <section class="relative flex items-center justify-center overflow-y-auto bg-[linear-gradient(145deg,_#13B948_0%,_#0B9E3B_48%,_#05752A_100%)] px-5 py-8 sm:px-9">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full border-[35px] border-white/5"></div>
            <div class="absolute -bottom-24 -left-20 h-64 w-64 rounded-full border-[42px] border-white/5"></div>

            <div class="relative w-full max-w-md">
                <div class="mb-5 text-center text-white">
                    <p class="text-xs font-extrabold uppercase tracking-[.28em] text-green-100">FINUS PUSDAI</p>
                    <h2 class="mt-2 font-serif text-3xl font-bold">@yield('panel-title', 'Masuk')</h2>
                    <p class="mt-1 text-sm text-green-50/90">@yield('panel-copy')</p>
                </div>

                <div class="rounded-2xl border border-white/35 bg-white/90 p-5 shadow-2xl backdrop-blur-sm sm:p-7">
                    @if(session('success'))
                        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm font-semibold text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('status'))
                        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm font-semibold text-blue-800">
                            {{ session('status') }}
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </section>
    </main>
</body>
</html>
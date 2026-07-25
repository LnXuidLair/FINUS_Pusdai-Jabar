<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0E5423">
    <title>@yield('title', 'FINUS')</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=21">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?v=21">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    @stack('styles')

    <style>
        :root{
            --a-dark:#0E5423;--a-green:#179B40;--a-light:#22BA51;--a-soft:#EAF8EE;
            --a-pale:#F5FBF7;--a-text:#172033;--a-muted:#64748B;--a-border:#DDE8E1;
            --a-red:#DC2626;--a-red-soft:#FFF1F2;--a-amber:#D97706;--a-blue:#2563EB
        }
        *{box-sizing:border-box}
        html{min-height:100%;scroll-behavior:smooth}
        body{min-height:100vh;margin:0;overflow-x:hidden;background:#EEF7F0;color:var(--a-text);font-family:"Nunito",sans-serif;-webkit-font-smoothing:antialiased}
        body.auth-busy{cursor:progress}
        button,input,a{ -webkit-tap-highlight-color:transparent }

        .auth-header{
            position:relative;z-index:40;display:grid;grid-template-columns:minmax(170px,1fr) auto minmax(170px,1fr);
            align-items:center;gap:18px;min-height:72px;padding:9px 26px;border-bottom:1px solid rgba(255,255,255,.13);
            background:radial-gradient(circle at 13% 20%,rgba(126,255,135,.18),transparent 16rem),
            linear-gradient(145deg,rgba(6, 61, 26, .97) 0%,rgba(14, 84, 35, .95) 46%,rgba(23, 155, 64, .91) 100%);
            box-shadow:0 8px 24px rgba(4,66,25,.17)
        }
        .auth-brand{display:inline-flex;align-items:center;justify-self:start;text-decoration:none!important}
        .auth-brand img{height:47px;max-width:195px;object-fit:contain;filter:drop-shadow(0 4px 9px rgba(0,38,13,.16))}
        .auth-brand-fallback{color:#fff;font-size:19px;font-weight:900;letter-spacing:.07em}
        .auth-header-title{justify-self:center;margin:0;color:#fff;font-family:Georgia,"Times New Roman",serif;font-size:clamp(17px,2vw,23px);font-weight:800;text-align:center}
        .auth-home-link{display:inline-flex;align-items:center;justify-content:center;justify-self:end;gap:8px;min-height:39px;padding:0 14px;border:1px solid rgba(255,255,255,.44);border-radius:11px;background:rgba(255,255,255,.94);color:var(--a-dark)!important;font-size:12px;font-weight:800;text-decoration:none!important;box-shadow:0 7px 16px rgba(0,48,17,.13);transition:.2s}
        .auth-home-link:hover{transform:translateY(-1px);background:#fff}
        .auth-home-icon{display:inline-flex;align-items:center;justify-content:center;width:23px;height:23px;border-radius:7px;background:var(--a-soft);color:var(--a-green)}

        .auth-layout{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(390px,.78fr);min-height:calc(100vh - 72px)}
        .auth-hero{position:relative;overflow:hidden;min-height:590px;background-position:center;background-size:cover}
        .auth-hero::before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(240,255,244,.35),rgba(255,255,255,.07) 58%,rgba(0,77,27,.19))}
        .auth-hero::after{content:"";position:absolute;inset:0;background:radial-gradient(circle at 35% 46%,rgba(255,255,255,.46),transparent 22rem)}
        .auth-batik{position:absolute;inset:0 auto 0 0;z-index:2;width:255px;height:100%;object-fit:cover;opacity:.34;mix-blend-mode:multiply;pointer-events:none}
        .auth-hero-content{position:relative;z-index:5;display:flex;align-items:center;min-height:590px;padding:58px 7%}
        .auth-hero-copy{max-width:620px;color:#064E24;text-shadow:0 1px 0 rgba(255,255,255,.88);animation:auth-up .5s ease both}
        .auth-hero-badge{display:inline-flex;align-items:center;gap:8px;margin-bottom:18px;padding:8px 12px;border:1px solid rgba(14,84,35,.18);border-radius:999px;background:rgba(255,255,255,.74);font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;backdrop-filter:blur(8px)}
        .auth-hero-badge::before{content:"";width:7px;height:7px;border-radius:50%;background:var(--a-green);box-shadow:0 0 0 4px rgba(23,155,64,.13)}
        .auth-hero-title{margin:0;font-family:"Lobster",cursive;font-size:clamp(46px,5vw,72px);font-weight:400;line-height:1.08}
        .auth-hero-copytext{max-width:570px;margin:22px 0 0;color:#0B4D24;font-family:Georgia,"Times New Roman",serif;font-size:clamp(18px,2vw,25px);font-style:italic;font-weight:700;line-height:1.65}
        .auth-features{display:flex;flex-wrap:wrap;gap:10px;margin-top:28px}
        .auth-feature{display:inline-flex;align-items:center;gap:8px;padding:9px 12px;border:1px solid rgba(14,84,35,.15);border-radius:11px;background:rgba(255,255,255,.75);color:#164F2A;font-size:11.5px;font-weight:800;backdrop-filter:blur(7px)}
        .auth-feature span{display:inline-flex;align-items:center;justify-content:center;width:23px;height:23px;border-radius:7px;background:var(--a-soft);color:var(--a-green)}

        .auth-panel{position:relative;overflow-y:auto;display:flex;align-items:center;justify-content:center;min-height:590px;padding:38px clamp(22px,4vw,44px);background:radial-gradient(circle at 87% 7%,rgba(255,255,255,.19),transparent 15rem),radial-gradient(circle at 8% 94%,rgba(126,255,135,.16),transparent 15rem),linear-gradient(155deg,#0E5423 0%,#179B40 44%,#22BA51 72%,#0E5423 100%)}
        .auth-panel-inner{position:relative;z-index:2;width:100%;max-width:470px;animation:auth-up .45s ease both}
        .auth-panel-heading{margin-bottom:20px;color:#fff;text-align:center}
        .auth-panel-logo{height:57px;max-width:220px;margin:0 auto 13px;object-fit:contain;filter:drop-shadow(0 5px 12px rgba(0,47,17,.19))}
        .auth-panel-logo-fallback{display:none;margin-bottom:12px;color:#fff;font-size:21px;font-weight:900}
        .auth-panel-title{margin:0;font-family:Georgia,"Times New Roman",serif;font-size:clamp(27px,4vw,34px);font-weight:800}
        .auth-panel-copy{max-width:390px;margin:7px auto 0;color:rgba(240,255,244,.86);font-size:12.5px;line-height:1.6}
        .auth-secure{display:inline-flex;align-items:center;gap:7px;margin-top:12px;padding:6px 10px;border:1px solid rgba(255,255,255,.17);border-radius:999px;background:rgba(0,76,27,.17);color:rgba(255,255,255,.86);font-size:10.5px;font-weight:800}
        .auth-secure::before{content:"";width:6px;height:6px;border-radius:50%;background:#A7FFAF;box-shadow:0 0 0 4px rgba(167,255,175,.11)}

        .auth-card{position:relative;overflow:hidden;padding:clamp(21px,4vw,30px);border:1px solid rgba(255,255,255,.52);border-radius:22px;background:rgba(255,255,255,.96);box-shadow:0 25px 60px rgba(2,42,16,.26);backdrop-filter:blur(16px)}
        .auth-card::before{content:"";position:absolute;top:-85px;right:-75px;width:180px;height:180px;border-radius:50%;background:rgba(34,186,81,.075)}
        .auth-card>*{position:relative;z-index:1}
        .auth-form{display:grid;gap:16px}
        .auth-label{display:flex;align-items:center;gap:7px;margin-bottom:6px;color:#274B34;font-size:12px;font-weight:800}
        .auth-label-icon{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:7px;background:var(--a-soft);color:var(--a-green);font-size:11px}
        .auth-input-wrap{position:relative}
        .auth-field{width:100%;border:1px solid rgba(5,86,33,.27);border-radius:11px;background:rgba(255,255,255,.98);padding:.78rem .92rem;color:#064E24;font:600 13px "Nunito",sans-serif;outline:none;transition:.2s}
        .auth-field::placeholder{color:#789084;font-weight:500}
        .auth-field:hover{border-color:rgba(5,86,33,.42)}
        .auth-field:focus{border-color:var(--a-green);background:#fff;box-shadow:0 0 0 4px rgba(23,155,64,.11)}
        .auth-field[readonly]{background:rgba(240,253,244,.82);color:#376047}
        .auth-password-field{padding-right:52px!important}
        .auth-password-toggle{position:absolute;top:50%;right:8px;display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;padding:0;border:0;border-radius:9px;background:transparent;cursor:pointer;transform:translateY(-50%);transition:.2s}
        .auth-password-toggle:hover{background:var(--a-soft)}
        .auth-password-toggle img{width:22px;height:22px;object-fit:contain}
        .auth-help{display:flex;gap:6px;margin-top:6px;color:var(--a-muted);font-size:10.5px;line-height:1.5}
        .auth-help b{color:var(--a-green)}
        .auth-error{display:flex;gap:5px;margin-top:6px;color:#B91C1C;font-size:.76rem;font-weight:700;line-height:1.5}
        .auth-error::before{content:"•"}
        .auth-form-row{display:flex;align-items:center;justify-content:space-between;gap:14px}
        .auth-checkbox{display:inline-flex;align-items:center;gap:8px;color:#52645A;font-size:12px;font-weight:700}
        .auth-checkbox input{width:16px;height:16px;accent-color:var(--a-green)}

        .auth-button,.auth-button-secondary,.auth-button-muted{display:inline-flex;align-items:center;justify-content:center;gap:9px;width:100%;min-height:46px;padding:.75rem 1rem;border-radius:11px;font:900 13px "Nunito",sans-serif;text-decoration:none!important;cursor:pointer;transition:.2s}
        .auth-button{border:1px solid rgba(255,255,255,.08);background:linear-gradient(100deg,#064E24,#0E7433 48%,#13A044);color:#fff!important;box-shadow:0 10px 22px rgba(4,78,36,.21)}
        .auth-button:hover:not(:disabled){filter:brightness(1.07);transform:translateY(-1px);box-shadow:0 14px 26px rgba(4,78,36,.27)}
        .auth-button-secondary{border:1px solid #BFD8C6;background:var(--a-pale);color:var(--a-dark)!important}
        .auth-button-muted{border:1px solid #D7E0DA;background:#F8FAF9;color:#475569!important}
        .auth-button:disabled,[aria-busy="true"]{cursor:wait;opacity:.82}
        .auth-link{color:#075D2A;font-weight:800;text-decoration:none}
        .auth-link:hover{color:var(--a-green);text-decoration:underline;text-underline-offset:3px}
        .auth-footer-text{margin:0;color:var(--a-muted);font-size:12px;line-height:1.6;text-align:center}
        .auth-info-card{padding:13px 14px;border:1px solid #CFE6D6;border-radius:12px;background:var(--a-pale);color:#3B5A46;font-size:11.5px;line-height:1.6}
        .auth-email-preview{margin-top:8px;padding:10px 12px;border:1px solid #CFE6D6;border-radius:10px;background:var(--a-pale);color:var(--a-muted);font-size:10.5px}
        .auth-email-preview strong{display:block;margin-top:3px;color:var(--a-dark);font-size:12px;word-break:break-all}
        .auth-password-strength{margin-top:8px}
        .auth-strength-bars{display:grid;grid-template-columns:repeat(4,1fr);gap:5px}
        .auth-strength-bar{height:4px;border-radius:999px;background:#E2E8E4}
        .auth-strength-text{display:block;margin-top:5px;color:var(--a-muted);font-size:10px;font-weight:700}
        .auth-code-intro{text-align:center}
        .auth-code-icon{display:flex;align-items:center;justify-content:center;width:58px;height:58px;margin:0 auto 14px;border-radius:18px;background:var(--a-soft);color:var(--a-green);font-size:25px}
        .auth-code-copy{margin:0 0 18px;color:var(--a-muted);font-size:12px;line-height:1.7}
        .auth-code-field{text-align:center;font-size:22px!important;font-weight:900!important;letter-spacing:.32em}

        .auth-alert{display:flex;gap:11px;margin-bottom:18px;padding:13px 14px;border:1px solid;border-radius:13px;font-size:12px;line-height:1.6}
        .auth-alert-icon{display:flex;align-items:center;justify-content:center;width:32px;min-width:32px;height:32px;border-radius:9px;font-weight:900}
        .auth-alert-title{margin:0;font-size:12.5px;font-weight:900}
        .auth-alert-message{margin:3px 0 0}
        .auth-alert-success{border-color:#BDE5C7;background:#F0FAF3;color:#166534}.auth-alert-success .auth-alert-icon{background:#D9F4E0}
        .auth-alert-danger{border-color:#F5C2C7;background:#FFF4F4;color:#991B1B}.auth-alert-danger .auth-alert-icon{background:#FEE2E2}
        .auth-alert-warning{border-color:#F4D99B;background:#FFF9EA;color:#92400E}.auth-alert-warning .auth-alert-icon{background:#FDECC8}
        .auth-alert-info{border-color:#BFDBFE;background:#EFF6FF;color:#1E40AF}.auth-alert-info .auth-alert-icon{background:#DBEAFE}

        .auth-dialog-overlay{position:fixed;inset:0;z-index:8000;display:flex;align-items:center;justify-content:center;padding:18px;background:rgba(2,35,13,.61);backdrop-filter:blur(7px)}
        .auth-dialog{width:min(100%,410px);overflow:hidden;border-radius:21px;background:#fff;text-align:center;box-shadow:0 30px 75px rgba(0,31,11,.31);animation:auth-pop .25s ease both}
        .auth-dialog-body{padding:28px 26px 22px}.auth-dialog-icon{display:flex;align-items:center;justify-content:center;width:65px;height:65px;margin:0 auto 16px;border-radius:20px;background:var(--a-soft);color:var(--a-green);font-size:27px;font-weight:900}
        .auth-dialog-title{margin:0;color:var(--a-dark);font-size:20px;font-weight:900}.auth-dialog-copy{margin:8px 0 0;color:var(--a-muted);font-size:12.5px;line-height:1.65}
        .auth-dialog-details{display:grid;gap:9px;margin:18px 0 0;padding:14px;border:1px solid #D7E8DC;border-radius:13px;background:var(--a-pale);text-align:left}
        .auth-dialog-detail{display:grid;grid-template-columns:95px 1fr;gap:10px;font-size:11.5px}.auth-dialog-detail dt{color:var(--a-muted);font-weight:700}.auth-dialog-detail dd{margin:0;color:var(--a-text);font-weight:800}
        .auth-dialog-footer{padding:0 26px 26px}

        .auth-loading-overlay{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;visibility:hidden;opacity:0;background:rgba(2,35,13,.60);backdrop-filter:blur(7px);transition:.18s}
        .auth-loading-overlay.is-visible{visibility:visible;opacity:1}
        .auth-loading-card{width:min(100%,280px);padding:28px 24px;border-radius:20px;background:rgba(255,255,255,.97);text-align:center;box-shadow:0 28px 70px rgba(0,28,10,.30)}
        .auth-loading-wheel{position:relative;width:56px;height:56px;margin:0 auto 16px;border:5px solid #DDF2E3;border-top-color:var(--a-green);border-right-color:var(--a-light);border-radius:50%;animation:auth-spin .78s linear infinite}
        .auth-loading-wheel::after{content:"";position:absolute;inset:9px;border-radius:50%;background:var(--a-soft)}
        .auth-loading-title{margin:0;color:var(--a-dark);font-size:15px;font-weight:900}.auth-loading-message{display:block;margin-top:5px;color:var(--a-muted);font-size:11.5px}
        .auth-inline-spinner{width:17px;height:17px;border:2px solid rgba(255,255,255,.36);border-top-color:#fff;border-radius:50%;animation:auth-spin .7s linear infinite}
        @keyframes auth-spin{to{transform:rotate(360deg)}}@keyframes auth-up{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}@keyframes auth-pop{from{opacity:0;transform:scale(.96) translateY(8px)}to{opacity:1;transform:none}}

        @media(max-width:1023.98px){.auth-header{grid-template-columns:minmax(150px,1fr) auto minmax(90px,1fr);padding-inline:18px}.auth-layout{grid-template-columns:1fr}.auth-hero{display:none}.auth-panel{min-height:calc(100vh - 72px)}}
        @media(max-width:639.98px){.auth-header{grid-template-columns:1fr auto;min-height:66px;padding:8px 13px}.auth-brand img{height:41px;max-width:150px}.auth-header-title{display:none}.auth-home-link{min-height:37px;padding:0 11px}.auth-home-link-text{display:none}.auth-panel{align-items:flex-start;min-height:calc(100vh - 66px);padding:27px 14px 34px}.auth-panel-logo{height:50px}.auth-panel-title{font-size:27px}.auth-card{padding:21px 17px;border-radius:18px}.auth-form-row{align-items:flex-start;flex-direction:column}.auth-dialog-detail{grid-template-columns:1fr;gap:2px}}
        @media(prefers-reduced-motion:reduce){*{animation-duration:.01ms!important;transition-duration:.01ms!important}}
    </style>
</head>

<body>
    <div id="authLoadingOverlay" class="auth-loading-overlay" role="status" aria-live="polite" aria-hidden="true">
        <div class="auth-loading-card">
            <div class="auth-loading-wheel" aria-hidden="true"></div>
            <p class="auth-loading-title" id="authLoadingTitle">Memproses permintaan...</p>
            <span class="auth-loading-message" id="authLoadingMessage">Mohon tunggu sebentar.</span>
        </div>
    </div>

    <header class="auth-header">
        <a href="{{ route('home') }}" class="auth-brand" data-loading-title="Membuka beranda...">
            <img src="{{ asset('assets/images/FINUS_login.png') }}" alt="FINUS PUSDAI"
                 onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.src='{{ asset('assets/images/pusdai_dashboard.png') }}';}else{this.style.display='none';document.getElementById('authBrandFallback').classList.remove('hidden');}">
            <span id="authBrandFallback" class="auth-brand-fallback hidden">FINUS PUSDAI</span>
        </a>

        <h1 class="auth-header-title">@yield('header-title', 'FINUS')</h1>

        <a href="{{ route('home') }}" class="auth-home-link" data-loading-title="Kembali ke beranda...">
            <span class="auth-home-icon">⌂</span>
            <span class="auth-home-link-text">Beranda</span>
        </a>
    </header>

    <main class="auth-layout">
        <section class="auth-hero" style="background-image:url('{{ asset('assets/images/bg-welcome.png') }}')">
            <img src="{{ asset('assets/images/pusdai_batik.png') }}" alt="" class="auth-batik" onerror="this.style.display='none'">

            <div class="auth-hero-content">
                <div class="auth-hero-copy">
                    <div class="auth-hero-badge">Sistem Keuangan Masjid</div>
                    <p class="auth-hero-title">@yield('hero-title', 'Masjid PUSDAI Jawa Barat')</p>
                    <p class="auth-hero-copytext">@yield('hero-copy', 'Sistem Informasi Keuangan Masjid yang aman, tertib, dan transparan.')</p>

                    <div class="auth-features">
                        <div class="auth-feature"><span>✓</span>Aman</div>
                        <div class="auth-feature"><span>◎</span>Transparan</div>
                        <div class="auth-feature"><span>≡</span>Terintegrasi</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-panel-inner">
                <div class="auth-panel-heading">
                    <img src="{{ asset('assets/images/FINUS_logo.png') }}" alt="FINUS PUSDAI" class="auth-panel-logo"
                         onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.src='{{ asset('assets/images/pusdai_dashboard.png') }}';}else{this.style.display='none';document.getElementById('authPanelLogoFallback').style.display='block';}">
                    <span id="authPanelLogoFallback" class="auth-panel-logo-fallback">FINUS PUSDAI</span>
                    <h2 class="auth-panel-title">@yield('panel-title', 'Masuk')</h2>
                    <p class="auth-panel-copy">@yield('panel-copy')</p>
                    <div class="auth-secure">Koneksi aman FINUS</div>
                </div>

                <div class="auth-card">
                    @php
                        $statusMessages = [
                            'verification-code-sent' => ['title' => 'Kode Verifikasi Dikirim', 'message' => 'Silakan periksa email dan masukkan kode untuk mengaktifkan akun.'],
                            'email-verified' => ['title' => 'Verifikasi Berhasil', 'message' => 'Akun jamaah berhasil diverifikasi. Silakan login.'],
                            'password-reset-link-sent' => ['title' => 'Tautan Reset Dikirim', 'message' => 'Silakan periksa email untuk melanjutkan reset password.'],
                            'password-updated' => ['title' => 'Password Diperbarui', 'message' => 'Silakan masuk menggunakan password baru.'],
                            'account-activated' => ['title' => 'Akun Diaktifkan', 'message' => 'Akun sudah aktif dan dapat digunakan untuk login.'],
                        ];
                        $currentStatus = session('status');
                        $statusData = $statusMessages[$currentStatus] ?? null;
                    @endphp

                    @if(session('success'))
                        <div class="auth-alert auth-alert-success"><div class="auth-alert-icon">✓</div><div><p class="auth-alert-title">Berhasil</p><p class="auth-alert-message">{{ session('success') }}</p></div></div>
                    @endif
                    @if(session('error'))
                        <div class="auth-alert auth-alert-danger"><div class="auth-alert-icon">!</div><div><p class="auth-alert-title">Terjadi Kesalahan</p><p class="auth-alert-message">{{ session('error') }}</p></div></div>
                    @endif
                    @if($statusData)
                        <div class="auth-alert auth-alert-success"><div class="auth-alert-icon">✓</div><div><p class="auth-alert-title">{{ $statusData['title'] }}</p><p class="auth-alert-message">{{ $statusData['message'] }}</p></div></div>
                    @elseif($currentStatus)
                        <div class="auth-alert auth-alert-info"><div class="auth-alert-icon">i</div><div><p class="auth-alert-message">{{ $currentStatus }}</p></div></div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </section>
    </main>

    <script>
    (() => {
        const overlay = document.getElementById('authLoadingOverlay');
        const title = document.getElementById('authLoadingTitle');
        const message = document.getElementById('authLoadingMessage');

        const showLoading = (text = 'Memproses permintaan...') => {
            if (!overlay) return;
            title.textContent = text;
            message.textContent = 'Jangan tutup atau memuat ulang halaman ini.';
            overlay.classList.add('is-visible');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.classList.add('auth-busy');
        };

        const hideLoading = () => {
            overlay?.classList.remove('is-visible');
            overlay?.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('auth-busy');
        };

        window.addEventListener('pageshow', hideLoading);

        document.addEventListener('DOMContentLoaded', () => {
            const showIcon = "{{ asset('assets/images/ShowPassword.png') }}";
            const hideIcon = "{{ asset('assets/images/HidePassword.png') }}";

            document.querySelectorAll('[data-toggle-password]').forEach(button => {
                const input = document.querySelector(button.dataset.target);
                const image = button.querySelector('img');
                if (!input) return;

                button.classList.add('auth-password-toggle');
                button.addEventListener('click', () => {
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    button.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
                    if (image) {
                        image.src = show ? hideIcon : showIcon;
                        image.alt = show ? 'Sembunyikan password' : 'Tampilkan password';
                    }
                });
            });

            document.querySelectorAll('[data-password-strength]').forEach(input => {
                const box = document.getElementById(input.dataset.passwordStrength);
                if (!box) return;
                const bars = [...box.querySelectorAll('[data-strength-bar]')];
                const label = box.querySelector('[data-strength-text]');
                const colors = ['#E2E8E4','#DC2626','#D97706','#179B40','#0E5423'];
                const labels = ['Masukkan minimal 8 karakter','Lemah','Cukup','Kuat','Sangat kuat'];

                const update = () => {
                    const v = input.value;
                    let score = 0;
                    if (v.length >= 8) score++;
                    if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
                    if (/\d/.test(v)) score++;
                    if (/[^A-Za-z0-9]/.test(v)) score++;
                    bars.forEach((bar, i) => bar.style.background = i < score ? colors[score] : colors[0]);
                    if (label) {
                        label.textContent = labels[score];
                        label.style.color = score ? colors[score] : '#64748B';
                    }
                };
                input.addEventListener('input', update);
                update();
            });

            document.querySelectorAll('[data-verification-code]').forEach(input => {
                input.addEventListener('input', () => {
                    input.value = input.value.replace(/\D/g, '').slice(0, 6);
                });
            });

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', event => {
                    if (form.hasAttribute('data-no-loading')) return;
                    if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }
                    if (form.dataset.submitting === 'true') {
                        event.preventDefault();
                        return;
                    }

                    form.dataset.submitting = 'true';
                    const buttons = form.querySelectorAll('button[type="submit"]');
                    let loadingText = form.dataset.loadingTitle || 'Memproses permintaan...';

                    buttons.forEach(button => {
                        loadingText = button.dataset.loadingTitle || loadingText;
                        button.disabled = true;
                        button.setAttribute('aria-busy', 'true');
                        button.innerHTML = `<span class="auth-inline-spinner"></span><span>${button.dataset.loadingText || 'Memproses...'}</span>`;
                    });

                    requestAnimationFrame(() => showLoading(loadingText));
                });
            });

            document.addEventListener('click', event => {
                const link = event.target.closest('a[href]');
                if (!link || link.target === '_blank' || link.hasAttribute('download') || link.hasAttribute('data-no-loading')) return;
                const href = link.getAttribute('href');
                if (!href || href === '#' || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

                try {
                    const destination = new URL(link.href, location.href);
                    if (destination.origin === location.origin) {
                        showLoading(link.dataset.loadingTitle || 'Membuka halaman...');
                    }
                } catch (_) {}
            });
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
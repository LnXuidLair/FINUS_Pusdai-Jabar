<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0FB442">

    <title>Location | FINUS PUSDAI</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=21">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --g950:#033D18;
            --g900:#075923;
            --g800:#0E6E2C;
            --g700:#118A37;
            --g600:#16A344;
            --g500:#22BA51;
            --g300:#7EFF87;
            --g100:#E7F9EB;
            --g50:#F5FCF7;
            --text:#122018;
            --muted:#607369;
            --white:#FFFFFF;
            --header:78px;
            --container:1180px;
            --shadow:0 24px 65px rgba(3,61,24,.14);
        }

        *,*::before,*::after { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body {
            min-height:100vh;
            margin:0;
            overflow-x:hidden;
            background:var(--g50);
            color:var(--text);
            font-family:"Nunito",sans-serif;
            -webkit-font-smoothing:antialiased;
        }
        img { display:block; max-width:100%; }
        button,a { font:inherit; -webkit-tap-highlight-color:transparent; }
        .page-container { width:min(calc(100% - 36px),var(--container)); margin-inline:auto; }

        .page-header {
            position:fixed;
            inset:0 0 auto;
            z-index:1000;
            min-height:calc(var(--header) + env(safe-area-inset-top));
            padding-top:env(safe-area-inset-top);
            border-bottom:1px solid rgba(255,255,255,.18);
            background-image:linear-gradient(to right,#0FB442 0%,#1AAF48 39%,#118635 75%,#004716 100%);
            box-shadow:0 11px 32px rgba(0,58,18,.18);
        }
        .page-header-inner {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:20px;
            width:100%;
            min-height:var(--header);
            padding-inline:clamp(14px,2.4vw,42px);
        }
        .page-logo { width:auto; height:52px; max-width:210px; object-fit:contain; filter:drop-shadow(0 5px 12px rgba(0,35,10,.18)); }
        .page-nav { display:flex; align-items:center; gap:10px; }
        .page-nav-link {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            min-height:44px;
            padding:0 16px;
            border:1px solid rgba(255,255,255,.34);
            border-radius:13px;
            background:rgba(255,255,255,.94);
            color:var(--g950);
            font-size:12px;
            font-weight:900;
            text-decoration:none;
            box-shadow:0 9px 20px rgba(0,54,17,.14);
            transition:transform .2s ease,box-shadow .2s ease,background .2s ease;
        }
        .page-nav-link:hover,.page-nav-link:focus-visible { transform:translateY(-2px); background:#fff; box-shadow:0 14px 28px rgba(0,54,17,.2); outline:none; }

        main { padding-top:calc(var(--header) + env(safe-area-inset-top)); }
        .hero {
            position:relative;
            overflow:hidden;
            padding:clamp(62px,8vw,105px) 0 clamp(42px,5vw,68px);
            background:
                radial-gradient(circle at 10% 15%,rgba(126,255,135,.22),transparent 20rem),
                radial-gradient(circle at 88% 12%,rgba(34,186,81,.14),transparent 24rem),
                linear-gradient(180deg,#F7FFF9,#EDF9F1);
        }
        .hero::after { content:""; position:absolute; right:-140px; bottom:-190px; width:430px; height:430px; border:74px solid rgba(17,138,55,.06); border-radius:50%; }
        .hero-inner { position:relative; z-index:2; max-width:800px; margin-inline:auto; text-align:center; }
        .eyebrow {
            display:inline-flex;
            align-items:center;
            gap:9px;
            padding:8px 13px;
            border:1px solid rgba(7,89,35,.15);
            border-radius:999px;
            background:rgba(255,255,255,.8);
            color:var(--g900);
            font-size:10px;
            font-weight:900;
            letter-spacing:.14em;
            text-transform:uppercase;
            box-shadow:0 8px 24px rgba(3,61,24,.08);
        }
        .eyebrow::before { content:""; width:7px; height:7px; border-radius:50%; background:var(--g500); box-shadow:0 0 0 4px rgba(34,186,81,.14); }
        .hero-title { margin:21px 0 0; color:var(--g950); font-family:Georgia,"Times New Roman",serif; font-size:clamp(38px,6vw,68px); line-height:1.1; letter-spacing:-.04em; }
        .hero-copy { max-width:650px; margin:18px auto 0; color:var(--muted); font-size:clamp(14px,1.6vw,18px); line-height:1.8; font-weight:600; }

        .location-section { position:relative; padding:clamp(52px,7vw,92px) 0; }
        .location-grid { display:grid; grid-template-columns:minmax(0,1.5fr) minmax(310px,.7fr); gap:22px; align-items:stretch; }
        .map-card,.info-card,.mini-card {
            position:relative;
            overflow:hidden;
            border:1px solid rgba(7,89,35,.11);
            border-radius:24px;
            background:rgba(255,255,255,.94);
            box-shadow:var(--shadow);
        }
        .map-card { min-height:560px; padding:13px; }
        .map-frame-wrap { position:relative; width:100%; height:100%; min-height:534px; overflow:hidden; border-radius:17px; background:#DDEFE2; }
        .map-frame { position:absolute; inset:0; width:100%; height:100%; border:0; }
        .map-live-label {
            position:absolute;
            top:18px;
            left:18px;
            z-index:3;
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:9px 12px;
            border:1px solid rgba(255,255,255,.58);
            border-radius:999px;
            background:rgba(3,61,24,.88);
            color:#fff;
            font-size:10px;
            font-weight:900;
            letter-spacing:.08em;
            text-transform:uppercase;
            box-shadow:0 10px 28px rgba(0,39,12,.24);
            backdrop-filter:blur(10px);
        }
        .map-live-label::before { content:""; width:8px; height:8px; border-radius:50%; background:#7EFF87; box-shadow:0 0 0 5px rgba(126,255,135,.17); animation:pulse 1.8s ease-in-out infinite; }
        .info-stack { display:grid; gap:17px; }
        .info-card { padding:clamp(27px,4vw,38px); background:linear-gradient(145deg,#033D18,#075923 49%,#16A344); color:#fff; }
        .info-kicker { display:inline-flex; align-items:center; gap:8px; padding:7px 10px; border:1px solid rgba(255,255,255,.16); border-radius:999px; background:rgba(255,255,255,.08); color:rgba(255,255,255,.82); font-size:9px; font-weight:900; letter-spacing:.14em; text-transform:uppercase; }
        .info-title { margin:18px 0 0; font-family:Georgia,"Times New Roman",serif; font-size:clamp(26px,3vw,37px); line-height:1.2; }
        .info-copy { margin:13px 0 0; color:rgba(255,255,255,.74); font-size:13px; line-height:1.75; }
        .contact-list { display:grid; gap:11px; margin-top:24px; }
        .contact-link { display:grid; grid-template-columns:42px minmax(0,1fr); gap:12px; align-items:center; padding:12px; border:1px solid rgba(255,255,255,.15); border-radius:15px; background:rgba(255,255,255,.09); color:#fff; text-decoration:none; transition:transform .2s ease,background .2s ease,border-color .2s ease; }
        .contact-link:hover,.contact-link:focus-visible { transform:translateX(3px); border-color:rgba(255,255,255,.3); background:rgba(255,255,255,.14); outline:none; }
        .contact-icon { display:inline-flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:13px; background:rgba(255,255,255,.12); color:#B7FFBE; }
        .contact-label { display:block; color:rgba(255,255,255,.61); font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.11em; }
        .contact-value { display:block; margin-top:3px; font-size:12px; font-weight:900; line-height:1.45; }
        .action-row { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:20px; }
        .action-button { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:46px; padding:0 13px; border:1px solid rgba(255,255,255,.22); border-radius:13px; background:rgba(255,255,255,.1); color:#fff; font-size:11px; font-weight:900; text-decoration:none; cursor:pointer; transition:.2s ease; }
        .action-button.is-primary { border-color:rgba(218,255,222,.7); background:linear-gradient(100deg,#7EFF87,#4FE66A 55%,#22BA51); color:var(--g950); }
        .action-button:hover,.action-button:focus-visible { transform:translateY(-2px); outline:none; }
        .copy-feedback { min-height:20px; margin:10px 0 0; color:#B7FFBE; font-size:10.5px; font-weight:800; text-align:center; }
        .mini-card { padding:24px; }
        .mini-card h2 { margin:0; color:var(--g950); font-size:18px; }
        .mini-card p { margin:9px 0 0; color:var(--muted); font-size:12.5px; line-height:1.7; }

        .scroll-card,.scroll-fade { opacity:0; filter:blur(6px); transform:translateY(42px) scale(.975); transition:opacity .68s cubic-bezier(.2,.72,.2,1),transform .72s cubic-bezier(.2,.72,.2,1),filter .58s ease; transition-delay:var(--delay,0ms); }
        .scroll-card.is-inview,.scroll-fade.is-inview { opacity:1; filter:none; transform:none; }
        @keyframes pulse { 0%,100% { transform:scale(1); opacity:1; } 50% { transform:scale(1.25); opacity:.65; } }

        .page-footer { padding:26px 0 calc(26px + env(safe-area-inset-bottom)); background:#022F12; color:rgba(255,255,255,.73); }
        .footer-inner { display:flex; align-items:center; justify-content:space-between; gap:22px; }
        .footer-brand { display:flex; align-items:center; gap:13px; }
        .footer-logo { height:40px; width:auto; max-width:130px; object-fit:contain; }
        .footer-copy { font-size:11px; line-height:1.65; }
        .footer-links { display:flex; align-items:center; justify-content:flex-end; gap:14px; flex-wrap:wrap; }
        .footer-links a { color:inherit; font-size:11px; font-weight:800; text-decoration:none; }
        .footer-links a:hover { color:#fff; }

        @media (max-width:980px) {
            .location-grid { grid-template-columns:1fr; }
            .map-card { min-height:510px; }
            .map-frame-wrap { min-height:484px; }
            .info-stack { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .info-card { grid-column:1 / -1; }
        }
        @media (max-width:720px) {
            :root { --header:70px; }
            .page-container { width:min(calc(100% - 24px),var(--container)); }
            .page-header-inner { padding-inline:12px; gap:10px; }
            .page-logo { height:44px; max-width:150px; }
            .page-nav-link { width:44px; padding:0; }
            .page-nav-label { display:none; }
            .hero { padding-top:56px; }
            .map-card { min-height:425px; padding:9px; border-radius:20px; }
            .map-frame-wrap { min-height:407px; border-radius:14px; }
            .map-live-label { top:14px; left:14px; }
            .info-stack { grid-template-columns:1fr; }
            .info-card { grid-column:auto; }
            .action-row { grid-template-columns:1fr; }
            .footer-inner { align-items:flex-start; flex-direction:column; }
            .footer-links { justify-content:flex-start; }
            .scroll-card,.scroll-fade { transform:translateY(30px) scale(.985); transition-delay:0ms; }
        }
        @media (max-width:390px) {
            .page-logo { height:41px; max-width:135px; }
            .map-card { min-height:365px; }
            .map-frame-wrap { min-height:347px; }
            .info-card,.mini-card { border-radius:19px; }
        }
        @media (prefers-reduced-motion:reduce) {
            html { scroll-behavior:auto; }
            .scroll-card,.scroll-fade { opacity:1!important; filter:none!important; transform:none!important; transition:none!important; }
            .map-live-label::before { animation:none!important; }
        }
    </style>
</head>
<body>
    <header class="page-header">
        <div class="page-header-inner">
            <img src="{{ asset('assets/images/FINUS_Welcome.png') }}" alt="FINUS PUSDAI" class="page-logo">

            <nav class="page-nav" aria-label="Navigasi halaman">
                <a href="{{ route('home') }}" class="page-nav-link">
                    <span aria-hidden="true">←</span>
                    <span class="page-nav-label">Beranda</span>
                </a>
                <a href="{{ route('visi-misi') }}" class="page-nav-link">
                    <span aria-hidden="true">✧</span>
                    <span class="page-nav-label">Visi &amp; Misi</span>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="page-container hero-inner scroll-fade">
                <span class="eyebrow">Live Map PUSDAI</span>
                <h1 class="hero-title">Lokasi PUSDAI Jawa Barat</h1>
                <p class="hero-copy">
                    Gunakan peta interaktif untuk melihat posisi PUSDAI, memperbesar area,
                    dan membuka petunjuk arah menuju lokasi.
                </p>
            </div>
        </section>

        <section class="location-section">
            <div class="page-container location-grid">
                <article class="map-card scroll-card" style="--delay:0ms">
                    <div class="map-frame-wrap">
                        <span class="map-live-label">Live Map</span>
                        <iframe
                            class="map-frame"
                            title="Peta lokasi PUSDAI Jawa Barat"
                            src="https://www.google.com/maps?q=PUSDAI%20Jawa%20Barat%2C%20Jalan%20Diponegoro%20No.%2063%2C%20Bandung&output=embed"
                            loading="lazy"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </article>

                <div class="info-stack">
                    <article class="info-card scroll-card" style="--delay:120ms">
                        <span class="info-kicker">Informasi Lokasi</span>
                        <h2 class="info-title">PUSDAI Jawa Barat</h2>
                        <p class="info-copy">
                            Pusat Dakwah Islam Jawa Barat berada di kawasan pusat Kota Bandung dan dapat
                            diakses melalui peta interaktif di samping.
                        </p>

                        <div class="contact-list">
                            <a
                                href="https://www.google.com/maps/dir/?api=1&destination=PUSDAI+Jawa+Barat+Jalan+Diponegoro+No+63+Bandung"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="contact-link"
                            >
                                <span class="contact-icon" aria-hidden="true">⌖</span>
                                <span>
                                    <span class="contact-label">Alamat</span>
                                    <span class="contact-value">Jalan Diponegoro No. 63, Bandung, Jawa Barat 40122</span>
                                </span>
                            </a>
                            <a href="tel:+62227217531" class="contact-link">
                                <span class="contact-icon" aria-hidden="true">☎</span>
                                <span>
                                    <span class="contact-label">Telepon</span>
                                    <span class="contact-value">(022) 7217531</span>
                                </span>
                            </a>
                            <a href="mailto:pusdaijabar@gmail.com" class="contact-link">
                                <span class="contact-icon" aria-hidden="true">✉</span>
                                <span>
                                    <span class="contact-label">Email</span>
                                    <span class="contact-value">pusdaijabar@gmail.com</span>
                                </span>
                            </a>
                        </div>

                        <div class="action-row">
                            <a
                                href="https://www.google.com/maps/dir/?api=1&destination=PUSDAI+Jawa+Barat+Jalan+Diponegoro+No+63+Bandung"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="action-button is-primary"
                            >
                                Buka Petunjuk Arah <span aria-hidden="true">↗</span>
                            </a>
                            <button type="button" class="action-button" id="copyAddressButton">
                                Salin Alamat <span aria-hidden="true">⧉</span>
                            </button>
                        </div>
                        <p class="copy-feedback" id="copyFeedback" aria-live="polite"></p>
                    </article>

                    <article class="mini-card scroll-card" style="--delay:170ms">
                        <h2>Peta interaktif</h2>
                        <p>Peta dapat digeser, diperbesar, diperkecil, dan dibuka langsung melalui Google Maps.</p>
                    </article>

                    <article class="mini-card scroll-card" style="--delay:220ms">
                        <h2>Responsif di semua perangkat</h2>
                        <p>Ukuran peta dan kartu informasi menyesuaikan tampilan laptop, tablet, serta HP.</p>
                    </article>
                </div>
            </div>
        </section>
    </main>

    <footer class="page-footer">
        <div class="page-container footer-inner">
            <div class="footer-brand">
                <img src="{{ asset('assets/images/FINUS_login.png') }}" alt="FINUS PUSDAI" class="footer-logo">
                <div class="footer-copy">
                    Sistem Informasi Keuangan Masjid<br>
                    PUSDAI Jawa Barat
                </div>
            </div>
            <div class="footer-links">
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('visi-misi') }}">Visi &amp; Misi</a>
                <a href="mailto:pusdaijabar@gmail.com">Contacts</a>
            </div>
        </div>
    </footer>

    <script>
        (() => {
            const address = 'Jalan Diponegoro No. 63, Bandung, Jawa Barat 40122';
            const copyButton = document.getElementById('copyAddressButton');
            const copyFeedback = document.getElementById('copyFeedback');

            copyButton?.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(address);
                    copyFeedback.textContent = 'Alamat berhasil disalin.';
                } catch (error) {
                    copyFeedback.textContent = 'Tidak dapat menyalin otomatis. Silakan salin alamat secara manual.';
                }

                window.setTimeout(() => {
                    copyFeedback.textContent = '';
                }, 2800);
            });

            const elements = document.querySelectorAll('.scroll-card, .scroll-fade');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (!('IntersectionObserver' in window) || reduceMotion) {
                elements.forEach(element => element.classList.add('is-inview'));
                return;
            }

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.intersectionRatio >= 0.16) {
                        entry.target.classList.add('is-inview');
                    } else if (!entry.isIntersecting) {
                        entry.target.classList.remove('is-inview');
                    }
                });
            }, {
                threshold:[0,.16,.45],
                rootMargin:'0px 0px -7% 0px',
            });

            elements.forEach(element => observer.observe(element));
        })();
    </script>
</body>
</html>

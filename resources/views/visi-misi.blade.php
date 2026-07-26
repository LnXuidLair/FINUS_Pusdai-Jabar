<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0FB442">

    <title>Visi & Misi | FINUS PUSDAI</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=21">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
            --shadow:0 24px 65px rgba(3,61,24,.13);
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
        a { color:inherit; }
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
            padding:clamp(70px,9vw,122px) 0 clamp(58px,7vw,94px);
            background:
                radial-gradient(circle at 12% 10%,rgba(126,255,135,.23),transparent 21rem),
                radial-gradient(circle at 88% 18%,rgba(34,186,81,.13),transparent 24rem),
                linear-gradient(180deg,#F7FFF9,#EDF9F1);
        }
        .hero::before {
            content:"";
            position:absolute;
            left:-130px;
            top:30px;
            width:360px;
            height:360px;
            border:64px solid rgba(17,138,55,.055);
            border-radius:50%;
        }
        .hero::after {
            content:"";
            position:absolute;
            right:-120px;
            bottom:-170px;
            width:410px;
            height:410px;
            border:72px solid rgba(17,138,55,.06);
            border-radius:50%;
        }
        .hero-inner { position:relative; z-index:2; max-width:850px; margin-inline:auto; text-align:center; }
        .eyebrow {
            display:inline-flex;
            align-items:center;
            gap:9px;
            padding:8px 13px;
            border:1px solid rgba(7,89,35,.15);
            border-radius:999px;
            background:rgba(255,255,255,.78);
            color:var(--g900);
            font-size:10px;
            font-weight:900;
            letter-spacing:.14em;
            text-transform:uppercase;
            box-shadow:0 8px 24px rgba(3,61,24,.08);
        }
        .eyebrow::before { content:""; width:7px; height:7px; border-radius:50%; background:var(--g500); box-shadow:0 0 0 4px rgba(34,186,81,.14); }
        .hero-title {
            margin:22px 0 0;
            color:var(--g950);
            font-family:Georgia,"Times New Roman",serif;
            font-size:clamp(38px,6vw,72px);
            line-height:1.08;
            letter-spacing:-.04em;
        }
        .hero-copy { max-width:680px; margin:20px auto 0; color:var(--muted); font-size:clamp(14px,1.6vw,18px); line-height:1.8; font-weight:600; }

        .content-section { position:relative; padding:clamp(58px,7vw,96px) 0; }
        .vision-grid { display:grid; grid-template-columns:minmax(0,.85fr) minmax(0,1.15fr); gap:22px; align-items:stretch; }
        .vision-card,.mission-panel,.value-card {
            position:relative;
            overflow:hidden;
            border:1px solid rgba(7,89,35,.11);
            border-radius:24px;
            background:rgba(255,255,255,.92);
            box-shadow:var(--shadow);
        }
        .vision-card {
            padding:clamp(28px,4vw,48px);
            background:
                radial-gradient(circle at 100% 0%,rgba(126,255,135,.19),transparent 17rem),
                linear-gradient(145deg,#033D18,#075923 48%,#16A344);
            color:#fff;
        }
        .card-icon { display:inline-flex; align-items:center; justify-content:center; width:58px; height:58px; border:1px solid rgba(255,255,255,.18); border-radius:18px; background:rgba(255,255,255,.11); color:#B7FFBE; font-size:25px; }
        .card-kicker { display:block; margin-top:24px; color:rgba(255,255,255,.67); font-size:10px; font-weight:900; letter-spacing:.15em; text-transform:uppercase; }
        .vision-title { margin:11px 0 0; font-family:Georgia,"Times New Roman",serif; font-size:clamp(27px,3vw,40px); line-height:1.22; }
        .vision-copy { margin:18px 0 0; color:rgba(255,255,255,.78); font-size:14px; line-height:1.85; }
        .mission-panel { padding:clamp(27px,4vw,45px); }
        .section-title { margin:0; color:var(--g950); font-family:Georgia,"Times New Roman",serif; font-size:clamp(28px,4vw,46px); line-height:1.2; }
        .section-copy { margin:12px 0 0; color:var(--muted); font-size:14px; line-height:1.75; }
        .mission-list { display:grid; gap:13px; margin-top:26px; }
        .mission-item { display:grid; grid-template-columns:44px minmax(0,1fr); gap:14px; align-items:start; padding:17px; border:1px solid rgba(7,89,35,.09); border-radius:16px; background:#F8FDF9; }
        .mission-number { display:inline-flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:13px; background:var(--g100); color:var(--g800); font-size:12px; font-weight:900; }
        .mission-item h3 { margin:1px 0 0; color:var(--g950); font-size:15px; }
        .mission-item p { margin:6px 0 0; color:var(--muted); font-size:12px; line-height:1.65; }

        .values-section { background:linear-gradient(180deg,#EDF8F0,#F8FDF9); }
        .values-heading { max-width:720px; margin:0 auto 38px; text-align:center; }
        .values-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:17px; }
        .value-card { padding:26px; min-height:210px; }
        .value-icon { display:inline-flex; align-items:center; justify-content:center; width:50px; height:50px; border-radius:15px; background:var(--g100); color:var(--g800); font-size:21px; }
        .value-card h3 { margin:18px 0 0; color:var(--g950); font-size:17px; }
        .value-card p { margin:9px 0 0; color:var(--muted); font-size:12.5px; line-height:1.7; }

        .scroll-card,.scroll-fade { opacity:0; filter:blur(6px); transform:translateY(42px) scale(.975); transition:opacity .68s cubic-bezier(.2,.72,.2,1),transform .72s cubic-bezier(.2,.72,.2,1),filter .58s ease; transition-delay:var(--delay,0ms); }
        .scroll-card.is-inview,.scroll-fade.is-inview { opacity:1; filter:none; transform:none; }

        .page-footer { padding:26px 0 calc(26px + env(safe-area-inset-bottom)); background:#022F12; color:rgba(255,255,255,.73); }
        .footer-inner { display:flex; align-items:center; justify-content:space-between; gap:22px; }
        .footer-brand { display:flex; align-items:center; gap:13px; }
        .footer-logo { height:40px; width:auto; max-width:130px; object-fit:contain; }
        .footer-copy { font-size:11px; line-height:1.65; }
        .footer-links { display:flex; align-items:center; justify-content:flex-end; gap:14px; flex-wrap:wrap; }
        .footer-links a { font-size:11px; font-weight:800; text-decoration:none; }
        .footer-links a:hover { color:#fff; }

        @media (max-width:900px) {
            .vision-grid { grid-template-columns:1fr; }
            .values-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }
        @media (max-width:720px) {
            :root { --header:70px; }
            .page-container { width:min(calc(100% - 24px),var(--container)); }
            .page-header-inner { padding-inline:12px; gap:10px; }
            .page-logo { height:44px; max-width:150px; }
            .page-nav-link { width:44px; padding:0; }
            .page-nav-label { display:none; }
            .hero { padding-top:58px; }
            .values-grid { grid-template-columns:1fr; }
            .value-card { min-height:0; }
            .footer-inner { align-items:flex-start; flex-direction:column; }
            .footer-links { justify-content:flex-start; }
            .scroll-card,.scroll-fade { transform:translateY(30px) scale(.985); transition-delay:0ms; }
        }
        @media (max-width:390px) {
            .page-logo { height:41px; max-width:135px; }
            .vision-card,.mission-panel,.value-card { border-radius:19px; }
            .mission-item { grid-template-columns:39px minmax(0,1fr); padding:14px; }
            .mission-number { width:39px; height:39px; }
        }
        @media (prefers-reduced-motion:reduce) {
            html { scroll-behavior:auto; }
            .scroll-card,.scroll-fade { opacity:1!important; filter:none!important; transform:none!important; transition:none!important; }
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
                <a href="{{ route('location') }}" class="page-nav-link">
                    <span aria-hidden="true">⌖</span>
                    <span class="page-nav-label">Location</span>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="page-container hero-inner scroll-fade">
                <span class="eyebrow">Arah dan Tujuan FINUS</span>
                <h1 class="hero-title">Visi &amp; Misi FINUS PUSDAI</h1>
                <p class="hero-copy">
                    Landasan pengembangan Sistem Informasi Keuangan Masjid untuk mendukung pengelolaan
                    yang tertib, transparan, terintegrasi, dan mudah digunakan oleh seluruh peran.
                </p>
            </div>
        </section>

        <section class="content-section">
            <div class="page-container vision-grid">
                <article class="vision-card scroll-card" style="--delay:0ms">
                    <span class="card-icon" aria-hidden="true">✦</span>
                    <span class="card-kicker">Visi FINUS</span>
                    <h2 class="vision-title">
                        Menjadi sistem informasi keuangan masjid yang transparan, terintegrasi, dan terpercaya.
                    </h2>
                    <p class="vision-copy">
                        FINUS diarahkan sebagai pusat pengelolaan data keuangan, transaksi jamaah,
                        kepegawaian, presensi, penggajian, dan laporan yang mampu mendukung pelayanan
                        PUSDAI Jawa Barat secara tertib serta berkelanjutan.
                    </p>
                </article>

                <article class="mission-panel scroll-card" style="--delay:130ms">
                    <h2 class="section-title">Misi FINUS</h2>
                    <p class="section-copy">
                        Mewujudkan pengelolaan masjid melalui proses digital yang jelas, aman, dan mudah dipantau.
                    </p>

                    <div class="mission-list">
                        <div class="mission-item">
                            <span class="mission-number">01</span>
                            <div>
                                <h3>Meningkatkan transparansi keuangan</h3>
                                <p>Menyusun pencatatan transaksi, pengeluaran, jurnal, dan laporan dalam alur yang terstruktur.</p>
                            </div>
                        </div>
                        <div class="mission-item">
                            <span class="mission-number">02</span>
                            <div>
                                <h3>Mengintegrasikan pengelolaan masjid</h3>
                                <p>Menghubungkan data jamaah, pegawai, jabatan, presensi, dan penggajian dalam satu aplikasi.</p>
                            </div>
                        </div>
                        <div class="mission-item">
                            <span class="mission-number">03</span>
                            <div>
                                <h3>Menjaga keamanan akses</h3>
                                <p>Menerapkan hak akses berdasarkan peran serta verifikasi tambahan untuk pengguna internal.</p>
                            </div>
                        </div>
                        <div class="mission-item">
                            <span class="mission-number">04</span>
                            <div>
                                <h3>Meningkatkan kualitas pelayanan</h3>
                                <p>Menyediakan layanan digital yang responsif dan nyaman digunakan melalui desktop, tablet, maupun HP.</p>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="content-section values-section">
            <div class="page-container">
                <div class="values-heading scroll-fade">
                    <span class="eyebrow">Nilai Utama</span>
                    <h2 class="section-title" style="margin-top:18px">Prinsip yang menjadi dasar FINUS</h2>
                    <p class="section-copy">Setiap fitur dikembangkan dengan mempertimbangkan ketertiban data, kemudahan penggunaan, dan akuntabilitas.</p>
                </div>

                <div class="values-grid">
                    <article class="value-card scroll-card" style="--delay:0ms">
                        <span class="value-icon" aria-hidden="true">✓</span>
                        <h3>Transparan</h3>
                        <p>Informasi keuangan dicatat secara jelas sehingga lebih mudah dipantau dan dipertanggungjawabkan.</p>
                    </article>
                    <article class="value-card scroll-card" style="--delay:100ms">
                        <span class="value-icon" aria-hidden="true">◎</span>
                        <h3>Terintegrasi</h3>
                        <p>Data lintas modul saling terhubung untuk mengurangi pencatatan berulang dan ketidaksesuaian informasi.</p>
                    </article>
                    <article class="value-card scroll-card" style="--delay:200ms">
                        <span class="value-icon" aria-hidden="true">◆</span>
                        <h3>Terpercaya</h3>
                        <p>Hak akses, validasi, dan struktur proses dirancang untuk menjaga keamanan serta konsistensi data.</p>
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
                <a href="{{ route('location') }}">Location</a>
                <a href="mailto:pusdaijabar@gmail.com">Contacts</a>
            </div>
        </div>
    </footer>

    <script>
        (() => {
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

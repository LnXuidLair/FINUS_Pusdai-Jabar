<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0FB442">
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

            const root = document.documentElement;
            root.dataset.finusTheme = theme;
            root.style.setProperty(
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
                    theme === 'dark' ? '#08150D' : '#0FB442'
                );
            }
        })();
    </script>


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
        .auth-home-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            justify-self: end;
            gap: 9px;
            min-height: 42px;
            padding: 0 15px;
            border: 1px solid rgba(255, 255, 255, .46);
            border-radius: 12px;
            background: rgba(255, 255, 255, .95);
            color: var(--auth-green-900);
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(0, 45, 16, .15);
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .auth-home-link:hover { transform: translateY(-2px); background: #fff; box-shadow: 0 12px 24px rgba(0, 45, 16, .20); }
        .auth-home-link:focus-visible { outline: 3px solid rgba(255,255,255,.55); outline-offset: 3px; }
        .auth-home-icon {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            min-width: 26px;
            height: 26px;
            border-radius: 8px;
            background: var(--auth-green-50);
            color: var(--auth-green-700);
            font-size: 0;
            line-height: 1;
        }
        .auth-home-icon::before {
            content: "";
            position: absolute;
            top: 8px;
            width: 12px;
            height: 10px;
            border: 2px solid currentColor;
            border-top: 0;
            border-radius: 2px 2px 3px 3px;
        }
        .auth-home-icon::after {
            content: "";
            position: absolute;
            top: 6px;
            width: 12px;
            height: 12px;
            border-left: 2px solid currentColor;
            border-top: 2px solid currentColor;
            transform: rotate(45deg);
            transform-origin: center;
        }
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
        @media (max-width: 767.98px) {
            .auth-home-link { min-height: 38px; padding: 0 11px; border-radius: 11px; }
            .auth-home-link-text { display: none; }
            .auth-home-icon { width: 25px; height: 25px; }
        }
    </style>

    <style>
        /* FINUS UI refresh */
        .hero{padding:clamp(58px,7vw,92px) 0 clamp(52px,6vw,78px);min-height:430px;display:flex;align-items:center}
        .hero-inner{max-width:980px}
        .hero-inner::before{content:"";display:block;width:72px;height:5px;margin:0 auto 22px;border-radius:999px;background:linear-gradient(90deg,var(--g300),var(--g500),var(--g800));box-shadow:0 8px 26px rgba(17,138,55,.18)}
        .hero-title{max-width:900px;margin-inline:auto;text-wrap:balance}
        .hero-copy{max-width:760px}
        .content-section{padding:clamp(48px,6vw,84px) 0}
        .vision-grid{grid-template-columns:minmax(0,.82fr) minmax(0,1.18fr);gap:28px}
        .vision-card,.mission-panel,.value-card{border-radius:28px;box-shadow:0 24px 70px rgba(3,61,24,.11)}
        .vision-card{display:flex;flex-direction:column;justify-content:center;min-height:610px;padding:clamp(34px,5vw,58px);isolation:isolate}
        .vision-card::before{content:"";position:absolute;right:-85px;top:-85px;width:240px;height:240px;border:46px solid rgba(255,255,255,.065);border-radius:50%;z-index:-1}
        .vision-card::after{content:"VISI";position:absolute;right:-4px;bottom:-32px;color:rgba(255,255,255,.045);font:900 112px/1 "Nunito",sans-serif;letter-spacing:-.06em;z-index:-1}
        .card-icon{width:64px;height:64px;border-radius:20px;font-size:28px;box-shadow:inset 0 1px 0 rgba(255,255,255,.18)}
        .vision-title{font-size:clamp(30px,3.3vw,44px);text-wrap:balance}
        .vision-copy{font-size:14px;line-height:1.9}
        .mission-panel{padding:clamp(32px,4vw,48px);background:linear-gradient(180deg,#fff,#FBFEFC)}
        .mission-panel::before{content:"04 MISI UTAMA";display:inline-flex;margin-bottom:18px;padding:7px 10px;border-radius:999px;background:var(--g100);color:var(--g800);font-size:9px;font-weight:900;letter-spacing:.14em}
        .mission-list{gap:12px;margin-top:24px}
        .mission-item{position:relative;overflow:hidden;grid-template-columns:50px minmax(0,1fr);gap:15px;padding:18px;border-radius:18px;background:#F8FDF9;transition:transform .22s ease,border-color .22s ease,box-shadow .22s ease}
        .mission-item::after{content:"";position:absolute;inset:0 auto 0 0;width:4px;background:linear-gradient(var(--g300),var(--g700));opacity:0;transition:opacity .2s ease}
        .mission-item:hover{transform:translateX(4px);border-color:rgba(17,138,55,.2);box-shadow:0 12px 28px rgba(3,61,24,.07)}
        .mission-item:hover::after{opacity:1}
        .mission-number{width:50px;height:50px;border-radius:15px;font-size:13px}
        .mission-item h3{font-size:15.5px}
        .values-section{position:relative;overflow:hidden;background:linear-gradient(180deg,#EDF8F0 0%,#F8FDF9 70%,#fff 100%)}
        .values-section::before{content:"";position:absolute;left:50%;top:0;width:min(900px,90vw);height:1px;background:linear-gradient(90deg,transparent,rgba(17,138,55,.25),transparent);transform:translateX(-50%)}
        .values-heading{margin-bottom:42px}
        .values-grid{gap:20px}
        .value-card{min-height:235px;padding:30px;transition:transform .24s ease,box-shadow .24s ease,border-color .24s ease}
        .value-card:hover{transform:translateY(-7px);border-color:rgba(17,138,55,.2);box-shadow:0 30px 70px rgba(3,61,24,.14)}
        .value-icon{width:54px;height:54px;border-radius:17px;box-shadow:0 9px 22px rgba(17,138,55,.09)}
        .value-card h3{font-size:18px}
        .value-card p{font-size:13px;line-height:1.75}
        .page-footer{position:relative;overflow:hidden;padding:0;background:linear-gradient(135deg,#012A0F,#033D18 52%,#075923);color:rgba(255,255,255,.72)}
        .page-footer::before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 12% 20%,rgba(126,255,135,.10),transparent 20rem);pointer-events:none}
        .footer-inner{position:relative;display:grid;grid-template-columns:1.2fr .8fr;align-items:center;gap:30px;padding-top:34px;padding-bottom:34px}
        .footer-brand{gap:16px}.footer-logo{height:48px;max-width:155px}.footer-copy{font-size:11.5px;line-height:1.7}
        .footer-links{gap:8px}.footer-links a{padding:9px 12px;border:1px solid rgba(255,255,255,.1);border-radius:10px;background:rgba(255,255,255,.045);transition:.2s ease}.footer-links a:hover{background:rgba(255,255,255,.10);transform:translateY(-2px)}
        @media(max-width:900px){.vision-card{min-height:auto}.vision-grid{gap:20px}.footer-inner{grid-template-columns:1fr}}
        @media(max-width:720px){.hero{min-height:365px}.vision-card,.mission-panel,.value-card{border-radius:22px}.mission-item{grid-template-columns:44px minmax(0,1fr)}.mission-number{width:44px;height:44px}.footer-inner{padding-top:28px;padding-bottom:28px}.footer-links{justify-content:flex-start}}
    

        /* =====================================================
           MODE TAMPILAN FINUS
           Default selalu terang dan tidak mengikuti mode perangkat.
        ===================================================== */
        html[data-finus-theme="light"] { color-scheme: only light !important; }
        html[data-finus-theme="dark"] { color-scheme: dark; }

        .page-theme-toggle {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:44px;
            min-width:44px;
            height:44px;
            padding:0;
            border:1px solid rgba(255,255,255,.34);
            border-radius:13px;
            background:rgba(255,255,255,.94);
            color:var(--g950);
            cursor:pointer;
            box-shadow:0 9px 20px rgba(0,54,17,.14);
            transition:transform .2s ease,background .2s ease,box-shadow .2s ease;
        }

        .page-theme-toggle:hover,
        .page-theme-toggle:focus-visible {
            background:#fff;
            outline:none;
            transform:translateY(-2px);
            box-shadow:0 14px 28px rgba(0,54,17,.20);
        }

        .theme-icon {
            width:20px;
            height:20px;
            fill:none;
            stroke:currentColor;
            stroke-width:2;
            stroke-linecap:round;
            stroke-linejoin:round;
        }

        .theme-icon-moon { display:none; }
        html[data-finus-theme="dark"] .theme-icon-sun { display:none; }
        html[data-finus-theme="dark"] .theme-icon-moon { display:block; }

        html[data-finus-theme="dark"] {
            --g50:#0A120D;
            --text:#EAF4ED;
            --muted:#A6B7AB;
            --shadow:0 24px 65px rgba(0,0,0,.34);
        }

        html[data-finus-theme="dark"] body {
            background:#0A120D;
            color:var(--text);
        }

        html[data-finus-theme="dark"] .page-theme-toggle,
        html[data-finus-theme="dark"] .auth-home-link {
            border-color:#35503B;
            background:#132119;
            color:#BCEFC7;
            box-shadow:0 9px 20px rgba(0,0,0,.24);
        }

        html[data-finus-theme="dark"] .page-theme-toggle:hover,
        html[data-finus-theme="dark"] .auth-home-link:hover {
            background:#192B1F;
        }

        html[data-finus-theme="dark"] .auth-home-icon {
            background:#17331F;
            color:#79E790;
        }

        html.finus-theme-changing *,
        html.finus-theme-changing *::before,
        html.finus-theme-changing *::after {
            transition:
                background-color .2s ease,
                border-color .2s ease,
                color .2s ease,
                box-shadow .2s ease !important;
        }

        @media (max-width:720px) {
            .page-theme-toggle {
                width:44px;
                min-width:44px;
                height:44px;
                border-radius:12px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html.finus-theme-changing *,
            html.finus-theme-changing *::before,
            html.finus-theme-changing *::after {
                transition:none !important;
            }
        }

        html[data-finus-theme="dark"] .hero {
            background:
                radial-gradient(circle at 12% 10%,rgba(34,186,81,.10),transparent 21rem),
                radial-gradient(circle at 88% 18%,rgba(34,186,81,.07),transparent 24rem),
                linear-gradient(180deg,#0E1811,#0A120D);
        }

        html[data-finus-theme="dark"] .eyebrow,
        html[data-finus-theme="dark"] .mission-panel,
        html[data-finus-theme="dark"] .value-card {
            border-color:#2C4132;
            background:#111C15;
            color:#EAF4ED;
            box-shadow:0 18px 48px rgba(0,0,0,.24);
        }

        html[data-finus-theme="dark"] .hero-title,
        html[data-finus-theme="dark"] .section-title,
        html[data-finus-theme="dark"] .mission-item h3,
        html[data-finus-theme="dark"] .value-card h3 {
            color:#EAF4ED;
        }

        html[data-finus-theme="dark"] .hero-copy,
        html[data-finus-theme="dark"] .section-copy,
        html[data-finus-theme="dark"] .mission-item p,
        html[data-finus-theme="dark"] .value-card p {
            color:#A6B7AB;
        }

        html[data-finus-theme="dark"] .mission-item {
            border-color:#2C4132;
            background:#15231A;
        }

        html[data-finus-theme="dark"] .mission-number,
        html[data-finus-theme="dark"] .value-icon {
            background:#17331F;
            color:#79E790;
        }

        html[data-finus-theme="dark"] .values-section {
            background:linear-gradient(180deg,#0E1811,#0A120D);
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

<style data-finus-dark-local="visi-misi.blade.php">
/* FINUS DARK MODE LOCAL: visi-misi.blade.php */
html[data-finus-theme="dark"] body { background:#0A110D !important; color:#F1F6F3 !important; }
html[data-finus-theme="dark"] :where(.mission-panel,.mission-item,.value-card) { border-color:#293D31 !important; background:linear-gradient(155deg,#15211A,#111A15) !important; color:#F1F6F3 !important; box-shadow:0 14px 34px rgba(0,0,0,.18) !important; }
html[data-finus-theme="dark"] :where(.hero-title,.section-title,.mission-item h3,.value-card h3) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] :where(.hero-copy,.section-copy,.mission-item p,.value-card p) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] .values-section { background:#0D1711 !important; }
</style>

</head>
<body>
    <header class="page-header">
        <div class="page-header-inner">
            <img src="{{ asset('assets/images/FINUS_Welcome.png') }}" alt="FINUS PUSDAI" class="page-logo">

            <nav class="page-nav" aria-label="Navigasi halaman">
                <button
                    type="button"
                    class="page-theme-toggle"
                    data-finus-theme-toggle
                    aria-label="Ubah mode tampilan FINUS"
                    aria-pressed="false"
                    title="Mode gelap"
                >
                    <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.65 17.65l1.42 1.42M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.65 6.35l1.42-1.42"></path>
                    </svg>
                    <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20.5 14.5A8 8 0 0 1 9.5 3.5 8.5 8.5 0 1 0 20.5 14.5Z"></path>
                    </svg>
                </button>

                <a href="{{ route('home') }}" class="auth-home-link" data-loading-title="Kembali ke beranda...">
                    <span class="auth-home-icon" aria-hidden="true"></span>
                    <span class="auth-home-link-text">Beranda</span>
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

            const updateThemeButtons = theme => {
                const dark = theme === 'dark';

                document
                    .querySelectorAll('[data-finus-theme-toggle]')
                    .forEach(button => {
                        button.setAttribute(
                            'aria-pressed',
                            dark ? 'true' : 'false'
                        );

                        button.setAttribute(
                            'aria-label',
                            dark
                                ? 'Ubah FINUS ke mode terang'
                                : 'Ubah FINUS ke mode gelap'
                        );

                        button.setAttribute(
                            'title',
                            dark
                                ? 'Mode terang'
                                : 'Mode gelap'
                        );
                    });
            };

            const applyTheme = theme => {
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
                            : '#0FB442'
                    );
                }

                try {
                    localStorage.setItem(storageKey, normalized);
                } catch (_) {}

                updateThemeButtons(normalized);

                window.setTimeout(() => {
                    root.classList.remove('finus-theme-changing');
                }, 220);
            };

            updateThemeButtons(readTheme());

            document.addEventListener('click', event => {
                const button = event.target.closest(
                    '[data-finus-theme-toggle]'
                );

                if (!button) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                applyTheme(
                    readTheme() === 'dark'
                        ? 'light'
                        : 'dark'
                );
            });
        })();
    </script>
</body>
</html>
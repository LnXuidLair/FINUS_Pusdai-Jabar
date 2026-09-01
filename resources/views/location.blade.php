<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0FB442">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
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
            root.style.colorScheme = theme;

            const colorSchemeMeta = document.querySelector(
                'meta[name="color-scheme"]'
            );

            if (colorSchemeMeta) {
                colorSchemeMeta.setAttribute('content', theme);
            }

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
        @media (max-width: 767.98px) {
            .auth-home-link { min-height: 38px; padding: 0 11px; border-radius: 11px; }
            .auth-home-link-text { display: none; }
            .auth-home-icon { width: 25px; height: 25px; }
        }
    </style>

    <style>
        /* FINUS location UI refresh */
        .hero{min-height:410px;display:flex;align-items:center;padding:clamp(58px,7vw,92px) 0 clamp(48px,6vw,72px)}
        .hero-inner{max-width:900px}.hero-inner::before{content:"";display:block;width:72px;height:5px;margin:0 auto 22px;border-radius:999px;background:linear-gradient(90deg,var(--g300),var(--g500),var(--g800));box-shadow:0 8px 26px rgba(17,138,55,.18)}
        .hero-title{text-wrap:balance}.hero-copy{max-width:710px}
        .location-section{padding:clamp(46px,6vw,80px) 0 clamp(60px,7vw,96px)}
        .location-grid{grid-template-columns:minmax(0,1.55fr) minmax(330px,.72fr);gap:26px}
        .map-card,.info-card,.mini-card{border-radius:28px;box-shadow:0 24px 70px rgba(3,61,24,.11)}
        .map-card{min-height:620px;padding:10px;background:linear-gradient(145deg,#fff,#EEF9F1)}
        .map-frame-wrap{min-height:600px;border-radius:21px;box-shadow:inset 0 0 0 1px rgba(3,61,24,.06)}
        .map-frame{filter:saturate(.93) contrast(1.02)}
        .map-live-label{top:20px;left:20px;padding:10px 14px;box-shadow:0 12px 32px rgba(0,39,12,.26)}
        .info-stack{gap:18px}.info-card{padding:clamp(30px,4vw,42px);isolation:isolate}
        .info-card::after{content:"";position:absolute;right:-90px;bottom:-105px;width:260px;height:260px;border:48px solid rgba(255,255,255,.055);border-radius:50%;z-index:-1}
        .info-title{font-size:clamp(28px,3.1vw,40px)}.info-copy{font-size:13.2px;line-height:1.8}
        .contact-list{gap:10px}.contact-link{grid-template-columns:46px minmax(0,1fr);padding:13px;border-radius:17px}.contact-icon{width:46px;height:46px;border-radius:14px;font-size:18px}
        .action-button{min-height:49px;border-radius:14px}.copy-feedback{margin-bottom:-4px}
        .mini-card{min-height:128px;padding:25px 26px 24px 74px;display:flex;flex-direction:column;justify-content:center}
        .mini-card::before{position:absolute;left:24px;top:50%;display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:var(--g100);color:var(--g800);font-weight:900;transform:translateY(-50%)}
        .mini-card.map-feature::before{content:"+"}.mini-card.device-feature::before{content:"✓"}
        .mini-card h2{font-size:17px}.mini-card p{font-size:12.5px;line-height:1.65}
        .page-footer{position:relative;overflow:hidden;padding:0;background:linear-gradient(135deg,#012A0F,#033D18 52%,#075923)}
        .page-footer::before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 12% 20%,rgba(126,255,135,.10),transparent 20rem);pointer-events:none}
        .footer-inner{position:relative;display:grid;grid-template-columns:1.2fr .8fr;align-items:center;gap:30px;padding-top:34px;padding-bottom:34px}.footer-brand{gap:16px}.footer-logo{height:48px;max-width:155px}.footer-copy{font-size:11.5px;line-height:1.7}
        .footer-links{gap:8px}.footer-links a{padding:9px 12px;border:1px solid rgba(255,255,255,.1);border-radius:10px;background:rgba(255,255,255,.045);transition:.2s ease}.footer-links a:hover{background:rgba(255,255,255,.10);transform:translateY(-2px)}


        .current-location-overlay{position:absolute;left:20px;right:20px;bottom:20px;z-index:5;display:grid;grid-template-columns:42px minmax(0,1fr) auto;align-items:center;gap:11px;padding:11px 13px;border:1px solid rgba(255,255,255,.22);border-radius:15px;background:rgba(3,61,24,.93);color:#fff;box-shadow:0 14px 34px rgba(0,35,12,.28);backdrop-filter:blur(12px)}
        .current-location-icon{display:grid;place-items:center;width:42px;height:42px;border-radius:12px;background:rgba(126,255,135,.15);color:#9BFFA5;font-size:20px}
        .current-location-label{display:block;color:rgba(255,255,255,.62);font-size:8px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}
        .current-location-value{display:block;margin-top:3px;color:#fff;font-size:11px;font-weight:900;line-height:1.4;overflow-wrap:anywhere}
        .current-location-state{display:inline-flex;align-items:center;justify-content:center;min-height:28px;padding:0 9px;border-radius:999px;background:rgba(255,255,255,.10);color:#B7FFBE;font-size:8px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap}
        .current-location-state.is-error{background:rgba(220,53,69,.17);color:#FFD0D5}
        /* Live GPS & travel estimate */
        .gps-card{position:relative;overflow:hidden;padding:25px 26px;border:1px solid rgba(7,89,35,.11);border-radius:28px;background:linear-gradient(145deg,#ffffff,#f4fbf6);box-shadow:0 24px 70px rgba(3,61,24,.11);container-type:inline-size}
        .gps-head{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:start;gap:14px}
        .gps-head>div{min-width:0}
        .gps-kicker{display:inline-flex;align-items:center;gap:8px;color:var(--g800);font-size:9px;font-weight:900;letter-spacing:.14em;text-transform:uppercase}
        .gps-kicker::before{content:"";width:8px;height:8px;border-radius:50%;background:var(--g500);box-shadow:0 0 0 5px rgba(34,186,81,.13);animation:pulse 1.8s ease-in-out infinite}
        .gps-card h2{margin:9px 0 0;color:var(--g950);font-size:20px;line-height:1.28;text-wrap:balance}
        .gps-status{max-width:48ch;margin:9px 0 0;color:var(--muted);font-size:11.5px;line-height:1.65}
        .gps-badge{display:inline-flex;align-items:center;justify-content:center;min-height:30px;padding:0 10px;border:1px solid rgba(7,89,35,.10);border-radius:999px;background:var(--g100);color:var(--g800);font-size:8.5px;font-weight:900;white-space:nowrap}
        .gps-badge.is-error{background:#FFF1F2;color:#B42333;border-color:#FFD8DC}
        .gps-badge.is-live{background:#E9FBEF;color:#08752D;border-color:#BCEFCB}
        .travel-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:18px}
        .travel-card{position:relative;min-width:0;overflow:hidden;padding:16px 15px;border:1px solid rgba(7,89,35,.10);border-radius:17px;background:linear-gradient(145deg,#fff,#f7fcf8);box-shadow:0 8px 22px rgba(3,61,24,.055)}
        .travel-card::after{content:"";position:absolute;right:-28px;bottom:-34px;width:92px;height:92px;border:18px solid rgba(34,186,81,.05);border-radius:50%}
        .travel-card>*{position:relative;z-index:1}
        .travel-top{display:flex;align-items:center;justify-content:space-between;gap:8px}
        .travel-mode{display:flex;min-width:0;align-items:center;gap:8px;color:var(--g950);font-size:11.5px;font-weight:900}
        .travel-icon{display:grid;place-items:center;width:32px;height:32px;flex:0 0 32px;border-radius:10px;background:var(--g100);color:var(--g800);font-size:16px}
        .travel-distance{margin-top:13px;color:var(--g950);font-family:Georgia,"Times New Roman",serif;font-size:26px;font-weight:800;line-height:1;white-space:nowrap}
        .travel-time{margin-top:8px;color:var(--g800);font-size:11.5px;font-weight:900;white-space:nowrap}
        .gps-position{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(115px,.65fr);gap:10px;margin-top:11px}
        .gps-position-item{min-width:0;padding:12px 13px;border:1px solid rgba(7,89,35,.08);border-radius:14px;background:#F8FDF9}
        .gps-position-label{display:block;color:#7A8A80;font-size:7.8px;font-weight:900;letter-spacing:.1em;text-transform:uppercase}
        .gps-position-value{display:block;margin-top:5px;color:var(--g950);font-size:11px;font-weight:900;line-height:1.45;overflow-wrap:anywhere}
        .gps-position-item.is-warning{border-color:#F0D59C;background:#FFF9EC}
        .gps-position-item.is-warning .gps-position-value{color:#8A5A00}
        .location-correction{margin-top:11px;padding:11px 12px;border:1px dashed rgba(7,89,35,.17);border-radius:14px;background:rgba(251,254,252,.9);transition:border-color .2s ease,background .2s ease}
        .location-correction.is-confirmed{border-style:solid;border-color:#BCEFCB;background:#F2FCF5}
        .location-correction-toggle{display:flex;width:100%;align-items:center;justify-content:space-between;gap:10px;padding:0;border:0;background:transparent;color:var(--g800);font-size:10px;font-weight:900;line-height:1.4;text-align:left;cursor:pointer}
        .location-correction-toggle::after{content:"+";display:grid;place-items:center;width:22px;height:22px;flex:0 0 22px;border-radius:7px;background:var(--g100);color:var(--g800);font-size:14px;line-height:1}
        .location-correction-toggle[aria-expanded="true"]::after{content:"−"}
        .location-correction-toggle:hover,.location-correction-toggle:focus-visible{color:var(--g950);outline:none}
        .location-correction-form{display:none;grid-template-columns:minmax(0,1fr) auto;gap:8px;margin-top:10px}
        .location-correction-form.is-open{display:grid}
        .location-correction-input{width:100%;min-width:0;min-height:42px;padding:0 12px;border:1px solid rgba(7,89,35,.14);border-radius:11px;background:#fff;color:var(--g950);font:inherit;font-size:11px;font-weight:700;outline:none}
        .location-correction-input:focus{border-color:var(--g600);box-shadow:0 0 0 3px rgba(34,186,81,.10)}
        .location-correction-submit{min-height:42px;padding:0 12px;border:1px solid var(--g700);border-radius:11px;background:var(--g800);color:#fff;font:inherit;font-size:10px;font-weight:900;white-space:nowrap;cursor:pointer}
        .location-correction-submit:disabled{opacity:.65;cursor:wait}
        .location-correction-feedback{margin:8px 0 0;color:var(--muted);font-size:9.2px;line-height:1.5}
        .location-correction-feedback:empty{display:none}
        .location-correction-feedback.is-error{color:#B42333}
        .travel-note{margin:11px 0 0;padding-top:10px;border-top:1px solid rgba(7,89,35,.08);color:#78877E;font-size:9.5px;line-height:1.55}
        .gps-actions{display:grid;grid-template-columns:minmax(0,.82fr) minmax(0,1.35fr);gap:8px;margin-top:13px}
        .gps-action{display:inline-flex;min-width:0;align-items:center;justify-content:center;gap:7px;min-height:42px;padding:0 12px;border:1px solid rgba(7,89,35,.13);border-radius:12px;background:#fff;color:var(--g900);font-size:10px;font-weight:900;line-height:1.25;text-align:center;text-decoration:none;cursor:pointer;transition:.2s ease}
        .gps-action:hover,.gps-action:focus-visible{transform:translateY(-2px);outline:none;box-shadow:0 10px 22px rgba(3,61,24,.09)}
        .gps-action.is-primary{border-color:var(--g700);background:linear-gradient(100deg,#075923,#118A37 56%,#16A344);color:#fff}
        @container (max-width:390px){
            .gps-head{grid-template-columns:1fr;gap:9px}.gps-badge{justify-self:start}.gps-card h2{font-size:19px}
            .gps-position{grid-template-columns:1fr}.location-correction-form{grid-template-columns:1fr}.location-correction-submit{width:100%}
            .gps-actions{grid-template-columns:1fr}.gps-action{width:100%}
        }
        @container (max-width:300px){.travel-grid{grid-template-columns:1fr}.travel-distance{font-size:25px}}
        @media(max-width:720px){.gps-card{border-radius:22px;padding:20px}.current-location-overlay{left:13px;right:13px;bottom:13px;grid-template-columns:36px minmax(0,1fr);padding:9px 10px}.current-location-icon{width:36px;height:36px}.current-location-state{grid-column:1/-1;justify-self:start;margin-left:47px}}
        @media(max-width:980px){.location-grid{gap:20px}.map-card{min-height:520px}.map-frame-wrap{min-height:500px}.footer-inner{grid-template-columns:1fr}}
        @media(max-width:720px){.hero{min-height:350px}.map-card{min-height:440px;border-radius:22px}.map-frame-wrap{min-height:420px;border-radius:16px}.info-card,.mini-card{border-radius:22px}.mini-card{padding-left:68px}.footer-inner{padding-top:28px;padding-bottom:28px}.footer-links{justify-content:flex-start}}
    

        /* =====================================================
           MODE TAMPILAN FINUS
           Default selalu terang dan tidak mengikuti mode perangkat.
        ===================================================== */
        html[data-finus-theme="light"] { color-scheme: light; }
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
                radial-gradient(circle at 10% 15%,rgba(34,186,81,.10),transparent 20rem),
                radial-gradient(circle at 88% 12%,rgba(34,186,81,.07),transparent 24rem),
                linear-gradient(180deg,#0E1811,#0A120D);
        }

        html[data-finus-theme="dark"] .eyebrow,
        html[data-finus-theme="dark"] .map-card,
        html[data-finus-theme="dark"] .mini-card,
        html[data-finus-theme="dark"] .gps-card,
        html[data-finus-theme="dark"] .travel-card,
        html[data-finus-theme="dark"] .gps-position-item {
            border-color:#2C4132;
            background:#111C15;
            color:#EAF4ED;
            box-shadow:0 18px 48px rgba(0,0,0,.24);
        }

        html[data-finus-theme="dark"] .hero-title,
        html[data-finus-theme="dark"] .mini-card h2,
        html[data-finus-theme="dark"] .gps-card h2,
        html[data-finus-theme="dark"] .travel-distance,
        html[data-finus-theme="dark"] .gps-position-value {
            color:#EAF4ED;
        }

        html[data-finus-theme="dark"] .hero-copy,
        html[data-finus-theme="dark"] .mini-card p,
        html[data-finus-theme="dark"] .gps-status,
        html[data-finus-theme="dark"] .travel-time,
        html[data-finus-theme="dark"] .travel-note,
        html[data-finus-theme="dark"] .gps-position-label {
            color:#A6B7AB;
        }

        html[data-finus-theme="dark"] .map-frame-wrap {
            background:#17251B;
        }

        html[data-finus-theme="dark"] .location-correction,
        html[data-finus-theme="dark"] .device-feature {
            border-color:#2C4132;
            background:#142119;
            color:#D8E8DC;
        }

        html[data-finus-theme="dark"] .location-correction-input {
            border-color:#36503C;
            background:#0E1811;
            color:#EDF7EF;
        }

        html[data-finus-theme="dark"] .location-correction-input::placeholder {
            color:#74867A;
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
                <span class="eyebrow">Live Map PUSDAI</span>
                <h1 class="hero-title">Lokasi PUSDAI Jawa Barat</h1>
                <p class="hero-copy">
                    Aktifkan lokasi perangkat untuk melihat posisi Anda saat ini, rute menuju PUSDAI,
                    serta jarak dan estimasi waktu perjalanan menggunakan mobil atau motor.
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
                            src="https://www.google.com/maps?q=Masjid%20PUSDAI%20Jabar%2C%20Jl.%20Diponegoro%20No.%2063%2C%20Cihaur%20Geulis%2C%20Bandung&z=17&output=embed"
                            loading="lazy"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                        <div class="current-location-overlay" aria-live="polite">
                            <span class="current-location-icon" aria-hidden="true">⌖</span>
                            <span>
                                <span class="current-location-label">Lokasi Anda Saat Ini</span>
                                <strong class="current-location-value" id="mapCurrentLocation">Sedang meningkatkan akurasi GPS...</strong>
                            </span>
                            <span class="current-location-state" id="mapGpsState">Mencari GPS</span>
                        </div>
                    </div>
                </article>

                <div class="info-stack">
                    <article class="gps-card" aria-labelledby="gpsTitle">
                        <div class="gps-head">
                            <div>
                                <span class="gps-kicker">Live GPS</span>
                                <h2 id="gpsTitle">Perjalanan dari lokasi Anda</h2>
                                <p class="gps-status" id="gpsStatus" aria-live="polite">Meminta izin lokasi perangkat...</p>
                            </div>
                            <span class="gps-badge" id="gpsBadge">Mencari GPS</span>
                        </div>

                        <div class="travel-grid" aria-label="Estimasi perjalanan menuju PUSDAI">
                            <div class="travel-card">
                                <div class="travel-top">
                                    <div class="travel-mode"><span class="travel-icon" aria-hidden="true">🚗</span>Mobil</div>
                                </div>
                                <div class="travel-distance" id="carDistance">—</div>
                                <div class="travel-time" id="carDuration">Menunggu lokasi</div>
                            </div>
                            <div class="travel-card">
                                <div class="travel-top">
                                    <div class="travel-mode"><span class="travel-icon" aria-hidden="true">🏍</span>Motor</div>
                                </div>
                                <div class="travel-distance" id="motorDistance">—</div>
                                <div class="travel-time" id="motorDuration">Menunggu lokasi</div>
                            </div>
                        </div>

                        <div class="gps-position">
                            <div class="gps-position-item" id="currentLocationBox">
                                <span class="gps-position-label">Lokasi Anda Saat Ini</span>
                                <span class="gps-position-value" id="currentCoords">Sedang meningkatkan akurasi GPS...</span>
                            </div>
                            <div class="gps-position-item" id="gpsAccuracyBox">
                                <span class="gps-position-label" id="gpsAccuracyLabel">Akurasi GPS</span>
                                <span class="gps-position-value" id="gpsAccuracy">—</span>
                            </div>
                        </div>
                        <div class="location-correction">
                            <button type="button" class="location-correction-toggle" id="toggleLocationCorrection" aria-expanded="false" aria-controls="locationCorrectionForm">
                                Lokasi kurang tepat? Atur alamat
                            </button>
                            <div class="location-correction-form" id="locationCorrectionForm">
                                <input type="text" class="location-correction-input" id="manualLocationInput" placeholder="Contoh: nama jalan, nomor, kelurahan, kecamatan, kota" autocomplete="street-address">
                                <button type="button" class="location-correction-submit" id="applyManualLocationButton">Gunakan alamat</button>
                            </div>
                            <p class="location-correction-feedback" id="locationCorrectionFeedback" aria-live="polite"></p>
                        </div>
                        <p class="travel-note" id="travelNote">
                            Jarak mengikuti rute jalan. Estimasi waktu memakai asumsi kecepatan rata-rata perkotaan (mobil ±30 km/jam, motor ±36 km/jam) dan tidak menggunakan lalu lintas langsung.
                        </p>
                        <div class="gps-actions">
                            <button type="button" class="gps-action" id="refreshGpsButton">Perbarui Lokasi</button>
                            <a class="gps-action is-primary" id="liveDirectionsButton" href="https://www.google.com/maps/dir/?api=1&destination=-6.900079,107.625866" target="_blank" rel="noopener noreferrer">Buka Rute di Google Maps ↗</a>
                        </div>
                    </article>
                    <article class="info-card scroll-card" style="--delay:120ms">
                        <span class="info-kicker">Informasi Lokasi</span>
                        <h2 class="info-title">PUSDAI Jawa Barat</h2>
                        <p class="info-copy">
                            Pusat Dakwah Islam Jawa Barat berada di kawasan pusat Kota Bandung dan dapat
                            diakses melalui peta interaktif di samping.
                        </p>
                        <div class="contact-list">
                            <a href="https://www.google.com/maps/dir/?api=1&destination=PUSDAI+Jawa+Barat+Jalan+Diponegoro+No+63+Bandung" target="_blank" rel="noopener noreferrer" class="contact-link">
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
                    <article class="mini-card map-feature scroll-card" style="--delay:190ms">
                        <h2>Peta interaktif</h2>
                        <p>Peta dapat digeser, diperbesar, diperkecil, dan dibuka langsung melalui Google Maps.</p>
                    </article>
                    <article class="mini-card device-feature scroll-card" style="--delay:230ms">
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
            const PUSDAI = { lat: -6.900079, lng: 107.625866 };
            const PUSDAI_LABEL = 'Masjid PUSDAI Jabar, Jl. Diponegoro No. 63, Cihaur Geulis, Cibeunying Kaler, Kota Bandung, Jawa Barat 40122';
            const copyButton = document.getElementById('copyAddressButton');
            const copyFeedback = document.getElementById('copyFeedback');
            const gpsStatus = document.getElementById('gpsStatus');
            const gpsBadge = document.getElementById('gpsBadge');
            const currentCoords = document.getElementById('currentCoords');
            const gpsAccuracy = document.getElementById('gpsAccuracy');
            const gpsAccuracyLabel = document.getElementById('gpsAccuracyLabel');
            const carDistance = document.getElementById('carDistance');
            const carDuration = document.getElementById('carDuration');
            const motorDistance = document.getElementById('motorDistance');
            const motorDuration = document.getElementById('motorDuration');
            const refreshGpsButton = document.getElementById('refreshGpsButton');
            const liveDirectionsButton = document.getElementById('liveDirectionsButton');
            const mapFrame = document.querySelector('.map-frame');
            const mapCurrentLocation = document.getElementById('mapCurrentLocation');
            const mapGpsState = document.getElementById('mapGpsState');
            const currentLocationBox = document.getElementById('currentLocationBox');
            const gpsAccuracyBox = document.getElementById('gpsAccuracyBox');
            const toggleLocationCorrection = document.getElementById('toggleLocationCorrection');
            const locationCorrectionForm = document.getElementById('locationCorrectionForm');
            const manualLocationInput = document.getElementById('manualLocationInput');
            const applyManualLocationButton = document.getElementById('applyManualLocationButton');
            const locationCorrectionFeedback = document.getElementById('locationCorrectionFeedback');
            const locationCorrection = document.querySelector('.location-correction');
            let watchId = null;
            let lastRouteAt = 0;
            let lastLat = null;
            let lastLng = null;
            let lastReverseLat = null;
            let lastReverseLng = null;
            let bestPosition = null;
            let gpsStartedAt = 0;
            let appliedAccuracy = Infinity;
            let manualOrigin = false;
            let refinementTimer = null;
            let lastAppliedTimestamp = 0;
            const EXCELLENT_ACCURACY_METERS = 25;
            const GOOD_ACCURACY_METERS = 50;
            const ACCEPTABLE_ACCURACY_METERS = 100;
            const COARSE_ACCURACY_METERS = 200;
            const GPS_REFINEMENT_WINDOW_MS = 25000;
            const POSITION_MAX_AGE_MS = 15000;
            const CAR_AVG_SPEED_KMH = 30;
            const MOTOR_AVG_SPEED_KMH = 36;
            let hasGpsFix = false;

            copyButton?.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(address);
                    copyFeedback.textContent = 'Alamat berhasil disalin.';
                } catch (error) {
                    copyFeedback.textContent = 'Tidak dapat menyalin otomatis. Silakan salin alamat secara manual.';
                }
                window.setTimeout(() => { copyFeedback.textContent = ''; }, 2800);
            });

            const formatDuration = seconds => {
                const minutes = Math.max(1, Math.round(seconds / 60));
                if (minutes < 60) return `${minutes} menit`;
                const hours = Math.floor(minutes / 60);
                const rest = minutes % 60;
                return rest ? `${hours} jam ${rest} menit` : `${hours} jam`;
            };

            const haversineKm = (lat1, lon1, lat2, lon2) => {
                const toRad = value => value * Math.PI / 180;
                const R = 6371;
                const dLat = toRad(lat2 - lat1);
                const dLon = toRad(lon2 - lon1);
                const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) ** 2;
                return 2 * R * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            };

            const buildReadableLocation = addressData => {
                if (!addressData) return null;
                const a = addressData.address || {};
                const road = a.road || a.pedestrian || a.residential || a.path;
                const houseNumber = a.house_number || '';
                const firstLine = road ? `${road}${houseNumber ? ` No. ${houseNumber}` : ''}` : null;
                const parts = [];
                const seen = new Set();
                const addPart = part => {
                    const value = String(part || '').trim();
                    if (!value) return;
                    const key = value.toLocaleLowerCase('id-ID');
                    if (seen.has(key)) return;
                    seen.add(key);
                    parts.push(value);
                };
                [
                    firstLine,
                    a.neighbourhood,
                    a.quarter,
                    a.suburb,
                    a.village,
                    a.hamlet,
                    a.city_district,
                    a.district,
                    a.municipality,
                    a.city,
                    a.town,
                    a.county,
                    a.state
                ].forEach(addPart);

                return parts.length ? parts.slice(0, 7).join(', ') : (addressData.display_name || null);
            };

            const setDisplayedLocation = (text, warning = false) => {
                currentCoords.textContent = text;
                if (mapCurrentLocation) mapCurrentLocation.textContent = text;
                currentLocationBox?.classList.toggle('is-warning', warning);
                gpsAccuracyBox?.classList.toggle('is-warning', warning);
            };

            const openManualCorrectionForCoarseGps = (suggestedAddress = '', accuracy = Infinity) => {
                if (manualOrigin || accuracy <= ACCEPTABLE_ACCURACY_METERS) return;

                locationCorrectionForm?.classList.add('is-open');
                toggleLocationCorrection?.setAttribute('aria-expanded', 'true');
                locationCorrectionFeedback?.classList.remove('is-error');

                const rounded = Number.isFinite(accuracy) ? Math.round(accuracy) : null;
                locationCorrectionFeedback.textContent = rounded
                    ? `GPS perangkat masih sekitar ±${rounded} meter, Ketik alamat lengkap Anda agar peta dan kotak lokasi memakai alamat yang sama.`
                    : 'GPS perangkat belum cukup presisi. Ketik alamat lengkap Anda agar peta dan kotak lokasi memakai alamat yang sama.';
            };

            const reverseGeocode = async (lat, lng, accuracy = 0) => {
                try {
                    const isCoarse = accuracy > ACCEPTABLE_ACCURACY_METERS;
                    setDisplayedLocation(isCoarse ? 'GPS belum cukup presisi untuk menentukan alamat...' : 'Mendeteksi nama lokasi...', isCoarse);
                    const controller = new AbortController();
                    const timeout = window.setTimeout(() => controller.abort(), 8000);
                    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}&zoom=18&addressdetails=1&accept-language=id-ID,id;q=0.9,en;q=0.8`;
                    const response = await fetch(url, {
                        signal: controller.signal,
                        headers: {
                            'Accept': 'application/json',
                            'Accept-Language': 'id-ID,id;q=0.9,en;q=0.8'
                        }
                    });
                    window.clearTimeout(timeout);
                    if (!response.ok) throw new Error('Reverse geocoding gagal');
                    const data = await response.json();
                    const placeName = buildReadableLocation(data);
                    if (!placeName) throw new Error('Nama lokasi tidak tersedia');
                    if (isCoarse) {
                        setDisplayedLocation(`Perkiraan GPS: ${placeName}`, true);
                        openManualCorrectionForCoarseGps(placeName, accuracy);
                    } else {
                        setDisplayedLocation(placeName, false);
                    }
                    return placeName;
                } catch (error) {
                    const fallback = `Koordinat GPS: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    setDisplayedLocation(fallback, accuracy > ACCEPTABLE_ACCURACY_METERS);
                    openManualCorrectionForCoarseGps('', accuracy);
                    return null;
                }
            };

            const setGpsState = (text, mode = '') => {
                gpsBadge.textContent = text;
                gpsBadge.classList.toggle('is-error', mode === 'error');
                gpsBadge.classList.toggle('is-live', mode === 'live');
                if (mapGpsState) {
                    mapGpsState.textContent = text;
                    mapGpsState.classList.toggle('is-error', mode === 'error');
                }
            };

            const updateDirectionsLinks = (lat, lng, originLabel = null) => {
                const origin = originLabel || `${lat},${lng}`;
                const url = `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(PUSDAI_LABEL)}&travelmode=driving`;
                liveDirectionsButton.href = url;
            };

            const updateEmbeddedMap = (lat, lng, originLabel = null) => {
                if (!mapFrame) return;
                const origin = originLabel || `${lat},${lng}`;
                mapFrame.src = `https://www.google.com/maps?saddr=${encodeURIComponent(origin)}&daddr=${encodeURIComponent(PUSDAI_LABEL)}&output=embed`;
                mapFrame.title = 'Rute dari lokasi Anda menuju Masjid PUSDAI Jabar';
            };

            const setTravelEstimate = km => {
                const safeKm = Math.max(0, Number(km) || 0);
                carDistance.textContent = `${safeKm.toFixed(1)} km`;
                motorDistance.textContent = `${safeKm.toFixed(1)} km`;

                // Gunakan dasar estimasi yang sama untuk mobil dan motor agar perbandingan konsisten.
                // Ini bukan data lalu lintas langsung. Motor diberi rata-rata sedikit lebih tinggi untuk
                // perjalanan perkotaan, bukan dicampur dengan durasi profil mobil OSRM.
                const carSeconds = Math.max(60, (safeKm / CAR_AVG_SPEED_KMH) * 3600);
                const motorSeconds = Math.max(60, (safeKm / MOTOR_AVG_SPEED_KMH) * 3600);
                carDuration.textContent = `± ${formatDuration(carSeconds)}`;
                motorDuration.textContent = `± ${formatDuration(motorSeconds)}`;
            };

            const updateFallbackEstimate = (lat, lng) => {
                const direct = haversineKm(lat, lng, PUSDAI.lat, PUSDAI.lng);
                const estimatedRoad = Math.max(direct, direct * 1.25);
                setTravelEstimate(estimatedRoad);
            };

            const loadRoadRoute = async (lat, lng) => {
                try {
                    const controller = new AbortController();
                    const timeout = window.setTimeout(() => controller.abort(), 9000);
                    const url = `https://router.project-osrm.org/route/v1/driving/${lng},${lat};${PUSDAI.lng},${PUSDAI.lat}?overview=false&alternatives=false&steps=false`;
                    const response = await fetch(url, { signal: controller.signal });
                    window.clearTimeout(timeout);
                    if (!response.ok) throw new Error('Routing service unavailable');
                    const data = await response.json();
                    const route = data?.routes?.[0];
                    if (!route) throw new Error('Route unavailable');

                    const km = route.distance / 1000;
                    setTravelEstimate(km);
                } catch (error) {
                    updateFallbackEstimate(lat, lng);
                }
            };

            const getPositionAccuracy = position => {
                const value = Number(position?.coords?.accuracy);
                return Number.isFinite(value) && value > 0 ? value : Infinity;
            };

            const isFreshPosition = position => {
                const timestamp = Number(position?.timestamp) || Date.now();
                return Date.now() - timestamp <= POSITION_MAX_AGE_MS;
            };

            const shouldApplyPosition = (position, force = false) => {
                if (force || !hasGpsFix) return true;

                const accuracy = getPositionAccuracy(position);
                const timestamp = Number(position.timestamp) || Date.now();
                const lat = Number(position.coords.latitude);
                const lng = Number(position.coords.longitude);
                if (accuracy + 3 < appliedAccuracy) return true;
                if (lastLat !== null && lastLng !== null) {
                    const movedMeters = haversineKm(lastLat, lastLng, lat, lng) * 1000;
                    const movementThreshold = Math.max(35, Math.min(120, appliedAccuracy * 0.75));
                    const speed = Number(position.coords.speed);
                    if (movedMeters >= movementThreshold && (Number.isFinite(speed) ? speed >= 0.8 : timestamp > lastAppliedTimestamp + 8000)) {
                        return true;
                    }
                }

                return false;
            };

            const applyPosition = async (position, force = false) => {
                const { latitude: lat, longitude: lng } = position.coords;
                const accuracy = getPositionAccuracy(position);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
                if (!isFreshPosition(position) && hasGpsFix && !force) return;
                if (!shouldApplyPosition(position, force)) return;

                const previousAccuracy = appliedAccuracy;
                const roundedAccuracy = Number.isFinite(accuracy) ? Math.round(accuracy) : null;
                const isExcellent = accuracy <= EXCELLENT_ACCURACY_METERS;
                const isPrecise = accuracy <= GOOD_ACCURACY_METERS;
                const isAcceptable = accuracy <= ACCEPTABLE_ACCURACY_METERS;
                const isCoarse = accuracy > COARSE_ACCURACY_METERS;

                hasGpsFix = true;
                appliedAccuracy = accuracy;
                lastAppliedTimestamp = Number(position.timestamp) || Date.now();
                manualOrigin = false;

                gpsAccuracy.textContent = roundedAccuracy ? `± ${roundedAccuracy} meter` : 'Tidak diketahui';

                if (isExcellent) {
                    gpsStatus.textContent = 'Lokasi GPS sangat akurat. Titik dan rute diperbarui dari posisi perangkat Anda saat ini.';
                    setGpsState('GPS Sangat Akurat', 'live');
                } else if (isPrecise) {
                    gpsStatus.textContent = 'Lokasi GPS sudah akurat. Sistem tetap memantau jika perangkat memberikan pembacaan yang lebih presisi.';
                    setGpsState('GPS Akurat', 'live');
                } else if (isAcceptable) {
                    gpsStatus.textContent = `Lokasi terdeteksi dengan akurasi sekitar ±${roundedAccuracy} meter. Sistem masih meningkatkan ketepatan GPS secara otomatis.`;
                    setGpsState('Memperbaiki Akurasi', 'live');
                } else if (isCoarse) {
                    gpsStatus.textContent = `Lokasi awal terdeteksi, tetapi akurasinya baru sekitar ±${roundedAccuracy} meter. Jangan anggap titik ini final — sistem sedang mencari GPS perangkat yang lebih presisi.`;
                    setGpsState('Meningkatkan GPS', 'live');
                } else {
                    gpsStatus.textContent = `Lokasi terdeteksi dengan akurasi sekitar ±${roundedAccuracy} meter. Sistem sedang menunggu pembacaan GPS yang lebih baik.`;
                    setGpsState('Meningkatkan GPS', 'live');
                }

                updateDirectionsLinks(lat, lng);

                const movedKm = lastLat === null ? Infinity : haversineKm(lastLat, lastLng, lat, lng);
                const movedForNameKm = lastReverseLat === null ? Infinity : haversineKm(lastReverseLat, lastReverseLng, lat, lng);
                const now = Date.now();
                const accuracyImprovedEnough = accuracy + 3 < previousAccuracy;
                if(movedForNameKm > 0.035 || force || accuracyImprovedEnough || lastReverseLat === null){
                    lastReverseLat = lat;
                    lastReverseLng = lng;
                    await reverseGeocode(lat, lng, accuracy);
                }
                if (movedKm > 0.025 || now - lastRouteAt > 30000 || force || lastLat === null || accuracyImprovedEnough) {
                    lastLat = lat;
                    lastLng = lng;
                    lastRouteAt = now;
                    updateEmbeddedMap(lat, lng);
                    loadRoadRoute(lat, lng);
                }
                if (isPrecise && refinementTimer !== null) {
                    window.clearTimeout(refinementTimer);
                    refinementTimer = null;
                }
            };

            const onPosition = position => {
                if (manualOrigin) return;
                if (!position?.coords) return;

                const accuracy = getPositionAccuracy(position);
                const currentBestAccuracy = getPositionAccuracy(bestPosition);

                if (!bestPosition || accuracy < currentBestAccuracy) {
                    bestPosition = position;
                }

                // Posisi pertama boleh ditampilkan agar UI responsif, tetapi fix kasar tidak
                // lagi dianggap final. Fix berikutnya yang lebih akurat otomatis menggantikannya.
                applyPosition(position, !hasGpsFix);
            };

            const onPositionError = error => {
                // Jika sudah ada fix, jangan hapus posisi yang valid hanya karena satu update gagal.
                if (hasGpsFix) {
                    const roundedAccuracy = Number.isFinite(appliedAccuracy) ? Math.round(appliedAccuracy) : null;
                    gpsStatus.textContent = roundedAccuracy && appliedAccuracy > GOOD_ACCURACY_METERS
                        ? `Lokasi terakhir masih digunakan (akurasi sekitar ±${roundedAccuracy} meter). Sistem akan terus mencoba mendapatkan GPS yang lebih presisi.`
                        : 'Lokasi terakhir tetap digunakan. Sistem akan mencoba pembaruan GPS berikutnya.';
                    setGpsState(appliedAccuracy <= GOOD_ACCURACY_METERS ? 'GPS Akurat' : 'Meningkatkan GPS', 'live');
                    return;
                }

                const messages = {
                    1: 'Izin lokasi ditolak. Aktifkan izin lokasi dan opsi lokasi presisi/precise location pada browser, lalu tekan “Perbarui Lokasi”.',
                    2: 'Lokasi perangkat belum dapat ditemukan. Pastikan layanan lokasi/GPS perangkat aktif.',
                    3: 'Pencarian GPS presisi membutuhkan waktu lebih lama. Silakan tekan “Perbarui Lokasi” dan tunggu beberapa saat.'
                };
                gpsStatus.textContent = messages[error.code] || 'GPS tidak dapat digunakan pada perangkat ini.';
                setGpsState('GPS Tidak Aktif', 'error');
                currentCoords.textContent = 'Lokasi tidak tersedia';
                if (mapCurrentLocation) mapCurrentLocation.textContent = 'Lokasi tidak tersedia — periksa izin lokasi browser';
                gpsAccuracy.textContent = '—';
                carDistance.textContent = '—';
                motorDistance.textContent = '—';
                carDuration.textContent = 'Lokasi diperlukan';
                motorDuration.textContent = 'Lokasi diperlukan';
            };

            const startGps = () => {
                if (!('geolocation' in navigator)) {
                    gpsStatus.textContent = 'Browser ini tidak mendukung GPS/geolocation.';
                    if (mapCurrentLocation) mapCurrentLocation.textContent = 'Browser tidak mendukung geolocation';
                    setGpsState('Tidak Didukung', 'error');
                    return;
                }

                if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                    gpsStatus.textContent = 'GPS browser hanya dapat digunakan melalui HTTPS atau localhost.';
                    if (mapCurrentLocation) mapCurrentLocation.textContent = 'GPS membutuhkan HTTPS atau localhost';
                    setGpsState('Butuh HTTPS', 'error');
                    return;
                }

                manualOrigin = false;
                locationCorrection?.classList.remove('is-confirmed');
                if (toggleLocationCorrection) {
                    toggleLocationCorrection.textContent = 'Lokasi kurang tepat? Atur alamat';
                    toggleLocationCorrection.setAttribute('aria-expanded', 'false');
                }
                locationCorrectionForm?.classList.remove('is-open');
                if (locationCorrectionFeedback) locationCorrectionFeedback.textContent = '';
                if (gpsAccuracyLabel) gpsAccuracyLabel.textContent = 'Akurasi GPS';
                bestPosition = null;
                appliedAccuracy = Infinity;
                hasGpsFix = false;
                gpsStartedAt = Date.now();
                lastAppliedTimestamp = 0;
                lastLat = null;
                lastLng = null;
                lastReverseLat = null;
                lastReverseLng = null;
                lastRouteAt = 0;

                if (refinementTimer !== null) {
                    window.clearTimeout(refinementTimer);
                    refinementTimer = null;
                }
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
                setGpsState('Mencari GPS');
                gpsStatus.textContent = 'Mengaktifkan GPS presisi tinggi dan mencari posisi terbaik perangkat Anda...';
                setDisplayedLocation('Mendeteksi lokasi saat ini...', false);
                gpsAccuracy.textContent = 'Mengukur...';
                const highAccuracyOptions = {
                    enableHighAccuracy: true,
                    timeout: 30000,
                    maximumAge: 0
                };
                watchId = navigator.geolocation.watchPosition(
                    onPosition,
                    onPositionError,
                    highAccuracyOptions
                );
                navigator.geolocation.getCurrentPosition(
                    onPosition,
                    onPositionError,
                    highAccuracyOptions
                );

                // Setelah 25 detik, gunakan fix terbaik yang pernah diterima bila ternyata callback
                // terakhir bukan yang terbaik. Watch tetap berjalan tanpa dihentikan.
                refinementTimer = window.setTimeout(() => {
                    refinementTimer = null;
                    if (manualOrigin || !bestPosition) return;

                    const bestAccuracy = getPositionAccuracy(bestPosition);
                    if (bestAccuracy + 3 < appliedAccuracy) {
                        applyPosition(bestPosition, true);
                    }

                    if (bestAccuracy > ACCEPTABLE_ACCURACY_METERS) {
                        const rounded = Number.isFinite(bestAccuracy) ? Math.round(bestAccuracy) : null;
                        gpsStatus.textContent = rounded
                            ? `Perangkat baru memberikan akurasi terbaik sekitar ±${rounded} meter. Live GPS tetap aktif dan akan otomatis memakai titik yang lebih presisi jika tersedia. Untuk hasil terbaik, aktifkan “Precise location/Lokasi presisi” dan gunakan perangkat yang memiliki GPS.`
                            : 'Live GPS tetap aktif, tetapi perangkat belum memberikan informasi akurasi yang cukup.';
                        setGpsState('GPS Terbatas', 'live');
                    }
                }, GPS_REFINEMENT_WINDOW_MS);
            };

            toggleLocationCorrection?.addEventListener('click', () => {
                const open = !locationCorrectionForm.classList.contains('is-open');
                locationCorrectionForm.classList.toggle('is-open', open);
                toggleLocationCorrection.setAttribute('aria-expanded', String(open));
                if (open) manualLocationInput?.focus();
            });

            const geocodeManualAddress = async () => {
                const rawQuery = manualLocationInput?.value || '';
                const query = rawQuery.replace(/\s+/g, ' ').trim();
                if (!query) {
                    locationCorrectionFeedback.textContent = 'Masukkan alamat lengkap terlebih dahulu.';
                    locationCorrectionFeedback.classList.add('is-error');
                    return;
                }

                // Alamat yang diketik pengguna menjadi sumber kebenaran untuk teks UI dan
                // Google Maps. Jangan menggantinya dengan hasil reverse-geocoder lain yang
                // bisa menamai jalan berbeda.
                manualOrigin = true;
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
                if (refinementTimer !== null) {
                    window.clearTimeout(refinementTimer);
                    refinementTimer = null;
                }

                setDisplayedLocation(query, false);
                if (gpsAccuracyLabel) gpsAccuracyLabel.textContent = 'Sumber Lokasi';
                gpsAccuracy.textContent = 'Alamat manual';
                gpsStatus.textContent = 'Alamat manual dipakai untuk peta, rute, dan estimasi perjalanan. Live GPS dijeda agar lokasi tidak berubah kembali.';
                setGpsState('Alamat Manual', 'live');
                updateDirectionsLinks(lastLat, lastLng, query);
                updateEmbeddedMap(lastLat, lastLng, query);

                locationCorrection?.classList.add('is-confirmed');
                locationCorrectionForm?.classList.remove('is-open');
                if (toggleLocationCorrection) {
                    toggleLocationCorrection.textContent = 'Alamat manual aktif · Ubah alamat';
                    toggleLocationCorrection.setAttribute('aria-expanded', 'false');
                }
                locationCorrectionFeedback.classList.remove('is-error');
                locationCorrectionFeedback.textContent = 'Alamat manual aktif. Sedang mencocokkan koordinat untuk estimasi jarak…';
                applyManualLocationButton.disabled = true;

                let result = null;
                try {
                    const searchQueries = [query];
                    if (!/indonesia/i.test(query)) searchQueries.push(`${query}, Indonesia`);

                    for (const candidate of searchQueries) {
                        const controller = new AbortController();
                        const timeout = window.setTimeout(() => controller.abort(), 9000);
                        try {
                            const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(candidate)}&limit=1&countrycodes=id&addressdetails=1&accept-language=id-ID,id;q=0.9`;
                            const response = await fetch(url, {
                                signal: controller.signal,
                                headers: {
                                    'Accept': 'application/json',
                                    'Accept-Language': 'id-ID,id;q=0.9'
                                }
                            });
                            if (!response.ok) continue;
                            const results = await response.json();
                            if (results?.[0]) {
                                result = results[0];
                                break;
                            }
                        } finally {
                            window.clearTimeout(timeout);
                        }
                    }

                    if (result) {
                        const lat = Number(result.lat);
                        const lng = Number(result.lon);
                        if (Number.isFinite(lat) && Number.isFinite(lng)) {
                            lastLat = lat;
                            lastLng = lng;
                            updateDirectionsLinks(lat, lng, query);
                            updateEmbeddedMap(lat, lng, query);
                            await loadRoadRoute(lat, lng);
                            locationCorrectionFeedback.textContent = 'Alamat manual aktif dan sudah dipakai untuk peta, rute, serta estimasi perjalanan.';
                            return;
                        }
                    }
                    carDistance.textContent = '—';
                    motorDistance.textContent = '—';
                    carDuration.textContent = 'Lihat Google Maps';
                    motorDuration.textContent = 'Lihat Google Maps';
                    locationCorrectionFeedback.textContent = 'Alamat manual tetap aktif. Google Maps akan mencocokkan alamat, tetapi estimasi FINUS belum tersedia karena koordinatnya belum ditemukan dengan pasti.';
                } catch (error) {
                    carDistance.textContent = '—';
                    motorDistance.textContent = '—';
                    carDuration.textContent = 'Lihat Google Maps';
                    motorDuration.textContent = 'Lihat Google Maps';
                    locationCorrectionFeedback.textContent = 'Alamat manual tetap aktif untuk peta dan rute, tetapi estimasi jarak FINUS belum dapat dihitung.';
                } finally {
                    applyManualLocationButton.disabled = false;
                }
            };

            applyManualLocationButton?.addEventListener('click', geocodeManualAddress);
            manualLocationInput?.addEventListener('keydown', event => {
                if (event.key === 'Enter') { event.preventDefault(); geocodeManualAddress(); }
            });

            refreshGpsButton?.addEventListener('click', startGps);
            startGps();

            const elements = document.querySelectorAll('.scroll-card, .scroll-fade');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (!('IntersectionObserver' in window) || reduceMotion) {
                elements.forEach(element => element.classList.add('is-inview'));
            } else {
                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.intersectionRatio >= 0.16) entry.target.classList.add('is-inview');
                        else if (!entry.isIntersecting) entry.target.classList.remove('is-inview');
                    });
                }, { threshold:[0,.16,.45], rootMargin:'0px 0px -7% 0px' });
                elements.forEach(element => observer.observe(element));
            }
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
                root.style.colorScheme = normalized;

                const colorSchemeMeta = document.querySelector(
                    'meta[name="color-scheme"]'
                );

                if (colorSchemeMeta) {
                    colorSchemeMeta.setAttribute(
                        'content',
                        normalized
                    );
                }

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
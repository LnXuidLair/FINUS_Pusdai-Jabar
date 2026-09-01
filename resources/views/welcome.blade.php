<!DOCTYPE html>
<html lang="id" class="finus-light-theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0FB442">
    <meta name="color-scheme" content="light">

    {{-- FINUS tidak mengikuti dark mode perangkat. Tema hanya mengikuti pilihan pengguna di FINUS. --}}
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


    <title>FINUS | PUSDAI Jawa Barat</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=21">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?v=21">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root{
            color-scheme:light only;
            --g950:#033D18;--g900:#075923;--g800:#0E6E2C;--g700:#118A37;
            --g600:#16A344;--g500:#22BA51;--g300:#7EFF87;--g100:#E7F9EB;
            --g50:#F4FCF6;--text:#122018;--muted:#637168;--white:#fff;
            --danger:#DC3545;--header:76px;--container:1180px;
            --shadow-sm:0 8px 24px rgba(3,61,24,.08);
            --shadow-md:0 18px 46px rgba(3,61,24,.15);
            --shadow-lg:0 30px 80px rgba(1,38,14,.24)
        }
        *,*::before,*::after{box-sizing:border-box}
        html{min-height:100%;scroll-behavior:smooth;background:var(--g950)}
        body{
            min-height:100vh;min-height:100dvh;margin:0;overflow-x:hidden;
            background:var(--g50);color:var(--text);font-family:"Nunito",sans-serif;
            -webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility
        }
        body.is-locked{overflow:hidden}
        button,input,a{font:inherit;-webkit-tap-highlight-color:transparent}
        button,a{touch-action:manipulation}
        img{display:block;max-width:100%}
        [hidden]{display:none!important}
        .finus-container{width:min(calc(100% - 32px),var(--container));margin-inline:auto}
        /* HEADER */
        .finus-header{
            position:fixed;inset:0 0 auto;z-index:1000;
            min-height:calc(var(--header) + env(safe-area-inset-top));
            padding-top:env(safe-area-inset-top);
            border-bottom:1px solid rgba(255,255,255,.12);
            background:
                radial-gradient(circle at 18% 0%,rgba(126,255,135,.20),transparent 16rem),
                linear-gradient(0deg,#004716 0%,#0FB442 15%,#1AAF48 39%,#118635 75%,#004716 100%);
            box-shadow:0 10px 32px rgba(2,54,20,.17);backdrop-filter:blur(10px)
        }
        .finus-header-inner{
            display:flex;align-items:center;justify-content:space-between;gap:20px;
            min-height:var(--header)
        }
        .finus-brand{
            display:inline-flex;align-items:center;gap:12px;min-width:0;
            color:var(--white);text-decoration:none
        }
        .finus-brand-logo{
            width:auto;height:52px;max-width:185px;object-fit:contain;
            opacity:1!important;mix-blend-mode:normal!important;
            forced-color-adjust:none!important;
            filter:drop-shadow(0 5px 12px rgba(0,30,10,.18))
        }
        .finus-brand-copy{display:none}
        .finus-brand-name{display:block;font-size:13px;font-weight:900;letter-spacing:.12em}
        .finus-brand-subtitle{
            display:block;margin-top:2px;color:rgba(255,255,255,.72);
            font-size:10px;font-weight:700
        }
        .finus-nav{display:flex;align-items:center;justify-content:flex-end;gap:10px}
        .finus-nav-group{position:relative}
        .finus-nav-button{
            display:inline-flex;align-items:center;justify-content:center;gap:8px;
            min-height:42px;padding:0 14px;border:1px solid rgba(255,255,255,.27);
            border-radius:12px;background:rgba(255,255,255,.93);color:var(--g950);
            font-size:12px;font-weight:900;cursor:pointer;
            box-shadow:0 8px 18px rgba(0,48,17,.13);transition:.2s
        }
        .finus-nav-button:hover,.finus-nav-button:focus-visible,
        .finus-nav-button[aria-expanded="true"]{
            border-color:rgba(255,255,255,.75);background:#fff;outline:none;
            transform:translateY(-1px);box-shadow:0 12px 23px rgba(0,48,17,.18)
        }
        .finus-nav-button-icon{
            display:inline-flex;align-items:center;justify-content:center;width:23px;height:23px;
            border-radius:7px;background:var(--g100);color:var(--g800);font-size:14px
        }
        .finus-nav-chevron{
            width:7px;height:7px;border-right:2px solid currentColor;border-bottom:2px solid currentColor;
            transform:translateY(-2px) rotate(45deg);transition:.2s
        }
        .finus-nav-button[aria-expanded="true"] .finus-nav-chevron{
            transform:translateY(2px) rotate(225deg)
        }
        .finus-menu-button{width:44px;padding:0}
        .finus-dropdown{
            position:absolute;top:calc(100% + 10px);right:0;z-index:1200;
            width:190px;padding:7px;border:1px solid rgba(14,110,44,.15);
            border-radius:15px;background:rgba(255,255,255,.98);box-shadow:var(--shadow-md);
            visibility:hidden;opacity:0;transform:translateY(-6px) scale(.98);
            transform-origin:top right;transition:.18s;backdrop-filter:blur(14px)
        }
        .finus-dropdown.is-open{visibility:visible;opacity:1;transform:none}
        .finus-dropdown::before{
            content:"";position:absolute;top:-5px;right:21px;width:10px;height:10px;
            border-top:1px solid rgba(14,110,44,.15);border-left:1px solid rgba(14,110,44,.15);
            background:#fff;transform:rotate(45deg)
        }
        .finus-dropdown-label{
            padding:7px 10px 5px;color:#88948C;font-size:9px;font-weight:900;
            letter-spacing:.14em;text-transform:uppercase
        }
        .finus-dropdown-link{
            display:flex;align-items:center;gap:9px;min-height:42px;padding:8px 10px;
            border-radius:10px;color:#294334;font-size:12px;font-weight:800;
            text-decoration:none;transition:.18s
        }
        .finus-dropdown-link:hover,.finus-dropdown-link:focus-visible{
            background:var(--g100);color:var(--g900);outline:none;transform:translateX(2px)
        }
        .finus-dropdown-link-icon{
            display:inline-flex;align-items:center;justify-content:center;width:28px;min-width:28px;height:28px;
            border-radius:8px;background:var(--g50);color:var(--g700)
        }
        /* HERO */
        .finus-main{
            min-height:100vh;min-height:100dvh;
            padding-top:calc(var(--header) + env(safe-area-inset-top))
        }
        .finus-hero{
            position:relative;overflow:hidden;display:flex;align-items:center;
            min-height:calc(100vh - var(--header) - env(safe-area-inset-top));
            min-height:calc(100dvh - var(--header) - env(safe-area-inset-top));
            padding:clamp(42px,7vw,88px) 0 clamp(42px,6vw,72px);
            background-image:
                linear-gradient(90deg,rgba(240,255,244,.91),rgba(244,255,247,.74) 42%,rgba(236,255,241,.37) 70%,rgba(4,73,27,.18)),
                url('{{ asset('assets/images/bg-welcome.png') }}');
            background-position:center;background-size:cover
        }
        .finus-hero::before{
            content:"";position:absolute;inset:0;
            background:
                radial-gradient(circle at 14% 18%,rgba(126,255,135,.18),transparent 22rem),
                radial-gradient(circle at 76% 72%,rgba(255,255,255,.32),transparent 25rem)
        }
        .finus-hero::after{
            content:"";position:absolute;right:-110px;bottom:-150px;width:410px;height:410px;
            border:70px solid rgba(23,155,64,.09);border-radius:50%
        }
        .finus-hero-grid{
            position:relative;z-index:2;display:grid;
            grid-template-columns:minmax(0,1.15fr) minmax(330px,.75fr);
            align-items:center;gap:clamp(30px,7vw,90px)
        }
        .finus-hero-content{max-width:700px;animation:finus-reveal .65s ease both}
        .finus-eyebrow{
            display:inline-flex;align-items:center;gap:9px;margin-bottom:18px;padding:8px 13px;
            border:1px solid rgba(11,93,39,.16);border-radius:999px;background:rgba(255,255,255,.70);
            color:var(--g900);font-size:10px;font-weight:900;letter-spacing:.13em;
            text-transform:uppercase;box-shadow:var(--shadow-sm);backdrop-filter:blur(8px)
        }
        .finus-eyebrow-dot{
            width:7px;height:7px;border-radius:50%;background:var(--g500);
            box-shadow:0 0 0 4px rgba(34,186,81,.13)
        }
        .finus-hero-title{
            margin:0;color:var(--g950);font-family:"Lobster",cursive;
            font-size:clamp(56px,8vw,102px);font-weight:400;line-height:.98;letter-spacing:-.025em;
            text-shadow:0 2px 0 rgba(255,255,255,.78)
        }
        .finus-hero-subtitle{
            max-width:660px;margin:clamp(20px,3vw,30px) 0 0;color:#184E2B;
            font-family:Georgia,"Times New Roman",serif;font-size:clamp(22px,3.3vw,38px);
            font-weight:800;line-height:1.25
        }
        .finus-hero-description{
            max-width:640px;margin:18px 0 0;color:#557163;
            font-size:clamp(13px,1.6vw,17px);font-weight:600;line-height:1.75
        }
        .finus-hero-actions{display:flex;flex-wrap:wrap;gap:11px;margin-top:28px}
        .finus-primary-button,.finus-secondary-button{
            display:inline-flex;align-items:center;justify-content:center;gap:9px;
            min-height:48px;padding:0 18px;border-radius:13px;font-size:13px;
            font-weight:900;text-decoration:none;cursor:pointer;transition:.2s
        }
        .finus-primary-button{
            border:1px solid var(--g700);
            background:linear-gradient(100deg,#075923,#118A37 56%,#16A344);
            color:#fff;box-shadow:0 13px 27px rgba(5,95,34,.22)
        }
        .finus-primary-button:hover,.finus-primary-button:focus-visible{
            background:linear-gradient(100deg,#064D1E,#0E7D31 56%,#13923B);
            outline:none;transform:translateY(-2px);box-shadow:0 17px 32px rgba(5,95,34,.27)
        }
        .finus-secondary-button{
            border:1px solid rgba(7,89,35,.18);background:rgba(255,255,255,.76);
            color:var(--g900);box-shadow:var(--shadow-sm);backdrop-filter:blur(8px)
        }
        .finus-secondary-button:hover,.finus-secondary-button:focus-visible{
            border-color:rgba(7,89,35,.34);background:#fff;outline:none;transform:translateY(-2px)
        }
        .finus-hero-meta{display:flex;flex-wrap:wrap;gap:10px;margin-top:26px}
        .finus-meta-item{
            display:inline-flex;align-items:center;gap:10px;min-height:42px;padding:0 14px;
            border:1px solid rgba(8,83,32,.12);border-radius:11px;background:rgba(255,255,255,.63);
            color:#466452;font-size:12px;font-weight:800;backdrop-filter:blur(7px)
        }
        .finus-meta-icon{
            position:relative;display:inline-flex;align-items:center;justify-content:center;width:28px;min-width:28px;height:28px;
            border-radius:9px;background:var(--g100);color:var(--g700);font-size:0
        }
        .finus-meta-icon::before,
        .finus-meta-icon::after{
            content:"";position:absolute;display:block
        }
        .finus-meta-icon.is-integrated::before,
        .finus-hero-meta .finus-meta-item:nth-child(1) .finus-meta-icon::before{
            width:13px;height:8px;border-left:2px solid currentColor;border-bottom:2px solid currentColor;
            transform:translateY(-1px) rotate(-45deg)
        }
        .finus-meta-icon.is-transparent::before,
        .finus-hero-meta .finus-meta-item:nth-child(2) .finus-meta-icon::before{
            width:14px;height:14px;border:2px solid currentColor;border-radius:999px
        }
        .finus-meta-icon.is-transparent::after,
        .finus-hero-meta .finus-meta-item:nth-child(2) .finus-meta-icon::after{
            width:4px;height:4px;border-radius:999px;background:currentColor;
            box-shadow:-5px 0 0 currentColor,5px 0 0 currentColor,0 -5px 0 currentColor,0 5px 0 currentColor
        }
        .finus-meta-icon.is-responsive::before,
        .finus-hero-meta .finus-meta-item:nth-child(3) .finus-meta-icon::before{
            width:14px;height:10px;border:2px solid currentColor;border-radius:2px
        }
        .finus-meta-icon.is-responsive::after,
        .finus-hero-meta .finus-meta-item:nth-child(3) .finus-meta-icon::after{
            bottom:5px;width:10px;height:2px;border-radius:999px;background:currentColor;
            box-shadow:0 4px 0 currentColor
        }
        /* ROLE CARD */
        .finus-role-card{
            position:relative;overflow:hidden;padding:clamp(22px,3vw,31px);
            border:1px solid rgba(255,255,255,.42);border-radius:24px;
            background:
                radial-gradient(circle at 100% 0%,rgba(126,255,135,.19),transparent 15rem),
                linear-gradient(145deg,rgba(3,61,24,.97),rgba(14,110,44,.94) 52%,rgba(22,163,68,.91));
            color:#fff;box-shadow:var(--shadow-lg);animation:finus-reveal .65s .12s ease both;
            backdrop-filter:blur(13px)
        }
        .finus-role-card::before{
            content:"";position:absolute;right:-70px;bottom:-95px;width:220px;height:220px;
            border:42px solid rgba(255,255,255,.07);border-radius:50%
        }
        .finus-role-card>*{position:relative;z-index:2}
        .finus-role-card-kicker{
            display:inline-flex;align-items:center;gap:8px;padding:7px 10px;
            border:1px solid rgba(255,255,255,.16);border-radius:999px;
            background:rgba(0,48,17,.18);color:rgba(255,255,255,.82);
            font-size:9px;font-weight:900;letter-spacing:.13em;text-transform:uppercase
        }
        .finus-role-title{margin:16px 0 0;font-size:23px;font-weight:900;line-height:1.3}
        .finus-role-copy{
            margin:8px 0 0;color:rgba(255,255,255,.76);font-size:12px;line-height:1.65
        }
        .finus-role-list{display:grid;gap:10px;margin-top:21px}
        .finus-role-link{
            display:grid;grid-template-columns:42px minmax(0,1fr) auto;align-items:center;gap:11px;
            min-height:59px;padding:8px 11px;border:1px solid rgba(255,255,255,.16);
            border-radius:14px;background:rgba(255,255,255,.09);color:#fff;
            text-decoration:none;cursor:pointer;transition:.2s
        }
        .finus-role-link:hover,.finus-role-link:focus-visible{
            border-color:rgba(255,255,255,.34);background:rgba(255,255,255,.15);
            outline:none;transform:translateX(3px)
        }
        .finus-role-icon{
            display:inline-flex;align-items:center;justify-content:center;width:42px;height:42px;
            border:1px solid rgba(255,255,255,.15);border-radius:12px;
            background:rgba(255,255,255,.12);font-size:18px
        }
        .finus-role-name{display:block;font-size:13px;font-weight:900}
        .finus-role-description{
            display:block;margin-top:3px;color:rgba(255,255,255,.68);font-size:10px;line-height:1.4
        }
        .finus-role-arrow{
            display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;
            border-radius:9px;background:rgba(126,255,135,.16);color:#B7FFBE;
            font-size:16px;font-weight:900
        }
        .finus-role-security{
            display:flex;align-items:flex-start;gap:9px;margin-top:18px;padding-top:17px;
            border-top:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.67);
            font-size:10.5px;line-height:1.55
        }
        /* MOBILE PANEL */
        .finus-mobile-panel{
            position:fixed;inset:calc(var(--header) + env(safe-area-inset-top)) 0 auto;
            z-index:950;max-height:calc(100dvh - var(--header) - env(safe-area-inset-top));
            padding:12px 16px calc(18px + env(safe-area-inset-bottom));overflow-y:auto;
            border-bottom:1px solid rgba(7,89,35,.14);background:rgba(247,255,249,.98);
            visibility:hidden;opacity:0;transform:translateY(-12px);box-shadow:var(--shadow-md);
            transition:.22s;backdrop-filter:blur(16px)
        }
        .finus-mobile-panel.is-open{visibility:visible;opacity:1;transform:none}
        .finus-mobile-menu-grid{display:grid;gap:9px}
        .finus-mobile-menu-title{
            margin:8px 0 2px;color:#7B8B81;font-size:9px;font-weight:900;
            letter-spacing:.14em;text-transform:uppercase
        }
        .finus-mobile-menu-link{
            display:flex;align-items:center;gap:10px;min-height:48px;padding:9px 11px;
            border:1px solid rgba(7,89,35,.11);border-radius:13px;background:#fff;color:#294334;
            font-size:12px;font-weight:900;text-decoration:none;
            box-shadow:0 7px 18px rgba(3,61,24,.05)
        }
        .finus-mobile-menu-link-icon{
            display:inline-flex;align-items:center;justify-content:center;width:31px;min-width:31px;height:31px;
            border-radius:9px;background:var(--g100);color:var(--g800)
        }
        /* ACCESS MODAL */
        .finus-modal{
            position:fixed;inset:0;z-index:3000;display:flex;align-items:center;justify-content:center;
            padding:calc(18px + env(safe-area-inset-top)) 18px calc(18px + env(safe-area-inset-bottom));
            background:rgba(1,29,10,.68);visibility:hidden;opacity:0;transition:.22s;
            backdrop-filter:blur(7px)
        }
        .finus-modal.is-open{visibility:visible;opacity:1}
        .finus-modal-dialog{
            position:relative;overflow:hidden;width:min(100%,430px);
            border:1px solid rgba(183,255,190,.30);border-radius:24px;
            background:
                radial-gradient(circle at 100% 0%,rgba(126,255,135,.18),transparent 15rem),
                linear-gradient(145deg,rgba(3,61,24,.99),rgba(14,110,44,.97) 48%,rgba(22,163,68,.94));
            color:#fff;box-shadow:var(--shadow-lg);transform:translateY(14px) scale(.97);transition:.22s
        }
        .finus-modal.is-open .finus-modal-dialog{transform:none}
        .finus-modal-dialog::after{
            content:"";position:absolute;right:-65px;bottom:-85px;width:190px;height:190px;
            border:38px solid rgba(255,255,255,.065);border-radius:50%
        }
        .finus-modal-content{position:relative;z-index:2;padding:clamp(23px,5vw,32px)}
        .finus-modal-close{
            position:absolute;top:13px;right:13px;z-index:5;display:inline-flex;
            align-items:center;justify-content:center;width:37px;height:37px;padding:0;
            border:1px solid rgba(255,255,255,.18);border-radius:11px;
            background:rgba(255,255,255,.10);color:#fff;font-size:23px;cursor:pointer;transition:.2s
        }
        .finus-modal-close:hover,.finus-modal-close:focus-visible{
            background:rgba(255,255,255,.17);outline:none;transform:rotate(4deg)
        }
        .finus-modal-icon{
            display:flex;align-items:center;justify-content:center;width:58px;height:58px;
            margin:0 auto 15px;border:1px solid rgba(255,255,255,.16);border-radius:18px;
            background:rgba(255,255,255,.11);color:#B7FFBE;font-size:25px
        }
        .finus-modal-title{margin:0;font-size:22px;font-weight:900;text-align:center}
        .finus-modal-copy{
            margin:7px auto 20px;color:rgba(255,255,255,.74);
            font-size:11.5px;line-height:1.65;text-align:center
        }
        .finus-field-label{
            display:block;margin-bottom:7px;color:rgba(255,255,255,.92);
            font-size:11px;font-weight:900
        }
        .finus-code-field-wrap{position:relative}
        .finus-code-field{
            width:100%;height:51px;padding:0 49px 0 15px;
            border:1px solid rgba(199,255,208,.50);border-radius:13px;
            background:rgba(242,255,245,.96);color:var(--g950);
            font-size:14px;font-weight:900;letter-spacing:.05em;outline:none;transition:.2s
        }
        .finus-code-field::placeholder{color:#789084;font-weight:700;letter-spacing:normal}
        .finus-code-field:focus{
            border-color:var(--g300);background:#fff;
            box-shadow:0 0 0 4px rgba(126,255,135,.19)
        }
        .finus-code-field:disabled{cursor:wait;opacity:.78}
        .finus-toggle-password{
            position:absolute;top:50%;right:8px;display:inline-flex;align-items:center;
            justify-content:center;width:37px;height:37px;padding:0;border:0;border-radius:10px;
            background:transparent;cursor:pointer;transform:translateY(-50%)
        }
        .finus-toggle-password:hover,.finus-toggle-password:focus-visible{
            background:rgba(23,155,64,.11);outline:none
        }
        .finus-toggle-password img{
            width:22px;height:22px;object-fit:contain;opacity:1!important;
            forced-color-adjust:none!important
        }
        .finus-form-message{
            min-height:21px;margin:8px 0 0;color:rgba(255,255,255,.80);
            font-size:11px;font-weight:700;line-height:1.5;text-align:center
        }
        .finus-form-message.is-error{color:#FFD5D9}
        .finus-form-message.is-success{color:#B7FFBE}
        .finus-modal-actions{
            display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:17px
        }
        .finus-verify-button,.finus-cancel-button{
            display:inline-flex;align-items:center;justify-content:center;gap:8px;
            min-height:45px;padding:0 13px;border-radius:12px;font-size:12px;
            font-weight:900;cursor:pointer;transition:.2s
        }
        .finus-verify-button{
            border:1px solid rgba(224,255,227,.72);
            background:linear-gradient(100deg,#7EFF87,#4FE66A 55%,#22BA51);
            color:var(--g950);box-shadow:0 10px 21px rgba(0,42,15,.20)
        }
        .finus-verify-button:hover:not(:disabled),.finus-verify-button:focus-visible{
            background:linear-gradient(100deg,#91FF9B,#61EC75 55%,#31C85E);
            outline:none;transform:translateY(-1px)
        }
        .finus-verify-button:disabled{cursor:wait;opacity:.75}
        .finus-cancel-button{
            border:1px solid rgba(255,255,255,.20);background:rgba(0,48,17,.17);
            color:rgba(255,255,255,.88)
        }
        .finus-cancel-button:hover,.finus-cancel-button:focus-visible{
            border-color:rgba(255,255,255,.35);background:rgba(255,255,255,.11);
            outline:none;transform:translateY(-1px)
        }
        .finus-spinner{
            width:17px;height:17px;border:2px solid rgba(3,61,24,.22);
            border-top-color:var(--g950);border-radius:50%;animation:finus-spin .68s linear infinite
        }
        /* PAGE LOADER */
        .finus-page-loader{
            position:fixed;inset:0;z-index:5000;display:flex;align-items:center;justify-content:center;
            padding:20px;visibility:hidden;opacity:0;background:rgba(1,29,10,.61);
            transition:.18s;backdrop-filter:blur(7px)
        }
        .finus-page-loader.is-visible{visibility:visible;opacity:1}
        .finus-loader-card{
            width:min(100%,275px);padding:27px 23px;border:1px solid rgba(255,255,255,.50);
            border-radius:20px;background:rgba(255,255,255,.97);text-align:center;
            box-shadow:var(--shadow-lg)
        }
        .finus-loader-wheel{
            width:55px;height:55px;margin:0 auto 15px;border:5px solid #DDF2E3;
            border-top-color:var(--g700);border-right-color:var(--g500);
            border-radius:50%;animation:finus-spin .78s linear infinite
        }
        .finus-loader-title{margin:0;color:var(--g950);font-size:14px;font-weight:900}
        .finus-loader-copy{display:block;margin-top:5px;color:var(--muted);font-size:11px;line-height:1.5}
        @keyframes finus-spin{to{transform:rotate(360deg)}}
        @keyframes finus-reveal{
            from{opacity:0;transform:translateY(18px)}
            to{opacity:1;transform:none}
        }
        @media(min-width:1100px){.finus-brand-copy{display:block}}
        @media(max-width:980px){
            :root{--header:70px}
            .is-desktop-only{display:none}
            .finus-hero-grid{grid-template-columns:1fr;gap:30px}
            .finus-role-card{width:min(100%,640px)}
        }
        @media(max-width:720px){
            .finus-container{width:min(calc(100% - 24px),var(--container))}
            .finus-header-inner{gap:10px}
            .finus-brand-logo{height:46px;max-width:155px}
            .finus-nav-button-text,.finus-nav-chevron{display:none}
            .finus-nav-button{width:44px;padding:0}
            .finus-dropdown{display:none!important}
            .finus-hero{
                align-items:flex-start;padding:38px 0 calc(34px + env(safe-area-inset-bottom));
                background-image:
                    linear-gradient(180deg,rgba(240,255,244,.93),rgba(244,255,247,.77) 48%,rgba(235,255,241,.57)),
                    url('{{ asset('assets/images/bg-welcome.png') }}');
                background-position:57% center
            }
            .finus-hero::after{right:-185px;bottom:-205px}
            .finus-hero-grid{gap:24px}
            .finus-eyebrow{margin-bottom:15px}
            .finus-hero-title{font-size:clamp(49px,16vw,70px)}
            .finus-hero-subtitle{font-size:clamp(21px,7vw,29px)}
            .finus-hero-description{font-size:13px;line-height:1.68}
            .finus-hero-actions{display:grid;grid-template-columns:1fr;margin-top:24px}
            .finus-primary-button,.finus-secondary-button{width:100%}
            .finus-hero-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr))}
            .finus-meta-item{justify-content:center;padding:0 10px;font-size:11px}
            .finus-role-card{border-radius:20px}
            .finus-role-title{font-size:20px}
            .finus-role-link{grid-template-columns:39px minmax(0,1fr) auto;min-height:56px}
            .finus-role-icon{width:39px;height:39px}
            .finus-modal-actions{grid-template-columns:1fr}
            .finus-cancel-button{order:2}
        }
        @media(max-width:390px){
            .finus-brand-logo{height:42px;max-width:137px}
            .finus-nav{gap:7px}
            .finus-hero{padding-top:30px}
            .finus-hero-meta{grid-template-columns:1fr}
            .finus-modal-content{padding:23px 18px}
        }
        @media(max-width:720px) and (orientation:landscape) and (max-height:520px){
            .finus-hero{padding-top:26px}
            .finus-hero-grid{grid-template-columns:minmax(0,1fr) minmax(280px,.72fr);gap:24px}
            .finus-role-card{padding:20px}
            .finus-role-list{gap:7px;margin-top:15px}
            .finus-role-security,.finus-hero-meta{display:none}
        }
        @media(prefers-reduced-motion:reduce){
            html{scroll-behavior:auto}
            *,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important}
        }
        /* =====================================================
           WELCOME V2 — POLISH & MULTI-DEVICE
        ===================================================== */
        .finus-header {
            transition:
                min-height .25s ease,
                background .25s ease,
                box-shadow .25s ease;
        }
        .finus-header.is-scrolled {
            background:
                linear-gradient(
                    105deg,
                    rgba(3, 61, 24, .97),
                    rgba(14, 110, 44, .96) 48%,
                    rgba(3, 61, 24, .98)
                );
            box-shadow:
                0 12px 34px rgba(0, 42, 15, .25);
        }
        .finus-brand-logo {
            color-scheme: only light !important;
            forced-color-adjust: none !important;
        }
        .finus-hero-batik {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1;

            width: min(27vw, 310px);
            height: 100%;
            object-fit: cover;
            opacity: .20;
            mix-blend-mode: multiply;
            pointer-events: none;
            -webkit-mask-image:
                linear-gradient(
                    to right,
                    #000 0%,
                    rgba(0, 0, 0, .84) 62%,
                    transparent 100%
                );
            mask-image:
                linear-gradient(
                    to right,
                    #000 0%,
                    rgba(0, 0, 0, .84) 62%,
                    transparent 100%
                );
        }
        .finus-hero-content {
            position: relative;
        }
        .finus-hero-content::before {
            content: "";
            position: absolute;
            top: -28px;
            left: -30px;
            z-index: -1;
            width: 135px;
            height: 135px;
            border:
                1px solid rgba(17, 138, 55, .12);
            border-radius: 30px;
            background:
                rgba(255, 255, 255, .23);
            transform: rotate(12deg);
            backdrop-filter: blur(4px);
        }
        .finus-hero-description strong {
            color: var(--g900);
            font-weight: 900;
        }
        .finus-hero-meta {
            max-width: 700px;
        }
        .finus-role-card {
            transform:
                translateZ(0);
            transition:
                transform .25s ease,
                box-shadow .25s ease;
        }
        .finus-role-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 36px 92px rgba(1, 38, 14, .30);
        }
        .finus-role-link {
            text-align: left;
        }
        .finus-role-link:active {
            transform:
                translateX(2px)
                scale(.995);
        }
        .finus-section {
            position: relative;
            padding:
                clamp(56px, 7vw, 92px)
                0;
        }
        .finus-section-soft {
            background:
                radial-gradient(
                    circle at 12% 10%,
                    rgba(126, 255, 135, .12),
                    transparent 18rem
                ),
                linear-gradient(
                    180deg,
                    #F7FCF8,
                    #EEF8F1
                );
        }
        .finus-section-dark {
            overflow: hidden;
            background:
                radial-gradient(
                    circle at 90% 0%,
                    rgba(126, 255, 135, .15),
                    transparent 22rem
                ),
                linear-gradient(
                    135deg,
                    #033D18,
                    #075923 48%,
                    #0E6E2C
                );

            color: #FFFFFF;
        }
        .finus-section-dark::after {
            content: "";
            position: absolute;
            right: -130px;
            bottom: -210px;
            width: 410px;
            height: 410px;
            border:
                72px solid rgba(255, 255, 255, .055);
            border-radius: 50%;
            pointer-events: none;
        }
        .finus-section-heading {
            max-width: 720px;
            margin:0 auto clamp(26px, 4vw, 42px);
            text-align: center;
        }
        .finus-section-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            padding: 7px 11px;
            border: 1px solid rgba(7, 89, 35, .14);
            border-radius: 999px;
            background:
                rgba(255, 255, 255, .72);
            color: var(--g900);
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
            box-shadow: var(--shadow-sm);
        }
        .finus-section-kicker::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--g500);
            box-shadow: 0 0 0 4px rgba(34, 186, 81, .13);
        }
        .finus-section-dark
        .finus-section-kicker {
            border-color:rgba(255, 255, 255, .16);
            background:rgba(255, 255, 255, .09);
            color:
                rgba(255, 255, 255, .84);
            box-shadow: none;
        }
        .finus-section-title {
            margin: 0;
            color: var(--g950);
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(28px, 4vw, 45px);
            font-weight: 900;
            line-height: 1.18;
            letter-spacing: -.025em;
        }
        .finus-section-dark
        .finus-section-title {
            color: #FFFFFF;
        }
        .finus-section-copy {
            max-width: 650px;
            margin: 13px auto 0;
            color: #607369;
            font-size: clamp(12px, 1.4vw, 15px);
            line-height: 1.75;
        }
        .finus-section-dark
        .finus-section-copy {
            color: rgba(255, 255, 255, .70);
        }
        .finus-feature-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
        } 
        .finus-feature-card {
            position: relative;
            overflow: hidden;
            min-height: 240px;
            padding: 24px;
            border: 1px solid rgba(7, 89, 35, .10);
            border-radius: 20px;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 15px 34px rgba(3, 61, 24, .07);
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
            backdrop-filter: blur(10px);
        }
        .finus-feature-card::after {
            content: "";
            position: absolute;
            right: -45px;
            bottom: -55px;
            width: 135px;
            height: 135px;
            border-radius: 50%;
            background: var(--feature-soft, var(--g100));
            opacity: .72;
            pointer-events: none;
        }
        .finus-feature-card:hover {
            border-color: rgba(7, 89, 35, .22);
            transform: translateY(-5px);
            box-shadow: 0 22px 43px rgba(3, 61, 24, .11);
        }
        .finus-feature-icon,
        .finus-feature-title,
        .finus-feature-copy,
        .finus-feature-list {
            position: relative;
            z-index: 2;
        }
        .finus-feature-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border-radius: 15px;
            background: var(--feature-soft, var(--g100));
            color: var(--feature-color, var(--g700));
            font-size: 21px;
        }
        .finus-feature-title {
            margin: 18px 0 0;
            color: var(--g950);
            font-size: 17px;
            font-weight: 900;
        }
        .finus-feature-copy {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.65;
        }
        .finus-feature-list {
            display: grid;
            gap: 7px;
            margin: 15px 0 0;
            padding: 0;
            list-style: none;
        }
        .finus-feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            color: #50685A;
            font-size: 10.5px;
            font-weight: 700;
            line-height: 1.5;
        }
        .finus-feature-list li::before {
            content: "✓";
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            min-width: 18px;
            height: 18px;
            margin-top: 1px;
            border-radius: 6px;
            background: var(--g100);
            color: var(--g700);
            font-size: 10px;
            font-weight: 900;
        }
        .finus-feature-card.is-blue {
            --feature-color: #2563EB;
            --feature-soft: #EAF2FF;
        }
        .finus-feature-card.is-purple {
            --feature-color: #7C3AED;
            --feature-soft: #F2ECFF;
        }
        .finus-flow-grid {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }
        .finus-flow-step {
            position: relative;
            min-height: 180px;
            padding: 22px;
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 18px;
            background: rgba(255, 255, 255, .075);
            backdrop-filter: blur(8px);
        }
        .finus-flow-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border: 1px solid rgba(183, 255, 190, .30);
            border-radius: 12px;
            background: rgba(126, 255, 135, .14);
            color: #B7FFBE;
            font-size: 12px;
            font-weight: 900;
        }
        .finus-flow-title {
            margin: 17px 0 0;
            color: #FFFFFF;
            font-size: 16px;
            font-weight: 900;
        }
        .finus-flow-copy {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, .68);
            font-size: 11.5px;
            line-height: 1.7;
        }
        .finus-flow-arrow {
            position: absolute;
            top: 50%;
            right: -18px;
            z-index: 5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 50%;
            background: #075923;
            color: #B7FFBE;
            font-size: 15px;
            transform: translateY(-50%);
        }
        .finus-footer {
            padding: 24px 0 calc(24px + env(safe-area-inset-bottom));
            border-top: 1px solid rgba(255, 255, 255, .10);
            background: #022F12;
            color: rgba(255, 255, 255, .68);
        }
        .finus-footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }
        .finus-footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .finus-footer-logo {
            width: auto;
            height: 37px;
            max-width: 120px;
            object-fit: contain;
            color-scheme: only light !important;
            forced-color-adjust: none !important;
        }
        .finus-footer-text {
            font-size: 10.5px;
            line-height: 1.6;
        }
        .finus-footer-links {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 13px;
            flex-wrap: wrap;
        }
        .finus-footer-link {
            color: rgba(255, 255, 255, .72);
            font-size: 10.5px;
            font-weight: 800;
            text-decoration: none;
        }
        .finus-footer-link:hover {
            color: #FFFFFF;
        }
        .finus-reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity .55s ease, transform .55s ease;
        }
        .finus-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        #informasi,
        #layanan,
        #keunggulan,
        #alur {
            scroll-margin-top:
                calc(
                    var(--header)
                    + 18px
                );
        }
        @media (max-width: 980px) {
            .finus-feature-grid,
            .finus-flow-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .finus-flow-step:last-child {
                grid-column: 1 / -1;
            }
            .finus-flow-arrow {
                display: none;
            }
        }
        @media (max-width: 720px) {
            .finus-hero-batik {
                width: 180px;
                opacity: .13;
            }
            .finus-hero-content::before {
                top: -14px;
                left: -10px;
                width: 92px;
                height: 92px;
            }
            .finus-feature-grid,
            .finus-flow-grid {
                grid-template-columns: 1fr;
            }
            .finus-flow-step:last-child {
                grid-column: auto;
            }
            .finus-feature-card {
                min-height: 0;
            }
            .finus-footer-inner {
                align-items: flex-start;
                flex-direction: column;
            }
            .finus-footer-links {
                justify-content: flex-start;
            }
        }
        @media (max-width: 390px) {
            .finus-section {
                padding: 48px 0;
            }
            .finus-feature-card,
            .finus-flow-step {
                padding: 20px;
                border-radius: 17px;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .finus-reveal {
                opacity: 1;
                transform: none;
            }
        }
        /* =====================================================
           WELCOME V3 — CLEANER LAYOUT & NAVIGATION
           Menjaga seluruh alur login/verifikasi yang sudah ada,
           tetapi merapikan proporsi, warna, dropdown, dan mobile.
        ===================================================== */
        :root {
            --header: 72px;
            --container: 1160px;
        }
        .finus-header {
            background:
                radial-gradient(circle at 18% 0%, rgba(126, 255, 135, .12), transparent 15rem),
                linear-gradient(105deg, #064A1D 0%, #075923 48%, #118A37 76%, #033D18 100%);
            box-shadow: 0 8px 26px rgba(2, 49, 18, .18);
        }
        .finus-header.is-scrolled {
            background:
                linear-gradient(105deg, rgba(3, 61, 24, .99), rgba(7, 89, 35, .98) 54%, rgba(3, 61, 24, .99));
            box-shadow: 0 11px 32px rgba(1, 39, 14, .24);
        }
        .finus-header-inner {
            min-height: var(--header);
        }
        .finus-brand {
            gap: 10px;
        }
        .finus-brand-logo {
            height: 47px;
            max-width: 176px;
        }
        .finus-brand-copy {
            line-height: 1.25;
        }
        .finus-brand-name {
            font-size: 12px;
            letter-spacing: .105em;
        }
        .finus-brand-subtitle {
            font-size: 9px;
        }
        .finus-nav {
            gap: 8px;
        }
        .finus-nav-button {
            min-height: 40px;
            padding-inline: 13px;
            border-radius: 11px;
        }
        .finus-dropdown {
            width: 178px;
            padding: 6px;
            border-radius: 13px;
        }
        .finus-dropdown-link {
            min-height: 40px;
            border-radius: 9px;
        }
        .finus-dropdown-link-centered {
            justify-content: center;
            text-align: center;
        }
        .finus-main {
            padding-top: calc(var(--header) + env(safe-area-inset-top));
        }
        .finus-hero {
            min-height: calc(100vh - var(--header) - env(safe-area-inset-top));
            min-height: calc(100dvh - var(--header) - env(safe-area-inset-top));
            padding: clamp(46px, 5.8vw, 72px) 0 clamp(42px, 5vw, 62px);
            background-image:
                linear-gradient(
                    90deg,
                    rgba(244, 253, 246, .95) 0%,
                    rgba(244, 253, 246, .88) 42%,
                    rgba(238, 250, 241, .60) 72%,
                    rgba(5, 82, 30, .13) 100%
                ),
                url('{{ asset('assets/images/bg-welcome.png') }}');
        }
        .finus-hero::before {
            background:
                radial-gradient(circle at 15% 16%, rgba(126, 255, 135, .13), transparent 21rem),
                radial-gradient(circle at 78% 76%, rgba(255, 255, 255, .20), transparent 24rem);
        }
        .finus-hero::after {
            right: -135px;
            bottom: -185px;
            width: 360px;
            height: 360px;
            border-width: 58px;
            opacity: .68;
        }
        .finus-hero-batik {
            width: min(20vw, 220px);
            opacity: .10;
        }
        .finus-hero-grid {
            grid-template-columns: minmax(0, 1.12fr) minmax(350px, .78fr);
            gap: clamp(40px, 5.4vw, 76px);
        }
        .finus-hero-content {
            max-width: 665px;
        }
        .finus-hero-content::before {
            top: -22px;
            left: -22px;
            width: 116px;
            height: 116px;
            border-radius: 26px;
            opacity: .72;
        }
        .finus-eyebrow {
            margin-bottom: 16px;
            padding: 7px 12px;
        }
        .finus-hero-title {
            font-size: clamp(54px, 7.3vw, 91px);
            line-height: .96;
        }
        .finus-hero-subtitle {
            max-width: 600px;
            margin-top: clamp(18px, 2.5vw, 25px);
            font-size: clamp(24px, 3vw, 36px);
            line-height: 1.22;
        }
        .finus-hero-description {
            max-width: 610px;
            margin-top: 15px;
            font-size: clamp(13px, 1.35vw, 16px);
            line-height: 1.7;
        }
        .finus-hero-actions {
            margin-top: 24px;
        }
        .finus-primary-button,
        .finus-secondary-button {
            min-height: 47px;
            border-radius: 12px;
        }
        .finus-hero-meta {
            margin-top: 22px;
        }
        .finus-meta-item {
            min-height: 35px;
        }
        .finus-role-card {
            width: min(100%, 430px);
            justify-self: end;
            padding: clamp(23px, 2.5vw, 29px);
            border-radius: 22px;
        }
        .finus-role-title {
            margin-top: 14px;
            font-size: 22px;
        }
        .finus-role-list {
            gap: 9px;
            margin-top: 18px;
        }
        .finus-role-link {
            min-height: 57px;
            border-radius: 13px;
        }
        .finus-role-security {
            margin-top: 16px;
            padding-top: 15px;
        }
        .finus-section {
            padding: clamp(54px, 6vw, 78px) 0;
        }
        .finus-section-heading {
            margin-bottom: clamp(24px, 3.3vw, 36px);
        }
        .finus-section-title {
            font-size: clamp(29px, 3.7vw, 43px);
            line-height: 1.15;
        }
        .finus-section-copy {
            margin-top: 11px;
        }
        .finus-feature-grid {
            gap: 16px;
        }
        .finus-feature-card {
            min-height: 228px;
            padding: 23px;
            border-radius: 19px;
        }
        .finus-feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
        }
        .finus-feature-title {
            margin-top: 16px;
        }
        .finus-flow-step {
            min-height: 168px;
            padding: 21px;
        }
        .finus-footer {
            padding-top: 21px;
        }
        .finus-mobile-menu-button {
            display: none;
        }
        @media (max-width: 980px) {
            .finus-hero-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .finus-role-card {
                justify-self: start;
                width: min(100%, 650px);
            }
        }
        @media (max-width: 720px) {
            .finus-about-group,
            .finus-desktop-more-group {
                display: none !important;
            }
            .finus-mobile-menu-button {
                display: inline-flex;
            }
            .finus-login-group .finus-nav-button {
                width: auto !important;
                min-width: 76px;
                padding-inline: 12px !important;
            }
            .finus-login-group .finus-nav-button-text,
            .finus-login-group .finus-nav-chevron {
                display: inline-block !important;
            }
            .finus-login-group .finus-nav-button-icon {
                display: none;
            }
            .finus-login-group .finus-dropdown {
                display: block !important;
                right: auto;
                left: 50%;
                width: 158px;
                transform: translate(-50%, -6px) scale(.98);
                transform-origin: top center;
            }
            .finus-login-group .finus-dropdown.is-open {
                transform: translate(-50%, 0) scale(1);
            }
            .finus-login-group .finus-dropdown::before {
                right: auto;
                left: 50%;
                transform: translateX(-50%) rotate(45deg);
            }
            .finus-brand-logo {
                height: 44px;
                max-width: 148px;
            }
            .finus-hero {
                padding-top: 34px;
            }
            .finus-hero-title {
                font-size: clamp(48px, 15vw, 66px);
            }
            .finus-hero-subtitle {
                font-size: clamp(22px, 7vw, 29px);
            }
            .finus-feature-card,
            .finus-flow-step {
                border-radius: 17px;
            }
        }
        @media (max-width: 390px) {
            .finus-brand-logo {
                height: 41px;
                max-width: 132px;
            }
            .finus-login-group .finus-nav-button {
                min-width: 70px;
                padding-inline: 10px !important;
            }
        }
        /* =====================================================
           WELCOME V4 — EXACT HEADER GRADIENT & FULL RESPONSIVE
        ===================================================== */
        :root {
            --header: clamp(66px, 5.3vw, 78px);
            --container: 1180px;
        }
        .finus-header,
        .finus-header.is-scrolled {
            background: linear-gradient(
                to right,
                #0FB442 0%,
                #1AAF48 39%,
                #118635 75%,
                #004716 100%
            );
            box-shadow: 0 10px 30px rgba(0, 71, 22, .22);
        }
        .finus-header {
            width: 100%;
        }
        .finus-header-inner {
            display: grid;
            grid-template-columns: minmax(0, auto) minmax(0, 1fr);
            align-items: center;
            gap: clamp(14px, 3vw, 34px);
            width: 100%;
        }
        .finus-brand {
            display: flex;
            align-items: center;
            justify-self: start;
            min-width: 0;
            user-select: none;
        }
        .finus-brand-logo {
            width: auto;
            height: clamp(43px, 4vw, 54px);
            max-width: clamp(145px, 16vw, 205px);
            object-fit: contain;
        }
        .finus-nav {
            justify-self: end;
            justify-content: flex-end;
            margin-left: auto;
            min-width: 0;
        }
        .finus-nav-button {
            white-space: nowrap;
        }
        .finus-dropdown {
            max-width: calc(100vw - 24px);
        }
        .finus-faq-section {
            background:
                radial-gradient(circle at 88% 18%, rgba(126, 255, 135, .12), transparent 20rem),
                linear-gradient(180deg, #F7FCF8 0%, #EDF7F0 100%);
        }
        .finus-faq-layout {
            display: grid;
            grid-template-columns: minmax(260px, .72fr) minmax(0, 1.28fr);
            align-items: start;
            gap: clamp(28px, 6vw, 74px);
        }
        .finus-faq-heading .finus-section-title,
        .finus-faq-heading .finus-section-copy {
            margin-inline: 0;
            text-align: left;
        }
        .finus-faq-list {
            display: grid;
            gap: 12px;
        }
        .finus-faq-item {
            overflow: hidden;
            border: 1px solid rgba(7, 89, 35, .12);
            border-radius: 16px;
            background: rgba(255, 255, 255, .92);
            box-shadow: 0 12px 28px rgba(3, 61, 24, .06);
        }
        .finus-faq-item summary {
            position: relative;
            padding: 18px 50px 18px 19px;
            color: var(--g950);
            font-size: 13px;
            font-weight: 900;
            line-height: 1.5;
            list-style: none;
            cursor: pointer;
        }
        .finus-faq-item summary::-webkit-details-marker {
            display: none;
        }
        .finus-faq-item summary::after {
            content: "+";
            position: absolute;
            top: 50%;
            right: 17px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 9px;
            background: var(--g100);
            color: var(--g800);
            font-size: 18px;
            transform: translateY(-50%);
            transition: transform .2s ease;
        }
        .finus-faq-item[open] summary::after {
            content: "−";
            transform: translateY(-50%) rotate(180deg);
        }
        .finus-faq-item p {
            margin: 0;
            padding: 0 19px 19px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.72;
        }
        .finus-footer {
            padding: 0;
        }
        .finus-footer-inner {
            display: grid;
            grid-template-columns: minmax(230px, .9fr) minmax(310px, 1.2fr) minmax(135px, .45fr);
            align-items: start;
            gap: clamp(26px, 5vw, 64px);
            padding-top: 32px;
            padding-bottom: 28px;
        }
        .finus-footer-brand {
            align-items: flex-start;
        }
        .finus-footer-text {
            display: grid;
            gap: 4px;
            color: rgba(255, 255, 255, .70);
        }
        .finus-footer-text strong {
            color: #FFFFFF;
            font-size: 11px;
            letter-spacing: .08em;
        }
        .finus-footer-text span {
            max-width: 220px;
        }
        .finus-footer-address {
            display: grid;
            gap: 9px;
            margin: 0;
            font-style: normal;
        }
        .finus-footer-heading {
            display: block;
            margin-bottom: 3px;
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
        }
        .finus-footer-contact {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            color: rgba(255, 255, 255, .72);
            font-size: 10.5px;
            line-height: 1.55;
            text-decoration: none;
            transition: color .18s ease, transform .18s ease;
        }
        .finus-footer-contact:hover,
        .finus-footer-contact:focus-visible {
            color: #FFFFFF;
            outline: none;
            transform: translateX(2px);
        }
        .finus-footer-contact-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            min-width: 22px;
            height: 22px;
            border-radius: 7px;
            background: rgba(126, 255, 135, .12);
            color: #B7FFBE;
            font-size: 11px;
        }
        .finus-footer-links {
            display: grid;
            justify-content: stretch;
            gap: 8px;
        }
        .finus-footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-top: 14px;
            padding-bottom: calc(14px + env(safe-area-inset-bottom));
            border-top: 1px solid rgba(255, 255, 255, .09);
            color: rgba(255, 255, 255, .48);
            font-size: 10px;
            font-weight: 700;
            line-height: 1.5;
        }
        #faq,
        #kontak {
            scroll-margin-top: calc(var(--header) + 18px);
        }
        /* Medium laptop and small desktop */
        @media (max-width: 1180px) {
            .finus-container {
                width: min(calc(100% - 40px), var(--container));
            }
            .finus-hero-grid {
                grid-template-columns: minmax(0, 1.06fr) minmax(340px, .78fr);
                gap: 42px;
            }
        }
        /* Tablet landscape and compact laptop */
        @media (max-width: 1024px) {
            .finus-brand-copy {
                display: none;
            }
            .finus-nav-button {
                padding-inline: 12px;
            }
            .finus-hero-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .finus-role-card {
                justify-self: start;
                width: min(100%, 680px);
            }
            .finus-feature-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .finus-feature-card:last-child {
                grid-column: 1 / -1;
            }
            .finus-footer-inner {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .finus-footer-links {
                grid-column: 1 / -1;
                grid-template-columns: repeat(4, max-content);
                align-items: center;
                justify-content: start;
                gap: 10px 18px;
            }
            .finus-footer-links .finus-footer-heading {
                grid-column: 1 / -1;
            }
        }
        /* Tablet portrait: keep logo left, controls right */
        @media (max-width: 840px) {
            .finus-about-group,
            .finus-desktop-more-group {
                display: none !important;
            }
            .finus-mobile-menu-button {
                display: inline-flex;
            }
            .finus-header-inner {
                grid-template-columns: minmax(0, auto) minmax(0, 1fr);
            }
            .finus-login-group .finus-nav-button {
                width: auto !important;
                min-width: 78px;
                padding-inline: 12px !important;
            }
            .finus-login-group .finus-nav-button-text,
            .finus-login-group .finus-nav-chevron {
                display: inline-block !important;
            }
            .finus-login-group .finus-nav-button-icon {
                display: none;
            }
            .finus-login-group .finus-dropdown {
                display: block !important;
                right: 0;
                left: auto;
                width: 172px;
                transform: translateY(-6px) scale(.98);
                transform-origin: top right;
            }
            .finus-login-group .finus-dropdown.is-open {
                transform: none;
            }
            .finus-login-group .finus-dropdown::before {
                right: 22px;
                left: auto;
                transform: rotate(45deg);
            }
            .finus-mobile-panel {
                left: auto;
                width: min(100%, 430px);
                border-left: 1px solid rgba(7, 89, 35, .12);
                border-bottom-left-radius: 20px;
            }
            .finus-faq-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }
        }
        /* Phone and small tablet */
        @media (max-width: 640px) {
            :root {
                --header: 66px;
            }
            .finus-container {
                width: min(calc(100% - 24px), var(--container));
            }
            .finus-header-inner {
                gap: 8px;
            }
            .finus-brand-logo {
                height: 42px;
                max-width: 136px;
            }
            .finus-nav {
                gap: 7px;
            }
            .finus-nav-button,
            .finus-menu-button {
                min-height: 40px;
            }
            .finus-menu-button {
                width: 40px;
            }
            .finus-login-group .finus-nav-button {
                min-width: 69px;
                padding-inline: 9px !important;
            }
            .finus-hero {
                min-height: auto;
                padding-top: 34px;
                padding-bottom: 42px;
            }
            .finus-hero-content::before {
                display: none;
            }
            .finus-hero-actions,
            .finus-hero-meta {
                width: 100%;
            }
            .finus-role-card {
                width: 100%;
            }
            .finus-feature-grid,
            .finus-flow-grid {
                grid-template-columns: 1fr;
            }
            .finus-feature-card:last-child {
                grid-column: auto;
            }
            .finus-footer-inner {
                grid-template-columns: 1fr;
                gap: 25px;
                padding-top: 28px;
            }
            .finus-footer-links {
                grid-column: auto;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .finus-footer-links .finus-footer-heading {
                grid-column: 1 / -1;
            }
            .finus-footer-bottom {
                align-items: flex-start;
                flex-direction: column;
                gap: 4px;
            }
        }
        /* Narrow phone */
        @media (max-width: 420px) {
            .finus-brand-logo {
                height: 39px;
                max-width: 119px;
            }
            .finus-login-group .finus-nav-button {
                min-width: 64px;
                font-size: 11px;
            }
            .finus-nav-chevron {
                width: 6px;
                height: 6px;
            }
            .finus-hero-title {
                font-size: clamp(46px, 15vw, 60px);
            }
            .finus-hero-subtitle {
                font-size: clamp(21px, 7.2vw, 27px);
            }
            .finus-hero-meta {
                grid-template-columns: 1fr;
            }
            .finus-faq-item summary {
                padding: 16px 46px 16px 16px;
                font-size: 12px;
            }
            .finus-faq-item p {
                padding: 0 16px 16px;
            }
        }
        /* Extra narrow devices */
        @media (max-width: 350px) {
            .finus-brand-logo {
                max-width: 105px;
            }
            .finus-login-group .finus-nav-button-text {
                display: none !important;
            }
            .finus-login-group .finus-nav-button {
                width: 40px !important;
                min-width: 40px;
                padding: 0 !important;
            }
            .finus-login-group .finus-nav-button-icon {
                display: inline-flex;
            }
            .finus-login-group .finus-nav-chevron {
                display: none !important;
            }
        }
        /* Short-height landscape phones */
        @media (max-width: 900px) and (orientation: landscape) and (max-height: 560px) {
            :root {
                --header: 62px;
            }
            .finus-brand-logo {
                height: 38px;
            }
            .finus-hero {
                padding-top: 25px;
                padding-bottom: 30px;
            }
            .finus-hero-grid {
                grid-template-columns: minmax(0, 1fr) minmax(280px, .78fr);
                gap: 24px;
            }
            .finus-role-card {
                width: 100%;
                padding: 19px;
            }
            .finus-role-list {
                gap: 7px;
                margin-top: 14px;
            }
            .finus-role-security,
            .finus-hero-meta {
                display: none;
            }
        }
        /* =====================================================
           WELCOME V5 — FULL-BLEED HEADER FIX
           Logo benar-benar di kiri layar, tombol di kanan layar.
        ===================================================== */
        .finus-header,
        .finus-header.is-scrolled {
            background-color: #0FB442 !important;
            background-image: linear-gradient(
                to right,
                #0FB442 0%,
                #1AAF48 39%,
                #118635 75%,
                #004716 100%
            ) !important;
            box-shadow: 0 10px 30px rgba(0, 71, 22, .22) !important;
        }
        /* Lepaskan header dari batas max-width .finus-container. */
        .finus-header > .finus-header-inner,
        .finus-header .finus-header-inner.finus-container {
            width: 100% !important;
            max-width: none !important;
            margin-inline: 0 !important;
            padding-left: clamp(12px, 2vw, 34px) !important;
            padding-right: clamp(12px, 2vw, 34px) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: clamp(10px, 2vw, 24px) !important;
        }
        .finus-header .finus-brand {
            flex: 0 1 auto !important;
            justify-self: auto !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .finus-header .finus-brand-logo {
            width: auto !important;
            height: clamp(42px, 4vw, 56px) !important;
            max-width: clamp(145px, 18vw, 225px) !important;
        }
        .finus-header .finus-nav {
            flex: 0 0 auto !important;
            justify-self: auto !important;
            justify-content: flex-end !important;
            margin-left: auto !important;
        }
        /* Tablet */
        @media (max-width: 900px) {
            .finus-header > .finus-header-inner,
            .finus-header .finus-header-inner.finus-container {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }
            .finus-header .finus-brand-logo {
                height: 44px !important;
                max-width: 155px !important;
            }
        }
        /* HP */
        @media (max-width: 640px) {
            .finus-header > .finus-header-inner,
            .finus-header .finus-header-inner.finus-container {
                padding-left: 10px !important;
                padding-right: 10px !important;
                gap: 7px !important;
            }
            .finus-header .finus-brand-logo {
                height: 39px !important;
                max-width: 122px !important;
            }
            .finus-header .finus-nav {
                gap: 6px !important;
            }
        }
        /* HP sangat sempit */
        @media (max-width: 380px) {
            .finus-header .finus-brand-logo {
                height: 36px !important;
                max-width: 105px !important;
            }
        }
        /* =====================================================
           WELCOME V6 — MOTION & MICRO-INTERACTIONS
           Animasi ringan, halus, dan tetap ramah semua device.
        ===================================================== */
        @keyframes finus-header-enter {
            from { opacity: 0; transform: translateY(-100%); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes finus-logo-enter {
            0% { opacity: 0; transform: translateX(-24px) scale(.92); }
            70% { opacity: 1; transform: translateX(3px) scale(1.02); }
            100% { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes finus-nav-enter {
            from { opacity: 0; transform: translateY(-14px) scale(.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes finus-shimmer {
            0%, 18% { transform: translateX(-130%) skewX(-18deg); opacity: 0; }
            28% { opacity: .34; }
            48%, 100% { transform: translateX(160%) skewX(-18deg); opacity: 0; }
        }
        @keyframes finus-rise {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes finus-slide-left {
            from { opacity: 0; transform: translateX(34px) scale(.975); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes finus-batik-drift {
            0% { transform: translate3d(-8px, -4px, 0) scale(1.02); }
            50% { transform: translate3d(5px, 6px, 0) scale(1.045); }
            100% { transform: translate3d(-2px, 2px, 0) scale(1.025); }
        }
        @keyframes finus-dot-pulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(34, 186, 81, .13); }
            50% { box-shadow: 0 0 0 9px rgba(34, 186, 81, 0); }
        }
        @keyframes finus-soft-pulse {
            0%, 100% { opacity: .72; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.07); }
        }
        @keyframes finus-card-sheen {
            0%, 28% { transform: translateX(-150%) rotate(14deg); opacity: 0; }
            40% { opacity: .22; }
            62%, 100% { transform: translateX(190%) rotate(14deg); opacity: 0; }
        }
        @keyframes finus-dropdown-item-enter {
            from { opacity: 0; transform: translateY(-7px) translateX(4px); }
            to { opacity: 1; transform: translateY(0) translateX(0); }
        }
        @keyframes finus-faq-open {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes finus-arrow-nudge {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(4px); }
        }
        .finus-header {
            animation: finus-header-enter .62s cubic-bezier(.22, .8, .24, 1) both;
            will-change: transform, opacity;
        }
        .finus-header::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background: linear-gradient(
                105deg,
                transparent 0%,
                transparent 42%,
                rgba(255, 255, 255, .28) 50%,
                transparent 58%,
                transparent 100%
            );
            transform: translateX(-130%) skewX(-18deg);
            animation: finus-shimmer 8.5s 1.1s ease-in-out infinite;
        }
        .finus-header-inner {
            position: relative;
            z-index: 1;
        }
        .finus-brand {
            animation: finus-logo-enter .72s .13s cubic-bezier(.22, .85, .3, 1.12) both;
        }
        .finus-brand-logo {
            transition: transform .28s ease, filter .28s ease;
            transform-origin: left center;
        }
        .finus-brand:hover .finus-brand-logo {
            transform: translateY(-2px) scale(1.025);
            filter: drop-shadow(0 9px 17px rgba(0, 30, 10, .25));
        }
        .finus-nav-group {
            opacity: 0;
            animation: finus-nav-enter .5s cubic-bezier(.22, .8, .24, 1) forwards;
        }
        .finus-nav-group:nth-child(1) { animation-delay: .20s; }
        .finus-nav-group:nth-child(2) { animation-delay: .29s; }
        .finus-nav-group:nth-child(3) { animation-delay: .38s; }
        .finus-nav-group:nth-child(4) { animation-delay: .47s; }
        .finus-nav-button,
        .finus-dropdown-link,
        .finus-mobile-menu-link {
            position: relative;
            overflow: hidden;
        }
        .finus-nav-button::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(110deg, transparent 25%, rgba(126, 255, 135, .28), transparent 75%);
            transform: translateX(-120%);
            transition: transform .55s ease;
            pointer-events: none;
        }
        .finus-nav-button:hover::after,
        .finus-nav-button:focus-visible::after {
            transform: translateX(120%);
        }
        .finus-dropdown.is-open .finus-dropdown-link {
            animation: finus-dropdown-item-enter .28s ease both;
        }
        .finus-dropdown.is-open .finus-dropdown-link:nth-of-type(1) { animation-delay: .03s; }
        .finus-dropdown.is-open .finus-dropdown-link:nth-of-type(2) { animation-delay: .07s; }
        .finus-dropdown.is-open .finus-dropdown-link:nth-of-type(3) { animation-delay: .11s; }
        .finus-dropdown.is-open .finus-dropdown-link:nth-of-type(4) { animation-delay: .15s; }
        .finus-mobile-panel.is-open .finus-mobile-menu-link {
            animation: finus-dropdown-item-enter .32s ease both;
        }
        .finus-mobile-panel.is-open .finus-mobile-menu-link:nth-of-type(2n) { animation-delay: .05s; }
        .finus-mobile-panel.is-open .finus-mobile-menu-link:nth-of-type(3n) { animation-delay: .10s; }
        .finus-hero-content {
            animation: none !important;
        }
        .finus-eyebrow,
        .finus-hero-title,
        .finus-hero-subtitle,
        .finus-hero-description,
        .finus-hero-actions,
        .finus-hero-meta {
            opacity: 0;
            animation: finus-rise .66s cubic-bezier(.22, .8, .24, 1) forwards;
        }
        .finus-eyebrow { animation-delay: .18s; }
        .finus-hero-title { animation-delay: .28s; }
        .finus-hero-subtitle { animation-delay: .39s; }
        .finus-hero-description { animation-delay: .50s; }
        .finus-hero-actions { animation-delay: .61s; }
        .finus-hero-meta { animation-delay: .72s; }
        .finus-eyebrow-dot,
        .finus-section-kicker::before {
            animation: finus-dot-pulse 2.2s ease-in-out infinite;
        }
        .finus-hero-batik {
            animation: finus-batik-drift 13s ease-in-out infinite alternate;
            will-change: transform;
        }
        .finus-primary-button,
        .finus-secondary-button {
            position: relative;
            overflow: hidden;
        }
        .finus-primary-button::before {
            content: "";
            position: absolute;
            top: -40%;
            bottom: -40%;
            left: -45%;
            width: 32%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .42), transparent);
            transform: skewX(-20deg);
            transition: left .65s ease;
            pointer-events: none;
        }
        .finus-primary-button:hover::before,
        .finus-primary-button:focus-visible::before {
            left: 125%;
        }
        .finus-primary-button:hover span,
        .finus-primary-button:focus-visible span,
        .finus-role-link:hover .finus-role-arrow,
        .finus-role-link:focus-visible .finus-role-arrow {
            animation: finus-arrow-nudge .65s ease-in-out infinite;
        }
        .finus-secondary-button:hover span,
        .finus-secondary-button:focus-visible span {
            transform: translateY(2px);
        }
        .finus-meta-item {
            transition: transform .22s ease, box-shadow .22s ease, background .22s ease;
        }
        .finus-meta-item:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, .86);
            box-shadow: 0 12px 26px rgba(3, 61, 24, .10);
        }
        .finus-role-card {
            animation: finus-slide-left .78s .44s cubic-bezier(.22, .8, .24, 1) both !important;
        }
        .finus-role-card::after {
            content: "";
            position: absolute;
            top: -45%;
            bottom: -45%;
            left: -35%;
            z-index: 1;
            width: 28%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .25), transparent);
            transform: translateX(-150%) rotate(14deg);
            pointer-events: none;
            animation: finus-card-sheen 7.5s 1.8s ease-in-out infinite;
        }
        .finus-role-link {
            opacity: 0;
            animation: finus-rise .48s ease forwards;
        }
        .finus-role-link:nth-child(1) { animation-delay: .72s; }
        .finus-role-link:nth-child(2) { animation-delay: .82s; }
        .finus-role-link:nth-child(3) { animation-delay: .92s; }
        .finus-role-icon,
        .finus-role-arrow,
        .finus-feature-icon,
        .finus-flow-number,
        .finus-footer-contact-icon {
            transition: transform .24s ease, box-shadow .24s ease;
        }
        .finus-role-link:hover .finus-role-icon,
        .finus-role-link:focus-visible .finus-role-icon {
            transform: rotate(-5deg) scale(1.08);
            box-shadow: 0 10px 22px rgba(0, 35, 12, .18);
        }
        .finus-feature-grid .finus-reveal:nth-child(2) { transition-delay: .09s; }
        .finus-feature-grid .finus-reveal:nth-child(3) { transition-delay: .18s; }
        .finus-flow-grid .finus-reveal:nth-child(2) { transition-delay: .10s; }
        .finus-flow-grid .finus-reveal:nth-child(3) { transition-delay: .20s; }
        .finus-faq-list .finus-reveal:nth-child(2) { transition-delay: .08s; }
        .finus-faq-list .finus-reveal:nth-child(3) { transition-delay: .16s; }
        .finus-feature-card:hover .finus-feature-icon,
        .finus-flow-step:hover .finus-flow-number {
            transform: translateY(-3px) rotate(-4deg) scale(1.08);
            box-shadow: 0 12px 25px rgba(3, 61, 24, .12);
        }
        .finus-feature-card::after {
            animation: finus-soft-pulse 5.5s ease-in-out infinite;
        }
        .finus-flow-step {
            transition: transform .25s ease, background .25s ease, border-color .25s ease;
        }
        .finus-flow-step:hover {
            transform: translateY(-5px);
            border-color: rgba(183, 255, 190, .28);
            background: rgba(255, 255, 255, .105);
        }
        .finus-faq-item {
            transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
        }
        .finus-faq-item:hover {
            transform: translateY(-2px);
            border-color: rgba(7, 89, 35, .22);
            box-shadow: 0 18px 34px rgba(3, 61, 24, .09);
        }
        .finus-faq-item[open] p {
            animation: finus-faq-open .30s ease both;
        }
        .finus-faq-item[open] summary::after {
            box-shadow: 0 8px 18px rgba(3, 61, 24, .12);
        }
        .finus-footer-contact:hover .finus-footer-contact-icon,
        .finus-footer-contact:focus-visible .finus-footer-contact-icon {
            transform: rotate(-6deg) scale(1.08);
        }
        .finus-footer-link {
            position: relative;
            width: fit-content;
        }
        .finus-footer-link::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: -3px;
            left: 0;
            height: 1px;
            background: #B7FFBE;
            transform: scaleX(0);
            transform-origin: right;
            transition: transform .25s ease;
        }
        .finus-footer-link:hover::after,
        .finus-footer-link:focus-visible::after {
            transform: scaleX(1);
            transform-origin: left;
        }
        @media (max-width: 640px) {
            .finus-header::before,
            .finus-role-card::after {
                animation-duration: 10s;
            }
            .finus-hero-batik {
                animation-duration: 16s;
            }
            .finus-role-link:nth-child(1) { animation-delay: .58s; }
            .finus-role-link:nth-child(2) { animation-delay: .66s; }
            .finus-role-link:nth-child(3) { animation-delay: .74s; }
        }
        @media (hover: none), (pointer: coarse) {
            .finus-meta-item:hover,
            .finus-flow-step:hover,
            .finus-faq-item:hover,
            .finus-feature-card:hover,
            .finus-role-card:hover {
                transform: none;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .finus-header,
            .finus-brand,
            .finus-nav-group,
            .finus-eyebrow,
            .finus-hero-title,
            .finus-hero-subtitle,
            .finus-hero-description,
            .finus-hero-actions,
            .finus-hero-meta,
            .finus-hero-batik,
            .finus-role-card,
            .finus-role-link,
            .finus-header::before,
            .finus-role-card::after,
            .finus-feature-card::after,
            .finus-eyebrow-dot,
            .finus-section-kicker::before,
            .finus-dropdown.is-open .finus-dropdown-link,
            .finus-mobile-panel.is-open .finus-mobile-menu-link,
            .finus-faq-item[open] p {
                animation: none !important;
            }
            .finus-nav-group,
            .finus-eyebrow,
            .finus-hero-title,
            .finus-hero-subtitle,
            .finus-hero-description,
            .finus-hero-actions,
            .finus-hero-meta,
            .finus-role-link {
                opacity: 1 !important;
                transform: none !important;
            }
        }
        /* =====================================================
           SCROLL CARD REVEAL — TERPICU SETIAP KOTAK MASUK VIEWPORT
        ===================================================== */
        .finus-role-card.finus-scroll-card {
            animation: none;
        }
        .finus-scroll-card {
            opacity: 0;
            filter: blur(7px);
            transform: translate3d(0, 46px, 0) scale(.965);
            transform-origin: center bottom;
            transition:
                opacity .66s cubic-bezier(.2,.72,.2,1),
                transform .72s cubic-bezier(.2,.72,.2,1),
                filter .58s ease,
                border-color .25s ease,
                box-shadow .25s ease;
            transition-delay: var(--scroll-delay, 0ms);
            will-change: opacity, transform, filter;
        }
        .finus-scroll-card.is-inview {
            opacity: 1;
            filter: blur(0);
            transform: translate3d(0, 0, 0) scale(1);
        }
        .finus-feature-card.finus-scroll-card:nth-child(2n),
        .finus-flow-step.finus-scroll-card:nth-child(2n) {
            transform: translate3d(28px, 42px, 0) scale(.965);
        }
        .finus-feature-card.finus-scroll-card:nth-child(2n).is-inview,
        .finus-flow-step.finus-scroll-card:nth-child(2n).is-inview {
            transform: translate3d(0, 0, 0) scale(1);
        }
        .finus-faq-item.finus-scroll-card {
            transform: translate3d(-24px, 34px, 0) scale(.98);
        }
        .finus-faq-item.finus-scroll-card.is-inview {
            transform: translate3d(0, 0, 0) scale(1);
        }
        @media (max-width: 720px) {
            .finus-scroll-card,
            .finus-feature-card.finus-scroll-card:nth-child(2n),
            .finus-flow-step.finus-scroll-card:nth-child(2n),
            .finus-faq-item.finus-scroll-card {
                transform: translate3d(0, 34px, 0) scale(.98);
                transition-delay: 0ms;
            }
            .finus-scroll-card.is-inview,
            .finus-feature-card.finus-scroll-card:nth-child(2n).is-inview,
            .finus-flow-step.finus-scroll-card:nth-child(2n).is-inview,
            .finus-faq-item.finus-scroll-card.is-inview {
                transform: translate3d(0, 0, 0) scale(1);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .finus-scroll-card,
            .finus-scroll-card.is-inview {
                opacity: 1 !important;
                filter: none !important;
                transform: none !important;
                transition: none !important;
            }
        }
        /* PUBLIC HOMEPAGE — hero tetap sederhana, seluruh peran masuk dari dropdown Login */
        .finus-hero-grid {
            grid-template-columns: minmax(0, 900px) !important;
            justify-content: start;
        }
        .finus-hero-content {
            max-width: 900px;
        }
        .finus-hero-description {
            max-width: 760px;
        }
        .finus-hero-actions {
            width: fit-content;
        }
        @media (max-width: 980px) {
            .finus-hero-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
        @media (max-width: 720px) {
            .finus-hero-actions,
            .finus-primary-button {
                width: 100%;
            }
        }
    

        /* =====================================================
           MODE TAMPILAN FINUS
           Default selalu terang dan tidak mengikuti mode perangkat.
        ===================================================== */
        html[data-finus-theme="light"] { color-scheme:light; }
        html[data-finus-theme="dark"] { color-scheme:dark; }

        .finus-theme-toggle {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:42px;
            min-width:42px;
            height:42px;
            padding:0;
            border:1px solid rgba(255,255,255,.27);
            border-radius:12px;
            background:rgba(255,255,255,.93);
            color:var(--g950);
            cursor:pointer;
            box-shadow:0 8px 18px rgba(0,48,17,.13);
            transition:transform .2s ease,background .2s ease,box-shadow .2s ease;
        }

        .finus-theme-toggle:hover,
        .finus-theme-toggle:focus-visible {
            background:#fff;
            outline:none;
            transform:translateY(-1px);
            box-shadow:0 12px 23px rgba(0,48,17,.18);
        }

        .finus-theme-icon {
            width:19px;
            height:19px;
            fill:none;
            stroke:currentColor;
            stroke-width:2;
            stroke-linecap:round;
            stroke-linejoin:round;
        }

        .finus-theme-icon-moon { display:none; }
        html[data-finus-theme="dark"] .finus-theme-icon-sun { display:none; }
        html[data-finus-theme="dark"] .finus-theme-icon-moon { display:block; }

        html[data-finus-theme="dark"] {
            --g50:#0A120D;
            --text:#EAF4ED;
            --muted:#A7B7AC;
            --shadow-sm:0 8px 24px rgba(0,0,0,.22);
            --shadow-md:0 18px 46px rgba(0,0,0,.30);
            --shadow-lg:0 30px 80px rgba(0,0,0,.42);
        }

        html[data-finus-theme="dark"] body {
            background:#0A120D;
            color:var(--text);
        }

        html[data-finus-theme="dark"] .finus-theme-toggle {
            border-color:#35503B;
            background:#132119;
            color:#BCEFC7;
            box-shadow:0 8px 18px rgba(0,0,0,.26);
        }

        html[data-finus-theme="dark"] .finus-theme-toggle:hover {
            background:#192B1F;
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

        @media (prefers-reduced-motion: reduce) {
            html.finus-theme-changing *,
            html.finus-theme-changing *::before,
            html.finus-theme-changing *::after {
                transition:none !important;
            }
        }

        html[data-finus-theme="dark"] .finus-hero {
            background-image:
                linear-gradient(90deg,rgba(8,21,13,.95),rgba(10,26,15,.87) 42%,rgba(8,27,14,.70) 70%,rgba(0,31,11,.52)),
                url('{{ asset('assets/images/bg-welcome.png') }}');
        }

        html[data-finus-theme="dark"] .finus-hero-title {
            color:#C8FFD1;
            text-shadow:0 2px 0 rgba(0,0,0,.28);
        }

        html[data-finus-theme="dark"] .finus-hero-subtitle,
        html[data-finus-theme="dark"] .finus-section-title,
        html[data-finus-theme="dark"] .finus-feature-title,
        html[data-finus-theme="dark"] .finus-flow-title {
            color:#EAF4ED;
        }

        html[data-finus-theme="dark"] .finus-hero-description,
        html[data-finus-theme="dark"] .finus-section-copy,
        html[data-finus-theme="dark"] .finus-feature-copy,
        html[data-finus-theme="dark"] .finus-flow-copy {
            color:#A7B7AC;
        }

        html[data-finus-theme="dark"] .finus-eyebrow,
        html[data-finus-theme="dark"] .finus-meta-item,
        html[data-finus-theme="dark"] .finus-secondary-button,
        html[data-finus-theme="dark"] .finus-feature-card,
        html[data-finus-theme="dark"] .finus-flow-step,
        html[data-finus-theme="dark"] .finus-faq-item {
            border-color:#2C4132;
            background:#111C15;
            color:#DDECE1;
            box-shadow:0 14px 36px rgba(0,0,0,.22);
        }

        html[data-finus-theme="dark"] .finus-section-soft,
        html[data-finus-theme="dark"] .finus-faq-section {
            background:#0E1811;
        }

        html[data-finus-theme="dark"] .finus-dropdown,
        html[data-finus-theme="dark"] .finus-mobile-panel {
            border-color:#2B4031;
            background:#111C15;
            color:#EAF4ED;
            box-shadow:0 18px 46px rgba(0,0,0,.34);
        }

        html[data-finus-theme="dark"] .finus-dropdown::before {
            border-color:#2B4031;
            background:#111C15;
        }

        html[data-finus-theme="dark"] .finus-dropdown-label,
        html[data-finus-theme="dark"] .finus-mobile-menu-title {
            color:#95A69B;
        }

        html[data-finus-theme="dark"] .finus-dropdown-link,
        html[data-finus-theme="dark"] .finus-mobile-menu-link {
            border-color:#2C4132;
            background:#142119;
            color:#DDECE1;
        }

        html[data-finus-theme="dark"] .finus-dropdown-link:hover,
        html[data-finus-theme="dark"] .finus-mobile-menu-link:hover {
            background:#193522;
            color:#A7F4B6;
        }

        html[data-finus-theme="dark"] .finus-dropdown-link-icon,
        html[data-finus-theme="dark"] .finus-mobile-menu-link-icon,
        html[data-finus-theme="dark"] .finus-feature-icon,
        html[data-finus-theme="dark"] .finus-flow-number {
            background:#17331F;
            color:#79E790;
        }

        html[data-finus-theme="dark"] .finus-nav-button {
            border-color:#35503B;
            background:#132119;
            color:#BCEFC7;
        }

        html[data-finus-theme="dark"] .finus-nav-button:hover,
        html[data-finus-theme="dark"] .finus-nav-button[aria-expanded="true"] {
            background:#192B1F;
        }

        html[data-finus-theme="dark"] .finus-nav-button-icon {
            background:#17331F;
            color:#79E790;
        }

        html[data-finus-theme="dark"] .finus-loader-card {
            border-color:#304535;
            background:#111C15;
            color:#EAF4ED;
        }

    

        /* =====================================================
           DARK MODE POLISH — WELCOME
           Override khusus agar mode gelap tetap punya hierarki,
           kontras, dan warna aksen yang nyaman.
        ===================================================== */
        html[data-finus-theme="dark"] .finus-hero {
            background-image:
                linear-gradient(
                    90deg,
                    rgba(7, 18, 11, .88) 0%,
                    rgba(8, 24, 13, .78) 42%,
                    rgba(8, 28, 15, .54) 70%,
                    rgba(2, 34, 14, .34) 100%
                ),
                url('{{ asset('assets/images/bg-welcome.png') }}');
        }

        html[data-finus-theme="dark"] .finus-hero::before {
            background:
                radial-gradient(circle at 14% 18%, rgba(49, 194, 82, .11), transparent 22rem),
                radial-gradient(circle at 76% 72%, rgba(138, 230, 155, .07), transparent 25rem);
        }

        html[data-finus-theme="dark"] .finus-hero-title {
            color:#D7FFDE;
            text-shadow:0 3px 18px rgba(0,0,0,.34);
        }

        html[data-finus-theme="dark"] .finus-hero-subtitle {
            color:#F0F7F2;
        }

        html[data-finus-theme="dark"] .finus-hero-description {
            color:#C2D0C6;
        }

        html[data-finus-theme="dark"] .finus-hero-description strong,
        html[data-finus-theme="dark"] .finus-hero-description a {
            color:#63D77D;
        }

        html[data-finus-theme="dark"] .finus-eyebrow,
        html[data-finus-theme="dark"] .finus-meta-item {
            border-color:rgba(116, 206, 136, .20);
            background:rgba(15, 39, 23, .78);
            color:#CFE0D3;
            box-shadow:0 9px 26px rgba(0,0,0,.18);
        }

        html[data-finus-theme="dark"] .finus-feature-card {
            border-color:#294232;
            background:
                linear-gradient(145deg, #132219 0%, #101C15 100%);
            color:#EAF4ED;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.025),
                0 16px 38px rgba(0,0,0,.19);
        }

        html[data-finus-theme="dark"] .finus-feature-card:hover {
            border-color:#3A5A43;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.03),
                0 22px 45px rgba(0,0,0,.27);
        }

        /*
         * Lingkaran pastel versi light terlalu terang pada kartu gelap.
         * Di mode gelap diganti menjadi aksen transparan.
         */
        html[data-finus-theme="dark"] .finus-feature-card {
            --feature-dark-soft:rgba(34,186,81,.10);
            --feature-dark-color:#6EE489;
        }

        html[data-finus-theme="dark"] .finus-feature-card.is-blue {
            --feature-dark-soft:rgba(72,132,255,.12);
            --feature-dark-color:#82B1FF;
        }

        html[data-finus-theme="dark"] .finus-feature-card.is-purple {
            --feature-dark-soft:rgba(151,104,255,.13);
            --feature-dark-color:#BBA0FF;
        }

        html[data-finus-theme="dark"] .finus-feature-card::after {
            background:var(--feature-dark-soft);
            opacity:1;
        }

        html[data-finus-theme="dark"] .finus-feature-icon {
            border:1px solid rgba(255,255,255,.035);
            background:var(--feature-dark-soft);
            color:var(--feature-dark-color);
        }

        html[data-finus-theme="dark"] .finus-feature-title {
            color:#F1F7F3;
        }

        html[data-finus-theme="dark"] .finus-feature-copy {
            color:#AEBDB3;
        }

        html[data-finus-theme="dark"] .finus-feature-list li {
            color:#A5B7AB;
        }

        html[data-finus-theme="dark"] .finus-feature-list li::before {
            background:#183421;
            color:#78E991;
            box-shadow:inset 0 0 0 1px rgba(120,233,145,.08);
        }

        html[data-finus-theme="dark"] .finus-section:not(.finus-section-dark) {
            background:#0B150F;
        }

        html[data-finus-theme="dark"] .finus-section-heading .finus-section-kicker:not(.finus-section-dark *) {
            border-color:#294232;
            background:#132119;
            color:#8BE39E;
        }

        html[data-finus-theme="dark"] .finus-section-title {
            color:#EEF6F0;
        }

        html[data-finus-theme="dark"] .finus-section-copy {
            color:#A9B9AE;
        }

        html[data-finus-theme="dark"] .finus-section-dark {
            background:
                radial-gradient(circle at 12% 15%, rgba(34,186,81,.07), transparent 23rem),
                linear-gradient(145deg,#07140C,#0B1D11 55%,#0D2615);
        }

        html[data-finus-theme="dark"] .finus-flow-step {
            border-color:rgba(151, 211, 165, .13);
            background:rgba(255,255,255,.045);
            box-shadow:none;
        }

        html[data-finus-theme="dark"] .finus-flow-copy {
            color:#AFC0B4;
        }

        html[data-finus-theme="dark"] .finus-faq-item {
            border-color:#294232;
            background:#111E16;
        }

    

        /* Hilangkan kotak dekoratif di belakang tulisan "Selamat Datang". */
        .finus-hero-content::before {
            content: none !important;
            display: none !important;
        }

    

        /* =====================================================
           HERO META LABEL — KONSISTEN WELCOME & PENGELOLA
           Gaya gelap dipilih agar menyatu dengan hero dark mode.
        ===================================================== */
        html[data-finus-theme="dark"] .finus-meta-item {
            border:1px solid rgba(104, 203, 127, .24) !important;
            background:rgba(12, 34, 20, .88) !important;
            color:#DDEBE1 !important;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.025),
                0 9px 22px rgba(0,0,0,.16) !important;
            backdrop-filter:blur(8px);
        }

        html[data-finus-theme="dark"] .finus-meta-item:hover {
            border-color:rgba(121, 226, 145, .34) !important;
            background:rgba(18, 50, 29, .94) !important;
            color:#F0F8F2 !important;
        }

        html[data-finus-theme="dark"] .finus-meta-icon {
            border:1px solid rgba(121, 234, 145, .08) !important;
            background:#16351F !important;
            color:#79EA91 !important;
            box-shadow:none !important;
        }

        /* =====================================================
           FAQ — KONTRAS TEKS PADA MODE GELAP
        ===================================================== */
        html[data-finus-theme="dark"] .finus-faq-item {
            border-color:#2A4433 !important;
            background:#111F17 !important;
        }

        html[data-finus-theme="dark"] .finus-faq-item summary {
            color:#EAF4ED !important;
        }

        html[data-finus-theme="dark"] .finus-faq-item summary:hover,
        html[data-finus-theme="dark"] .finus-faq-item summary:focus-visible {
            color:#BDF3C8 !important;
        }

        html[data-finus-theme="dark"] .finus-faq-item summary::after {
            border:1px solid rgba(121, 234, 145, .10);
            background:#173820 !important;
            color:#7BEA92 !important;
        }

        html[data-finus-theme="dark"] .finus-faq-item[open] summary {
            color:#CFF6D7 !important;
        }

        html[data-finus-theme="dark"] .finus-faq-item p {
            color:#AFC0B4 !important;
        }

    </style>
</head>
<body>
    <div id="pageLoader" class="finus-page-loader" role="status" aria-live="polite" aria-hidden="true">
        <div class="finus-loader-card">
            <div class="finus-loader-wheel" aria-hidden="true"></div>
            <p class="finus-loader-title" id="pageLoaderTitle">Membuka halaman...</p>
            <span class="finus-loader-copy">Mohon tunggu sebentar.</span>
        </div>
    </div>
    <header class="finus-header">
        <div class="finus-container finus-header-inner">
            <div class="finus-brand" aria-label="FINUS PUSDAI Jawa Barat">
                <img src="{{ asset('assets/images/FINUS_Welcome.png') }}" alt="FINUS PUSDAI" class="finus-brand-logo" loading="eager" decoding="async">
                <span class="finus-brand-copy">
                    <span class="finus-brand-name">FINUS PUSDAI</span>
                    <span class="finus-brand-subtitle">Sistem Informasi Keuangan Masjid</span>
                </span>
            </div>
            <nav class="finus-nav" aria-label="Navigasi utama">
                <button
                    type="button"
                    class="finus-theme-toggle"
                    data-finus-theme-toggle
                    aria-label="Ubah mode tampilan FINUS"
                    aria-pressed="false"
                    title="Mode gelap"
                >
                    <svg class="finus-theme-icon finus-theme-icon-sun" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.65 17.65l1.42 1.42M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.65 6.35l1.42-1.42"></path>
                    </svg>
                    <svg class="finus-theme-icon finus-theme-icon-moon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20.5 14.5A8 8 0 0 1 9.5 3.5 8.5 8.5 0 1 0 20.5 14.5Z"></path>
                    </svg>
                </button>

                <div class="finus-nav-group finus-login-group">
                    <a href="{{ route('login.jamaah') }}" class="finus-nav-button" data-page-loading="Membuka login jamaah...">
                        <span class="finus-nav-button-icon" aria-hidden="true">↪</span>
                        <span class="finus-nav-button-text">Masuk</span>
                    </a>
                </div>
                <div class="finus-nav-group finus-about-group">
                    <button type="button" class="finus-nav-button" data-dropdown-toggle="aboutDropdown" aria-expanded="false" aria-controls="aboutDropdown">
                        <span class="finus-nav-button-icon" aria-hidden="true">i</span>
                        <span class="finus-nav-button-text">Tentang</span>
                        <span class="finus-nav-chevron" aria-hidden="true"></span>
                    </button>
                    <div class="finus-dropdown" id="aboutDropdown" role="menu">
                        <div class="finus-dropdown-label">PUSDAI Jawa Barat</div>
                        <a href="https://pusdai.or.id/Lahirnya_Sebuah_Gagasan" class="finus-dropdown-link" target="_blank" rel="noopener noreferrer" role="menuitem">
                            <span class="finus-dropdown-link-icon" aria-hidden="true">✦</span>
                            Sambutan
                        </a>
                        <a href="#informasi" class="finus-dropdown-link" role="menuitem">
                            <span class="finus-dropdown-link-icon" aria-hidden="true">◎</span>
                            Tentang FINUS
                        </a>
                        <a href="{{ route('visi-misi') }}" class="finus-dropdown-link" data-page-loading="Membuka halaman visi dan misi..." role="menuitem">
                            <span class="finus-dropdown-link-icon" aria-hidden="true">✧</span>
                            Visi &amp; Misi
                        </a>
                        <a href="#keunggulan" class="finus-dropdown-link" role="menuitem">
                            <span class="finus-dropdown-link-icon" aria-hidden="true">✓</span>
                            Keunggulan
                        </a>
                    </div>
                </div>
                <div class="finus-nav-group finus-desktop-more-group">
                    <button type="button" class="finus-nav-button finus-menu-button" data-dropdown-toggle="moreDropdown" aria-label="Buka menu informasi tambahan" aria-expanded="false" aria-controls="moreDropdown">
                        <span class="finus-nav-button-icon" aria-hidden="true">☰</span>
                    </button>
                    <div class="finus-dropdown" id="moreDropdown" role="menu">
                        <div class="finus-dropdown-label">Informasi lainnya</div>
                        <a href="#kontak" class="finus-dropdown-link" role="menuitem">
                            <span class="finus-dropdown-link-icon" aria-hidden="true">✉</span>
                            Contacts
                        </a>
                        <a href="{{ route('location') }}" class="finus-dropdown-link" data-page-loading="Membuka peta lokasi PUSDAI..." role="menuitem">
                            <span class="finus-dropdown-link-icon" aria-hidden="true">⌖</span>
                            Location
                        </a>
                        <a href="#faq" class="finus-dropdown-link" role="menuitem">
                            <span class="finus-dropdown-link-icon" aria-hidden="true">?</span>
                            FAQ
                        </a>
                    </div>
                </div>
                <button type="button" class="finus-nav-button finus-menu-button finus-mobile-menu-button" id="mobileMenuButton" aria-label="Buka menu" aria-expanded="false" aria-controls="mobileMenuPanel">
                    <span
                        class="finus-nav-button-icon"
                        id="mobileMenuIcon"
                        aria-hidden="true"
                    >☰</span>
                </button>
            </nav>
        </div>
    </header>
    <div class="finus-mobile-panel" id="mobileMenuPanel" aria-hidden="true">
        <div class="finus-mobile-menu-grid">
            <div class="finus-mobile-menu-title">Menu FINUS</div>
            <a href="#informasi" class="finus-mobile-menu-link">
                <span class="finus-mobile-menu-link-icon" aria-hidden="true">i</span>
                Tentang FINUS
            </a>
            <a href="{{ route('visi-misi') }}" class="finus-mobile-menu-link" data-page-loading="Membuka halaman visi dan misi...">
                <span class="finus-mobile-menu-link-icon" aria-hidden="true">✧</span>
                Visi &amp; Misi
            </a>
            <a href="#keunggulan" class="finus-mobile-menu-link">
                <span class="finus-mobile-menu-link-icon" aria-hidden="true">✓</span>
                Keunggulan FINUS
            </a>
            <div class="finus-mobile-menu-title">Informasi lainnya</div>
            <a href="#kontak" class="finus-mobile-menu-link">
                <span class="finus-mobile-menu-link-icon" aria-hidden="true">✉</span>
                Contacts
            </a>
            <a href="{{ route('location') }}" class="finus-mobile-menu-link" data-page-loading="Membuka peta lokasi PUSDAI...">
                <span class="finus-mobile-menu-link-icon" aria-hidden="true">⌖</span>
                Location
            </a>
            <a href="#faq" class="finus-mobile-menu-link">
                <span class="finus-mobile-menu-link-icon" aria-hidden="true">?</span>
                FAQ
            </a>
        </div>
    </div>
    <main class="finus-main">
        <section class="finus-hero">
            <img src="{{ asset('assets/images/pusdai_batik.png') }}" alt="" class="finus-hero-batik" aria-hidden="true" onerror="this.style.display='none'">
            <div class="finus-container finus-hero-grid">
                <div class="finus-hero-content" id="informasi">
                    <div class="finus-eyebrow">
                        <span class="finus-eyebrow-dot"></span>
                        Sistem Informasi Keuangan Masjid
                    </div>
                    <h1 class="finus-hero-title">Selamat Datang</h1>
                    <h2 class="finus-hero-subtitle">Website Masjid PUSDAI Jawa Barat</h2>
                    <p class="finus-hero-description">
                        FINUS memudahkan Jamaah mengakses layanan <strong>Zakat, Infak, dan Wakaf</strong>, melakukan pembayaran, serta memantau riwayat transaksi dalam satu layanan yang sederhana, aman, dan transparan.
                    </p>
                    <div class="finus-hero-actions" id="layanan">
                        <a href="{{ route('register.jamaah') }}" class="finus-primary-button" data-page-loading="Membuka login jamaah...">
                            Daftar Jamaah <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    <div class="finus-hero-meta">
                        <div class="finus-meta-item"><span class="finus-meta-icon">✓</span>Transaksi tercatat</div>
                        <div class="finus-meta-item"><span class="finus-meta-icon">◎</span>Riwayat transparan</div>
                        <div class="finus-meta-item"><span class="finus-meta-icon">⌁</span>Responsif di semua perangkat</div>
                    </div>
                </div>
            </div>
        </section>
        <section class="finus-section finus-section-soft" id="keunggulan">
            <div class="finus-container">
                <div class="finus-section-heading finus-reveal">
                    <span class="finus-section-kicker">
                        Keunggulan FINUS
                    </span>
                    <h2 class="finus-section-title">
                        Satu sistem untuk pengelolaan masjid yang lebih tertib
                    </h2>
                    <p class="finus-section-copy">
                        FINUS menghadirkan layanan Jamaah yang sederhana, transparan, dan mudah digunakan untuk mengakses transaksi ZISWAF serta memantau aktivitas transaksi secara mandiri.
                    </p>
                </div>
                <div class="finus-feature-grid">
                    <article class="finus-feature-card finus-scroll-card" style="--scroll-delay: 0ms">
                        <div class="finus-feature-icon" aria-hidden="true">
                            ✓
                        </div>
                        <h3 class="finus-feature-title">
                            Transparansi Keuangan
                        </h3>
                        <p class="finus-feature-copy">
                            Setiap transaksi Jamaah tersimpan dalam riwayat sehingga status dan nominal dapat dipantau dengan lebih jelas.
                        </p>
                        <ul class="finus-feature-list">
                            <li>Riwayat transaksi pribadi</li>
                            <li>Status pembayaran yang jelas</li>
                            <li>Laporan transaksi pribadi</li>
                        </ul>
                    </article>
                    <article class="finus-feature-card is-blue finus-scroll-card" style="--scroll-delay: 110ms">
                        <div class="finus-feature-icon" aria-hidden="true">
                            ◎
                        </div>
                        <h3 class="finus-feature-title">
                            Layanan ZISWAF
                        </h3>
                        <p class="finus-feature-copy">
                            Jamaah dapat memilih layanan Zakat, Infak, atau Wakaf sesuai kebutuhan melalui satu aplikasi.
                        </p>
                        <ul class="finus-feature-list">
                            <li>Zakat Maal dan Zakat Penghasilan</li>
                            <li>Infak melalui FINUS</li>
                            <li>Wakaf melalui FINUS</li>
                        </ul>
                    </article>
                    <article class="finus-feature-card is-purple finus-scroll-card" style="--scroll-delay: 220ms">
                        <div class="finus-feature-icon" aria-hidden="true">
                            ⌁
                        </div>
                        <h3 class="finus-feature-title">
                            Nyaman di Semua Perangkat
                        </h3>
                        <p class="finus-feature-copy">
                            Antarmuka menyesuaikan ukuran layar agar tetap nyaman digunakan dari HP hingga desktop.
                        </p>
                        <ul class="finus-feature-list">
                            <li>Navigasi responsif</li>
                            <li>Tombol ramah layar sentuh</li>
                            <li>Tampilan konsisten dan ringan</li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>
        <section class="finus-section finus-section-dark" id="alur">
            <div class="finus-container">
                <div class="finus-section-heading finus-reveal">
                    <span class="finus-section-kicker">
                        Alur Layanan
                    </span>
                    <h2 class="finus-section-title">
                        Gunakan FINUS dalam tiga langkah
                    </h2>
                    <p class="finus-section-copy">
                        Masuk atau buat akun Jamaah, pilih layanan yang dibutuhkan, kemudian pantau transaksi melalui dashboard pribadi.
                    </p>
                </div>
                <div class="finus-flow-grid">
                    <article class="finus-flow-step finus-scroll-card" style="--scroll-delay: 0ms">
                        <span class="finus-flow-number">01</span>
                        <h3 class="finus-flow-title">
                            Masuk atau Daftar
                        </h3>
                        <p class="finus-flow-copy">
                            Masuk menggunakan akun Jamaah yang sudah terdaftar atau buat akun baru untuk mulai menggunakan FINUS.
                        </p>
                        <span class="finus-flow-arrow" aria-hidden="true">
                            →
                        </span>
                    </article>
                    <article class="finus-flow-step finus-scroll-card" style="--scroll-delay: 110ms">
                        <span class="finus-flow-number">02</span>
                        <h3 class="finus-flow-title">
                            Pilih Layanan
                        </h3>
                        <p class="finus-flow-copy">
                            Pilih transaksi Zakat, Infak, atau Wakaf sesuai kebutuhan dan lanjutkan metode pembayaran yang tersedia.
                        </p>
                        <span class="finus-flow-arrow" aria-hidden="true">
                            →
                        </span>
                    </article>
                    <article class="finus-flow-step finus-scroll-card" style="--scroll-delay: 220ms">
                        <span class="finus-flow-number">03</span>
                        <h3 class="finus-flow-title">
                            Pantau Transaksi
                        </h3>
                        <p class="finus-flow-copy">
                            Lihat status pembayaran, riwayat transaksi, dan laporan pribadi secara langsung melalui dashboard Jamaah.
                        </p>
                    </article>
                </div>
            </div>
        </section>
        <section class="finus-section finus-faq-section" id="faq">
            <div class="finus-container finus-faq-layout">
                <div class="finus-faq-heading finus-reveal">
                    <span class="finus-section-kicker">FAQ FINUS</span>
                    <h2 class="finus-section-title">Pertanyaan yang sering ditanyakan</h2>
                    <p class="finus-section-copy">
                        Informasi singkat mengenai akses, keamanan, dan penggunaan layanan FINUS.
                    </p>
                </div>
                <div class="finus-faq-list">
                    <details class="finus-faq-item finus-scroll-card" style="--scroll-delay: 0ms">
                        <summary>Apa yang dapat dilakukan Jamaah melalui FINUS?</summary>
                        <p>
                            Jamaah dapat membuat akun, melakukan transaksi Zakat, Infak, dan Wakaf, serta melihat status pembayaran, riwayat, dan laporan transaksi pribadi.
                        </p>
                    </details>
                    <details class="finus-faq-item finus-scroll-card" style="--scroll-delay: 90ms">
                        <summary>Bagaimana cara mulai menggunakan FINUS?</summary>
                        <p>
                            Pilih tombol Login jika sudah memiliki akun Jamaah atau pilih Daftar Jamaah untuk membuat akun baru, lalu ikuti proses verifikasi yang tersedia.
                        </p>
                    </details>
                    <details class="finus-faq-item finus-scroll-card" style="--scroll-delay: 180ms">
                        <summary>Apakah FINUS dapat digunakan melalui HP dan tablet?</summary>
                        <p>
                            Ya. Tata letak, navigasi, kartu, formulir, dan tombol telah dibuat responsif untuk desktop, laptop, tablet, serta berbagai ukuran layar ponsel.
                        </p>
                    </details>
                </div>
            </div>
        </section>
    </main>
    <footer class="finus-footer" id="kontak">
        <div class="finus-container finus-footer-inner">
            <div class="finus-footer-brand finus-reveal">
                <img src="{{ asset('assets/images/FINUS_login.png') }}" alt="FINUS PUSDAI" class="finus-footer-logo" loading="lazy" decoding="async">
                <div class="finus-footer-text">
                    <strong>FINUS PUSDAI</strong>
                    <span>Sistem Informasi Keuangan Masjid PUSDAI Jawa Barat</span>
                </div>
            </div>
            <address class="finus-footer-address finus-reveal">
                <span class="finus-footer-heading">Alamat PUSDAI Jawa Barat</span>
                <a href="https://www.google.com/maps/search/?api=1&query=Jalan+Diponegoro+No+63+Bandung+40122" target="_blank" rel="noopener noreferrer" class="finus-footer-contact">
                    <span class="finus-footer-contact-icon" aria-hidden="true">⌖</span>
                    <span>Jalan Diponegoro No. 63, Bandung, Jawa Barat 40122</span>
                </a>
                <a href="tel:+62227217531" class="finus-footer-contact">
                    <span class="finus-footer-contact-icon" aria-hidden="true">☎</span>
                    <span>(022) 7217531</span>
                </a>
                <a href="mailto:pusdaijabar@gmail.com" class="finus-footer-contact">
                    <span class="finus-footer-contact-icon" aria-hidden="true">✉</span>
                    <span>pusdaijabar@gmail.com</span>
                </a>
            </address>
            <nav class="finus-footer-links finus-reveal" aria-label="Navigasi footer">
                <span class="finus-footer-heading">Navigasi</span>
                <a href="#informasi" class="finus-footer-link">Tentang FINUS</a>
                <a href="{{ route('visi-misi') }}" class="finus-footer-link" data-page-loading="Membuka halaman visi dan misi...">Visi &amp; Misi</a>
                <a href="#keunggulan" class="finus-footer-link">Keunggulan</a>
                <a href="{{ route('location') }}" class="finus-footer-link" data-page-loading="Membuka peta lokasi PUSDAI...">Location</a>
                <a href="#faq" class="finus-footer-link">FAQ</a>
            </nav>
        </div>
        <div class="finus-container finus-footer-bottom">
            <span>© {{ date('Y') }} FINUS PUSDAI Jawa Barat.</span>
            <span>Dibuat untuk pengelolaan masjid yang tertib dan transparan.</span>
        </div>
    </footer>
    <script>
    (() => {
        const body = document.body;
        const pageLoader = document.getElementById('pageLoader');
        const pageLoaderTitle = document.getElementById('pageLoaderTitle');
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenuIcon = document.getElementById('mobileMenuIcon');
        const mobileMenuPanel = document.getElementById('mobileMenuPanel');
        const pageHeader = document.querySelector('.finus-header');
        const updateHeaderState = () => {
            pageHeader?.classList.toggle('is-scrolled', window.scrollY > 18);
        };
        updateHeaderState();
        window.addEventListener('scroll', updateHeaderState, { passive: true });
        const revealElements = document.querySelectorAll('.finus-reveal');
        if(
            'IntersectionObserver' in window
            && !window.matchMedia('(prefers-reduced-motion: reduce)').matches
        ){
            const revealObserver = new IntersectionObserver(
                entries => {
                    entries.forEach(entry => {
                        if (!entry.isIntersecting) return;
                        entry.target.classList.add('is-visible');
                        revealObserver.unobserve(entry.target);
                    });
                },
                {
                    threshold: .14,
                    rootMargin: '0px 0px -45px 0px',
                }
            );

            revealElements.forEach(element => revealObserver.observe(element));
        }else{
            revealElements.forEach(element => element.classList.add('is-visible'));
        }
        const scrollCards = document.querySelectorAll('.finus-scroll-card');
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if('IntersectionObserver' in window && !reduceMotion){
            const scrollCardObserver = new IntersectionObserver(
                entries => {
                    entries.forEach(entry => {
                        if(entry.intersectionRatio >= 0.16){
                            entry.target.classList.add('is-inview');
                            return;
                        }
                        if(!entry.isIntersecting){
                            entry.target.classList.remove('is-inview');
                        }
                    });
                },
                {
                    threshold: [0, 0.16, 0.45],
                    rootMargin: '0px 0px -7% 0px',
                }
            );
            scrollCards.forEach(card => scrollCardObserver.observe(card));
        }else{
            scrollCards.forEach(card => card.classList.add('is-inview'));
        }
        const showPageLoader = (title = 'Membuka halaman...') => {
            pageLoaderTitle.textContent = title;
            pageLoader.classList.add('is-visible');
            pageLoader.setAttribute('aria-hidden', 'false');
            body.classList.add('is-locked');
        };
        const hidePageLoader = () => {
            pageLoader.classList.remove('is-visible');
            pageLoader.setAttribute('aria-hidden', 'true');
            if(!mobileMenuPanel.classList.contains('is-open')) {
                body.classList.remove('is-locked');
            }
        };
        const closeAllDropdowns = exceptId => {
            document.querySelectorAll('.finus-dropdown.is-open').forEach(dropdown => {
                if(dropdown.id === exceptId) return;
                dropdown.classList.remove('is-open');
                document
                    .querySelector(`[data-dropdown-toggle="${dropdown.id}"]`)
                    ?.setAttribute('aria-expanded', 'false');
            });
        };
        const toggleDropdown = button => {
            const dropdown = document.getElementById(button.dataset.dropdownToggle);
            if(!dropdown) return;
            const willOpen = !dropdown.classList.contains('is-open');
            closeAllDropdowns(willOpen ? dropdown.id : null);
            dropdown.classList.toggle('is-open', willOpen);
            button.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        };
        const closeMobileMenu = () => {
            mobileMenuPanel.classList.remove('is-open');
            mobileMenuPanel.setAttribute('aria-hidden', 'true');
            mobileMenuButton.setAttribute('aria-expanded', 'false');
            mobileMenuIcon.textContent = '☰';
            body.classList.remove('is-locked');
        };
        const toggleMobileMenu = () => {
            const willOpen = !mobileMenuPanel.classList.contains('is-open');
            mobileMenuPanel.classList.toggle('is-open', willOpen);
            mobileMenuPanel.setAttribute('aria-hidden', willOpen ? 'false' : 'true');
            mobileMenuButton.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            mobileMenuIcon.textContent = willOpen ? '×' : '☰';
            body.classList.toggle('is-locked', willOpen);
            if (willOpen) closeAllDropdowns();
        };
        document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
            button.addEventListener('click', event => {
                event.stopPropagation();
                toggleDropdown(button);
            });
        });
        document.querySelectorAll('[data-page-loading]').forEach(link => {
            link.addEventListener('click', event => {
                if (
                    event.ctrlKey
                    || event.metaKey
                    || event.shiftKey
                    || link.target === '_blank'
                ) {
                    return;
                }
                showPageLoader(link.dataset.pageLoading || 'Membuka halaman...');
            });
        });
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', () => {
                closeAllDropdowns();
                closeMobileMenu();
            });
        });
        document.addEventListener('click', event => {
            if (!event.target.closest('.finus-nav-group')) {
                closeAllDropdowns();
            }
        });
        mobileMenuButton.addEventListener('click', toggleMobileMenu);
        mobileMenuPanel.addEventListener('click', event => {
            if (event.target.closest('a')) closeMobileMenu();
        });
        document.addEventListener('keydown', event => {
            if (event.key !== 'Escape') return;
            if (mobileMenuPanel.classList.contains('is-open')) {
                closeMobileMenu();
                return;
            }
            closeAllDropdowns();
        });
        window.addEventListener('pageshow', hidePageLoader);
        window.matchMedia('(min-width: 721px)').addEventListener('change', event => {
            if (event.matches) closeMobileMenu();
        });
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

            window.addEventListener('storage', event => {
                if (event.key !== storageKey) {
                    return;
                }

                const theme = event.newValue === 'dark'
                    ? 'dark'
                    : 'light';

                root.dataset.finusTheme = theme;
                root.style.colorScheme = theme;
                updateThemeButtons(theme);

                const themeColorMeta = document.querySelector(
                    'meta[name="theme-color"]'
                );

                if (themeColorMeta) {
                    themeColorMeta.setAttribute(
                        'content',
                        theme === 'dark' ? '#08150D' : '#0FB442'
                    );
                }
            });
        })();
    </script>

</body>
</html>
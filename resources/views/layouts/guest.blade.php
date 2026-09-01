<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#075d2a">
    <meta name="color-scheme" content="light">

    {-- FINUS mengabaikan mode terang/gelap perangkat. Tema hanya mengikuti pilihan di FINUS. --}
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

            document.documentElement.dataset.finusTheme = theme;
            document.documentElement.style.colorScheme = theme;

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
                    theme === 'dark' ? '#08150D' : '#075d2a'
                );
            }
        })();
    </script>

    <title>@yield('title', 'FINUS')</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=22">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=22">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @stack('styles')

    <style>
        :root {
            --auth-green-950: #043d1a;
            --auth-green-900: #075d2a;
            --auth-green-800: #0e7433;
            --auth-green-700: #159342;
            --auth-green-600: #22a853;
            --auth-green-100: #dcf7e5;
            --auth-green-50: #f1fbf4;
            --auth-surface: #ffffff;
            --auth-page: #eef8f1;
            --auth-text: #14251b;
            --auth-muted: #607066;
            --auth-border: #d7e8dc;
            --auth-danger: #b42318;
            --auth-danger-soft: #fff1f0;
            --auth-warning: #a15c00;
            --auth-warning-soft: #fff8e8;
            --auth-info: #175cd3;
            --auth-info-soft: #eff8ff;
            --auth-shadow-sm: 0 8px 24px rgba(5, 63, 27, .10);
            --auth-shadow-lg: 0 28px 80px rgba(1, 42, 16, .24);
            --auth-header-height: 76px;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { min-height: 100%; scroll-behavior: smooth; }
        body {
            min-width: 320px;
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            overflow-x: hidden;
            background: var(--auth-page);
            color: var(--auth-text);
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        button, input, a { -webkit-tap-highlight-color: transparent; }
        button, input { font: inherit; }
        button { border: 0; }
        img { display: block; max-width: 100%; }
        a { color: inherit; }
        [hidden] { display: none !important; }
        body.auth-busy { cursor: progress; overflow: hidden; }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .auth-skip-link {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 10000;
            padding: 10px 14px;
            border-radius: 10px;
            background: #fff;
            color: var(--auth-green-900);
            font-weight: 800;
            text-decoration: none;
            box-shadow: var(--auth-shadow-sm);
            transform: translateY(-150%);
            transition: transform .2s ease;
        }
        .auth-skip-link:focus { transform: translateY(0); }

        .auth-header {
            position: sticky;
            top: 0;
            z-index: 50;
            display: grid;
            grid-template-columns: minmax(170px, 1fr) auto minmax(170px, 1fr);
            align-items: center;
            gap: 20px;
            min-height: var(--auth-header-height);
            padding: 10px clamp(18px, 3vw, 42px);
            border-bottom: 1px solid rgba(255, 255, 255, .13);
            background:
                radial-gradient(circle at 9% 10%, rgba(139, 255, 158, .20), transparent 17rem),
                linear-gradient(112deg, #043d1a 0%, #075d2a 38%, #159342 100%);
            box-shadow: 0 8px 28px rgba(3, 52, 21, .19);
        }

        .auth-brand {
            display: inline-flex;
            align-items: center;
            justify-self: start;
            min-width: 0;
            text-decoration: none;
        }
        .auth-brand img {
            width: auto;
            height: 49px;
            max-width: min(210px, 100%);
            object-fit: contain;
            filter: drop-shadow(0 4px 10px rgba(0, 35, 12, .18));
        }
        .auth-brand-fallback {
            color: #fff;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: .07em;
        }
        .auth-header-title {
            justify-self: center;
            margin: 0;
            color: #fff;
            font-family: "Lora", serif;
            font-size: clamp(17px, 1.8vw, 22px);
            font-weight: 700;
            text-align: center;
            text-shadow: 0 2px 10px rgba(0, 38, 14, .18);
        }
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

        .auth-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(430px, .85fr);
            min-height: calc(100vh - var(--auth-header-height));
            min-height: calc(100dvh - var(--auth-header-height));
        }

        .auth-hero {
            position: relative;
            isolation: isolate;
            display: flex;
            min-height: 620px;
            overflow: hidden;
            background-color: #e5f5e9;
            background-position: center;
            background-size: cover;
        }
        .auth-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -2;
            background:
                linear-gradient(90deg, rgba(241, 255, 245, .90) 0%, rgba(240, 255, 245, .65) 46%, rgba(5, 82, 31, .22) 100%),
                linear-gradient(180deg, rgba(255, 255, 255, .10), rgba(0, 68, 24, .16));
        }
        .auth-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background:
                radial-gradient(circle at 32% 42%, rgba(255,255,255,.70), transparent 21rem),
                radial-gradient(circle at 80% 85%, rgba(22, 147, 66, .18), transparent 18rem);
        }
        .auth-batik {
            position: absolute;
            inset: 0 auto 0 0;
            z-index: 0;
            width: clamp(150px, 18vw, 285px);
            height: 100%;
            object-fit: cover;
            opacity: .28;
            mix-blend-mode: multiply;
            pointer-events: none;
        }
        .auth-hero-orb {
            position: absolute;
            right: -90px;
            bottom: -130px;
            width: 360px;
            height: 360px;
            border: 1px solid rgba(255,255,255,.48);
            border-radius: 50%;
            background: rgba(255,255,255,.10);
            box-shadow: inset 0 0 0 44px rgba(255,255,255,.06);
            backdrop-filter: blur(3px);
            pointer-events: none;
        }
        .auth-hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            width: 100%;
            padding: clamp(54px, 7vw, 98px) clamp(34px, 7vw, 104px);
        }
        .auth-hero-copy {
            width: min(100%, 660px);
            color: var(--auth-green-950);
            animation: auth-rise .55s ease both;
        }
        .auth-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 20px;
            padding: 9px 13px;
            border: 1px solid rgba(7, 93, 42, .17);
            border-radius: 999px;
            background: rgba(255, 255, 255, .82);
            color: var(--auth-green-900);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            box-shadow: 0 8px 22px rgba(8, 88, 39, .08);
            backdrop-filter: blur(10px);
        }
        .auth-hero-badge::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--auth-green-600);
            box-shadow: 0 0 0 5px rgba(34, 168, 83, .13);
        }
        .auth-hero-title {
            max-width: 660px;
            margin: 0;
            font-family: "Lora", serif;
            font-size: clamp(42px, 5vw, 72px);
            font-weight: 700;
            letter-spacing: -.045em;
            line-height: 1.08;
            text-wrap: balance;
            text-shadow: 0 1px 0 rgba(255,255,255,.75);
        }
        .auth-hero-copytext {
            max-width: 600px;
            margin: 22px 0 0;
            color: #1c5631;
            font-family: "Lora", serif;
            font-size: clamp(17px, 1.7vw, 23px);
            font-style: italic;
            font-weight: 600;
            line-height: 1.65;
            text-wrap: pretty;
        }
        .auth-features {
            display: flex;
            flex-wrap: wrap;
            gap: 11px;
            margin-top: 30px;
        }
        .auth-feature {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            min-height: 42px;
            padding: 8px 13px 8px 9px;
            border: 1px solid rgba(7, 93, 42, .14);
            border-radius: 13px;
            background: rgba(255, 255, 255, .79);
            color: #214f31;
            font-size: 11px;
            font-weight: 800;
            box-shadow: 0 9px 24px rgba(5, 84, 35, .07);
            backdrop-filter: blur(8px);
        }
        .auth-feature span {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 27px;
            min-width: 27px;
            height: 27px;
            border-radius: 9px;
            background: var(--auth-green-50);
            color: var(--auth-green-700);
            font-size: 0;
        }
        .auth-feature-icon::before,
        .auth-feature-icon::after {
            content: "";
            position: absolute;
            display: block;
        }
        .auth-feature-icon.is-safe::before {
            width: 12px;
            height: 7px;
            border-left: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            transform: translateY(-1px) rotate(-45deg);
        }
        .auth-feature-icon.is-transparent::before {
            width: 14px;
            height: 14px;
            border: 2px solid currentColor;
            border-radius: 999px;
        }
        .auth-feature-icon.is-transparent::after {
            width: 4px;
            height: 4px;
            border-radius: 999px;
            background: currentColor;
            box-shadow:
                -5px 0 0 currentColor,
                5px 0 0 currentColor,
                0 -5px 0 currentColor,
                0 5px 0 currentColor;
        }
        .auth-feature-icon.is-integrated::before {
            width: 16px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
            box-shadow: 0 -6px 0 currentColor, 0 6px 0 currentColor;
        }
        .auth-feature-icon.is-integrated::after {
            width: 2px;
            height: 14px;
            border-radius: 999px;
            background: currentColor;
        }
        .auth-hero-note {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 26px 0 0;
            color: rgba(20, 75, 40, .80);
            font-size: 10.5px;
            font-weight: 700;
        }
        .auth-hero-note::before {
            content: "";
            width: 24px;
            height: 1px;
            background: rgba(7, 93, 42, .38);
        }

        .auth-panel {
            position: relative;
            isolation: isolate;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
            min-height: 620px;
            overflow: hidden;
            padding: clamp(34px, 4.5vw, 58px) clamp(24px, 4vw, 52px);
            background:
                radial-gradient(circle at 92% 4%, rgba(255,255,255,.20), transparent 16rem),
                radial-gradient(circle at 0% 100%, rgba(141,255,157,.16), transparent 18rem),
                linear-gradient(150deg, #043d1a 0%, #075d2a 38%, #159342 74%, #087031 100%);
        }
        .auth-panel::before,
        .auth-panel::after {
            content: "";
            position: absolute;
            z-index: -1;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 50%;
            pointer-events: none;
        }
        .auth-panel::before { top: -90px; right: -90px; width: 260px; height: 260px; }
        .auth-panel::after { bottom: -140px; left: -120px; width: 330px; height: 330px; }
        .auth-panel-inner {
            width: min(100%, 500px);
            animation: auth-rise .48s ease both;
        }
        .auth-panel-heading {
            margin-bottom: 20px;
            color: #fff;
            text-align: center;
        }
        .auth-panel-logo {
            width: auto;
            height: 56px;
            max-width: 220px;
            margin: 0 auto 13px;
            object-fit: contain;
            filter: drop-shadow(0 6px 14px rgba(0, 39, 14, .24));
        }
        .auth-panel-logo-fallback {
            display: none;
            margin: 0 0 12px;
            color: #fff;
            font-size: 20px;
            font-weight: 800;
        }
        .auth-panel-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 8px;
            color: rgba(235, 255, 240, .78);
            font-size: 9.5px;
            font-weight: 800;
            letter-spacing: .11em;
            text-transform: uppercase;
        }
        .auth-panel-eyebrow::before,
        .auth-panel-eyebrow::after {
            content: "";
            width: 18px;
            height: 1px;
            background: rgba(255,255,255,.35);
        }
        .auth-panel-title {
            margin: 0;
            font-family: "Lora", serif;
            font-size: clamp(27px, 3vw, 35px);
            font-weight: 700;
            letter-spacing: -.03em;
            line-height: 1.2;
            text-wrap: balance;
        }
        .auth-panel-copy {
            max-width: 405px;
            margin: 8px auto 0;
            color: rgba(240, 255, 244, .84);
            font-size: 12px;
            line-height: 1.65;
            text-wrap: pretty;
        }
        .auth-secure {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding: 6px 10px;
            border: 1px solid rgba(255,255,255,.16);
            border-radius: 999px;
            background: rgba(0, 58, 21, .18);
            color: rgba(255,255,255,.85);
            font-size: 9.5px;
            font-weight: 800;
        }
        .auth-secure::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #a9ffb6;
            box-shadow: 0 0 0 4px rgba(169,255,182,.11);
        }

        .auth-card {
            position: relative;
            overflow: hidden;
            padding: clamp(22px, 4vw, 31px);
            border: 1px solid rgba(255,255,255,.60);
            border-radius: 24px;
            background: rgba(255,255,255,.975);
            box-shadow: var(--auth-shadow-lg);
            backdrop-filter: blur(18px);
        }
        .auth-card::before {
            content: "";
            position: absolute;
            top: -95px;
            right: -85px;
            width: 205px;
            height: 205px;
            border-radius: 50%;
            background: rgba(34, 168, 83, .065);
            pointer-events: none;
        }
        .auth-card > * { position: relative; z-index: 1; }

        .auth-form { display: grid; gap: 17px; }
        .auth-field-group { min-width: 0; }
        .auth-form-divider { height: 1px; margin: 1px 0; background: linear-gradient(90deg, transparent, var(--auth-border), transparent); }
        .auth-context-card {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 11px;
            align-items: start;
            padding: 13px 14px;
            border: 1px solid var(--auth-border);
            border-radius: 14px;
            background: linear-gradient(135deg, #f7fcf8, var(--auth-green-50));
            color: #355640;
        }
        .auth-context-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #fff;
            color: var(--auth-green-700);
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 5px 14px rgba(5, 89, 36, .08);
        }
        .auth-context-title { margin: 0; color: var(--auth-green-900); font-size: 11.5px; font-weight: 800; }
        .auth-context-copy { margin: 4px 0 0; color: var(--auth-muted); font-size: 10.5px; line-height: 1.6; }

        .auth-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 7px;
            color: #294b35;
            font-size: 11.5px;
            font-weight: 800;
        }
        .auth-label-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 0 5px;
            border-radius: 8px;
            background: var(--auth-green-50);
            color: var(--auth-green-700);
            font-size: 9px;
            font-weight: 800;
            line-height: 1;
        }
        .auth-required { color: var(--auth-danger); }
        .auth-input-wrap { position: relative; }
        .auth-field {
            width: 100%;
            min-height: 48px;
            border: 1px solid #c9ddd0;
            border-radius: 13px;
            background: #fff;
            padding: 12px 14px;
            color: #123c23;
            font-size: 12.5px;
            font-weight: 600;
            line-height: 1.35;
            outline: none;
            box-shadow: 0 1px 2px rgba(10, 70, 31, .025);
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .auth-field::placeholder { color: #8a9c91; font-weight: 500; }
        .auth-field:hover { border-color: #a9c9b3; }
        .auth-field:focus {
            border-color: var(--auth-green-600);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(34, 168, 83, .12);
        }
        .auth-field[readonly] { cursor: default; background: #f4faf6; color: #42624c; }
        .auth-field[aria-invalid="true"] { border-color: #e48b84; background: #fffafa; }
        .auth-field:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px #fff inset;
            -webkit-text-fill-color: #123c23;
        }
        .auth-password-field { padding-right: 54px; }
        .auth-password-toggle {
            position: absolute;
            top: 50%;
            right: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            padding: 0;
            border-radius: 10px;
            background: transparent;
            cursor: pointer;
            transform: translateY(-50%);
            transition: background .2s ease, transform .2s ease;
        }
        .auth-password-toggle:hover { background: var(--auth-green-50); }
        .auth-password-toggle:active { transform: translateY(-50%) scale(.96); }
        .auth-password-toggle:focus-visible { outline: 3px solid rgba(34,168,83,.18); }
        .auth-password-toggle img { width: 21px; height: 21px; object-fit: contain; }

        .auth-help {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            margin: 7px 0 0;
            color: var(--auth-muted);
            font-size: 9.8px;
            line-height: 1.55;
        }
        .auth-help b { color: var(--auth-green-700); }
        .auth-error {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            margin: 7px 0 0;
            color: var(--auth-danger);
            font-size: 10px;
            font-weight: 700;
            line-height: 1.55;
        }
        .auth-error::before { content: "!"; flex: 0 0 auto; }
        .auth-error-center { justify-content: center; }
        .auth-match-message { margin-top: 6px; font-size: 9.8px; font-weight: 700; }
        .auth-match-message.is-match { color: var(--auth-green-700); }
        .auth-match-message.is-mismatch { color: var(--auth-danger); }

        .auth-form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }
        .auth-checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #516459;
            font-size: 10.5px;
            font-weight: 700;
            cursor: pointer;
            user-select: none;
        }
        .auth-checkbox input {
            width: 17px;
            height: 17px;
            margin: 0;
            accent-color: var(--auth-green-700);
        }
        .auth-link {
            color: var(--auth-green-900);
            font-size: inherit;
            font-weight: 800;
            text-decoration: none;
            text-underline-offset: 3px;
        }
        .auth-link:hover { color: var(--auth-green-600); text-decoration: underline; }
        .auth-link:focus-visible { outline: 3px solid rgba(34,168,83,.16); border-radius: 5px; }
        .auth-link-small { font-size: 10.5px; }

        .auth-button,
        .auth-button-secondary,
        .auth-button-muted {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            min-height: 48px;
            padding: 12px 16px;
            border-radius: 13px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.2;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease, background .2s ease;
        }
        .auth-button {
            border: 1px solid rgba(255,255,255,.10);
            background: linear-gradient(105deg, #043d1a 0%, #087432 50%, #1aa04a 100%);
            color: #fff;
            box-shadow: 0 11px 24px rgba(5, 94, 39, .22);
        }
        .auth-button:hover:not(:disabled) { transform: translateY(-2px); filter: brightness(1.05); box-shadow: 0 16px 30px rgba(5, 94, 39, .27); }
        .auth-button-secondary { border: 1px solid #bcd9c4; background: var(--auth-green-50); color: var(--auth-green-900); }
        .auth-button-secondary:hover:not(:disabled) { transform: translateY(-1px); background: #e5f8eb; box-shadow: var(--auth-shadow-sm); }
        .auth-button-muted { border: 1px solid #d8e2db; background: #f8faf9; color: #526158; }
        .auth-button-muted:hover:not(:disabled) { transform: translateY(-1px); background: #f1f5f2; }
        .auth-button:focus-visible,
        .auth-button-secondary:focus-visible,
        .auth-button-muted:focus-visible { outline: 4px solid rgba(34,168,83,.18); outline-offset: 2px; }
        .auth-button:disabled,
        [aria-busy="true"] { cursor: wait; opacity: .82; transform: none !important; }
        .auth-form-actions-stack { display: grid; gap: 10px; margin-top: 11px; }

        .auth-footer-text {
            margin: 0;
            color: var(--auth-muted);
            font-size: 10.5px;
            line-height: 1.65;
            text-align: center;
        }
        .auth-info-card {
            padding: 13px 14px;
            border: 1px solid var(--auth-border);
            border-radius: 13px;
            background: var(--auth-green-50);
            color: #3d6048;
            font-size: 10.5px;
            line-height: 1.65;
        }
        .auth-email-preview {
            margin-top: 9px;
            padding: 11px 12px;
            border: 1px dashed #b8d6c1;
            border-radius: 11px;
            background: #f8fcf9;
            color: var(--auth-muted);
            font-size: 9.5px;
        }
        .auth-email-preview strong {
            display: block;
            margin-top: 4px;
            color: var(--auth-green-900);
            font-size: 11px;
            overflow-wrap: anywhere;
        }
        .auth-password-strength { margin-top: 9px; }
        .auth-strength-bars { display: grid; grid-template-columns: repeat(4, 1fr); gap: 5px; }
        .auth-strength-bar { height: 5px; border-radius: 999px; background: #e3ebe6; transition: background .2s ease; }
        .auth-strength-text { display: block; margin-top: 6px; color: var(--auth-muted); font-size: 9px; font-weight: 700; }

        .auth-code-intro { text-align: center; }
        .auth-code-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            margin: 0 auto 15px;
            border: 1px solid #cce8d4;
            border-radius: 20px;
            background: linear-gradient(145deg, #f7fff9, var(--auth-green-100));
            color: var(--auth-green-700);
            font-size: 23px;
            font-weight: 800;
            box-shadow: 0 10px 26px rgba(5, 91, 37, .10);
        }
        .auth-code-copy { margin: 0 0 19px; color: var(--auth-muted); font-size: 10.8px; line-height: 1.75; }
        .auth-code-field {
            padding-left: 1.25em;
            text-align: center;
            font-size: clamp(20px, 4vw, 24px);
            font-weight: 800;
            letter-spacing: .32em;
            font-variant-numeric: tabular-nums;
        }

        .auth-alert {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 11px;
            align-items: start;
            margin-bottom: 18px;
            padding: 13px 14px;
            border: 1px solid;
            border-radius: 14px;
            font-size: 10.5px;
            line-height: 1.6;
        }
        .auth-alert-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 800;
        }
        .auth-alert-title { margin: 0; font-size: 11px; font-weight: 800; }
        .auth-alert-message { margin: 3px 0 0; overflow-wrap: anywhere; }
        .auth-alert-success { border-color: #b9dfc3; background: #effaf2; color: #166534; }
        .auth-alert-success .auth-alert-icon { background: #d9f4e0; }
        .auth-alert-danger { border-color: #f2bbb7; background: var(--auth-danger-soft); color: #912018; }
        .auth-alert-danger .auth-alert-icon { background: #fee4e2; }
        .auth-alert-warning { border-color: #f3d49a; background: var(--auth-warning-soft); color: #854a00; }
        .auth-alert-warning .auth-alert-icon { background: #ffedc7; }
        .auth-alert-info { border-color: #b2d5ff; background: var(--auth-info-soft); color: #1849a9; }
        .auth-alert-info .auth-alert-icon { background: #d1e9ff; }

        .auth-dialog-overlay {
            position: fixed;
            inset: 0;
            z-index: 8000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(2, 34, 13, .68);
            backdrop-filter: blur(8px);
        }
        .auth-dialog {
            width: min(100%, 430px);
            max-height: calc(100dvh - 36px);
            overflow: auto;
            border: 1px solid rgba(255,255,255,.60);
            border-radius: 23px;
            background: #fff;
            text-align: center;
            box-shadow: 0 32px 85px rgba(0, 31, 11, .34);
            animation: auth-pop .25s ease both;
        }
        .auth-dialog-body { padding: 29px 27px 22px; }
        .auth-dialog-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 68px;
            height: 68px;
            margin: 0 auto 16px;
            border-radius: 22px;
            background: linear-gradient(145deg, #f5fff7, var(--auth-green-100));
            color: var(--auth-green-700);
            font-size: 26px;
            font-weight: 800;
            box-shadow: 0 10px 24px rgba(7, 101, 42, .12);
        }
        .auth-dialog-title { margin: 0; color: var(--auth-green-900); font-family: "Lora", serif; font-size: 20px; font-weight: 700; }
        .auth-dialog-copy { margin: 9px 0 0; color: var(--auth-muted); font-size: 10.8px; line-height: 1.7; }
        .auth-dialog-details {
            display: grid;
            gap: 9px;
            margin: 18px 0 0;
            padding: 14px;
            border: 1px solid var(--auth-border);
            border-radius: 14px;
            background: var(--auth-green-50);
            text-align: left;
        }
        .auth-dialog-detail { display: grid; grid-template-columns: 105px minmax(0, 1fr); gap: 10px; font-size: 10.5px; }
        .auth-dialog-detail dt { color: var(--auth-muted); font-weight: 700; }
        .auth-dialog-detail dd { margin: 0; color: var(--auth-text); font-weight: 800; overflow-wrap: anywhere; }
        .auth-dialog-footer { padding: 0 27px 27px; }

        .auth-loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            visibility: hidden;
            opacity: 0;
            background: rgba(2, 34, 13, .66);
            backdrop-filter: blur(8px);
            transition: opacity .18s ease, visibility .18s ease;
        }
        .auth-loading-overlay.is-visible { visibility: visible; opacity: 1; }
        .auth-loading-card {
            width: min(100%, 300px);
            padding: 29px 24px;
            border: 1px solid rgba(255,255,255,.60);
            border-radius: 22px;
            background: rgba(255,255,255,.98);
            text-align: center;
            box-shadow: 0 30px 80px rgba(0, 28, 10, .34);
        }
        .auth-loading-wheel {
            position: relative;
            width: 58px;
            height: 58px;
            margin: 0 auto 17px;
            border: 5px solid #dff1e4;
            border-top-color: var(--auth-green-700);
            border-right-color: var(--auth-green-600);
            border-radius: 50%;
            animation: auth-spin .76s linear infinite;
        }
        .auth-loading-wheel::after { content: ""; position: absolute; inset: 10px; border-radius: 50%; background: var(--auth-green-50); }
        .auth-loading-title { margin: 0; color: var(--auth-green-900); font-size: 13px; font-weight: 800; }
        .auth-loading-message { display: block; margin-top: 6px; color: var(--auth-muted); font-size: 10px; line-height: 1.5; }
        .auth-inline-spinner { width: 17px; height: 17px; border: 2px solid rgba(255,255,255,.36); border-top-color: #fff; border-radius: 50%; animation: auth-spin .7s linear infinite; }

        .auth-mt-3 { margin-top: 12px; }
        .auth-block { display: block; }
        .auth-break-all { overflow-wrap: anywhere; }

        @keyframes auth-spin { to { transform: rotate(360deg); } }
        @keyframes auth-rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
        @keyframes auth-pop { from { opacity: 0; transform: scale(.96) translateY(8px); } to { opacity: 1; transform: none; } }

        @media (max-width: 1180px) {
            :root { --auth-header-height: 72px; }
            .auth-layout { grid-template-columns: 1fr; }
            .auth-hero { min-height: 265px; }
            .auth-hero-content { align-items: flex-end; padding: 38px clamp(28px, 7vw, 72px); }
            .auth-hero-copy { width: min(100%, 820px); }
            .auth-hero-title { font-size: clamp(34px, 5vw, 52px); }
            .auth-hero-copytext { max-width: 740px; margin-top: 13px; font-size: clamp(15px, 2vw, 19px); line-height: 1.55; }
            .auth-features { margin-top: 18px; }
            .auth-hero-note { display: none; }
            .auth-panel { min-height: auto; padding-top: 42px; padding-bottom: 56px; }
            .auth-panel-inner { width: min(100%, 570px); }
        }

        @media (max-width: 767.98px) {
            :root { --auth-header-height: 66px; }
            .auth-header { grid-template-columns: minmax(0, 1fr) auto; gap: 12px; padding: 8px 14px; }
            .auth-brand img { height: 42px; max-width: 155px; }
            .auth-header-title { display: none; }
            .auth-home-link { min-height: 38px; padding: 0 11px; border-radius: 11px; }
            .auth-home-link-text { display: none; }
            .auth-home-icon { width: 25px; height: 25px; }
            .auth-hero { display: none; }
            .auth-panel {
                align-items: flex-start;
                min-height: calc(100vh - var(--auth-header-height));
                min-height: calc(100dvh - var(--auth-header-height));
                padding: 28px 14px max(32px, env(safe-area-inset-bottom));
                background:
                    radial-gradient(circle at 95% 0%, rgba(255,255,255,.19), transparent 12rem),
                    linear-gradient(160deg, #043d1a 0%, #087432 42%, #18a24a 100%);
            }
            .auth-panel-heading { margin-bottom: 17px; }
            .auth-panel-logo { height: 49px; margin-bottom: 10px; }
            .auth-panel-eyebrow { margin-bottom: 6px; }
            .auth-panel-title { font-size: clamp(25px, 7vw, 31px); }
            .auth-panel-copy { max-width: 345px; font-size: 10.8px; }
            .auth-secure { margin-top: 10px; }
            .auth-card { padding: 22px 18px; border-radius: 20px; }
            .auth-form { gap: 16px; }
            .auth-form-row { align-items: flex-start; flex-direction: column; gap: 11px; }
            .auth-field { min-height: 49px; font-size: 13px; }
            .auth-button, .auth-button-secondary, .auth-button-muted { min-height: 49px; font-size: 12.5px; }
            .auth-dialog-detail { grid-template-columns: 1fr; gap: 2px; }
        }

        @media (max-width: 389.98px) {
            .auth-header { padding-inline: 10px; }
            .auth-brand img { max-width: 138px; }
            .auth-panel { padding-inline: 10px; }
            .auth-card { padding-inline: 15px; }
            .auth-code-field { letter-spacing: .23em; padding-left: 1em; }
            .auth-dialog-body { padding-inline: 20px; }
            .auth-dialog-footer { padding-inline: 20px; }
        }

        @media (min-width: 1181px) and (max-height: 760px) {
            .auth-panel { align-items: flex-start; padding-top: 28px; padding-bottom: 34px; }
            .auth-panel-heading { margin-bottom: 15px; }
            .auth-panel-logo { height: 47px; margin-bottom: 8px; }
            .auth-panel-title { font-size: 28px; }
            .auth-card { padding: 22px 25px; }
            .auth-form { gap: 14px; }
            .auth-field { min-height: 44px; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
        }
    
        /* =====================================================
           FINUS APPEARANCE — GUEST / AUTH
           Tidak mengikuti dark mode perangkat.
        ===================================================== */
        html[data-finus-theme="light"] {
            color-scheme: light;
        }

        html[data-finus-theme="dark"] {
            color-scheme: dark;

            --auth-surface: #111C15;
            --auth-page: #0A120D;
            --auth-text: #EAF4ED;
            --auth-muted: #A7B7AC;
            --auth-border: #2A3F30;
            --auth-green-50: #15301E;
            --auth-green-100: #1A3B24;
            --auth-danger-soft: #321A1A;
            --auth-warning-soft: #2F2614;
            --auth-info-soft: #13263A;
            --auth-shadow-sm: 0 8px 24px rgba(0, 0, 0, .25);
            --auth-shadow-lg: 0 28px 80px rgba(0, 0, 0, .42);
        }

        .auth-header-actions {
            display: inline-flex;
            align-items: center;
            justify-self: end;
            gap: 9px;
        }

        .auth-theme-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            min-width: 42px;
            height: 42px;
            padding: 0;
            border: 1px solid rgba(255, 255, 255, .46);
            border-radius: 12px;
            background: rgba(255, 255, 255, .95);
            color: var(--auth-green-900);
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(0, 45, 16, .15);
            transition: transform .2s ease, background .2s ease;
        }

        .auth-theme-toggle:hover,
        .auth-theme-toggle:focus-visible {
            background: #FFFFFF;
            outline: none;
            transform: translateY(-2px);
        }

        .auth-theme-toggle svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .auth-theme-icon-moon {
            display: none;
        }

        html[data-finus-theme="dark"] .auth-theme-icon-sun {
            display: none;
        }

        html[data-finus-theme="dark"] .auth-theme-icon-moon {
            display: block;
        }

        html[data-finus-theme="dark"] body {
            background: #0A120D;
            color: var(--auth-text);
        }

        html[data-finus-theme="dark"] .auth-card {
            border-color: #304535;
            background: rgba(17, 28, 21, .985);
            box-shadow: var(--auth-shadow-lg);
        }

        html[data-finus-theme="dark"] .auth-card::before {
            background: rgba(34, 168, 83, .075);
        }

        html[data-finus-theme="dark"] .auth-context-card {
            border-color: var(--auth-border);
            background: linear-gradient(135deg, #15231A, #132D1B);
            color: #CDE0D2;
        }

        html[data-finus-theme="dark"] .auth-context-icon {
            background: #1B2C20;
            color: #7CEB93;
            box-shadow: 0 5px 14px rgba(0, 0, 0, .18);
        }

        html[data-finus-theme="dark"] .auth-context-title,
        html[data-finus-theme="dark"] .auth-label,
        html[data-finus-theme="dark"] .auth-email-preview strong,
        html[data-finus-theme="dark"] .auth-dialog-title,
        html[data-finus-theme="dark"] .auth-loading-title {
            color: #DDF0E2;
        }

        html[data-finus-theme="dark"] .auth-label-icon {
            background: #17331F;
            color: #79E790;
        }

        html[data-finus-theme="dark"] .auth-field {
            border-color: #36503C;
            background: #0E1811;
            color: #EDF7EF;
            box-shadow: none;
        }

        html[data-finus-theme="dark"] .auth-field::placeholder {
            color: #75877B;
        }

        html[data-finus-theme="dark"] .auth-field:hover {
            border-color: #4E6B56;
        }

        html[data-finus-theme="dark"] .auth-field:focus {
            border-color: var(--auth-green-600);
            background: #101D14;
            color: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(34, 168, 83, .13);
        }

        html[data-finus-theme="dark"] .auth-field[readonly] {
            background: #15221A;
            color: #B5C7BA;
        }

        html[data-finus-theme="dark"] .auth-field[aria-invalid="true"] {
            border-color: #B85D57;
            background: #211415;
        }

        html[data-finus-theme="dark"] .auth-field:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px #0E1811 inset;
            -webkit-text-fill-color: #EDF7EF;
        }

        html[data-finus-theme="dark"] .auth-password-toggle:hover {
            background: #17331F;
        }

        html[data-finus-theme="dark"] .auth-info-card,
        html[data-finus-theme="dark"] .auth-email-preview,
        html[data-finus-theme="dark"] .auth-dialog-details {
            border-color: var(--auth-border);
            background: #15251B;
            color: #BDCEC2;
        }

        html[data-finus-theme="dark"] .auth-strength-bar {
            background: #27392C;
        }

        html[data-finus-theme="dark"] .auth-code-icon,
        html[data-finus-theme="dark"] .auth-dialog-icon {
            border-color: #31543A;
            background: linear-gradient(145deg, #17281D, #1B3A24);
            color: #7BEA92;
            box-shadow: 0 10px 24px rgba(0, 0, 0, .22);
        }

        html[data-finus-theme="dark"] .auth-dialog {
            border-color: #304535;
            background: #111C15;
            color: var(--auth-text);
            box-shadow: 0 32px 85px rgba(0, 0, 0, .50);
        }

        html[data-finus-theme="dark"] .auth-loading-card {
            border-color: #304535;
            background: rgba(17, 28, 21, .99);
            color: var(--auth-text);
        }

        html[data-finus-theme="dark"] .auth-loading-wheel {
            border-color: #28402F;
            border-top-color: #22A853;
            border-right-color: #54D071;
        }

        html[data-finus-theme="dark"] .auth-loading-wheel::after {
            background: #14251A;
        }

        html[data-finus-theme="dark"] .auth-button-secondary {
            border-color: #31533A;
            background: #16341F;
            color: #CFF1D7;
        }

        html[data-finus-theme="dark"] .auth-button-muted {
            border-color: #33453A;
            background: #18221B;
            color: #BAC8BF;
        }

        html[data-finus-theme="dark"] .auth-alert-success {
            border-color: #285D37;
            background: #15301D;
            color: #A9E9B7;
        }

        html[data-finus-theme="dark"] .auth-alert-success .auth-alert-icon {
            background: #1C4528;
        }

        html[data-finus-theme="dark"] .auth-alert-danger {
            border-color: #75413E;
            background: #2D1818;
            color: #F1B6B0;
        }

        html[data-finus-theme="dark"] .auth-alert-danger .auth-alert-icon {
            background: #47201E;
        }

        html[data-finus-theme="dark"] .auth-alert-warning {
            border-color: #715A2D;
            background: #2E2514;
            color: #F2D08B;
        }

        html[data-finus-theme="dark"] .auth-alert-warning .auth-alert-icon {
            background: #453719;
        }

        html[data-finus-theme="dark"] .auth-alert-info {
            border-color: #315A7B;
            background: #132638;
            color: #AFCFEF;
        }

        html[data-finus-theme="dark"] .auth-alert-info .auth-alert-icon {
            background: #183650;
        }

        html[data-finus-theme="dark"] .auth-theme-toggle,
        html[data-finus-theme="dark"] .auth-home-link {
            border-color: #385040;
            background: #132119;
            color: #BDEBC8;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .28);
        }

        html[data-finus-theme="dark"] .auth-theme-toggle:hover,
        html[data-finus-theme="dark"] .auth-home-link:hover {
            background: #192B1F;
        }

        @media (max-width: 520px) {
            .auth-header-actions {
                gap: 7px;
            }

            .auth-theme-toggle {
                width: 42px;
                min-width: 42px;
            }
        }

        html.finus-theme-changing *,
        html.finus-theme-changing *::before,
        html.finus-theme-changing *::after {
            transition:
                background-color .20s ease,
                border-color .20s ease,
                color .20s ease,
                box-shadow .20s ease !important;
        }

        @media (prefers-reduced-motion: reduce) {
            html.finus-theme-changing *,
            html.finus-theme-changing *::before,
            html.finus-theme-changing *::after {
                transition: none !important;
            }
        }

    </style>
</head>

<body>
    <a href="#auth-content" class="auth-skip-link">Lewati ke formulir</a>

    <div id="authLoadingOverlay" class="auth-loading-overlay" role="status" aria-live="polite" aria-hidden="true">
        <div class="auth-loading-card">
            <div class="auth-loading-wheel" aria-hidden="true"></div>
            <p class="auth-loading-title" id="authLoadingTitle">Memproses permintaan...</p>
            <span class="auth-loading-message" id="authLoadingMessage">Mohon tunggu sebentar.</span>
        </div>
    </div>

    <header class="auth-header">
        <a href="{{ route('home') }}" class="auth-brand" data-loading-title="Membuka beranda..." aria-label="FINUS PUSDAI - Beranda">
            <img src="{{ asset('assets/images/FINUS_login.png') }}" alt="FINUS PUSDAI"
                 onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.src='{{ asset('assets/images/pusdai_dashboard.png') }}';}else{this.hidden=true;document.getElementById('authBrandFallback').hidden=false;}">
            <span id="authBrandFallback" class="auth-brand-fallback" hidden>FINUS PUSDAI</span>
        </a>

        <h1 class="auth-header-title">@yield('header-title', 'FINUS')</h1>

        <div class="auth-header-actions">
            <button
                type="button"
                class="auth-theme-toggle"
                data-finus-theme-toggle
                aria-label="Ubah mode tampilan FINUS"
                aria-pressed="false"
                title="Ubah mode tampilan"
                data-no-loading
            >
                <svg class="auth-theme-icon-sun" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.65 17.65l1.42 1.42M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.65 6.35l1.42-1.42"></path>
                </svg>
                <svg class="auth-theme-icon-moon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.5 14.5A8 8 0 0 1 9.5 3.5 8.5 8.5 0 1 0 20.5 14.5Z"></path>
                </svg>
                <span class="sr-only" data-finus-theme-label>Mode Terang</span>
            </button>

            <a href="{{ route('home') }}" class="auth-home-link" data-loading-title="Kembali ke beranda...">
                <span class="auth-home-icon" aria-hidden="true"></span>
                <span class="auth-home-link-text">Beranda</span>
            </a>
        </div>
    </header>

    <main class="auth-layout" id="auth-content">
        <section class="auth-hero" aria-label="Informasi FINUS" style="background-image:url('{{ asset('assets/images/bg-welcome.png') }}')">
            <img src="{{ asset('assets/images/pusdai_batik.png') }}" alt="" class="auth-batik" onerror="this.hidden=true">
            <div class="auth-hero-orb" aria-hidden="true"></div>

            <div class="auth-hero-content">
                <div class="auth-hero-copy">
                    <div class="auth-hero-badge">Sistem Informasi Keuangan Masjid</div>
                    <p class="auth-hero-title">@yield('hero-title', 'Masjid PUSDAI Jawa Barat')</p>
                    <p class="auth-hero-copytext">@yield('hero-copy', 'Sistem Informasi Keuangan Masjid yang aman, tertib, dan transparan.')</p>

                    <div class="auth-features" aria-label="Keunggulan FINUS">
                        <div class="auth-feature"><span class="auth-feature-icon is-safe" aria-hidden="true"></span>Aman</div>
                        <div class="auth-feature"><span class="auth-feature-icon is-transparent" aria-hidden="true"></span>Transparan</div>
                        <div class="auth-feature"><span class="auth-feature-icon is-integrated" aria-hidden="true"></span>Terintegrasi</div>
                    </div>

                    <p class="auth-hero-note">FINUS PUSDAI Jawa Barat</p>
                </div>
            </div>
        </section>

        <section class="auth-panel" aria-label="Formulir akses FINUS">
            <div class="auth-panel-inner">
                <div class="auth-panel-heading">
                    <img src="{{ asset('assets/images/FINUS_logo.png') }}" alt="FINUS PUSDAI" class="auth-panel-logo"
                         onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.src='{{ asset('assets/images/pusdai_dashboard.png') }}';}else{this.hidden=true;document.getElementById('authPanelLogoFallback').style.display='block';}">
                    <span id="authPanelLogoFallback" class="auth-panel-logo-fallback">FINUS PUSDAI</span>
                    <div class="auth-panel-eyebrow">@yield('panel-eyebrow', 'Akses FINUS')</div>
                    <h2 class="auth-panel-title">@yield('panel-title', 'Masuk')</h2>
                    <p class="auth-panel-copy">@yield('panel-copy')</p>
                    <div class="auth-secure">Koneksi aman dan terenkripsi</div>
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
                        <div class="auth-alert auth-alert-success" role="status">
                            <div class="auth-alert-icon" aria-hidden="true">✓</div>
                            <div><p class="auth-alert-title">Berhasil</p><p class="auth-alert-message">{{ session('success') }}</p></div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="auth-alert auth-alert-danger" role="alert">
                            <div class="auth-alert-icon" aria-hidden="true">!</div>
                            <div><p class="auth-alert-title">Terjadi Kesalahan</p><p class="auth-alert-message">{{ session('error') }}</p></div>
                        </div>
                    @endif

                    @if($statusData)
                        <div class="auth-alert auth-alert-success" role="status">
                            <div class="auth-alert-icon" aria-hidden="true">✓</div>
                            <div><p class="auth-alert-title">{{ $statusData['title'] }}</p><p class="auth-alert-message">{{ $statusData['message'] }}</p></div>
                        </div>
                    @elseif($currentStatus)
                        <div class="auth-alert auth-alert-info" role="status">
                            <div class="auth-alert-icon" aria-hidden="true">i</div>
                            <div><p class="auth-alert-message">{{ $currentStatus }}</p></div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </section>
    </main>

    <script>
    (() => {
        const overlay = document.getElementById('authLoadingOverlay');
        const loadingTitle = document.getElementById('authLoadingTitle');
        const loadingMessage = document.getElementById('authLoadingMessage');

        const showLoading = (text = 'Memproses permintaan...') => {
            if (!overlay) return;
            loadingTitle.textContent = text;
            loadingMessage.textContent = 'Jangan tutup atau memuat ulang halaman ini.';
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
                    const willShow = input.type === 'password';
                    input.type = willShow ? 'text' : 'password';
                    button.setAttribute('aria-label', willShow ? 'Sembunyikan password' : 'Tampilkan password');
                    button.setAttribute('aria-pressed', willShow ? 'true' : 'false');

                    if (image) {
                        image.src = willShow ? hideIcon : showIcon;
                        image.alt = '';
                    }

                    input.focus({ preventScroll: true });
                    const length = input.value.length;
                    input.setSelectionRange?.(length, length);
                });
            });

            document.querySelectorAll('[data-password-strength]').forEach(input => {
                const box = document.getElementById(input.dataset.passwordStrength);
                if (!box) return;

                const bars = [...box.querySelectorAll('[data-strength-bar]')];
                const label = box.querySelector('[data-strength-text]');
                const colors = ['#e3ebe6', '#d92d20', '#dc6803', '#22a853', '#075d2a'];
                const labels = ['Masukkan minimal 8 karakter', 'Lemah', 'Cukup', 'Kuat', 'Sangat kuat'];

                const update = () => {
                    const value = input.value;
                    let score = 0;
                    if (value.length >= 8) score++;
                    if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
                    if (/\d/.test(value)) score++;
                    if (/[^A-Za-z0-9]/.test(value)) score++;

                    bars.forEach((bar, index) => {
                        bar.style.background = index < score ? colors[score] : colors[0];
                    });

                    if (label) {
                        label.textContent = labels[score];
                        label.style.color = score ? colors[score] : '#607066';
                    }
                };

                input.addEventListener('input', update);
                update();
            });

            document.querySelectorAll('[data-password-match]').forEach(input => {
                const source = document.querySelector(input.dataset.passwordMatch);
                const message = document.getElementById(input.dataset.matchMessage || '');
                if (!source || !message) return;

                const update = () => {
                    if (!input.value) {
                        message.textContent = '';
                        message.className = 'auth-match-message';
                        input.removeAttribute('aria-invalid');
                        return;
                    }

                    const isMatch = input.value === source.value;
                    message.textContent = isMatch ? 'Password sudah sama.' : 'Konfirmasi password belum sama.';
                    message.className = `auth-match-message ${isMatch ? 'is-match' : 'is-mismatch'}`;
                    input.setAttribute('aria-invalid', isMatch ? 'false' : 'true');
                };

                input.addEventListener('input', update);
                source.addEventListener('input', update);
            });

            document.querySelectorAll('[data-verification-code]').forEach(input => {
                input.addEventListener('input', () => {
                    input.value = input.value.replace(/\D/g, '').slice(0, 6);
                });

                input.addEventListener('paste', event => {
                    const pasted = event.clipboardData?.getData('text') || '';
                    const digits = pasted.replace(/\D/g, '').slice(0, 6);
                    if (!digits) return;
                    event.preventDefault();
                    input.value = digits;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                });
            });

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', event => {
                    if (form.hasAttribute('data-no-loading')) return;

                    const mismatch = form.querySelector('[data-password-match][aria-invalid="true"]');
                    if (mismatch) {
                        event.preventDefault();
                        mismatch.focus();
                        return;
                    }

                    if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                        event.preventDefault();
                        form.reportValidity();
                        return;
                    }

                    if (form.dataset.submitting === 'true') {
                        event.preventDefault();
                        return;
                    }

                    form.dataset.submitting = 'true';
                    const buttons = form.querySelectorAll('button[type="submit"]');
                    let text = form.dataset.loadingTitle || 'Memproses permintaan...';

                    buttons.forEach(button => {
                        text = button.dataset.loadingTitle || text;
                        button.disabled = true;
                        button.setAttribute('aria-busy', 'true');
                        button.innerHTML = `<span class="auth-inline-spinner" aria-hidden="true"></span><span>${button.dataset.loadingText || 'Memproses...'}</span>`;
                    });

                    requestAnimationFrame(() => showLoading(text));
                });
            });

            document.addEventListener('click', event => {
                const dialogOverlay = event.target.closest('.auth-dialog-overlay');
                if (dialogOverlay && event.target === dialogOverlay && dialogOverlay.dataset.dismissible === 'true') {
                    dialogOverlay.remove();
                    return;
                }

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

            document.addEventListener('keydown', event => {
                if (event.key !== 'Escape') return;
                document.querySelectorAll('.auth-dialog-overlay[data-dismissible="true"]').forEach(dialog => dialog.remove());
            });

            const firstAlert = document.querySelector('.auth-alert[role="alert"]');
            firstAlert?.scrollIntoView({ behavior: 'smooth', block: 'center' });
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

        const updateControls = theme => {
            const dark = theme === 'dark';

            document
                .querySelectorAll('[data-finus-theme-toggle]')
                .forEach(button => {
                    button.setAttribute(
                        'aria-pressed',
                        dark ? 'true' : 'false'
                    );

                    button.setAttribute(
                        'title',
                        dark
                            ? 'Ubah ke mode terang'
                            : 'Ubah ke mode gelap'
                    );

                    const label = button.querySelector(
                        '[data-finus-theme-label]'
                    );

                    if (label) {
                        label.textContent = dark
                            ? 'Mode Gelap'
                            : 'Mode Terang';
                    }
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
                        : '#075D2A'
                );
            }

            try {
                localStorage.setItem(
                    storageKey,
                    normalized
                );
            } catch (_) {}

            updateControls(normalized);

            window.setTimeout(() => {
                root.classList.remove('finus-theme-changing');
            }, 230);
        };

        updateControls(readTheme());

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
    @stack('scripts')
</body>
</html>
{{--
    FINUS Unified UI Partial
    Gabungan gaya laporan/COA dan seluruh modul admin/pegawai/jamaah.
    Cukup gunakan: @include('layouts.partials.finus-ui')
--}}

@once
    @push('styles')
        <style>
            :root {
                --fr-dark: #0E5423;
                --fr-green: #179B40;
                --fr-light: #22BA51;
                --fr-lime: #7EFF87;
                --fr-soft: #EAF8EE;
                --fr-soft-2: #F5FBF7;
                --fr-text: #172033;
                --fr-muted: #64748B;
                --fr-border: #E1EAE4;
                --fr-blue: #2563EB;
                --fr-blue-soft: #EEF4FF;
                --fr-amber: #D97706;
                --fr-amber-soft: #FFF7E6;
                --fr-red: #DC2626;
                --fr-red-soft: #FFF1F2;
                --fr-purple: #7C3AED;
                --fr-purple-soft: #F5F0FF;
                --fr-shadow-sm: 0 8px 22px rgba(15, 23, 42, .055);
                --fr-shadow-md: 0 16px 38px rgba(15, 23, 42, .085);
                --fr-shadow-green: 0 18px 38px rgba(14, 84, 35, .17);
            }

            .fr-page,
            .fr-page * {
                box-sizing: border-box;
            }

            .fr-page {
                position: relative;
                padding: 2px 0 34px;
                color: var(--fr-text);
            }

            .fr-page::before {
                content: "";
                position: absolute;
                top: -58px;
                right: 2%;
                width: 270px;
                height: 270px;
                border-radius: 50%;
                background: rgba(34, 186, 81, .055);
                pointer-events: none;
            }

            .fr-hero {
                position: relative;
                z-index: 1;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 22px;
                margin-bottom: 17px;
                padding: 27px 29px;
                border-radius: 22px;
                background:
                    radial-gradient(circle at 91% 7%, rgba(255, 255, 255, .16), transparent 30%),
                    linear-gradient(135deg, var(--fr-dark) 0%, var(--fr-green) 48%, var(--fr-light) 100%);
                box-shadow: var(--fr-shadow-green), inset 0 1px 0 rgba(255, 255, 255, .13);
            }

            .fr-hero::before {
                content: "";
                position: absolute;
                top: -100px;
                right: -55px;
                width: 245px;
                height: 245px;
                border-radius: 50%;
                background: rgba(255, 255, 255, .085);
                pointer-events: none;
            }

            .fr-hero::after {
                content: "";
                position: absolute;
                right: 180px;
                bottom: -155px;
                width: 225px;
                height: 225px;
                border-radius: 50%;
                background: rgba(126, 255, 135, .12);
                pointer-events: none;
            }

            .fr-hero-main,
            .fr-hero-actions {
                position: relative;
                z-index: 2;
            }

            .fr-hero-main {
                display: flex;
                align-items: center;
                gap: 16px;
                min-width: 0;
            }

            .fr-hero-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 58px;
                min-width: 58px;
                height: 58px;
                border: 1px solid rgba(255, 255, 255, .21);
                border-radius: 17px;
                background: rgba(255, 255, 255, .14);
                color: #fff;
                font-size: 22px;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, .15);
                backdrop-filter: blur(8px);
            }

            .fr-hero-title {
                margin: 0;
                color: #fff;
                font-size: clamp(20px, 2.2vw, 24px);
                font-weight: 800;
                line-height: 1.28;
                letter-spacing: -.02em;
            }

            .fr-hero-subtitle {
                display: flex;
                align-items: center;
                gap: 8px;
                margin: 6px 0 0;
                color: rgba(255, 255, 255, .82);
                font-size: 13px;
                line-height: 1.55;
            }

            .fr-hero-subtitle::before {
                content: "";
                width: 7px;
                min-width: 7px;
                height: 7px;
                border-radius: 50%;
                background: #A5FFAE;
                box-shadow: 0 0 0 4px rgba(165, 255, 174, .13);
            }

            .fr-hero-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
                flex-wrap: wrap;
            }

            .fr-hero-badge,
            .fr-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                min-height: 43px;
                padding: 0 15px;
                border-radius: 12px;
                font-size: 12.5px;
                font-weight: 800;
                text-decoration: none !important;
                white-space: nowrap;
            }

            .fr-hero-badge {
                border: 1px solid rgba(255, 255, 255, .21);
                background: rgba(4, 75, 28, .22);
                color: #fff;
                backdrop-filter: blur(8px);
            }

            .fr-btn {
                border: 1px solid var(--fr-border);
                background: #fff;
                color: var(--fr-text) !important;
                cursor: pointer;
                box-shadow: 0 7px 18px rgba(15, 23, 42, .055);
                transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
            }

            .fr-btn:hover,
            .fr-btn:focus-visible {
                color: var(--fr-text) !important;
                border-color: rgba(23, 155, 64, .30);
                background: var(--fr-soft-2);
                outline: none;
                transform: translateY(-2px);
                box-shadow: 0 11px 24px rgba(15, 23, 42, .08);
            }

            .fr-btn-primary {
                border-color: var(--fr-green);
                background: linear-gradient(135deg, var(--fr-dark), var(--fr-green) 58%, var(--fr-light));
                color: #fff !important;
                box-shadow: 0 10px 23px rgba(14, 84, 35, .18);
            }

            .fr-btn-primary:hover,
            .fr-btn-primary:focus-visible {
                border-color: var(--fr-dark);
                background: linear-gradient(135deg, #0A471D, #138A39 58%, #1EA946);
                color: #fff !important;
            }

            .fr-btn-danger {
                border-color: #F0C8CE;
                background: var(--fr-red-soft);
                color: #B91C1C !important;
            }

            .fr-summary {
                position: relative;
                z-index: 1;
                display: grid;
                grid-template-columns: repeat(var(--summary-columns, 3), minmax(0, 1fr));
                gap: 14px;
                margin-bottom: 17px;
            }

            .fr-stat {
                position: relative;
                overflow: hidden;
                display: flex;
                align-items: center;
                gap: 13px;
                min-height: 96px;
                padding: 17px 18px;
                border: 1px solid var(--fr-border);
                border-radius: 17px;
                background: #fff;
                box-shadow: var(--fr-shadow-sm);
                transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
            }

            .fr-stat::after {
                content: "";
                position: absolute;
                right: -34px;
                bottom: -48px;
                width: 105px;
                height: 105px;
                border-radius: 50%;
                background: var(--stat-soft, var(--fr-soft));
                opacity: .56;
                pointer-events: none;
            }

            .fr-stat:hover {
                border-color: rgba(23, 155, 64, .24);
                transform: translateY(-2px);
                box-shadow: 0 14px 30px rgba(15, 23, 42, .075);
            }

            .fr-stat-icon,
            .fr-stat-copy {
                position: relative;
                z-index: 1;
            }

            .fr-stat-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 45px;
                min-width: 45px;
                height: 45px;
                border-radius: 13px;
                background: var(--stat-soft, var(--fr-soft));
                color: var(--stat-color, var(--fr-green));
                font-size: 17px;
            }

            .fr-stat-label {
                display: block;
                color: var(--fr-muted);
                font-size: 11.5px;
                font-weight: 700;
                line-height: 1.4;
            }

            .fr-stat-value {
                display: block;
                margin-top: 4px;
                color: var(--fr-text);
                font-size: clamp(17px, 2vw, 21px);
                font-weight: 800;
                line-height: 1.25;
                word-break: break-word;
            }

            .fr-stat-note {
                display: block;
                margin-top: 4px;
                color: #8996A8;
                font-size: 10.5px;
                line-height: 1.4;
            }

            .fr-stat-green { --stat-color: var(--fr-green); --stat-soft: var(--fr-soft); }
            .fr-stat-blue { --stat-color: var(--fr-blue); --stat-soft: var(--fr-blue-soft); }
            .fr-stat-amber { --stat-color: var(--fr-amber); --stat-soft: var(--fr-amber-soft); }
            .fr-stat-red { --stat-color: var(--fr-red); --stat-soft: var(--fr-red-soft); }
            .fr-stat-purple { --stat-color: var(--fr-purple); --stat-soft: var(--fr-purple-soft); }

            .fr-card {
                position: relative;
                z-index: 1;
                overflow: hidden;
                border: 1px solid var(--fr-border);
                border-radius: 20px;
                background: #fff;
                box-shadow: var(--fr-shadow-md);
            }

            .fr-card + .fr-card {
                margin-top: 17px;
            }

            .fr-card-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 19px 21px;
                border-bottom: 1px solid var(--fr-border);
                background: linear-gradient(180deg, #fff, #FBFDFC);
            }

            .fr-card-title-row {
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 0;
            }

            .fr-card-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                min-width: 42px;
                height: 42px;
                border-radius: 12px;
                background: var(--fr-soft);
                color: var(--fr-green);
                font-size: 16px;
            }

            .fr-card-title {
                margin: 0;
                color: var(--fr-text);
                font-size: 17px;
                font-weight: 800;
                line-height: 1.35;
            }

            .fr-card-subtitle {
                margin: 4px 0 0;
                color: var(--fr-muted);
                font-size: 11.5px;
                line-height: 1.55;
            }

            .fr-card-body {
                padding: 22px;
            }

            .fr-grid-2 {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 17px;
            }

            .fr-grid-main-aside {
                display: grid;
                grid-template-columns: minmax(0, 1.45fr) minmax(280px, .55fr);
                gap: 17px;
                align-items: start;
            }

            .fr-breakdown {
                display: grid;
                gap: 15px;
            }

            .fr-breakdown-item {
                padding: 15px 16px;
                border: 1px solid var(--fr-border);
                border-radius: 15px;
                background: #FCFEFC;
            }

            .fr-breakdown-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                margin-bottom: 10px;
            }

            .fr-breakdown-label {
                display: flex;
                align-items: center;
                gap: 9px;
                color: var(--fr-text);
                font-size: 12.5px;
                font-weight: 800;
            }

            .fr-breakdown-dot {
                width: 9px;
                min-width: 9px;
                height: 9px;
                border-radius: 50%;
                background: var(--bar-color, var(--fr-green));
                box-shadow: 0 0 0 4px color-mix(in srgb, var(--bar-color, var(--fr-green)) 14%, transparent);
            }

            .fr-breakdown-value {
                color: var(--fr-text);
                font-size: 12.5px;
                font-weight: 800;
                text-align: right;
            }

            .fr-progress {
                overflow: hidden;
                width: 100%;
                height: 8px;
                border-radius: 999px;
                background: #E9F0EB;
            }

            .fr-progress-bar {
                width: var(--bar-width, 0%);
                height: 100%;
                border-radius: inherit;
                background: linear-gradient(90deg, var(--bar-color, var(--fr-green)), color-mix(in srgb, var(--bar-color, var(--fr-green)) 72%, #fff));
                transition: width .7s cubic-bezier(.2, .7, .2, 1);
            }

            .fr-status-panel {
                position: relative;
                overflow: hidden;
                padding: 23px;
                border: 1px solid rgba(23, 155, 64, .17);
                border-radius: 18px;
                background:
                    radial-gradient(circle at 100% 0, rgba(34, 186, 81, .12), transparent 12rem),
                    var(--fr-soft-2);
            }

            .fr-status-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 52px;
                height: 52px;
                margin-bottom: 16px;
                border-radius: 15px;
                background: #fff;
                color: var(--status-color, var(--fr-green));
                font-size: 20px;
                box-shadow: var(--fr-shadow-sm);
            }

            .fr-status-label {
                display: block;
                color: var(--fr-muted);
                font-size: 10px;
                font-weight: 800;
                letter-spacing: .1em;
                text-transform: uppercase;
            }

            .fr-status-title {
                margin: 6px 0 0;
                color: var(--fr-text);
                font-size: 21px;
                font-weight: 800;
            }

            .fr-status-copy {
                margin: 9px 0 0;
                color: var(--fr-muted);
                font-size: 12px;
                line-height: 1.7;
            }

            .fr-search-wrap {
                position: relative;
                width: min(100%, 360px);
            }

            .fr-search-icon {
                position: absolute;
                top: 50%;
                left: 14px;
                z-index: 1;
                color: #829087;
                font-size: 13px;
                transform: translateY(-50%);
                pointer-events: none;
            }

            .fr-search {
                width: 100%;
                min-height: 42px;
                padding: 9px 38px 9px 39px;
                border: 1px solid var(--fr-border);
                border-radius: 12px;
                background: #fff;
                color: var(--fr-text);
                font-size: 12px;
                outline: none;
                transition: border-color .18s ease, box-shadow .18s ease;
            }

            .fr-search:focus {
                border-color: var(--fr-green);
                box-shadow: 0 0 0 4px rgba(23, 155, 64, .11);
            }

            .fr-search-clear {
                position: absolute;
                top: 50%;
                right: 8px;
                display: none;
                align-items: center;
                justify-content: center;
                width: 27px;
                height: 27px;
                padding: 0;
                border: 0;
                border-radius: 8px;
                background: var(--fr-soft);
                color: var(--fr-dark);
                cursor: pointer;
                transform: translateY(-50%);
            }

            .fr-search-clear.is-visible {
                display: inline-flex;
            }

            .fr-table-wrap {
                overflow: auto;
                width: 100%;
                -webkit-overflow-scrolling: touch;
            }

            .fr-table {
                width: 100%;
                min-width: 800px;
                margin: 0;
                border-collapse: separate;
                border-spacing: 0;
            }

            .fr-table thead th {
                position: sticky;
                top: 0;
                z-index: 2;
                padding: 13px 12px;
                border-top: 0;
                border-bottom: 1px solid var(--fr-border);
                background: #F8FBF9;
                color: #526056;
                font-size: 10.5px;
                font-weight: 800;
                letter-spacing: .055em;
                text-align: center;
                text-transform: uppercase;
                vertical-align: middle;
                white-space: nowrap;
            }

            .fr-table tbody td {
                padding: 13px 12px;
                border-top: 0;
                border-bottom: 1px solid #EDF2EE;
                color: #33413A;
                font-size: 12px;
                line-height: 1.5;
                text-align: center;
                vertical-align: middle;
            }

            .fr-table tbody tr:last-child td {
                border-bottom: 0;
            }

            .fr-table tbody tr:hover td {
                background: #F8FCF9;
            }

            .fr-money {
                display: inline-flex;
                align-items: center;
                justify-content: flex-end;
                min-width: 112px;
                font-variant-numeric: tabular-nums;
                font-weight: 800;
                white-space: nowrap;
            }

            .fr-money.is-debit { color: #176C32; }
            .fr-money.is-credit { color: #9A5A00; }
            .fr-money.is-muted { color: #A0AAA4; font-weight: 700; }

            .fr-chip {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                min-height: 29px;
                padding: 5px 10px;
                border-radius: 999px;
                background: var(--fr-soft);
                color: var(--fr-dark);
                font-size: 10.5px;
                font-weight: 800;
                white-space: nowrap;
            }

            .fr-empty {
                padding: 52px 20px !important;
                color: var(--fr-muted) !important;
                text-align: center !important;
            }

            .fr-empty-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 54px;
                height: 54px;
                margin-bottom: 12px;
                border-radius: 16px;
                background: var(--fr-soft);
                color: var(--fr-green);
                font-size: 20px;
            }

            .fr-empty strong {
                display: block;
                margin-bottom: 4px;
                color: var(--fr-text);
                font-size: 13px;
            }

            .fr-alert {
                display: flex;
                align-items: flex-start;
                gap: 11px;
                margin-bottom: 16px;
                padding: 14px 16px;
                border-radius: 14px;
                font-size: 12px;
                font-weight: 700;
                line-height: 1.55;
            }

            .fr-alert i {
                margin-top: 2px;
            }

            .fr-alert-success {
                border: 1px solid #BDE5C7;
                background: #EFFAF2;
                color: #166534;
            }

            .fr-alert-danger {
                border: 1px solid #F1C6CB;
                background: #FFF3F4;
                color: #B91C1C;
            }

            .fr-alert ul {
                margin: 7px 0 0;
                padding-left: 18px;
            }

            .fr-form-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 19px;
            }

            .fr-field-full {
                grid-column: 1 / -1;
            }

            .fr-label {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin-bottom: 8px;
                color: var(--fr-text);
                font-size: 12.5px;
                font-weight: 800;
            }

            .fr-required {
                color: var(--fr-red);
            }

            .fr-input-wrap {
                position: relative;
            }

            .fr-input-icon {
                position: absolute;
                top: 50%;
                left: 14px;
                z-index: 2;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 24px;
                color: #718078;
                font-size: 13px;
                transform: translateY(-50%);
                pointer-events: none;
            }

            .fr-input,
            .fr-select {
                width: 100%;
                min-height: 49px;
                padding: 11px 14px 11px 45px;
                border: 1px solid #CFDAD3;
                border-radius: 13px;
                background: #fff;
                color: var(--fr-text);
                font-size: 13px;
                outline: none;
                transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
            }

            .fr-select {
                cursor: pointer;
            }

            .fr-input:hover,
            .fr-select:hover {
                border-color: #B8C9BE;
            }

            .fr-input:focus,
            .fr-select:focus {
                border-color: var(--fr-green);
                background: #FCFFFD;
                box-shadow: 0 0 0 4px rgba(23, 155, 64, .11);
            }

            .fr-input.is-invalid,
            .fr-select.is-invalid {
                border-color: #E997A1;
                background: #FFFDFD;
            }

            .fr-help,
            .fr-error {
                margin-top: 7px;
                font-size: 11px;
                line-height: 1.55;
            }

            .fr-help {
                color: var(--fr-muted);
            }

            .fr-error {
                color: #C42030;
                font-weight: 700;
            }

            .fr-form-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-top: 23px;
                padding-top: 19px;
                border-top: 1px solid var(--fr-border);
            }

            .fr-tip-list {
                display: grid;
                gap: 12px;
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .fr-tip-item {
                display: flex;
                align-items: flex-start;
                gap: 11px;
                padding: 13px;
                border: 1px solid var(--fr-border);
                border-radius: 14px;
                background: #FCFEFC;
            }

            .fr-tip-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 31px;
                min-width: 31px;
                height: 31px;
                border-radius: 9px;
                background: var(--fr-soft);
                color: var(--fr-green);
                font-size: 12px;
            }

            .fr-tip-title {
                display: block;
                color: var(--fr-text);
                font-size: 11.5px;
                font-weight: 800;
            }

            .fr-tip-copy {
                display: block;
                margin-top: 3px;
                color: var(--fr-muted);
                font-size: 10.5px;
                line-height: 1.55;
            }

            .fr-reveal {
                opacity: 0;
                transform: translateY(18px);
                transition: opacity .5s ease, transform .5s ease;
            }

            .fr-reveal.is-visible {
                opacity: 1;
                transform: none;
            }

            @media (max-width: 1100px) {
                .fr-summary {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .fr-grid-main-aside {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .fr-page {
                    padding-bottom: 24px;
                }

                .fr-hero {
                    align-items: flex-start;
                    flex-direction: column;
                    padding: 22px;
                    border-radius: 19px;
                }

                .fr-hero-actions {
                    width: 100%;
                    justify-content: flex-start;
                }

                .fr-hero-badge,
                .fr-hero-actions .fr-btn {
                    flex: 1 1 180px;
                }

                .fr-summary,
                .fr-grid-2,
                .fr-form-grid {
                    grid-template-columns: 1fr;
                }

                .fr-field-full {
                    grid-column: auto;
                }

                .fr-card-head {
                    align-items: flex-start;
                    flex-direction: column;
                    padding: 17px;
                }

                .fr-card-body {
                    padding: 18px;
                }

                .fr-search-wrap {
                    width: 100%;
                }

                .fr-form-actions {
                    align-items: stretch;
                    flex-direction: column-reverse;
                }

                .fr-form-actions .fr-btn {
                    width: 100%;
                }
            }

            @media (max-width: 480px) {
                .fr-hero-main {
                    align-items: flex-start;
                }

                .fr-hero-icon {
                    width: 48px;
                    min-width: 48px;
                    height: 48px;
                    border-radius: 14px;
                    font-size: 18px;
                }

                .fr-hero-subtitle {
                    align-items: flex-start;
                }

                .fr-summary {
                    gap: 11px;
                }

                .fr-stat {
                    min-height: 88px;
                    padding: 15px;
                    border-radius: 15px;
                }

                .fr-breakdown-top {
                    align-items: flex-start;
                    flex-direction: column;
                    gap: 5px;
                }

                .fr-breakdown-value {
                    text-align: left;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .fr-reveal {
                    opacity: 1 !important;
                    transform: none !important;
                    transition: none !important;
                }

                .fr-progress-bar,
                .fr-btn,
                .fr-stat {
                    transition: none !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (() => {
                const elements = document.querySelectorAll('.fr-reveal');
                if (!elements.length) return;

                const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                if (reduceMotion || !('IntersectionObserver' in window)) {
                    elements.forEach(element => element.classList.add('is-visible'));
                    return;
                }

                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (!entry.isIntersecting) return;
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    });
                }, {
                    threshold: 0.12,
                    rootMargin: '0px 0px -35px 0px',
                });

                elements.forEach((element, index) => {
                    element.style.transitionDelay = `${Math.min(index * 45, 180)}ms`;
                    observer.observe(element);
                });
            })();
        </script>
    @endpush
@endonce

{{-- FINUS module styles and interactions --}}
@once
    @push('styles')
        <style>
            :root {
                --fmu-dark: #0E5423;
                --fmu-green: #179B40;
                --fmu-light: #22BA51;
                --fmu-lime: #7EFF87;
                --fmu-soft: #EAF8EE;
                --fmu-soft-2: #F5FBF7;
                --fmu-text: #172033;
                --fmu-muted: #64748B;
                --fmu-border: #E1EAE4;
                --fmu-danger: #DC2626;
                --fmu-warning: #D97706;
                --fmu-blue: #2563EB;
                --fmu-purple: #7C3AED;
                --fmu-shadow-sm: 0 10px 26px rgba(15, 23, 42, .06);
                --fmu-shadow-md: 0 20px 50px rgba(14, 84, 35, .11);
            }

            .fmu-page {
                position: relative;
                isolation: isolate;
                padding: 4px 0 34px;
            }
            .fmu-page::before {
                content: "";
                position: absolute;
                z-index: -1;
                top: -70px;
                right: -30px;
                width: 270px;
                height: 270px;
                border-radius: 999px;
                background: rgba(34, 186, 81, .055);
                pointer-events: none;
            }
            .fmu-hero,
            .employee-hero,
            .finus-data-hero,
            .finus-report-hero,
            .presensi-hero,
            .presensi-form-header,
            .header-gradient {
                background:
                    radial-gradient(circle at 92% 8%, rgba(255,255,255,.18), transparent 30%),
                    linear-gradient(to right, #0FB442 0%, #1AAF48 39%, #118635 75%, #004716 100%) !important;
            }
            .fmu-hero {
                position: relative;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 22px;
                margin-bottom: 18px;
                padding: 27px 29px;
                border: 1px solid rgba(255,255,255,.16);
                border-radius: 22px;
                color: #fff;
                box-shadow: 0 18px 42px rgba(14, 84, 35, .18);
            }
            .fmu-hero::after {
                content: "";
                position: absolute;
                right: 150px;
                bottom: -145px;
                width: 220px;
                height: 220px;
                border-radius: 50%;
                background: rgba(126,255,135,.13);
                pointer-events: none;
            }
            .fmu-hero-main,
            .fmu-hero-actions {
                position: relative;
                z-index: 2;
            }
            .fmu-hero-main {
                display: flex;
                align-items: center;
                gap: 16px;
                min-width: 0;
            }
            .fmu-hero-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 58px;
                min-width: 58px;
                height: 58px;
                border: 1px solid rgba(255,255,255,.22);
                border-radius: 17px;
                background: rgba(255,255,255,.14);
                color: #fff;
                font-size: 22px;
                box-shadow: inset 0 1px 0 rgba(255,255,255,.14);
                backdrop-filter: blur(8px);
            }
            .fmu-hero h1,
            .fmu-hero h2,
            .fmu-hero h3 {
                margin: 0;
                color: #fff;
                font-size: clamp(20px, 2.5vw, 25px);
                font-weight: 900;
                line-height: 1.25;
                letter-spacing: -.02em;
            }
            .fmu-hero p {
                margin: 6px 0 0;
                color: rgba(255,255,255,.83);
                font-size: 13px;
                line-height: 1.6;
            }
            .fmu-hero-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                min-height: 42px;
                padding: 0 14px;
                border: 1px solid rgba(255,255,255,.22);
                border-radius: 12px;
                background: rgba(0,72,25,.19);
                color: #fff;
                font-size: 12px;
                font-weight: 800;
                white-space: nowrap;
                backdrop-filter: blur(8px);
            }

            .fmu-grid {
                display: grid;
                gap: 16px;
            }
            .fmu-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .fmu-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .fmu-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

            .fmu-stat,
            .fmu-card {
                border: 1px solid var(--fmu-border);
                border-radius: 18px;
                background: #fff;
                box-shadow: var(--fmu-shadow-sm);
            }
            .fmu-stat {
                position: relative;
                overflow: hidden;
                display: flex;
                align-items: center;
                gap: 13px;
                min-height: 94px;
                padding: 17px 18px;
                transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
            }
            .fmu-stat::after {
                content: "";
                position: absolute;
                right: -44px;
                bottom: -62px;
                width: 125px;
                height: 125px;
                border-radius: 999px;
                background: var(--fmu-stat-soft, var(--fmu-soft));
                opacity: .65;
            }
            .fmu-stat:hover {
                border-color: rgba(23,155,64,.24);
                transform: translateY(-3px);
                box-shadow: 0 16px 34px rgba(15,23,42,.09);
            }
            .fmu-stat-icon,
            .fmu-card-icon {
                position: relative;
                z-index: 2;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 46px;
                min-width: 46px;
                height: 46px;
                border-radius: 13px;
                background: var(--fmu-stat-soft, var(--fmu-soft));
                color: var(--fmu-stat-color, var(--fmu-green));
                font-size: 17px;
            }
            .fmu-stat-copy { position: relative; z-index: 2; min-width: 0; }
            .fmu-stat-copy small {
                display: block;
                color: var(--fmu-muted);
                font-size: 11.5px;
                font-weight: 700;
            }
            .fmu-stat-copy strong {
                display: block;
                margin-top: 4px;
                color: var(--fmu-text);
                font-size: clamp(19px, 2.4vw, 23px);
                line-height: 1.2;
            }

            .fmu-card { overflow: hidden; }
            .fmu-card-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 19px 21px;
                border-bottom: 1px solid var(--fmu-border);
                background: linear-gradient(180deg, #FFFFFF, #FBFDFC);
            }
            .fmu-card-head-main {
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 0;
            }
            .fmu-card-head h2,
            .fmu-card-head h3,
            .fmu-card-head h4,
            .fmu-card-head h5 {
                margin: 0;
                color: var(--fmu-text);
                font-size: 17px;
                font-weight: 900;
            }
            .fmu-card-head p {
                margin: 4px 0 0;
                color: var(--fmu-muted);
                font-size: 12px;
                line-height: 1.5;
            }
            .fmu-card-body { padding: 24px; }

            .fmu-form-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px;
            }
            .fmu-field { min-width: 0; }
            .fmu-field-full { grid-column: 1 / -1; }
            .fmu-label {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin-bottom: 8px;
                color: #334155;
                font-size: 12px;
                font-weight: 850;
                letter-spacing: .02em;
            }
            .fmu-required { color: var(--fmu-danger); }
            .fmu-control,
            .fmu-select,
            .fmu-textarea {
                width: 100%;
                min-height: 48px;
                padding: 11px 14px;
                border: 1.5px solid #D9E4DD;
                border-radius: 12px;
                background: #FBFDFC;
                color: var(--fmu-text);
                font-size: 14px;
                outline: none;
                box-shadow: none;
                transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
            }
            .fmu-textarea { min-height: 112px; resize: vertical; }
            .fmu-control:focus,
            .fmu-select:focus,
            .fmu-textarea:focus {
                border-color: var(--fmu-green);
                background: #fff;
                box-shadow: 0 0 0 4px rgba(23,155,64,.11);
            }
            .fmu-control[readonly] {
                background: var(--fmu-soft-2);
                color: #476252;
            }
            .fmu-help,
            .fmu-error {
                display: block;
                margin-top: 7px;
                font-size: 11.5px;
                line-height: 1.5;
            }
            .fmu-help { color: var(--fmu-muted); }
            .fmu-error { color: var(--fmu-danger); font-weight: 700; }
            .is-invalid.fmu-control,
            .is-invalid.fmu-select,
            .is-invalid.fmu-textarea { border-color: var(--fmu-danger); }

            .fmu-input-icon-wrap { position: relative; }
            .fmu-input-icon-wrap > i {
                position: absolute;
                top: 50%;
                left: 15px;
                z-index: 2;
                color: #789083;
                transform: translateY(-50%);
            }
            .fmu-input-icon-wrap .fmu-control,
            .fmu-input-icon-wrap .fmu-select { padding-left: 43px; }

            .fmu-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 18px 24px;
                border-top: 1px solid var(--fmu-border);
                background: #FBFDFC;
            }
            .fmu-actions-right {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
            }
            .fmu-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                min-height: 43px;
                padding: 0 16px;
                border: 1px solid var(--fmu-border);
                border-radius: 11px;
                background: #fff;
                color: #405346 !important;
                font-size: 12.5px;
                font-weight: 850;
                text-decoration: none !important;
                cursor: pointer;
                box-shadow: 0 7px 18px rgba(15,23,42,.05);
                transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
            }
            .fmu-btn:hover,
            .fmu-btn:focus-visible {
                color: var(--fmu-dark) !important;
                background: var(--fmu-soft-2);
                outline: none;
                transform: translateY(-2px);
                box-shadow: 0 11px 22px rgba(15,23,42,.08);
            }
            .fmu-btn-primary {
                border-color: rgba(255,255,255,.20);
                background: linear-gradient(105deg, var(--fmu-dark), var(--fmu-green) 58%, var(--fmu-light));
                color: #fff !important;
                box-shadow: 0 10px 23px rgba(14,84,35,.20);
            }
            .fmu-btn-primary:hover,
            .fmu-btn-primary:focus-visible {
                background: linear-gradient(105deg, #0A481E, #148A38 58%, #1DAA49);
                color: #fff !important;
            }
            .fmu-btn-danger {
                border-color: #F6D1D4;
                background: #FFF5F5;
                color: var(--fmu-danger) !important;
            }

            .fmu-table-wrap { overflow-x: auto; }
            .fmu-table {
                width: 100%;
                min-width: 720px;
                border-collapse: separate;
                border-spacing: 0;
            }
            .fmu-table th,
            .fmu-table td {
                padding: 14px 17px;
                border-bottom: 1px solid #EDF2EE;
                vertical-align: middle;
                font-size: 13px;
            }
            .fmu-table th {
                background: #F7FAF8;
                color: #526359;
                font-size: 10.5px;
                font-weight: 900;
                letter-spacing: .07em;
                text-transform: uppercase;
                white-space: nowrap;
            }
            .fmu-table tbody tr { transition: background .18s ease; }
            .fmu-table tbody tr:hover { background: #F8FCF9; }
            .fmu-empty {
                padding: 42px 20px !important;
                color: var(--fmu-muted);
                text-align: center;
            }
            .fmu-empty i { display: block; margin-bottom: 10px; font-size: 30px; color: #9BB2A1; }

            .fmu-badge {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 6px 10px;
                border-radius: 999px;
                background: var(--badge-soft, var(--fmu-soft));
                color: var(--badge-color, var(--fmu-green));
                font-size: 10.5px;
                font-weight: 850;
                line-height: 1;
                white-space: nowrap;
            }
            .fmu-badge::before {
                content: "";
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: currentColor;
            }

            .fmu-side-note {
                padding: 20px;
                border: 1px solid #DCEAE1;
                border-radius: 16px;
                background: linear-gradient(180deg, #F8FCF9, #F1F9F3);
                color: #486153;
            }
            .fmu-side-note h3 { margin: 0; color: var(--fmu-dark); font-size: 15px; font-weight: 900; }
            .fmu-side-note p { margin: 8px 0 0; font-size: 12px; line-height: 1.65; }
            .fmu-side-note ul { display: grid; gap: 8px; margin: 14px 0 0; padding: 0; list-style: none; }
            .fmu-side-note li { display: flex; gap: 8px; font-size: 11.5px; line-height: 1.5; }
            .fmu-side-note li::before { content: "✓"; color: var(--fmu-green); font-weight: 900; }

            .fmu-upload {
                position: relative;
                display: grid;
                place-items: center;
                min-height: 150px;
                padding: 22px;
                border: 2px dashed #BED6C5;
                border-radius: 16px;
                background: #F7FCF8;
                color: var(--fmu-muted);
                text-align: center;
                cursor: pointer;
                transition: border-color .2s ease, background .2s ease, transform .2s ease;
            }
            .fmu-upload:hover,
            .fmu-upload.is-dragging {
                border-color: var(--fmu-green);
                background: var(--fmu-soft);
                transform: translateY(-2px);
            }
            .fmu-upload i { margin-bottom: 10px; color: var(--fmu-green); font-size: 31px; }
            .fmu-upload strong { display: block; color: #334B3B; font-size: 13px; }
            .fmu-upload span { display: block; margin-top: 5px; font-size: 11px; }
            .fmu-file-name {
                display: none;
                margin-top: 10px;
                padding: 6px 10px;
                border-radius: 999px;
                background: #fff;
                color: var(--fmu-dark);
                font-size: 10.5px;
                font-weight: 800;
                box-shadow: 0 5px 14px rgba(15,23,42,.06);
            }
            .fmu-file-name.is-visible { display: inline-flex; }

            .fmu-choice-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 11px;
            }
            .fmu-choice {
                position: relative;
                display: grid;
                place-items: center;
                min-height: 105px;
                padding: 15px;
                border: 1.5px solid var(--fmu-border);
                border-radius: 15px;
                background: #fff;
                color: #53645A;
                text-align: center;
                cursor: pointer;
                transition: .2s ease;
            }
            .fmu-choice i { margin-bottom: 8px; font-size: 22px; }
            .fmu-choice strong { font-size: 12.5px; }
            .fmu-choice:hover,
            .fmu-choice.is-active {
                border-color: var(--fmu-green);
                background: var(--fmu-soft);
                color: var(--fmu-dark);
                transform: translateY(-2px);
                box-shadow: 0 9px 20px rgba(14,84,35,.08);
            }

            .finus-auto-reveal {
                opacity: 0;
                filter: blur(5px);
                transform: translateY(24px) scale(.99);
                transition: opacity .58s ease, transform .62s cubic-bezier(.2,.72,.2,1), filter .5s ease;
            }
            .finus-auto-reveal.is-visible {
                opacity: 1;
                filter: none;
                transform: none;
            }

            .employee-hero,
            .finus-data-hero,
            .finus-report-hero,
            .presensi-hero,
            .presensi-form-card,
            .employee-stat,
            .finus-data-stat,
            .finus-summary-card,
            .presensi-stat-card {
                border-radius: 20px !important;
            }
            .employee-hero,
            .finus-data-hero,
            .finus-report-hero,
            .presensi-hero {
                box-shadow: 0 18px 42px rgba(14,84,35,.18) !important;
            }

            @media (max-width: 991.98px) {
                .fmu-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .fmu-grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .fmu-hero { align-items: flex-start; }
            }
            @media (max-width: 767.98px) {
                .fmu-page { padding-bottom: 24px; }
                .fmu-hero { align-items: flex-start; flex-direction: column; padding: 22px; border-radius: 18px; }
                .fmu-hero-main { align-items: flex-start; }
                .fmu-hero-icon { width: 50px; min-width: 50px; height: 50px; border-radius: 14px; }
                .fmu-grid-2,
                .fmu-grid-3,
                .fmu-grid-4,
                .fmu-form-grid,
                .fmu-grid[style*="grid-template-columns"] { grid-template-columns: 1fr !important; }
                .fmu-field-full { grid-column: auto; }
                .fmu-card-head { align-items: flex-start; flex-direction: column; }
                .fmu-card-body { padding: 20px; }
                .fmu-actions { align-items: stretch; flex-direction: column-reverse; padding: 17px 20px; }
                .fmu-actions-right { width: 100%; }
                .fmu-btn { width: 100%; }
                .fmu-choice-grid { grid-template-columns: 1fr; }
                .finus-auto-reveal { transform: translateY(18px); transition-delay: 0ms !important; }
            }
            @media (max-width: 420px) {
                .fmu-hero { padding: 19px; }
                .fmu-card-body { padding: 17px; }
            }
            @media (prefers-reduced-motion: reduce) {
                .finus-auto-reveal { opacity: 1 !important; filter: none !important; transform: none !important; transition: none !important; }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (() => {
                const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const selectors = [
                    '.fmu-hero', '.fmu-stat', '.fmu-card', '.fmu-side-note',
                    '.employee-hero', '.employee-stat', '.employee-table-card',
                    '.finus-data-hero', '.finus-data-stat', '.finus-data-table-card',
                    '.finus-report-hero', '.finus-summary-card', '.finus-filter', '.finus-table-card',
                    '.presensi-hero', '.presensi-stat-card', '.presensi-table-card', '.presensi-form-card',
                    '.jamaah-hero', '.finus-stat-card', '.agenda-card', '.jm-hero', '.jm-stat', '.jm-table-card',
                    '.jt-heading', '.jt-card', '.payment-card', '.page-hero', '.qris-box'
                ];
                const elements = Array.from(document.querySelectorAll(selectors.join(',')));
                elements.forEach((element, index) => {
                    element.classList.add('finus-auto-reveal');
                    element.style.transitionDelay = `${Math.min(index % 5, 4) * 55}ms`;
                });
                if (reduceMotion || !('IntersectionObserver' in window)) {
                    elements.forEach(element => element.classList.add('is-visible'));
                    return;
                }
                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (!entry.isIntersecting) return;
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    });
                }, { threshold: .12, rootMargin: '0px 0px -35px 0px' });
                elements.forEach(element => observer.observe(element));

                document.querySelectorAll('form').forEach(form => {
                    form.addEventListener('submit', () => {
                        const button = form.querySelector('button[type="submit"]');
                        if (!button || button.disabled) return;
                        button.dataset.originalHtml = button.innerHTML;
                        button.disabled = true;
                        button.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...';
                    });
                });
            })();
        </script>
    @endpush
@endonce


{{-- FINUS Jamaah transaction components (merged from jamaah/partials/transaction-page-styles) --}}
@once
    @push('styles')
        <style>
            :root {
                --jt-green: #179B40;
                --jt-green-dark: #0E5423;
                --jt-green-soft: #EAF8EE;
                --jt-blue: #2563EB;
                --jt-orange: #EA8B22;
                --jt-red: #E5484D;
                --jt-text: #172033;
                --jt-muted: #64748B;
                --jt-border: #E2EAE5;
                --jt-surface: #FFFFFF;
                --jt-bg: #F7FAF8;
                --jt-shadow: 0 12px 30px rgba(15, 23, 42, .07);
            }

            .jt-page {
                padding: 8px 0 34px;
                color: var(--jt-text);
            }

            .jt-heading,
            .jt-card {
                border: 1px solid var(--jt-border);
                border-radius: 18px;
                background: var(--jt-surface);
                box-shadow: var(--jt-shadow);
            }

            .jt-heading {
                position: relative;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                margin-bottom: 20px;
                padding: 20px 22px;
            }

            .jt-heading::after {
                content: "";
                position: absolute;
                top: -64px;
                right: -34px;
                width: 155px;
                height: 155px;
                border-radius: 50%;
                background: var(--jt-green-soft);
            }

            .jt-heading-main,
            .jt-heading-actions,
            .jt-title-row,
            .jt-card-head,
            .jt-status,
            .jt-pagination-info {
                display: flex;
                align-items: center;
            }

            .jt-heading-main {
                position: relative;
                z-index: 1;
                gap: 13px;
            }

            .jt-heading-actions {
                position: relative;
                z-index: 1;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 9px;
            }

            .jt-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 46px;
                min-width: 46px;
                height: 46px;
                border-radius: 13px;
                background: var(--jt-green-soft);
                color: var(--jt-green);
                font-size: 17px;
            }

            .jt-eyebrow {
                color: var(--jt-green);
                font-size: 10px;
                font-weight: 800;
                letter-spacing: .13em;
                text-transform: uppercase;
            }

            .jt-heading h1,
            .jt-card-head h2,
            .jt-stat strong {
                margin: 0;
            }

            .jt-heading h1 {
                margin-top: 2px;
                font-size: 22px;
                font-weight: 800;
            }

            .jt-heading p,
            .jt-card-head p {
                margin: 4px 0 0;
                color: var(--jt-muted);
                font-size: 12px;
            }

            .jt-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 7px;
                min-height: 40px;
                padding: 9px 14px;
                border: 1px solid var(--jt-border);
                border-radius: 11px;
                background: #FFFFFF;
                color: var(--jt-text);
                font-size: 12px;
                font-weight: 700;
                text-decoration: none !important;
                cursor: pointer;
                transition: .2s ease;
            }

            .jt-btn:hover {
                border-color: #C9D9CE;
                color: var(--jt-green-dark);
                transform: translateY(-1px);
            }

            .jt-btn-primary {
                border-color: var(--jt-green);
                background: linear-gradient(135deg, var(--jt-green-dark), var(--jt-green));
                color: #FFFFFF !important;
            }

            .jt-btn-soft {
                border-color: #D7E9DC;
                background: var(--jt-green-soft);
                color: var(--jt-green-dark);
            }

            .jt-grid {
                display: grid;
                gap: 16px;
                margin-bottom: 18px;
            }

            .jt-grid-4 {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .jt-grid-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .jt-stat {
                position: relative;
                overflow: hidden;
                min-height: 126px;
                padding: 17px;
            }

            .jt-stat::after {
                content: "";
                position: absolute;
                right: -31px;
                bottom: -43px;
                width: 96px;
                height: 96px;
                border-radius: 50%;
                background: var(--stat-soft, var(--jt-green-soft));
            }

            .jt-stat-top,
            .jt-stat-body {
                position: relative;
                z-index: 1;
            }

            .jt-stat-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }

            .jt-stat .jt-icon {
                width: 40px;
                min-width: 40px;
                height: 40px;
                border-radius: 11px;
                background: var(--stat-soft, var(--jt-green-soft));
                color: var(--stat-color, var(--jt-green));
            }

            .jt-stat-body {
                margin-top: 12px;
            }

            .jt-stat span {
                color: var(--jt-muted);
                font-size: 11px;
            }

            .jt-stat strong {
                display: block;
                margin-top: 3px;
                color: var(--stat-color, var(--jt-text));
                font-size: 20px;
                font-weight: 800;
            }

            .jt-card {
                margin-bottom: 18px;
                overflow: hidden;
            }

            .jt-card-head {
                justify-content: space-between;
                gap: 14px;
                padding: 16px 18px;
                border-bottom: 1px solid var(--jt-border);
            }

            .jt-title-row {
                gap: 11px;
            }

            .jt-card-head h2 {
                font-size: 15px;
                font-weight: 800;
            }

            .jt-card-body {
                padding: 20px;
            }

            .jt-filter {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
                align-items: flex-end;
            }

            .jt-field {
                flex: 1 1 180px;
                min-width: 140px;
            }

            .jt-field-wide {
                flex: 2 1 280px;
            }

            .jt-field-small {
                flex: 1 1 150px;
            }

            .jt-field label {
                display: block;
                margin-bottom: 8px;
                color: #334155;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: .05em;
                text-transform: uppercase;
            }

            .jt-search-wrapper {
                position: relative;
                width: 100%;
            }

            .jt-search-wrapper i {
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: var(--jt-muted);
                font-size: 13px;
                pointer-events: none;
            }

            .jt-search-wrapper .jt-control {
                padding-left: 36px;
            }

            .jt-control {
                width: 100%;
                min-height: 44px;
                padding: 10px 14px;
                border: 1.5px solid var(--jt-border);
                border-radius: 12px;
                outline: none;
                background: #FFFFFF;
                color: var(--jt-text);
                font-size: 13px;
                transition: all 0.2s ease;
            }

            .jt-control:focus {
                border-color: var(--jt-green);
                box-shadow: 0 0 0 4px rgba(23, 155, 64, .12);
            }

            /* Filter Pills for ZISWAF Types */
            .jt-filter-pills {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 6px;
            }

            .jt-pill {
                border: 1.5px solid var(--jt-border);
                padding: 8px 16px;
                border-radius: 99px;
                font-size: 11.5px;
                font-weight: 700;
                background: #ffffff;
                color: var(--jt-text);
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .jt-pill:hover {
                border-color: var(--jt-green);
                color: var(--jt-green-dark);
                background-color: var(--jt-green-soft);
            }

            .jt-pill.active {
                background-color: var(--jt-green);
                border-color: var(--jt-green);
                color: #ffffff;
                box-shadow: 0 4px 10px rgba(23, 155, 64, 0.15);
            }

            .jt-filter-actions {
                display: flex;
                flex-wrap: nowrap;
                gap: 8px;
                align-self: flex-end;
                margin-left: auto;
                padding-bottom: 2px;
            }

            .jt-table-wrap {
                overflow-x: auto;
            }

            .jt-table {
                width: 100%;
                margin: 0;
                border-collapse: separate;
                border-spacing: 0;
            }

            .jt-table th,
            .jt-table td {
                padding: 14px 16px;
                border-bottom: 1.5px solid #f1f5f9;
                font-size: 12.5px;
                vertical-align: middle;
                transition: all 0.2s ease;
            }

            .jt-table th {
                background: #f8fafc;
                color: #475569;
                font-size: 10.5px;
                font-weight: 800;
                letter-spacing: .06em;
                text-transform: uppercase;
                white-space: nowrap;
                border-bottom: 2px solid #e2e8f0;
            }

            .jt-table tbody tr:hover td {
                background-color: #f8fafc;
            }

            .jt-reference {
                color: var(--jt-green-dark);
                font-weight: 700;
                white-space: nowrap;
                font-size: 12px;
            }

            .jt-money {
                color: var(--jt-green-dark);
                font-weight: 800;
                white-space: nowrap;
                font-size: 13.5px;
            }

            .jt-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                border-radius: 30px;
                font-size: 11px;
                font-weight: 700;
                white-space: nowrap;
                border: 0;
            }

            .jt-badge::before {
                content: "";
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: currentColor;
            }

            .jt-badge-pending {
                background: #fef3c7;
                color: #d97706;
            }

            .jt-badge-diterima {
                background: #dcfce7;
                color: #15803d;
            }

            .jt-badge-ditolak {
                background: #fee2e2;
                color: #b91c1c;
            }

            .jt-type {
                display: inline-flex;
                padding: 5px 10px;
                border-radius: 8px;
                background: #eff6ff;
                color: #1d4ed8;
                font-size: 10.5px;
                font-weight: 700;
                white-space: nowrap;
            }

            .jt-note {
                max-width: 260px;
                color: var(--jt-muted);
                line-height: 1.55;
                font-size: 12px;
            }

            .jt-admin-note {
                display: block;
                margin-top: 4px;
                color: var(--jt-red);
                font-size: 10px;
                font-weight: 600;
            }

            .jt-empty {
                padding: 40px 20px !important;
                color: var(--jt-muted);
                text-align: center;
                font-size: 13px;
            }

            .jt-pagination {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
                padding: 14px 18px;
                border-top: 1px solid var(--jt-border);
            }

            .jt-pagination-info {
                gap: 7px;
                color: var(--jt-muted);
                font-size: 10.5px;
            }

            .jt-pagination .pagination {
                margin: 0;
            }

            .jt-pagination .page-link {
                border-color: var(--jt-border);
                color: var(--jt-green-dark);
                font-size: 11px;
            }

            .jt-pagination .page-item.active .page-link {
                border-color: var(--jt-green);
                background: var(--jt-green);
                color: #FFFFFF;
            }

            .jt-chart {
                height: 310px;
            }

            .jt-chart-small {
                height: 280px;
            }

            .jt-breakdown {
                display: grid;
                gap: 11px;
            }

            .jt-breakdown-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 13px;
                padding: 12px 13px;
                border: 1px solid var(--jt-border);
                border-radius: 12px;
                background: #FBFDFB;
            }

            .jt-breakdown-item strong {
                display: block;
                color: var(--jt-green-dark);
                font-size: 12px;
            }

            .jt-breakdown-item small {
                color: var(--jt-muted);
                font-size: 10px;
            }

            .jt-alert {
                margin-bottom: 16px;
                padding: 12px 14px;
                border: 1px solid #BFE4C9;
                border-radius: 12px;
                background: var(--jt-green-soft);
                color: var(--jt-green-dark);
                font-size: 12px;
            }

            @media (max-width: 1199px) {
                .jt-grid-4 {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .jt-field,
                .jt-field-wide,
                .jt-field-small {
                    grid-column: span 4;
                }

                .jt-filter-actions {
                    grid-column: span 12;
                }
            }

            @media (max-width: 767px) {
                .jt-heading {
                    align-items: flex-start;
                    padding: 17px;
                }

                .jt-heading-actions {
                    width: 100%;
                    justify-content: flex-start;
                }
                .jt-grid-4,
                .jt-grid-2 {
                    grid-template-columns: 1fr;
                }
                .jt-filter {
                    grid-template-columns: 1fr;
                }
                .jt-field,
                .jt-field-wide,
                .jt-field-small,
                .jt-filter-actions {
                    grid-column: auto;
                }
                .jt-card-head,
                .jt-pagination {
                    align-items: flex-start;
                    flex-direction: column;
                }
                .jt-chart,
                .jt-chart-small {
                    height: 260px;
                }
            }
            @media print {
                .sidebar,
                .header,
                .jt-heading-actions,
                .jt-filter-card,
                .jt-no-print,
                .jt-pagination {
                    display: none !important;
                }
                .content-wrap {
                    margin-left: 0 !important;
                    padding-top: 0 !important;
                }
                .content-wrap .main {
                    padding: 0 !important;
                }
                .jt-page {
                    padding: 0;
                }
                .jt-heading,
                .jt-card {
                    box-shadow: none;
                    break-inside: avoid;
                }
                .jt-table th,
                .jt-table td {
                    padding: 8px;
                    font-size: 9px;
                }
            }
        </style>
    @endpush
@endonce
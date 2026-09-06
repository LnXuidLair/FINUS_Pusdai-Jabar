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
        width: 48px;
        min-width: 48px;
        height: 48px;
        border-radius: 13px;
        background: var(--stat-soft, var(--jt-green-soft));
        color: var(--stat-color, var(--jt-green));
        font-size: 21px;
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

{{-- FINUS DARK MODE TRANSACTION COMPONENTS: jamaah/partials/transaction-page-styles.blade.php --}}
@push('dark-styles')
<style data-finus-dark-local="jamaah/partials/transaction-page-styles.blade.php">
html[data-finus-theme="dark"] {
    --fr-text:#F1F6F3; --fr-muted:#9EAEA4; --fr-border:#293D31;
    --fr-soft:#173620; --fr-soft-2:#101C15;
    --fr-blue:#82AEFF; --fr-blue-soft:#192C47;
    --fr-amber:#F4B65A; --fr-amber-soft:#382A18;
    --fr-red:#FF7D84; --fr-red-soft:#3C2024;
    --fr-purple:#B295FF; --fr-purple-soft:#302442;
    --fr-shadow-sm:0 10px 28px rgba(0,0,0,.18); --fr-shadow-md:0 18px 42px rgba(0,0,0,.25);

    --fmu-text:#F1F6F3; --fmu-muted:#9EAEA4; --fmu-border:#293D31;
    --fmu-soft:#173620; --fmu-soft-2:#101C15;
    --fmu-green:#64DD81; --fmu-dark:#B9F4C7;

    --jt-text:#F1F6F3; --jt-muted:#9EAEA4; --jt-border:#293D31;
    --jt-bg:#0D1510; --jt-surface:#111A15; --jt-green:#64DD81;
    --jt-green-dark:#B9F4C7; --jt-green-soft:#173620; --jt-shadow:0 16px 36px rgba(0,0,0,.23);
}
html[data-finus-theme="dark"] body :where(.fr-card,.fr-stat,.fr-status-panel,.fmu-card,.fmu-stat,.fmu-side-note,.jt-card,.jt-stat,.jt-filter-card,.jt-heading) {
    border-color:#293D31 !important;
    background:linear-gradient(155deg,#15211A 0%,#111A15 100%) !important;
    color:#F1F6F3 !important;
    box-shadow:0 14px 34px rgba(0,0,0,.20) !important;
}
html[data-finus-theme="dark"] body :where(.fr-card-head,.fmu-card-head,.jt-card-head,.fmu-actions) {
    border-color:#293D31 !important;
    background:linear-gradient(180deg,#17251D 0%,#121D17 100%) !important;
    color:#F1F6F3 !important;
}
html[data-finus-theme="dark"] body :where(.fr-card-title,.fr-stat-value,.fr-status-title,.fmu-label,.fmu-card h1,.fmu-card h2,.fmu-card h3,.fmu-stat strong,.jt-heading,.jt-card h1,.jt-card h2,.jt-stat strong) { color:#F1F6F3 !important; }
html[data-finus-theme="dark"] body :where(.fr-card-subtitle,.fr-stat-label,.fr-stat-note,.fr-status-copy,.fr-help,.fmu-help,.fmu-side-note p,.jt-note,.jt-card-head p,.jt-stat span,.jt-field label) { color:#9EAEA4 !important; }
html[data-finus-theme="dark"] body :where(.fr-input,.fr-select,.fr-search,.fmu-control,.fmu-select,.fmu-textarea,.jt-control) {
    border-color:#31493A !important; background:#0C1610 !important; color:#F1F6F3 !important; box-shadow:none !important;
}
html[data-finus-theme="dark"] body :where(.fr-input,.fr-select,.fr-search,.fmu-control,.fmu-select,.fmu-textarea,.jt-control):focus {
    border-color:#64DD81 !important; background:#0F1A13 !important; box-shadow:0 0 0 4px rgba(100,221,129,.12) !important;
}
html[data-finus-theme="dark"] body :where(.fr-input,.fr-search,.fmu-control,.fmu-textarea,.jt-control)::placeholder { color:#71847A !important; }
html[data-finus-theme="dark"] body :where(.fr-breakdown-item,.fr-tip-item,.fmu-upload,.fmu-choice,.jt-breakdown-item,.jt-pill) {
    border-color:#293D31 !important; background:#101B14 !important; color:#F1F6F3 !important;
}
html[data-finus-theme="dark"] body :where(.fr-table-wrap,.fmu-table-wrap,.jt-table-wrap) { border-color:#293D31 !important; background:#111A15 !important; }
html[data-finus-theme="dark"] body :where(.fr-table,.fmu-table,.jt-table) thead th {
    border-color:#293D31 !important; background:#17261D !important; color:#D5E4D9 !important;
}
html[data-finus-theme="dark"] body :where(.fr-table,.fmu-table,.jt-table) tbody td {
    border-color:#24372B !important; background:#111A15 !important; color:#DDE8E1 !important;
}
html[data-finus-theme="dark"] body :where(.fr-table,.fmu-table,.jt-table) tbody tr:hover td { background:#17251D !important; }
html[data-finus-theme="dark"] body :where(.fr-btn,.fmu-btn,.jt-btn):not(.fr-btn-primary):not(.fmu-btn-primary):not(.jt-btn-primary) {
    border-color:#304A39 !important; background:#121F18 !important; color:#DDE9E1 !important;
}
html[data-finus-theme="dark"] body :where(.fr-btn,.fmu-btn,.jt-btn):not(.fr-btn-primary):not(.fmu-btn-primary):not(.jt-btn-primary):hover {
    border-color:#3D5D48 !important; background:#192A20 !important; color:#C9F5D3 !important;
}
html[data-finus-theme="dark"] body .fmu-upload:hover,
html[data-finus-theme="dark"] body .fmu-upload:focus-within { border-color:#64DD81 !important; background:#14261A !important; }
html[data-finus-theme="dark"] body .fmu-file-name { background:#0E1812 !important; color:#F1F6F3 !important; border-color:#293D31 !important; }
html[data-finus-theme="dark"] body .fr-progress { background:#0C1510 !important; }
</style>
@endpush


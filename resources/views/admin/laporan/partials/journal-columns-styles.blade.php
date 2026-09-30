@once
    @push('styles')
        <style>
            .jct-table {
                min-width: 1260px;
                table-layout: fixed;
            }

            .jct-table thead th {
                letter-spacing: 0;
            }

            .jct-table .jct-debit-heading,
            .jct-table .jct-credit-heading,
            .jct-table .jct-amount-cell {
                text-align: right;
            }

            .jct-table .jct-debit-heading {
                background: #f1faf4;
                color: #176c32;
            }

            .jct-table .jct-credit-heading {
                background: #fff8eb;
                color: #8a5200;
            }

            .jct-table tbody .jct-debit-cell { background: rgba(23, 108, 50, .025); }
            .jct-table tbody .jct-credit-cell { background: rgba(154, 90, 0, .025); }

            .jct-description-cell {
                text-align: left !important;
            }

            .jct-description-title {
                display: block;
                color: #17231b;
                font-size: 11.5px;
                line-height: 1.45;
            }

            .jct-description-note {
                display: block;
                margin-top: 6px;
                padding-left: 9px;
                border-left: 2px solid #cbdad0;
                color: #68776e;
                font-size: 10.5px;
                line-height: 1.45;
            }

            .jct-account-list,
            .jct-amount-list {
                display: grid;
                align-content: start;
            }

            .jct-account-line,
            .jct-amount-line {
                display: flex;
                min-height: 48px;
                padding: 7px 0;
                border-bottom: 1px dashed #e2ebe5;
            }

            .jct-account-line {
                align-items: flex-start;
                flex-direction: column;
                justify-content: center;
                gap: 2px;
                text-align: left;
            }

            .jct-account-line:last-child,
            .jct-amount-line:last-child {
                border-bottom: 0;
            }

            .jct-account-name {
                color: #17231b;
                font-size: 11.5px;
                font-weight: 700;
                line-height: 1.35;
            }

            .jct-account-code {
                color: #6b7a71;
                font-family: Consolas, "Courier New", monospace;
                font-size: 10px;
                letter-spacing: 0;
            }

            .jct-amount-line {
                align-items: center;
                justify-content: flex-end;
            }

            .jct-money {
                display: grid;
                grid-template-columns: 23px minmax(0, 1fr);
                align-items: baseline;
                width: 100%;
                font-variant-numeric: tabular-nums;
                font-weight: 800;
                white-space: nowrap;
            }

            .jct-money.is-debit { color: #176c32; }
            .jct-money.is-credit { color: #9a5a00; }

            .jct-currency {
                font-size: 9.5px;
                font-weight: 700;
                text-align: left;
            }

            .jct-value { text-align: right; }

            .jct-empty {
                color: #9aa69f;
                font-weight: 700;
            }

            .jct-balance {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                margin-top: 7px;
                color: #17733a;
                font-size: 10px;
                font-weight: 800;
            }

            .jct-balance.is-unbalanced { color: #c2410c; }

            .jct-table tfoot td {
                padding: 14px 12px;
                border-top: 1px solid #dce9df;
                background: #f8fbf9;
            }

            .jct-total-label {
                color: #405047;
                font-size: 10.5px;
                font-weight: 800;
                text-align: right;
                text-transform: uppercase;
            }

            .jct-total-cell .jct-money {
                font-size: 13px;
            }

            html[data-finus-theme="dark"] body .jct-table .jct-debit-heading,
            html[data-finus-theme="dark"] body .jct-table tbody .jct-debit-cell {
                background: rgba(76, 175, 105, .07) !important;
            }

            html[data-finus-theme="dark"] body .jct-table .jct-credit-heading,
            html[data-finus-theme="dark"] body .jct-table tbody .jct-credit-cell {
                background: rgba(230, 168, 70, .07) !important;
            }

            html[data-finus-theme="dark"] body .jct-account-name {
                color: #edf5ef !important;
            }

            html[data-finus-theme="dark"] body .jct-description-title {
                color: #edf5ef !important;
            }

            html[data-finus-theme="dark"] body .jct-description-note {
                border-left-color: #3a5142 !important;
                color: #a9b8ae !important;
            }

            html[data-finus-theme="dark"] body .jct-account-code {
                color: #a9b8ae !important;
            }

            html[data-finus-theme="dark"] body .jct-account-line,
            html[data-finus-theme="dark"] body .jct-amount-line {
                border-bottom-color: #293d31 !important;
            }

            html[data-finus-theme="dark"] body .jct-money.is-debit {
                color: #7bd895 !important;
            }

            html[data-finus-theme="dark"] body .jct-money.is-credit {
                color: #f1bd68 !important;
            }

            html[data-finus-theme="dark"] body .jct-table tfoot td {
                border-top-color: #2b4334 !important;
                background: #111d16 !important;
            }

            html[data-finus-theme="dark"] body .jct-total-label {
                color: #c7d6cc !important;
            }
        </style>
    @endpush
@endonce

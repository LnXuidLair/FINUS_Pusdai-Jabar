@extends('layouts.app')
@section('content')
<style>
    :root {
        --finus-dark: #0E5423;
        --finus-green: #179B40;
        --finus-green-light: #22BA51;
        --finus-green-soft: #EAF8EE;
        --finus-green-soft-2: #F4FBF6;
        --finus-text: #172033;
        --finus-muted: #64748B;
        --finus-border: #E2EAE5;
        --finus-danger: #DC2626;
    }
    .finus-page {
        position: relative;
        padding: 10px 2px 32px;
    }
    .finus-page::before {
        content: "";
        position: absolute;
        top: -30px;
        right: 3%;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: rgba(34, 186, 81, .06);
        pointer-events: none;
    }
    .finus-table-card {
        position: relative;
        z-index: 1;
        overflow: hidden;
        border: 1px solid rgba(14, 84, 35, .09);
        border-radius: 22px;
        background: #ffffff;
        box-shadow:
            0 22px 50px rgba(15, 23, 42, .08),
            0 6px 18px rgba(14, 84, 35, .04);
    }
    .finus-header-gradient {
        position: relative;
        overflow: hidden;
        padding: 27px 29px;
        background: linear-gradient(
            135deg,
            #0E5423 0%,
            #179B40 48%,
            #22BA51 100%
        );
    }
    .finus-header-gradient::before {
        content: "";
        position: absolute;
        top: -100px;
        right: -55px;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .10);
    }
    .finus-header-gradient::after {
        content: "";
        position: absolute;
        right: 160px;
        bottom: -130px;
        width: 210px;
        height: 210px;
        border-radius: 50%;
        background: rgba(126, 255, 135, .12);
    }
    .finus-header-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 22px;
    }
    .finus-header-left {
        display: flex;
        align-items: center;
        min-width: 0;
    }
    .finus-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 58px;
        min-width: 58px;
        height: 58px;
        margin-right: 16px;
        border: 1px solid rgba(255, 255, 255, .20);
        border-radius: 17px;
        background: rgba(255, 255, 255, .14);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .14);
        backdrop-filter: blur(8px);
    }
    .finus-header-icon i {
        color: #ffffff;
        font-size: 23px;
    }
    .finus-header-title {
        margin: 0;
        color: #ffffff;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.3;
        letter-spacing: -.02em;
    }
    .finus-header-subtitle {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 6px;
        color: rgba(255, 255, 255, .80);
        font-size: 13px;
    }
    .finus-header-subtitle::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #9EFFA7;
        box-shadow: 0 0 0 4px rgba(158, 255, 167, .13);
    }
    .finus-header-actions {
        display: flex;
        align-items: center;
        gap: 11px;
    }
    .finus-search-wrapper {
        position: relative;
        width: 290px;
    }
    .finus-search-icon {
        position: absolute;
        top: 50%;
        left: 15px;
        z-index: 2;
        color: #718096;
        font-size: 14px;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .finus-search {
        width: 100%;
        height: 45px;
        padding: 0 42px 0 41px;
        border: 1px solid rgba(255, 255, 255, .42);
        border-radius: 12px;
        background: rgba(255, 255, 255, .96);
        color: var(--finus-text);
        font-size: 13px;
        box-shadow: 0 8px 18px rgba(0, 65, 24, .12);
        outline: none;
        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }
    .finus-search::placeholder {
        color: #94A3B8;
    }
    .finus-search:focus {
        border-color: #A3F3B0;
        background: #ffffff;
        box-shadow:
            0 8px 20px rgba(0, 65, 24, .15),
            0 0 0 4px rgba(126, 255, 135, .14);
    }
    .finus-clear-search {
        position: absolute;
        top: 50%;
        right: 10px;
        display: none;
        align-items: center;
        justify-content: center;
        width: 27px;
        height: 27px;
        padding: 0;
        border: 0;
        border-radius: 8px;
        background: #EFF5F1;
        color: #64748B;
        cursor: pointer;
        transform: translateY(-50%);
    }
    .finus-clear-search:hover {
        background: #E1F2E6;
        color: var(--finus-green);
    }
    .finus-add-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 45px;
        padding: 0 18px;
        border: 1px solid rgba(255, 255, 255, .68);
        border-radius: 12px;
        background: #ffffff;
        color: var(--finus-dark) !important;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none !important;
        white-space: nowrap;
        box-shadow: 0 8px 18px rgba(0, 65, 24, .14);
        transition:
            transform .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }
    .finus-add-button i {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 23px;
        height: 23px;
        border-radius: 7px;
        background: var(--finus-green-soft);
        color: var(--finus-green);
        font-size: 11px;
    }
    .finus-add-button:hover {
        background: #F1FFF4;
        color: var(--finus-dark) !important;
        transform: translateY(-2px);
        box-shadow: 0 12px 22px rgba(0, 65, 24, .20);
    }
    .finus-card-body {
        padding: 25px 27px 29px;
        background:
            linear-gradient(
                180deg,
                rgba(234, 248, 238, .50) 0,
                #ffffff 95px
            );
    }
    .finus-table-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }
    .finus-section-title {
        margin: 0;
        color: var(--finus-text);
        font-size: 15px;
        font-weight: 800;
    }
    .finus-section-description {
        margin: 4px 0 0;
        color: var(--finus-muted);
        font-size: 12.5px;
    }
    .finus-total-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        border: 1px solid #D8EBDE;
        border-radius: 10px;
        background: var(--finus-green-soft);
        color: var(--finus-dark);
        font-size: 12px;
        font-weight: 700;
    }
    .finus-total-badge i {
        color: var(--finus-green);
    }
    .finus-alert-success {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding: 14px 16px;
        border: 1px solid #BBE8C7;
        border-radius: 13px;
        background: #F0FBF3;
        color: #166534;
        font-size: 13px;
        font-weight: 600;
    }
    .finus-alert-success i {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 31px;
        min-width: 31px;
        height: 31px;
        border-radius: 9px;
        background: #D8F6E0;
        color: #16A34A;
    }
    .finus-table-wrapper {
        overflow: hidden;
        border: 1px solid var(--finus-border);
        border-radius: 16px;
        background: #ffffff;
    }
    .finus-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .finus-table thead th {
        padding: 15px 17px;
        border: 0;
        border-bottom: 1px solid var(--finus-border);
        background: #F4F9F5;
        color: #4B6253;
        font-size: 11px;
        font-weight: 800;
        line-height: 1.4;
        letter-spacing: .07em;
        text-transform: uppercase;
        vertical-align: middle;
    }
    .finus-table tbody td {
        padding: 15px 17px;
        border: 0;
        border-bottom: 1px solid #ECF1ED;
        background: #ffffff;
        color: #334155;
        font-size: 13.5px;
        vertical-align: middle;
        transition: background .2s ease;
    }
    .finus-table tbody tr:last-child td {
        border-bottom: 0;
    }
    .finus-table tbody tr:hover td {
        background: #F7FCF8;
    }
    .finus-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: 1px solid #D8EADE;
        border-radius: 10px;
        background: var(--finus-green-soft-2);
        color: var(--finus-dark);
        font-size: 12px;
        font-weight: 800;
    }
    .finus-position {
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
    }
    .finus-position-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 39px;
        min-width: 39px;
        height: 39px;
        border-radius: 11px;
        background: var(--finus-green-soft);
        color: var(--finus-green);
        font-size: 14px;
    }
    .finus-position-name {
        color: var(--finus-text);
        font-weight: 700;
        line-height: 1.35;
    }
    .finus-position-label {
        margin-top: 3px;
        color: var(--finus-muted);
        font-size: 11px;
    }
    .finus-salary {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        border: 1px solid #CDE8D5;
        border-radius: 10px;
        background: #EFFAF2;
        color: #137035;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
    }
    .finus-salary i {
        font-size: 11px;
    }
    .finus-edit-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 36px;
        padding: 0 12px;
        border: 1px solid #F1D89F;
        border-radius: 10px;
        background: #FFF9EA;
        color: #A16207 !important;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none !important;
        transition:
            background .2s ease,
            border-color .2s ease,
            transform .2s ease,
            box-shadow .2s ease;
    }
    .finus-edit-button:hover {
        border-color: #E7C66F;
        background: #FFF3CC;
        color: #854D0E !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 13px rgba(161, 98, 7, .13);
    }
    .finus-empty-state {
        padding: 55px 20px !important;
        text-align: center;
    }
    .finus-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        margin: 0 auto 14px;
        border-radius: 19px;
        background: var(--finus-green-soft);
        color: var(--finus-green);
        font-size: 24px;
    }
    .finus-empty-title {
        margin-bottom: 5px;
        color: var(--finus-text);
        font-size: 14px;
        font-weight: 800;
    }
    .finus-empty-description {
        margin: 0;
        color: var(--finus-muted);
        font-size: 12.5px;
    }
    @media (max-width: 991px) {
        .finus-header-content {
            align-items: flex-start;
            flex-direction: column;
        }
        .finus-header-actions {
            width: 100%;
        }
        .finus-search-wrapper {
            flex: 1;
            width: auto;
        }
    }
    @media (max-width: 767px) {
        .finus-table-card {
            border-radius: 17px;
        }
        .finus-header-gradient {
            padding: 22px 19px;
        }
        .finus-header-icon {
            width: 50px;
            min-width: 50px;
            height: 50px;
            margin-right: 13px;
            border-radius: 14px;
        }
        .finus-header-title {
            font-size: 19px;
        }
        .finus-header-actions {
            align-items: stretch;
            flex-direction: column;
        }
        .finus-search-wrapper {
            width: 100%;
        }
        .finus-add-button {
            width: 100%;
        }
        .finus-card-body {
            padding: 20px 15px;
        }
        .finus-table-info {
            align-items: flex-start;
            flex-direction: column;
        }
        .finus-table-wrapper {
            overflow: visible;
            border: 0;
            background: transparent;
        }
        .finus-table,
        .finus-table tbody,
        .finus-table tr,
        .finus-table td {
            display: block;
            width: 100%;
        }
        .finus-table thead {
            display: none;
        }
        .finus-table tbody tr[data-search-row] {
            overflow: hidden;
            margin-bottom: 14px;
            border: 1px solid var(--finus-border);
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 7px 18px rgba(15, 23, 42, .04);
        }
        .finus-table tbody tr[data-search-row] td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            min-height: 54px;
            padding: 12px 15px;
            border-bottom: 1px solid #ECF1ED;
            text-align: right !important;
        }
        .finus-table tbody tr[data-search-row] td:last-child {
            border-bottom: 0;
        }
        .finus-table tbody tr[data-search-row] td::before {
            content: attr(data-label);
            color: #64748B;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .04em;
            text-align: left;
            text-transform: uppercase;
        }
        .finus-position {
            justify-content: flex-end;
        }
        .finus-position-label {
            display: none;
        }
        .finus-number {
            width: 30px;
            height: 30px;
        }
        .finus-empty-state {
            display: block !important;
            padding: 45px 20px !important;
            border: 1px solid var(--finus-border);
            border-radius: 15px;
            background: #ffffff !important;
        }
        .finus-empty-state::before {
            display: none;
        }
    }
    @media (max-width: 430px) {
        .finus-header-left {
            align-items: flex-start;
        }
        .finus-position-icon {
            display: none;
        }
        .finus-salary {
            padding: 7px 9px;
            font-size: 12px;
        }
    }
</style>
<div class="finus-page">
    <div class="row">
        <div class="col-12">
            <div class="card finus-table-card">
                <div class="finus-header-gradient">
                    <div class="finus-header-content">
                        <div class="finus-header-left">
                            <div class="finus-header-icon">
                                <i class="fa fa-briefcase"></i>
                            </div>
                            <div>
                                <h4 class="finus-header-title">
                                    Gaji & Jabatan
                                </h4>
                                <div class="finus-header-subtitle" id="recordCount" data-label="data jabatan">
                                    Total:
                                    {{ is_countable($gajiJabatans) ? count($gajiJabatans) : 0 }}
                                    data jabatan
                                </div>
                            </div>
                        </div>
                        <div class="finus-header-actions">
                            <div class="finus-search-wrapper">
                                <i class="fa fa-search finus-search-icon"></i>
                                <input id="searchInput" type="search" class="finus-search" placeholder="Cari jabatan atau nominal gaji..." autocomplete="off">
                                <button type="button" id="clearSearch" class="finus-clear-search" aria-label="Hapus pencarian">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <a href="{{ route('admin.gaji-jabatan.create') }}" class="finus-add-button">
                                <i class="fa fa-plus"></i>
                                Tambah Jabatan
                            </a>
                        </div>
                    </div>
                </div>
                <div class="finus-card-body">
                    @if(session('success'))
                        <div class="finus-alert-success">
                            <i class="fa fa-check"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    <div class="finus-table-info">
                        <div>
                            <h5 class="finus-section-title">
                                Daftar Gaji Jabatan
                            </h5>
                            <p class="finus-section-description">
                                Kelola jabatan pegawai dan nominal gaji yang diterima setiap hari.
                            </p>
                        </div>
                        <div class="finus-total-badge">
                            <i class="fa fa-database"></i>
                            <span id="visibleCount">
                                {{ is_countable($gajiJabatans) ? count($gajiJabatans) : 0 }}
                            </span>
                            data ditampilkan
                        </div>
                    </div>
                    <div class="finus-table-wrapper">
                        <table id="searchTable" class="table finus-table">
                            <thead>
                                <tr>
                                    <th width="80" class="text-center">No.</th>
                                    <th>Nama Jabatan</th>
                                    <th width="240">Gaji per Hari</th>
                                    <th width="130" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gajiJabatans as $item)
                                    @php
                                        $gajiFormatted = 'Rp ' . number_format(
                                            $item->gaji_perhari,
                                            0,
                                            ',',
                                            '.'
                                        );
                                        $gajiRaw = preg_replace(
                                            '/\D+/',
                                            '',
                                            (string) $item->gaji_perhari
                                        );
                                    @endphp
                                    <tr data-search-row data-search-start="{{ $item->jabatan }}|{{ $gajiFormatted }}|{{ $gajiRaw }}">
                                        <td class="text-center" data-label="Nomor">
                                            <span class="finus-number" data-row-number>
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        <td data-label="Jabatan">
                                            <div class="finus-position">
                                                <div class="finus-position-icon">
                                                    <i class="fa fa-user-tie"></i>
                                                </div>
                                                <div>
                                                    <div class="finus-position-name">
                                                        {{ $item->jabatan }}
                                                    </div>
                                                    <div class="finus-position-label">
                                                        Jabatan pegawai
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Gaji per Hari">
                                            <span class="finus-salary">
                                                <i class="fa fa-money-bill-wave"></i>
                                                {{ $gajiFormatted }}
                                            </span>
                                        </td>
                                        <td class="text-center" data-label="Aksi">
                                            <a href="{{ route('admin.gaji-jabatan.edit', $item) }}" class="finus-edit-button">
                                                <i class="fa fa-pen"></i>
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="finus-empty-state">
                                            <div class="finus-empty-icon">
                                                <i class="fa fa-briefcase"></i>
                                            </div>
                                            <div class="finus-empty-title">
                                                Belum ada data gaji jabatan
                                            </div>
                                            <p class="finus-empty-description">
                                                Tekan tombol Tambah Jabatan untuk memasukkan data pertama.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="emptySearchRow" style="display: none;">
                                    <td colspan="4" class="finus-empty-state">
                                        <div class="finus-empty-icon">
                                            <i class="fa fa-search"></i>
                                        </div>
                                        <div class="finus-empty-title">
                                            Data tidak ditemukan
                                        </div>
                                        <p class="finus-empty-description">
                                            Coba gunakan kata kunci jabatan atau nominal gaji yang berbeda.
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    const input = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearSearch');
    const rows = Array.from(
        document.querySelectorAll('[data-search-row]')
    );
    const emptyRow = document.getElementById('emptySearchRow');
    const countEl = document.getElementById('recordCount');
    const visibleCountEl = document.getElementById('visibleCount');
    function normalize(value) {
        return (value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }
    function onlyDigits(value) {
        return (value || '').replace(/\D/g, '');
    }
    function filterRows() {
        if (!input) {
            return;
        }
        const keyword = normalize(input.value);
        const keywordDigits = onlyDigits(keyword);
        let visible = 0;
        rows.forEach(function (row) {
            const values = (row.dataset.searchStart || '')
                .split('|')
                .map(function (value) {
                    return normalize(value);
                })
                .filter(Boolean);
            const matchText =
                keyword === '' ||
                values.some(function (value) {
                    return value.startsWith(keyword);
                });
            const matchNumber =
                keywordDigits !== '' &&
                values.some(function (value) {
                    const digitValue = onlyDigits(value);

                    return digitValue.startsWith(keywordDigits);
                });
            const match =
                keyword === '' ||
                matchText ||
                matchNumber;
            row.style.display = match ? '' : 'none';
            if (match) {
                visible++;
                const numberCell = row.querySelector(
                    '[data-row-number]'
                );
                if (numberCell) {
                    numberCell.textContent = visible;
                }
            }
        });
        if (emptyRow) {
            emptyRow.style.display =
                visible === 0 && rows.length > 0
                    ? ''
                    : 'none';
        }
        if (countEl) {
            const label = countEl.dataset.label || 'data';
            countEl.textContent =
                `Total: ${visible} ${label}`;
        }
        if (visibleCountEl) {
            visibleCountEl.textContent = visible;
        }
        if (clearButton) {
            clearButton.style.display =
                input.value.trim().length > 0
                    ? 'flex'
                    : 'none';
        }
    }
    if (input) {
        input.addEventListener('input', filterRows);
    }
    if (clearButton && input) {
        clearButton.addEventListener('click', function () {
            input.value = '';
            input.focus();
            filterRows();
        });
    }
    filterRows();
})();
</script>
@endsection
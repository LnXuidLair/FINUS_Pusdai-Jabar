<?php

return [
    'fund_types' => [
        'operasional' => 'Dana Operasional',
        'zakat' => 'Dana Zakat',
        'infak_sedekah' => 'Dana Infak/Sedekah',
        'wakaf' => 'Dana Wakaf',
        'wakaf_temporer' => 'Wakaf Temporer',
    ],

    'form_types' => [
        'general' => 'Pengeluaran Umum',
        'honorarium' => 'Honorarium Pegawai',
        'zakat_distribution' => 'Penyaluran Zakat',
        'wakaf_distribution' => 'Penyaluran Manfaat Wakaf',
        'temporary_wakaf_return' => 'Pengembalian Wakaf Temporer',
    ],

    /*
     * Kode akun adalah sumber kebenaran golongan dana pengeluaran.
     * Aturan dibaca dari awalan terpanjang agar kode khusus seperti 2201
     * dapat dipetakan sebelum kelompok akun yang lebih umum.
     */
    'fund_source_rules' => [
        '2201' => 'wakaf_temporer',
        '51' => 'operasional',
        '52' => 'zakat',
        '54' => 'wakaf',
    ],

    'defaults' => [
        ['code' => 'ADM-ATK', 'name' => 'Administrasi dan ATK', 'coa_code' => '5101'],
        ['code' => 'DAKWAH', 'name' => 'Program Dakwah dan Keagamaan', 'coa_code' => '5102'],
        ['code' => 'PEMELIHARAAN', 'name' => 'Pemeliharaan Sarana dan Bangunan', 'coa_code' => '5103'],
        ['code' => 'HONORARIUM', 'name' => 'Honorarium Pegawai', 'coa_code' => '5104', 'form_type' => 'honorarium', 'requires_employee' => true, 'requires_assignment_proof' => true],
        ['code' => 'KONSUMSI', 'name' => 'Konsumsi', 'coa_code' => '5105'],
        ['code' => 'ADMIN-BANK', 'name' => 'Administrasi Bank', 'coa_code' => '5106'],
        ['code' => 'UTILITAS', 'name' => 'Listrik, Air, Internet, dan Utilitas', 'coa_code' => '5107'],
        ['code' => 'KEBERSIHAN', 'name' => 'Peralatan dan Bahan Kebersihan', 'coa_code' => '5108'],
        ['code' => 'KEGIATAN', 'name' => 'Kegiatan dan Acara', 'coa_code' => '5109'],
        ['code' => 'PERLENGKAPAN', 'name' => 'Pengadaan Perlengkapan', 'coa_code' => '5110'],
        ['code' => 'TRANSPORTASI', 'name' => 'Transportasi dan Perjalanan', 'coa_code' => '5111'],
        ['code' => 'JASA-PROFESIONAL', 'name' => 'Jasa dan Tenaga Profesional', 'coa_code' => '5112'],
        ['code' => 'OPERASIONAL-LAIN', 'name' => 'Operasional Lain-lain', 'coa_code' => '5199'],
        ['code' => 'PENYALURAN-ZAKAT', 'name' => 'Penyaluran Zakat kepada Mustahik', 'coa_code' => '5210', 'form_type' => 'zakat_distribution', 'requires_asnaf' => true],
        ['code' => 'PENYALURAN-WAKAF', 'name' => 'Penyaluran Manfaat Wakaf', 'coa_code' => '5411', 'form_type' => 'wakaf_distribution'],
        ['code' => 'PENGEMBALIAN-WAKAF', 'name' => 'Pengembalian Pokok Wakaf Temporer', 'coa_code' => '2201', 'form_type' => 'temporary_wakaf_return'],
    ],
];

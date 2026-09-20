<?php

return [
    /*
     * Master COA dipakai sebagai daftar akun umum FINUS. Pemisahan dana
     * PSAK 109 disimpan pada dimensi jurnal dan hanya dirinci pada akun
     * penyaluran zakat, bukan dipaksakan ke seluruh akun operasional.
     */
    'accounts' => [
        [1, '1101', 'Kas'],
        [1, '1102', 'Bank'],
        [1, '1103', 'Piutang'],
        [1, '1104', 'Persediaan'],
        [1, '1201', 'Peralatan dan Inventaris'],
        [1, '1202', 'Bangunan dan Sarana'],
        [1, '1291', 'Akumulasi Penyusutan'],

        [2, '2101', 'Utang Usaha'],
        [2, '2102', 'Utang Kegiatan'],
        [2, '2103', 'Beban yang Masih Harus Dibayar'],

        [3, '3101', 'Dana Operasional'],
        [3, '3102', 'Dana Pengembangan'],
        [3, '3103', 'Saldo Dana Awal'],

        [4, '4101', 'Infak Kotak Amal'],
        [4, '4102', 'Infak Layanan QRIS'],
        [4, '4103', 'Penerimaan Parkir dan Kegiatan'],
        [4, '4104', 'Jasa Giro dan Bagi Hasil Bank'],
        [4, '4105', 'Penerimaan Zakat'],
        [4, '4106', 'Penerimaan Infak dan Sedekah'],
        [4, '4107', 'Penerimaan Wakaf'],
        [4, '4108', 'Penerimaan Fidyah'],
        [4, '4199', 'Penerimaan Lain-lain'],
        [4, '4301', 'Bagian Amil dari Zakat'],
        [4, '4302', 'Bagian Amil dari Infak dan Sedekah'],

        [5, '5101', 'Beban Administrasi dan ATK'],
        [5, '5102', 'Beban Program Dakwah dan Keagamaan'],
        [5, '5103', 'Beban Pemeliharaan Sarana dan Bangunan'],
        [5, '5104', 'Beban Gaji dan Honorarium'],
        [5, '5105', 'Beban Konsumsi'],
        [5, '5106', 'Beban Administrasi Bank'],
        [5, '5107', 'Beban Utilitas'],
        [5, '5108', 'Beban Kebersihan'],
        [5, '5109', 'Beban Kegiatan dan Acara'],
        [5, '5110', 'Beban Pengadaan Perlengkapan'],
        [5, '5111', 'Beban Transportasi dan Perjalanan'],
        [5, '5112', 'Beban Jasa dan Profesional'],
        [5, '5199', 'Beban Operasional Lain-lain'],

        [5, '5210', 'Penyaluran Zakat'],

        [5, '5311', 'Penyaluran Infak dan Sedekah'],
        [5, '5312', 'Alokasi Infak dan Sedekah - Bagian Amil'],
        [5, '5411', 'Penyaluran Wakaf'],
        [5, '5511', 'Penyaluran Fidyah'],
    ],

    'expense_groups' => [
        'operasional' => 'Beban Operasional',
        'zakat' => 'Penyaluran Zakat kepada Mustahik',
        'sosial' => 'Penyaluran Dana Lainnya',
    ],

    'manual_expense_accounts' => [
        'operasional' => [
            '5101' => 'Administrasi, surat-menyurat, dan alat tulis kantor',
            '5102' => 'Dakwah, kajian, imam, khatib, dan kegiatan keagamaan',
            '5103' => 'Perawatan bangunan, sarana, dan prasarana',
            '5105' => 'Konsumsi rapat, kegiatan, dan pelayanan',
            '5106' => 'Administrasi rekening, transfer, dan layanan bank',
            '5107' => 'Listrik, air, internet, telepon, dan utilitas lainnya',
            '5108' => 'Peralatan dan bahan kebersihan',
            '5109' => 'Acara, kepanitiaan, dan program umum',
            '5110' => 'Perlengkapan operasional bernilai tidak material',
            '5111' => 'Transportasi, perjalanan dinas, dan pengiriman',
            '5112' => 'Jasa teknis, konsultan, dan tenaga profesional',
            '5199' => 'Beban operasional yang tidak termasuk kategori lainnya',
        ],
        'zakat' => [
            '5210' => 'Rincian golongan dan jumlah penerima diisi pada form penyaluran',
        ],
        'sosial' => [
            '5311' => 'Penyaluran infak dan sedekah sesuai program atau amanah',
            '5411' => 'Penyaluran atau pemanfaatan dana wakaf',
            '5511' => 'Penyaluran fidyah kepada penerima yang berhak',
        ],
    ],

    'automatic_expense_accounts' => ['5104', '5312'],
];

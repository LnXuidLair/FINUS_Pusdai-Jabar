<?php

return [
    /* Pegawai hanya dapat melakukan presensi datang pada rentang ini. */
    'datang' => [
        'start' => '07:30',
        'end' => '10:00',
    ],

    /* Presensi pulang normal. */
    'pulang' => [
        'start' => '15:30',
        'end' => '18:00',
    ],

    /* Izin / sakit satu hari dapat diajukan selama jam kerja. */
    'izin_sakit' => [
        'start' => '07:30',
        'end' => '18:00',
    ],

    /* Pulang lembur tetap dicatat pada hari kerja yang sama. */
    'lembur' => [
        'start' => '18:01',
        'end' => '23:59',
    ],

    /*
     * Admin tidak dibatasi oleh jam saat membuka sistem karena fungsinya
     * sebagai koreksi administratif. Namun jam yang dicatat untuk hari kerja
     * normal tetap berada pada rentang ini. Lembur boleh sampai lembur.end.
     */
    'jam_kerja' => [
        'start' => '07:30',
        'end' => '18:00',
    ],
];
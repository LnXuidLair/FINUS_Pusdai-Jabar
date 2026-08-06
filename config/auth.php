<?php

use App\Models\User;

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],
    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Guard web tetap dipertahankan untuk kompatibilitas Laravel/Breeze.
    | FINUS memakai guard admin, pegawai, dan jamaah agar ketiga role dapat
    | aktif bersamaan di satu browser tanpa saling menimpa sesi login.
    |
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        User::ROLE_ADMIN => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        User::ROLE_PEGAWAI => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        User::ROLE_JAMAAH => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],
    ],
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],
    'password_timeout' => 10800,
];
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection
    |--------------------------------------------------------------------------
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env(
                'DB_DATABASE',
                database_path('database.sqlite')
            ),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),

            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'finus'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),

            'unix_socket' => env('DB_SOCKET', ''),

            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),

            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,

            /*
            |--------------------------------------------------------------------------
            | MySQL SSL Configuration
            |--------------------------------------------------------------------------
            |
            | MYSQL_ATTR_SSL_CA diisi dengan lokasi sertifikat CA Aiven.
            | Jika variabel tersebut kosong, koneksi MySQL lokal tetap dapat
            | berjalan tanpa SSL certificate.
            |
            */

            'options' => extension_loaded('pdo_mysql')
                ? array_filter([
                    \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA')
                        ? base_path(env('MYSQL_ATTR_SSL_CA'))
                        : null,
                ])
                : [],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */

    'migrations' => 'migrations',

];
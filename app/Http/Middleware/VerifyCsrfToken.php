<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'payment/midtrans/notification',
        'logout',
        'logout/admin',
        'logout/pegawai',
        'logout/jamaah',
    ];
}

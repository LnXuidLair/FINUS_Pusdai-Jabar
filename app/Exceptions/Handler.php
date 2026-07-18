<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
            $message = str_replace(
                ["\r", "\n"],
                ' ',
                $e->getMessage()
            );

            error_log(sprintf(
                'FINUS_EXCEPTION | %s | %s | %s:%d',
                get_class($e),
                substr($message, 0, 1500),
                $e->getFile(),
                $e->getLine()
            ));
        });
    }
}
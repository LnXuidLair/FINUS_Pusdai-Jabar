<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }
        return match ($this->guardFromRoute($request)) {
            User::ROLE_ADMIN => route('login.admin'),
            User::ROLE_PEGAWAI => route('login.staff'),
            User::ROLE_JAMAAH => route('login.jamaah'),
            default => route('login'),
        };
    }
    private function guardFromRoute(Request $request): ?string
    {
        foreach ($request->route()?->gatherMiddleware() ?? [] as $middleware) {
            if (! is_string($middleware) || ! str_starts_with($middleware, 'auth:')) {
                continue;
            }
            foreach (explode(',', substr($middleware, 5)) as $guard) {
                if (in_array($guard, [
                    User::ROLE_ADMIN,
                    User::ROLE_PEGAWAI,
                    User::ROLE_JAMAAH,
                ], true)) {
                    return $guard;
                }
            }
        }
        return null;
    }
}
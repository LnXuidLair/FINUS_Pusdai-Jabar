<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        foreach ($guards ?: [null] as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(match ($request->user()->role) {
                    User::ROLE_ADMIN => route('dashboard'),
                    User::ROLE_PEGAWAI => route('pegawai.dashboard'),
                    default => route('jamaah.dashboard'),
                });
            }
        }

        return $next($request);
    }
}

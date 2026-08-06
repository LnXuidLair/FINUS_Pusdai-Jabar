<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$guards
    ): Response {
        foreach ($guards ?: [config('auth.defaults.guard')] as $guard) {
            if (! Auth::guard($guard)->check()) {
                continue;
            }
            /** @var User|null $user */
            $user = Auth::guard($guard)->user();
            return redirect()->route(
                $this->dashboardRoute($guard, $user)
            );
        }
        return $next($request);
    }
    private function dashboardRoute(string $guard, ?User $user): string
    {
        return match ($guard) {
            User::ROLE_ADMIN => 'dashboard',
            User::ROLE_PEGAWAI => 'pegawai.dashboard',
            User::ROLE_JAMAAH => 'jamaah.dashboard',
            default => match ($user?->role) {
                User::ROLE_ADMIN => 'dashboard',
                User::ROLE_PEGAWAI => 'pegawai.dashboard',
                User::ROLE_JAMAAH => 'jamaah.dashboard',
                default => 'home',
            },
        };
    }
}
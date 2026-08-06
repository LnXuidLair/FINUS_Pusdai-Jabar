<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $guard = $this->guardForRoles($roles);
        $user = $guard
            ? Auth::guard($guard)->user()
            : $request->user();

        if (! $user) {
            return redirect()->route($this->loginRoute($guard));
        }
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        return $next($request);
    }
    private function guardForRoles(array $roles): ?string
    {
        foreach ([
            User::ROLE_ADMIN,
            User::ROLE_PEGAWAI,
            User::ROLE_JAMAAH,
        ] as $guard) {
            if (in_array($guard, $roles, true)) {
                return $guard;
            }
        }
        return null;
    }
    private function loginRoute(?string $guard): string
    {
        return match ($guard) {
            User::ROLE_ADMIN => 'login.admin',
            User::ROLE_PEGAWAI => 'login.staff',
            User::ROLE_JAMAAH => 'login.jamaah',
            default => 'login',
        };
    }
}
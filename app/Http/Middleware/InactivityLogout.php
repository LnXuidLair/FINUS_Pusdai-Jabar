<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InactivityLogout
{
    private const TIMEOUT_SECONDS = 15 * 60;
    private const GUARDS = [
        User::ROLE_ADMIN,
        User::ROLE_PEGAWAI,
        User::ROLE_JAMAAH,
    ];
    public function handle(
        Request $request,
        Closure $next,
        ?string $guard = null
    ): Response {
        $guard = $guard ?: $this->guardFromRoute($request);
        if (! $guard || ! Auth::guard($guard)->check()) {
            return $next($request);
        }
        $activityKey = "last_activity_at.{$guard}";
        $lastActivity = (int) $request->session()->get($activityKey, 0);

        if (
            $lastActivity > 0
            && now()->timestamp - $lastActivity >= self::TIMEOUT_SECONDS
        ) {
            Auth::guard($guard)->logout();
            $request->session()->forget($activityKey);
            $request->session()->migrate(true);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi anda telah berakhir. Silakan masuk kembali.',
                    'guard' => $guard,
                ], 401);
            }
            return redirect()->route('home')->with(
                'warning',
                'Akun ' . ucfirst($guard) . ' otomatis keluar karena tidak ada aktivitas.'
            );
        }
        $request->session()->put($activityKey, now()->timestamp);
        return $next($request);
    }
    private function guardFromRoute(Request $request): ?string
    {
        foreach ($request->route()?->gatherMiddleware() ?? [] as $middleware) {
            if (! is_string($middleware) || ! str_starts_with($middleware, 'auth:')) {
                continue;
            }
            foreach (explode(',', substr($middleware, 5)) as $guard) {
                if (in_array($guard, self::GUARDS, true)) {
                    return $guard;
                }
            }
        }
        $routeName = (string) $request->route()?->getName();
        if ($routeName === 'dashboard' || str_starts_with($routeName, 'admin.')) {
            return User::ROLE_ADMIN;
        }
        if (str_starts_with($routeName, 'pegawai.')) {
            return User::ROLE_PEGAWAI;
        }
        if (str_starts_with($routeName, 'jamaah.')) {
            return User::ROLE_JAMAAH;
        }
        return null;
    }
}
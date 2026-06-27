<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InactivityLogout
{
    private const TIMEOUT_SECONDS = 15 * 60;

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        $lastActivity = (int) $request->session()->get('last_activity_at', 0);

        if ($lastActivity > 0 && now()->timestamp - $lastActivity >= self::TIMEOUT_SECONDS) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi berakhir karena tidak ada aktivitas selama 15 menit.',
                ], 401);
            }

            return redirect()->route('home')
                ->with('warning', 'Anda otomatis keluar karena tidak aktif selama 15 menit.');
        }

        $request->session()->put('last_activity_at', now()->timestamp);

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    private const ALLOWED_GUARDS = [
        User::ROLE_ADMIN,
        User::ROLE_PEGAWAI,
        User::ROLE_JAMAAH,
    ];
    public function adminLogin(Request $request): RedirectResponse
    {
        return $this->login(
            $request,
            User::ROLE_ADMIN,
            User::ROLE_ADMIN,
            'dashboard',
            'admin'
        );
    }
    public function staffLogin(Request $request): RedirectResponse
    {
        return $this->login(
            $request,
            User::ROLE_PEGAWAI,
            User::ROLE_PEGAWAI,
            'pegawai.dashboard',
            'pegawai'
        );
    }
    public function jamaahLogin(Request $request): RedirectResponse
    {
        return $this->login(
            $request,
            User::ROLE_JAMAAH,
            User::ROLE_JAMAAH,
            'jamaah.dashboard',
            'jamaah'
        );
    }
    public function adminLogout(Request $request): RedirectResponse|JsonResponse
    {
        return $this->logoutGuard($request, User::ROLE_ADMIN);
    }
    public function staffLogout(Request $request): RedirectResponse|JsonResponse
    {
        return $this->logoutGuard($request, User::ROLE_PEGAWAI);
    }
    public function jamaahLogout(Request $request): RedirectResponse|JsonResponse
    {
        return $this->logoutGuard($request, User::ROLE_JAMAAH);
    }
    /**
     * Route kompatibilitas untuk pemanggilan route('logout') lama.
     * Form baru sebaiknya memakai logout.admin, logout.pegawai, atau
     * logout.jamaah agar guard yang dikeluarkan selalu pasti.
     */
    public function logoutLegacy(Request $request): RedirectResponse|JsonResponse
    {
        $requestedGuard = trim((string) $request->input('guard'));
        if (
            in_array($requestedGuard, self::ALLOWED_GUARDS, true)
            && Auth::guard($requestedGuard)->check()
        ) {
            return $this->logoutGuard($request, $requestedGuard);
        }
        $refererGuard = $this->guardFromReferer($request);
        if ($refererGuard && Auth::guard($refererGuard)->check()) {
            return $this->logoutGuard($request, $refererGuard);
        }
        $activeGuards = collect(self::ALLOWED_GUARDS)
            ->filter(fn (string $guard): bool => Auth::guard($guard)->check())
            ->values();
        if ($activeGuards->count() === 1) {
            return $this->logoutGuard($request, $activeGuards->first());
        }
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $request->session()->forget('last_activity_at');
            $request->session()->migrate(true);
            return redirect()->route('home')->with(
                'success',
                'Sesi login lama berhasil dibersihkan.'
            );
        }
        return redirect()->route('home')->with(
            'warning',
            $activeGuards->isEmpty()
                ? 'Sesi login sudah berakhir.'
                : 'Pilih tombol keluar dari dashboard role yang ingin dikeluarkan.'
        );
    }
    private function login(
        Request $request,
        string $guard,
        string $role,
        string $route,
        string $label
    ): RedirectResponse {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'string'],
        ]);
        $authenticated = Auth::guard($guard)->attempt(
            $credentials + ['role' => $role],
            $request->boolean('remember')
        );
        if (! $authenticated) {
            return back()
                ->withErrors(['email' => "Email atau password {$label} salah."])
                ->onlyInput('email');
        }
        // Rotasi ID sesi tetapi pertahankan data guard role lain.
        $request->session()->regenerate();
        $request->session()->put(
            "last_activity_at.{$guard}",
            now()->timestamp
        );
        /** @var User $user */
        $user = Auth::guard($guard)->user();
        // Bersihkan sesi guard web lama setelah deployment versi multi-guard.
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $request->session()->forget('last_activity_at');
        }
        if (
            $guard === User::ROLE_JAMAAH
            && ! $user->hasVerifiedEmail()
        ) {
            return redirect()->route('verification.notice');
        }
        return redirect()->route($route);
    }
    private function logoutGuard(
        Request $request,
        string $guard
    ): RedirectResponse|JsonResponse {
        abort_unless(
            in_array($guard, self::ALLOWED_GUARDS, true),
            404
        );
        Auth::guard($guard)->logout();
        $request->session()->forget("last_activity_at.{$guard}");
        /*
         * Jangan invalidate() karena itu akan menghapus semua guard.
         * migrate(true) mengganti session ID sambil mempertahankan login
         * role lain yang masih aktif.
         */
        $request->session()->migrate(true);
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'logged_out',
                'guard' => $guard,
                'redirect' => route($this->loginRouteForGuard($guard)),
            ]);
        }
        return redirect()->route($this->loginRouteForGuard($guard))->with(
            'success',
            'Anda berhasil keluar dari akun ' . ucfirst($guard) . '.'
        );
    }

    private function loginRouteForGuard(string $guard): string
    {
        return match ($guard) {
            User::ROLE_ADMIN => 'login.admin',
            User::ROLE_PEGAWAI => 'login.staff',
            User::ROLE_JAMAAH => 'login.jamaah',
            default => 'login',
        };
    }

    private function guardFromReferer(Request $request): ?string
    {
        $path = parse_url(
            (string) $request->headers->get('referer'),
            PHP_URL_PATH
        );
        if (! is_string($path)) {
            return null;
        }
        if (str_starts_with($path, '/pegawai')) {
            return User::ROLE_PEGAWAI;
        }
        if (str_starts_with($path, '/jamaah')) {
            return User::ROLE_JAMAAH;
        }
        if ($path === '/dashboard' || str_starts_with($path, '/admin')) {
            return User::ROLE_ADMIN;
        }
        return null;
    }
}

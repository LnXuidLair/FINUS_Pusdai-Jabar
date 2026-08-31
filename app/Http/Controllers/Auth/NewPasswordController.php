<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    private const SESSION_KEY = 'password_recovery';

    public function create(Request $request): View|RedirectResponse
    {
        $portal = $this->portalFromSession($request);
        $recovery = $this->verifiedRecovery($request);

        if (! $recovery) {
            return redirect()->route('password.request', [
                'portal' => $portal,
            ])->withErrors([
                'recovery' => 'Sesi pemulihan password sudah berakhir. Silakan ulangi proses lupa password.',
            ]);
        }

        return view('auth.reset-password', [
            'portal' => $recovery['portal'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $portal = $this->portalFromSession($request);
        $recovery = $this->verifiedRecovery($request);

        if (! $recovery) {
            return redirect()->route('password.request', [
                'portal' => $portal,
            ])->withErrors([
                'recovery' => 'Sesi pemulihan password sudah berakhir. Silakan ulangi proses lupa password.',
            ]);
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        /** @var User|null $user */
        $user = User::query()
            ->whereKey($recovery['user_id'])
            ->where('role', $recovery['role'])
            ->first();

        if (! $user) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('password.request', [
                'portal' => $recovery['portal'],
            ])->withErrors([
                'recovery' => 'Akun untuk pemulihan password tidak ditemukan.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
            'password_changed_at' => now(),
        ]);

        // Reset password Pegawai selalu menghasilkan Recovery Code baru.
        if ($user->role === User::ROLE_PEGAWAI) {
            $user->rotateRecoveryCode();
        }

        $user->save();
        event(new PasswordReset($user));

        $request->session()->forget(self::SESSION_KEY);

        $loginRoute = match ($user->role) {
            User::ROLE_ADMIN => 'login.admin',
            User::ROLE_PEGAWAI => 'login.staff',
            default => 'login.jamaah',
        };

        return redirect()->route($loginRoute)
            ->with('status', 'password-updated');
    }

    private function verifiedRecovery(Request $request): ?array
    {
        $recovery = $request->session()->get(self::SESSION_KEY);

        if (! is_array($recovery)
            || empty($recovery['user_id'])
            || empty($recovery['role'])
            || empty($recovery['portal'])
            || ($recovery['verified'] ?? false) !== true
            || empty($recovery['reset_expires_at'])
        ) {
            return null;
        }

        if ((int) $recovery['reset_expires_at'] < now()->timestamp) {
            $request->session()->forget(self::SESSION_KEY);

            return null;
        }

        if (
            $recovery['role'] === User::ROLE_JAMAAH
            && ($recovery['otp_verified'] ?? false) !== true
        ) {
            return null;
        }

        return $recovery;
    }

    private function portalFromSession(Request $request): string
    {
        $recovery = $request->session()->get(self::SESSION_KEY);

        return is_array($recovery)
            ? (string) ($recovery['portal'] ?? 'jamaah')
            : 'jamaah';
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Kode verifikasi wajib diisi.',
            'code.digits' => 'Kode verifikasi harus 6 digit.',
        ]);

        /** @var User|null $user */
        $user = Auth::guard(User::ROLE_JAMAAH)->user();
        abort_unless($user, 401);

        if ($user->role !== User::ROLE_JAMAAH) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('jamaah.dashboard');
        }

        if (
            ! $user->email_verification_code
            || ! $user->email_verification_code_expires_at
            || now()->greaterThan($user->email_verification_code_expires_at)
        ) {
            return back()->withErrors([
                'code' => 'Kode verifikasi sudah kedaluwarsa. Silakan kirim ulang kode.',
            ]);
        }

        if (! Hash::check((string) $request->input('code'), $user->email_verification_code)) {
            return back()->withErrors([
                'code' => 'Kode verifikasi salah.',
            ]);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
        ])->save();

        event(new Verified($user));

        Auth::guard(User::ROLE_JAMAAH)->logout();
        $request->session()->forget('last_activity_at.jamaah');
        $request->session()->migrate(true);

        return redirect()
            ->route('login.jamaah')
            ->with('status', 'email-verified');
    }
}

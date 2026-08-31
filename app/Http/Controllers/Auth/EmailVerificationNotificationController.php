<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyCodeJamaah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::guard(User::ROLE_JAMAAH)->user();
        abort_unless($user, 401);

        if ($user->role !== User::ROLE_JAMAAH) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('jamaah.dashboard');
        }

        $kode = (string) random_int(100000, 999999);

        $user->forceFill([
            'email_verification_code' => Hash::make($kode),
            'email_verification_code_expires_at' => now()->addMinutes(5),
        ])->save();

        Mail::to($user->email)->send(
            new VerifyCodeJamaah($kode, $user->name)
        );

        return back()->with('status', 'verification-code-sent');
    }
}

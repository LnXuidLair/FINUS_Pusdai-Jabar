<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function __invoke(
        EmailVerificationRequest $request
    ): RedirectResponse {
        /** @var User|null $user */
        $user = Auth::guard(User::ROLE_JAMAAH)->user();
        abort_unless($user, 401);
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('jamaah.dashboard', ['verified' => 1]);
        }
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        return redirect()->route('jamaah.dashboard')
            ->with('verified', true);
    }
}
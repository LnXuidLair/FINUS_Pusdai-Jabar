<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::guard(User::ROLE_JAMAAH)->user();
        abort_unless($user, 401);
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('jamaah.dashboard');
        }
        $user->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    }
}
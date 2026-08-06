<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        /** @var User|null $user */
        $user = Auth::guard(User::ROLE_JAMAAH)->user();
        abort_unless($user, 401);
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('jamaah.dashboard');
        }
        return view('auth.verify-email');
    }
}
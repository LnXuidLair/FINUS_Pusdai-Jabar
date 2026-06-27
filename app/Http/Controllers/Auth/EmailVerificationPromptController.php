<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect($this->dashboardUrl($request));
        }

        return view('auth.verify-email');
    }

    private function dashboardUrl(Request $request): string
    {
        return match ($request->user()?->role) {
            'admin' => route('dashboard'),
            'pegawai' => route('pegawai.dashboard'),
            'jamaah' => route('jamaah.dashboard'),
            default => route('home'),
        };
    }
}

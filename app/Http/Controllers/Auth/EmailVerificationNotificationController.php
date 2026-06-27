<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect($this->dashboardUrl($request));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
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

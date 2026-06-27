<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->to($this->dashboardUrl($request).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect($this->dashboardUrl($request))->with('verified', true);
    }

    private function dashboardUrl(EmailVerificationRequest $request): string
    {
        return match ($request->user()?->role) {
            'admin' => route('dashboard'),
            'pegawai' => route('pegawai.dashboard'),
            'jamaah' => route('jamaah.dashboard'),
            default => route('home'),
        };
    }
}

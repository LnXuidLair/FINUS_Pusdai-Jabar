<?php

use App\Mail\VerifyCodeJamaah;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::middleware('guest')->group(function () {
    Route::view('/forgot-password', 'auth.forgot-password')
        ->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'password-reset-link-sent')
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('/reset-password/{token}', function (string $token, Request $request) {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'password_changed_at' => now(),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors([
                'email' => __($status),
            ]);
        }

        $user = User::where('email', strtolower($request->email))->first();

        $loginRoute = match ($user?->role) {
            User::ROLE_ADMIN => 'login.admin',
            User::ROLE_PEGAWAI => 'login.staff',
            User::ROLE_JAMAAH => 'login.jamaah',
            default => 'login.jamaah',
        };

        return redirect()->route($loginRoute)
            ->with('status', 'password-updated');
    })->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::view('/verify-email', 'auth.verify-email')
        ->name('verification.notice');

    Route::post('/verify-email/code', function (Request $request) {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Kode verifikasi wajib diisi.',
            'code.digits' => 'Kode verifikasi harus 6 digit.',
        ]);

        $user = $request->user();

        if ($user->role !== User::ROLE_JAMAAH) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.jamaah')
                ->with('status', 'email-verified');
        }

        if (
            ! $user->email_verification_code ||
            ! $user->email_verification_code_expires_at ||
            now()->greaterThan(\Carbon\Carbon::parse($user->email_verification_code_expires_at))
        ) {
            return back()->withErrors([
                'code' => 'Kode verifikasi sudah kedaluwarsa. Silakan kirim ulang kode.',
            ]);
        }

        if (! Hash::check($request->input('code'), $user->email_verification_code)) {
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

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.jamaah')
            ->with('status', 'email-verified');
    })->middleware('throttle:5,1')->name('verification.code.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $user = $request->user();

        if ($user->role !== User::ROLE_JAMAAH) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.jamaah')
                ->with('status', 'email-verified');
        }

        $kode = (string) random_int(100000, 999999);

        $user->forceFill([
            'email_verification_code' => Hash::make($kode),
            'email_verification_code_expires_at' => now()->addMinutes(5),
        ])->save();

        Mail::to($user->email)->send(new VerifyCodeJamaah($kode));

        return back()->with('status', 'verification-code-sent');
    })->middleware('throttle:3,1')->name('verification.send');

    Route::post('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});
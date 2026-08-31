<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $guard = (string) $request->route('auth_guard');

        abort_unless(in_array($guard, [
            User::ROLE_ADMIN,
            User::ROLE_PEGAWAI,
            User::ROLE_JAMAAH,
        ], true), 403);

        /** @var User|null $user */
        $user = Auth::guard($guard)->user();
        abort_unless($user, 401);

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (! Hash::check((string) $validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password lama tidak sesuai.',
            ]);
        }

        if (Hash::check((string) $validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password baru harus berbeda dari password lama.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
            'password_changed_at' => now(),
        ]);

        // Recovery Code Pegawai wajib berubah setiap password berubah.
        if ($user->role === User::ROLE_PEGAWAI) {
            $user->rotateRecoveryCode();
        }

        $user->save();

        $settingsRoute = match ($guard) {
            User::ROLE_ADMIN => 'admin.settings',
            User::ROLE_PEGAWAI => 'pegawai.settings',
            User::ROLE_JAMAAH => 'jamaah.settings',
        };

        return redirect()
            ->route($settingsRoute)
            ->with('status', 'password-updated');
    }
}

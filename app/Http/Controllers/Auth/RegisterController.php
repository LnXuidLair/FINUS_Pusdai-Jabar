<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function registerAdmin(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Cache::lock('finus-register-admin', 10)->block(5, function () use ($validated) {
            if (User::where('role', User::ROLE_ADMIN)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Akun admin sudah tersedia. FINUS hanya mengizinkan satu admin.',
                ]);
            }

            User::create([
                'name' => 'Admin',
                'email' => strtolower($validated['email']),
                'email_verified_at' => now(),
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_ADMIN,
            ]);
        });

        return redirect()->route('login.admin')
            ->with('success', 'Akun admin berhasil dibuat.');
    }

    public function registerStaff(Request $request)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $pegawai = Pegawai::where('nip', $validated['nip'])->first();

        if (!$pegawai) {
            return back()->withErrors(['nip' => 'NIP tidak ditemukan.']);
        }

        if ($pegawai->is_verified || User::where('email', $pegawai->email)->exists()) {
            return back()->withErrors(['nip' => 'Akun pegawai sudah aktif.']);
        }

        User::create([
            'name' => $validated['name'],
            'email' => strtolower($pegawai->email),
            'email_verified_at' => now(),
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_PEGAWAI,
        ]);

        $pegawai->update(['is_verified' => true]);

        return redirect()->route('login.staff');
    }

    public function registerJamaah(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                'regex:/^[A-Z0-9._%+\-]+@gmail\.com$/i',
                'unique:users,email',
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.regex' => 'Jamaah wajib menggunakan alamat Gmail (@gmail.com).',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_JAMAAH,
        ]);

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('last_activity_at', now()->timestamp);

        return redirect()->route('verification.notice')
            ->with('status', 'verification-link-sent');
    }
}

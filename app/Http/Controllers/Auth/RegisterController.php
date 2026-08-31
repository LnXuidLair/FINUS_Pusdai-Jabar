<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyCodeJamaah;
use App\Models\User;
use App\Rules\PublicEmailDomain;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    private const ADMIN_EMAIL = 'admin@pusdai.finus.id';
    private const ADMIN_RECOVERY_MIN_LENGTH = 16;
    private const ADMIN_RECOVERY_MAX_LENGTH = 64;

    /**
     * Membuat kandidat Recovery Code saat proses pembuatan akun Admin.
     * Kode belum disimpan sebelum formulir registrasi Admin dikirim.
     */
    public function generateAdminRecoveryCode(): JsonResponse
    {
        abort_if(
            User::where('role', User::ROLE_ADMIN)->exists(),
            403,
            'Akun Admin sudah tersedia.'
        );

        return response()->json([
            'recovery_code' => User::generateRecoveryCode(),
        ]);
    }

    public function registerAdmin(Request $request)
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'recovery_code' => User::normalizeRecoveryCode(
                (string) $request->input('recovery_code')
            ),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'recovery_code' => [
                'required',
                'string',
                'min:' . self::ADMIN_RECOVERY_MIN_LENGTH,
                'max:' . self::ADMIN_RECOVERY_MAX_LENGTH,
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
                'not_regex:/\\s/',
            ],
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'recovery_code.required' => 'Recovery Code Admin wajib dibuat sebelum akun disimpan.',
            'recovery_code.min' => 'Recovery Code Admin minimal ' . self::ADMIN_RECOVERY_MIN_LENGTH . ' karakter.',
            'recovery_code.max' => 'Recovery Code Admin maksimal ' . self::ADMIN_RECOVERY_MAX_LENGTH . ' karakter.',
            'recovery_code.regex' => 'Recovery Code harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
            'recovery_code.not_regex' => 'Recovery Code tidak boleh mengandung spasi.',
        ]);

        $name = trim((string) $validated['name']);
        $email = self::ADMIN_EMAIL;
        $recoveryCode = User::normalizeRecoveryCode(
            (string) $validated['recovery_code']
        );

        Cache::lock('finus-register-admin', 10)->block(
            5,
            function () use ($name, $email, $recoveryCode, $validated): void {
                if (User::where('role', User::ROLE_ADMIN)->exists()) {
                    throw ValidationException::withMessages([
                        'name' => 'Akun admin sudah tersedia. FINUS hanya mengizinkan satu admin.',
                    ]);
                }

                if (User::where('email', $email)->exists()) {
                    throw ValidationException::withMessages([
                        'name' => 'Email admin FINUS sudah digunakan.',
                    ]);
                }

                User::create([
                    'name' => $name,
                    // Email Admin tidak dibentuk dari nama. Identitas login tetap.
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => Hash::make($validated['password']),
                    // Disimpan terenkripsi oleh cast User::recovery_code.
                    'recovery_code' => $recoveryCode,
                    'role' => User::ROLE_ADMIN,
                ]);
            }
        );

        return redirect()
            ->route('login.admin')
            ->with([
                'success' => 'Akun admin berhasil dibuat.',
                'admin_email' => $email,
            ]);
    }

    public function registerJamaah(Request $request)
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'bail',
                'required',
                'email:rfc',
                'max:255',
                'unique:users,email',
                new PublicEmailDomain,
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_JAMAAH,
        ]);

        $this->kirimKodeVerifikasiJamaah($user);

        Auth::guard(User::ROLE_JAMAAH)->login($user);
        $request->session()->regenerate();
        $request->session()->put(
            'last_activity_at.' . User::ROLE_JAMAAH,
            now()->timestamp
        );

        return redirect()->route('verification.notice')
            ->with('status', 'verification-code-sent');
    }

    private function kirimKodeVerifikasiJamaah(User $user): void
    {
        $kode = (string) random_int(100000, 999999);

        $user->forceFill([
            'email_verification_code' => Hash::make($kode),
            'email_verification_code_expires_at' => now()->addMinutes(5),
        ])->save();

        Mail::to($user->email)->send(
            new VerifyCodeJamaah($kode, $user->name)
        );
    }
}

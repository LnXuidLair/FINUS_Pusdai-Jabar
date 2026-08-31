<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class AccountController extends Controller
{
    private const ADMIN_RECOVERY_MIN_LENGTH = 16;
    private const ADMIN_RECOVERY_MAX_LENGTH = 64;

    public function adminProfile(): View
    {
        return $this->profile(User::ROLE_ADMIN);
    }

    /**
     * Nama Admin boleh diubah dari Profil, tetapi email Admin tetap
     * admin@pusdai.finus.id dan tidak ikut berubah.
     */
    public function updateAdminProfile(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::guard(User::ROLE_ADMIN)->user();
        abort_unless($user && $user->isAdmin(), 403);

        $request->merge([
            'name' => trim((string) $request->input('name')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama Admin wajib diisi.',
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'email' => 'admin@pusdai.finus.id',
        ])->save();

        return redirect()
            ->route('admin.profile')
            ->with('success', 'Nama Admin berhasil diperbarui. Email login tetap admin@pusdai.finus.id.');
    }

    public function adminSettings(): View
    {
        return $this->settings(User::ROLE_ADMIN);
    }

    public function pegawaiProfile(): View
    {
        return $this->profile(User::ROLE_PEGAWAI);
    }

    public function pegawaiSettings(): View
    {
        return $this->settings(User::ROLE_PEGAWAI);
    }

    public function jamaahProfile(): View
    {
        return $this->profile(User::ROLE_JAMAAH);
    }

    public function jamaahSettings(): View
    {
        return $this->settings(User::ROLE_JAMAAH);
    }

    /**
     * Membuat kandidat Recovery Code baru untuk Admin yang sudah login.
     * Recovery Code pertama wajib dibuat saat registrasi akun Admin.
     * Endpoint ini dipakai bila Admin ingin mengganti kode melalui Pengaturan.
     */
    public function generateAdminRecoveryCode(): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::guard(User::ROLE_ADMIN)->user();
        abort_unless($user && $user->isAdmin(), 403);

        return response()->json([
            'recovery_code' => User::generateRecoveryCode(),
        ]);
    }

    /**
     * Admin boleh mengganti Recovery Code dengan hasil Generate Code atau
     * kode manual. Penyimpanan tetap memakai encrypted cast User::recovery_code.
     */
    public function updateAdminRecoveryCode(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::guard(User::ROLE_ADMIN)->user();
        abort_unless($user && $user->isAdmin(), 403);

        $request->merge([
            'recovery_code' => trim((string) $request->input('recovery_code')),
        ]);

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
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
            'current_password.required' => 'Password saat ini wajib diisi.',
            'recovery_code.required' => 'Recovery Code Admin wajib diisi.',
            'recovery_code.min' => 'Recovery Code Admin minimal ' . self::ADMIN_RECOVERY_MIN_LENGTH . ' karakter.',
            'recovery_code.max' => 'Recovery Code Admin maksimal ' . self::ADMIN_RECOVERY_MAX_LENGTH . ' karakter.',
            'recovery_code.regex' => 'Recovery Code harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
            'recovery_code.not_regex' => 'Recovery Code tidak boleh mengandung spasi.',
        ]);

        if (! Hash::check((string) $validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $newCode = User::normalizeRecoveryCode((string) $validated['recovery_code']);
        $currentCode = '';

        try {
            $currentCode = User::normalizeRecoveryCode((string) ($user->recovery_code ?? ''));
        } catch (Throwable) {
            $currentCode = '';
        }

        if ($currentCode !== '' && hash_equals($currentCode, $newCode)) {
            throw ValidationException::withMessages([
                'recovery_code' => 'Recovery Code baru harus berbeda dari Recovery Code yang sedang digunakan.',
            ]);
        }

        $user->forceFill([
            'recovery_code' => $newCode,
        ])->save();

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Recovery Code Admin berhasil diganti. Simpan kode terbaru di tempat yang aman.');
    }

    private function profile(string $guard): View
    {
        /** @var User|null $user */
        $user = Auth::guard($guard)->user();
        abort_unless($user && $user->role === $guard, 403);

        $pegawai = $guard === User::ROLE_PEGAWAI
            ? $user->pegawai
            : null;

        return view('account.profile', [
            'user' => $user,
            'pegawai' => $pegawai,
            ...$this->navigationFor($guard),
        ]);
    }

    private function settings(string $guard): View
    {
        /** @var User|null $user */
        $user = Auth::guard($guard)->user();
        abort_unless($user && $user->role === $guard, 403);

        $pegawai = $guard === User::ROLE_PEGAWAI
            ? $user->pegawai
            : null;

        return view('account.settings', [
            'user' => $user,
            'pegawai' => $pegawai,
            'adminRecoveryMinLength' => self::ADMIN_RECOVERY_MIN_LENGTH,
            'adminRecoveryMaxLength' => self::ADMIN_RECOVERY_MAX_LENGTH,
            ...$this->navigationFor($guard),
        ]);
    }

    private function navigationFor(string $guard): array
    {
        return match ($guard) {
            User::ROLE_ADMIN => [
                'portalLabel' => 'Admin',
                'profileRoute' => 'admin.profile',
                'settingsRoute' => 'admin.settings',
                'passwordRoute' => 'admin.password.edit',
                'dashboardRoute' => 'dashboard',
            ],
            User::ROLE_PEGAWAI => [
                'portalLabel' => 'Pegawai',
                'profileRoute' => 'pegawai.profile',
                'settingsRoute' => 'pegawai.settings',
                'passwordRoute' => 'pegawai.password.edit',
                'dashboardRoute' => 'pegawai.dashboard',
            ],
            User::ROLE_JAMAAH => [
                'portalLabel' => 'Jamaah',
                'profileRoute' => 'jamaah.profile',
                'settingsRoute' => 'jamaah.settings',
                'passwordRoute' => 'jamaah.password.edit',
                'dashboardRoute' => 'jamaah.dashboard',
            ],
            default => abort(404),
        };
    }
}

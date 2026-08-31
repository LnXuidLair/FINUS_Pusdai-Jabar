<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StaffActivationController extends Controller
{
    private const SESSION_KEY = 'staff_activation';
    private const EXPIRES_MINUTES = 10;

    public function create(): View
    {
        return view('auth.verify-staff');
    }

    public function verify(Request $request): View|RedirectResponse
    {
        $request->merge([
            'name' => trim((string) ($request->input('name') ?: $request->input('nama_pegawai'))),
            'nip' => trim((string) $request->input('nip')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:100'],
        ], [
            'name.required' => 'Nama pegawai wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
        ]);

        $pegawai = Pegawai::query()
            ->where('nip', $validated['nip'])
            ->first();

        if (
            ! $pegawai
            || $this->normalizeName($pegawai->nama_pegawai) !== $this->normalizeName($validated['name'])
        ) {
            throw ValidationException::withMessages([
                'nip' => 'Nama atau NIP tidak sesuai dengan data pegawai.',
            ]);
        }

        if ($pegawai->is_verified) {
            throw ValidationException::withMessages([
                'nip' => 'Akun pegawai sudah aktif. Silakan login.',
            ]);
        }

        $existingUser = User::query()->where('email', strtolower($pegawai->email))->first();

        if ($existingUser && $existingUser->role !== User::ROLE_PEGAWAI) {
            throw ValidationException::withMessages([
                'nip' => 'Email pegawai digunakan oleh akun dengan role lain.',
            ]);
        }

        $request->session()->put(self::SESSION_KEY, [
            'pegawai_id' => $pegawai->id,
            'expires_at' => now()->addMinutes(self::EXPIRES_MINUTES)->timestamp,
        ]);

        return view('auth.verify-staff', [
            'verifiedPegawai' => $pegawai,
        ]);
    }

    public function createPassword(Request $request): View|RedirectResponse
    {
        $pegawai = $this->verifiedPegawaiFromSession($request);

        if (! $pegawai) {
            return redirect()->route('register.staff')
                ->withErrors([
                    'nip' => 'Sesi verifikasi pegawai berakhir. Silakan verifikasi ulang.',
                ]);
        }

        return view('auth.activate-staff', [
            'pegawai' => $pegawai,
            'email' => $pegawai->email,
        ]);
    }

    public function storePassword(Request $request): RedirectResponse
    {
        $pegawai = $this->verifiedPegawaiFromSession($request);

        if (! $pegawai) {
            return redirect()->route('register.staff')
                ->withErrors([
                    'nip' => 'Sesi verifikasi pegawai berakhir. Silakan verifikasi ulang.',
                ]);
        }

        if ($pegawai->is_verified) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('login.staff')
                ->withErrors([
                    'email' => 'Akun pegawai sudah aktif. Silakan login.',
                ]);
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $recoveryCode = null;
        $email = strtolower(trim((string) $pegawai->email));

        DB::transaction(function () use ($pegawai, $email, $validated, &$recoveryCode): void {
            $user = User::query()->where('email', $email)->first();

            if ($user && $user->role !== User::ROLE_PEGAWAI) {
                throw ValidationException::withMessages([
                    'password' => 'Email pegawai digunakan oleh akun dengan role lain.',
                ]);
            }

            // Kompatibilitas untuk data Pegawai lama yang belum memiliki User.
            if (! $user) {
                $user = new User([
                    'name' => $pegawai->nama_pegawai,
                    'email' => $email,
                    'password' => Hash::make(Str::random(64)),
                    'role' => User::ROLE_PEGAWAI,
                ]);
                $user->rotateRecoveryCode();
            }

            if (! $user->recovery_code) {
                $user->rotateRecoveryCode();
            }

            $user->forceFill([
                'name' => $pegawai->nama_pegawai,
                'email_verified_at' => now(),
                'password' => Hash::make($validated['password']),
                'password_changed_at' => now(),
            ])->save();

            $pegawai->forceFill([
                'is_verified' => true,
            ])->save();

            $recoveryCode = (string) $user->recovery_code;
        });

        $request->session()->forget(self::SESSION_KEY);

        return redirect()
            ->route('register.staff.success')
            ->with('staff_activation_success', [
                'email' => $email,
                'recovery_code' => $recoveryCode,
            ]);
    }

    public function success(Request $request): View|RedirectResponse
    {
        $activation = $request->session()->get('staff_activation_success');

        if (! is_array($activation) || empty($activation['recovery_code'])) {
            return redirect()->route('login.staff');
        }

        return view('auth.staff-activation-success', compact('activation'));
    }

    private function verifiedPegawaiFromSession(Request $request): ?Pegawai
    {
        $data = $request->session()->get(self::SESSION_KEY);

        if (! is_array($data) || empty($data['pegawai_id']) || empty($data['expires_at'])) {
            return null;
        }

        if ((int) $data['expires_at'] < now()->timestamp) {
            $request->session()->forget(self::SESSION_KEY);

            return null;
        }

        return Pegawai::query()->find($data['pegawai_id']);
    }

    private function normalizeName(string $name): string
    {
        return preg_replace('/\s+/', ' ', mb_strtolower(trim($name)));
    }
}

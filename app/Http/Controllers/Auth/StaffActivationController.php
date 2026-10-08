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

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pegawai' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:100'],
        ], [
            'nama_pegawai.required' => 'Nama pegawai wajib diisi.',
            'nip.required' => 'NIP wajib diisi.',
        ]);

        $nama = Str::lower(trim($validated['nama_pegawai']));
        $nip = trim($validated['nip']);

        $pegawai = Pegawai::query()
            ->where('nip', $nip)
            ->whereRaw('LOWER(TRIM(nama_pegawai)) = ?', [$nama])
            ->first();

        if (! $pegawai) {
            throw ValidationException::withMessages([
                'nip' => 'Data pegawai tidak ditemukan. Pastikan nama dan NIP sesuai data dari admin.',
            ]);
        }

        if ($pegawai->is_verified) {
            throw ValidationException::withMessages([
                'nip' => 'Akun pegawai sudah aktif. Silakan login.',
            ]);
        }

        $email = $this->staffEmailFor($pegawai);

        session([
            self::SESSION_KEY => [
                'pegawai_id' => $pegawai->getKey(),
                'expires_at' => now()->addMinutes(self::EXPIRES_MINUTES)->timestamp,
            ],
        ]);

        return back()
            ->with('verified_staff', [
                'nama_pegawai' => $pegawai->nama_pegawai,
                'nip' => $pegawai->nip,
                'jabatan' => $pegawai->jabatan ?: '-',
                'email' => $email,
                'message' => 'Verify Success',
            ])
            ->with('activation_next_url', route('register.staff'));
    }

    public function createPassword(Request $request): View|RedirectResponse
    {
        $pegawai = $this->verifiedPegawaiFromSession($request);

        if (! $pegawai) {
            return redirect()
                ->route('register.staff')
                ->withErrors(['nip' => 'Sesi verifikasi pegawai sudah habis. Silakan verifikasi ulang.']);
        }

        if ($pegawai->is_verified) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()
                ->route('login.staff')
                ->withErrors(['email' => 'Akun pegawai sudah aktif. Silakan login.']);
        }

        return view('auth.activate-staff', [
            'pegawai' => $pegawai,
            'email' => $this->staffEmailFor($pegawai),
        ]);
    }

    public function storePassword(Request $request): RedirectResponse
    {
        $pegawai = $this->verifiedPegawaiFromSession($request);

        if (! $pegawai) {
            return redirect()
                ->route('register.staff')
                ->withErrors(['nip' => 'Sesi verifikasi pegawai sudah habis. Silakan verifikasi ulang.']);
        }

        if ($pegawai->is_verified) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()
                ->route('login.staff')
                ->withErrors(['email' => 'Akun pegawai sudah aktif. Silakan login.']);
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $email = $this->staffEmailFor($pegawai);

        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser && $existingUser->role !== 'pegawai') {
            throw ValidationException::withMessages([
                'password' => 'Email pegawai sudah dipakai oleh role lain.',
            ]);
        }

        $recoveryCode = '';

        DB::transaction(function () use ($pegawai, $email, $validated, &$recoveryCode): void {
            $user = User::query()->where('email', $email)->first();

            // Menangani data pegawai lama yang belum memiliki akun User.
            if (! $user) {
                $user = new User([
                    'name' => $pegawai->nama_pegawai,
                    'organization_id' => $pegawai->organization_id,
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
                'organization_id' => $pegawai->organization_id,
                'email_verified_at' => now(),
                'password' => Hash::make($validated['password']),
                'password_changed_at' => now(),
            ])->save();

            $pegawai->forceFill([
                'email' => $email,
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
        $activation = $request->session()->pull('staff_activation_success');

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

    private function staffEmailFor(Pegawai $pegawai): string
    {
        $staffDomain = $this->staffDomain($pegawai);
        $currentEmail = strtolower(trim((string) $pegawai->email));

        if ($currentEmail !== '' && str_ends_with($currentEmail, '@'.$staffDomain)) {
            return $currentEmail;
        }

        $email = $this->makeStaffEmail(
            $pegawai->nama_pegawai,
            $pegawai->nip,
            $staffDomain,
            $pegawai->id,
            $currentEmail ?: null
        );

        $pegawai->forceFill([
            'email' => $email,
        ])->save();

        return $email;
    }

    private function staffDomain(Pegawai $pegawai): string
    {
        $admin = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->when(
                $pegawai->organization_id,
                fn ($query) => $query->where('organization_id', $pegawai->organization_id)
            )
            ->firstOrFail();

        $adminEmail = strtolower(trim((string) $admin->email));

        if (! preg_match('/^admin@([a-z0-9]+)\.finus\.id$/', $adminEmail, $matches)) {
            throw ValidationException::withMessages([
                'email' => 'Email Admin tidak sesuai format FINUS.',
            ]);
        }

        return 'staff'.$matches[1].'.finus.id';
    }

    private function makeStaffEmail(
        string $name,
        string $nip,
        string $domain,
        ?int $ignorePegawaiId = null,
        ?string $allowedUserEmail = null
    ): string {
        $parts = collect(preg_split('/\s+/', trim($name)) ?: [])
            ->filter()
            ->map(fn ($part) => Str::of($part)
                ->ascii()
                ->lower()
                ->replaceMatches('/[^a-z0-9]/', '')
                ->toString()
            )
            ->filter()
            ->take(2)
            ->values();

        $selectedName = $parts->implode('');

        if ($selectedName === '') {
            $selectedName = 'pegawai';
        }

        $nipDigits = preg_replace('/\D+/', '', $nip);
        $nipSuffix = substr($nipDigits, -4);

        if ($nipSuffix === '') {
            $nipSuffix = (string) random_int(1000, 9999);
        }

        $email = strtolower($selectedName.$nipSuffix.'@'.$domain);

        if ($this->emailAlreadyUsed($email, $ignorePegawaiId, $allowedUserEmail)) {
            $email = strtolower($selectedName.$nipSuffix.random_int(10, 99).'@'.$domain);
        }

        return $email;
    }

    private function emailAlreadyUsed(string $email, ?int $ignorePegawaiId = null, ?string $allowedUserEmail = null): bool
    {
        $usedByPegawai = Pegawai::where('email', $email)
            ->when($ignorePegawaiId, fn ($query) => $query->where('id', '!=', $ignorePegawaiId))
            ->exists();

        if ($usedByPegawai) {
            return true;
        }

        return User::where('email', $email)
            ->when($allowedUserEmail, fn ($query) => $query->where('email', '!=', $allowedUserEmail))
            ->exists();
    }
}
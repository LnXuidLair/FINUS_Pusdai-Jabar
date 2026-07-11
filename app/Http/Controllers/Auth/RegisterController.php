<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyCodeJamaah;
use App\Models\Pegawai;
use App\Models\User;
use App\Rules\PublicEmailDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    private const STAFF_ACTIVATION_SESSION = 'staff_activation';
    private const STAFF_ACTIVATION_MINUTES = 10;
    private const ADMIN_DOMAIN = 'adminfinuspusdai.org';
    private const STAFF_DOMAIN = 'stafffinuspusdai.org';

    public function registerAdmin(Request $request)
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (empty($validated['name']) && empty($validated['email'])) {
            throw ValidationException::withMessages([
                'name' => 'Nama admin wajib diisi.',
            ]);
        }

        $name = $validated['name'] ?: 'Admin';
        $email = ! empty($validated['email'])
            ? strtolower($validated['email'])
            : $this->makeInstitutionalEmail($name, self::ADMIN_DOMAIN);

        Cache::lock('finus-register-admin', 10)->block(5, function () use ($name, $email, $validated) {
            if (User::where('role', User::ROLE_ADMIN)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Akun admin sudah tersedia. FINUS hanya mengizinkan satu admin.',
                ]);
            }

            if (User::where('email', $email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Email admin sudah digunakan.',
                ]);
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_ADMIN,
            ]);
        });

        return redirect()->route('login.admin')
            ->with('success', 'Akun admin berhasil dibuat. Silakan login.');
    }

    public function showStaffActivation()
    {
        return view('auth.verify-staff');
    }

    public function verifyStaff(Request $request)
    {
        $request->merge([
            'nip' => trim((string) $request->input('nip')),
            'name' => trim((string) ($request->input('name') ?: $request->input('nama_pegawai'))),
        ]);

        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'name.required' => 'Nama pegawai wajib diisi.',
        ]);

        $pegawai = Pegawai::where('nip', $validated['nip'])->first();

        if (
            ! $pegawai ||
            $this->normalizeName($pegawai->nama_pegawai) !== $this->normalizeName($validated['name'])
        ) {
            throw ValidationException::withMessages([
                'nip' => 'Nama atau NIP tidak sesuai dengan data pegawai.',
            ]);
        }

        $email = $this->staffEmailFor($pegawai);

        if ($pegawai->is_verified || User::where('email', strtolower($email))->exists()) {
            throw ValidationException::withMessages([
                'nip' => 'Akun pegawai sudah aktif. Silakan login.',
            ]);
        }

        $request->session()->put(self::STAFF_ACTIVATION_SESSION, [
            'pegawai_id' => $pegawai->id,
            'expires_at' => now()->addMinutes(self::STAFF_ACTIVATION_MINUTES)->timestamp,
        ]);

        return redirect()->route('register.staff.account')
            ->with('staff_verified', [
                'nama' => $pegawai->nama_pegawai,
                'nip' => $pegawai->nip,
                'jabatan' => $pegawai->jabatan,
                'email' => $email,
            ]);
    }

    public function showStaffAccountRegistration(Request $request)
    {
        $pegawai = $this->getStaffActivationPegawai($request);

        if (! $pegawai) {
            return redirect()->route('register.staff')
                ->withErrors(['nip' => 'Sesi verifikasi pegawai berakhir. Silakan verifikasi ulang.']);
        }

        return view('auth.activate-staff', [
            'pegawai' => $pegawai,
            'email' => $this->staffEmailFor($pegawai),
        ]);
    }

    public function registerStaff(Request $request)
    {
        $pegawai = $this->getStaffActivationPegawai($request);

        if (! $pegawai) {
            return redirect()->route('register.staff')
                ->withErrors(['nip' => 'Silakan verifikasi nama dan NIP terlebih dahulu.']);
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = $this->staffEmailFor($pegawai);

        if ($pegawai->is_verified || User::where('email', strtolower($email))->exists()) {
            $request->session()->forget(self::STAFF_ACTIVATION_SESSION);

            return redirect()->route('login.staff')
                ->withErrors(['email' => 'Akun pegawai sudah aktif. Silakan login.']);
        }

        User::create([
            'name' => $pegawai->nama_pegawai,
            'email' => strtolower($email),
            'email_verified_at' => now(),
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_PEGAWAI,
        ]);

        $pegawai->update([
            'email' => strtolower($email),
            'is_verified' => true,
        ]);

        $request->session()->forget(self::STAFF_ACTIVATION_SESSION);

        return redirect()->route('login.staff')
            ->with('account_activated', true);
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

        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->put('last_activity_at', now()->timestamp);

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

        Mail::to($user->email)->send(new VerifyCodeJamaah($kode, $user->name));
    }

    private function getStaffActivationPegawai(Request $request): ?Pegawai
    {
        $activation = $request->session()->get(self::STAFF_ACTIVATION_SESSION);

        if (! is_array($activation)) {
            return null;
        }

        if (($activation['expires_at'] ?? 0) < now()->timestamp) {
            $request->session()->forget(self::STAFF_ACTIVATION_SESSION);

            return null;
        }

        return Pegawai::find($activation['pegawai_id'] ?? null);
    }

    private function staffEmailFor(Pegawai $pegawai): string
    {
        $currentEmail = strtolower(trim((string) $pegawai->email));

        if ($currentEmail !== '' && str_ends_with($currentEmail, '@' . self::STAFF_DOMAIN)) {
            return $currentEmail;
        }

        $email = $this->makeStaffEmail(
            $pegawai->nama_pegawai,
            $pegawai->nip,
            self::STAFF_DOMAIN,
            $pegawai->id,
            $currentEmail ?: null
        );

        $pegawai->forceFill([
            'email' => $email,
        ])->save();

        return $email;
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

        $email = strtolower($selectedName . $nipSuffix . '@' . $domain);

        if ($this->emailAlreadyUsed($email, $ignorePegawaiId, $allowedUserEmail)) {
            $email = strtolower($selectedName . $nipSuffix . random_int(10, 99) . '@' . $domain);
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

    private function normalizeName(string $name): string
    {
        return preg_replace('/\s+/', ' ', mb_strtolower(trim($name)));
    }

    private function makeInstitutionalEmail(string $name, string $domain): string
    {
        $local = Str::slug($name, '.');

        if ($local === '') {
            $local = 'admin';
        }

        return strtolower($local . '@' . $domain);
    }
}
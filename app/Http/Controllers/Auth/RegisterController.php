<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    private const STAFF_ACTIVATION_SESSION = 'staff_activation';
    private const STAFF_ACTIVATION_MINUTES = 10;

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
        $email = !empty($validated['email'])
            ? strtolower($validated['email'])
            : $this->makeInstitutionalEmail($name, 'AdminFinusPusdai.ac.id');

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

        if (empty(trim((string) $pegawai->email))) {
            throw ValidationException::withMessages([
                'nip' => 'Email pegawai belum ditentukan oleh admin.',
            ]);
        }

        if ($pegawai->is_verified || User::where('email', strtolower($pegawai->email))->exists()) {
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
            ]);
    }

    public function showStaffAccountRegistration(Request $request)
    {
        $pegawai = $this->getStaffActivationPegawai($request);

        if (! $pegawai) {
            return redirect()->route('register.staff')
                ->withErrors(['nip' => 'Sesi verifikasi pegawai berakhir. Silakan verifikasi ulang.']);
        }

        return view('auth.activate-staff', compact('pegawai'));
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

        if ($pegawai->is_verified || User::where('email', strtolower($pegawai->email))->exists()) {
            $request->session()->forget(self::STAFF_ACTIVATION_SESSION);

            return redirect()->route('login.staff')
                ->withErrors(['email' => 'Akun pegawai sudah aktif. Silakan login.']);
        }

        User::create([
            'name' => $pegawai->nama_pegawai,
            'email' => strtolower($pegawai->email),
            'email_verified_at' => now(),
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_PEGAWAI,
        ]);

        $pegawai->update([
            'is_verified' => true,
        ]);

        $request->session()->forget(self::STAFF_ACTIVATION_SESSION);

        return redirect()->route('login.staff')
            ->with('account_activated', true);
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
            'email_verified_at' => now(),
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_JAMAAH,
        ]);

        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->put('last_activity_at', now()->timestamp);

        return redirect()->route('jamaah.dashboard')
            ->with('success', 'Akun jamaah berhasil dibuat.');
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

        return $local . '@' . $domain;
    }
}
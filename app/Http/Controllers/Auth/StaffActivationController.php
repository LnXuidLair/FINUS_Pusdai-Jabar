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
    private const STAFF_DOMAIN = 'StaffFinusPusdai.ac.id';

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
                ->route('staff.verify')
                ->withErrors(['nip' => 'Sesi verifikasi pegawai sudah habis. Silakan verifikasi ulang.']);
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
                ->route('staff.verify')
                ->withErrors(['nip' => 'Sesi verifikasi pegawai sudah habis. Silakan verifikasi ulang.']);
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

        DB::transaction(function () use ($pegawai, $email, $validated): void {
            User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $pegawai->nama_pegawai,
                    'password' => Hash::make($validated['password']),
                    'role' => 'pegawai',
                    'email_verified_at' => now(),
                ]
            );

            $pegawai->forceFill([
                'email' => $email,
                'is_verified' => true,
            ])->save();
        });

        $request->session()->forget(self::SESSION_KEY);

        return redirect()
            ->route('login.staff')
            ->with('account_activated', true)
            ->with('status', 'Account Activated');
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
        if (is_string($pegawai->email) && trim($pegawai->email) !== '') {
            return trim($pegawai->email);
        }

        return $this->makeOrganizationEmail($pegawai->nama_pegawai, self::STAFF_DOMAIN);
    }

    private function makeOrganizationEmail(string $name, string $domain): string
    {
        $localPart = Str::of($name)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '.')
            ->trim('.')
            ->toString();

        if ($localPart === '') {
            $localPart = 'pegawai';
        }

        return $localPart.'@'.$domain;
    }
}

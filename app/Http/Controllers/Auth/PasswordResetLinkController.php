<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpJamaah;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    private const SESSION_KEY = 'password_recovery';
    private const RESET_EXPIRES_MINUTES = 10;
    private const OTP_EXPIRES_SECONDS = 20;

    /**
     * Nama controller dipertahankan dari struktur Laravel lama,
     * tetapi FINUS tidak lagi mengirim reset link.
     */
    public function create(Request $request): View
    {
        $portal = $this->normalizePortal((string) $request->query('portal', 'jamaah'));

        return view('auth.forgot-password', compact('portal'));
    }

    public function store(Request $request): RedirectResponse
    {
        $portal = $this->normalizePortal((string) $request->input('portal', 'jamaah'));

        return match ($portal) {
            'admin' => $this->verifyAdmin($request),
            'pegawai' => $this->verifyPegawai($request),
            default => $this->sendJamaahOtp($request),
        };
    }

    public function showOtp(Request $request): View|RedirectResponse
    {
        $recovery = $request->session()->get(self::SESSION_KEY);

        if (! $this->hasJamaahOtpSession($recovery)) {
            return redirect()->route('password.request', ['portal' => 'jamaah']);
        }

        $user = User::query()
            ->whereKey($recovery['user_id'])
            ->where('role', User::ROLE_JAMAAH)
            ->first();

        if (! $user) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('password.request', ['portal' => 'jamaah']);
        }

        $secondsRemaining = max(
            0,
            (int) ($recovery['otp_expires_at'] ?? 0) - now()->timestamp
        );

        return view('auth.verify-password-otp', [
            'maskedEmail' => $this->maskEmail($user->email),
            'secondsRemaining' => $secondsRemaining,
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.digits' => 'Kode OTP harus 6 digit.',
        ]);

        $recovery = $request->session()->get(self::SESSION_KEY);

        if (! $this->hasJamaahOtpSession($recovery)) {
            return redirect()->route('password.request', ['portal' => 'jamaah'])
                ->withErrors(['email' => 'Sesi pemulihan password sudah berakhir.']);
        }

        if ((int) $recovery['otp_expires_at'] < now()->timestamp) {
            return back()->withErrors([
                'code' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang kode.',
            ]);
        }

        if (! Hash::check((string) $validated['code'], (string) $recovery['otp_hash'])) {
            return back()->withErrors([
                'code' => 'Kode OTP tidak sesuai.',
            ]);
        }

        $recovery['otp_verified'] = true;
        $recovery['verified'] = true;
        $recovery['reset_expires_at'] = now()
            ->addMinutes(self::RESET_EXPIRES_MINUTES)
            ->timestamp;
        unset($recovery['otp_hash'], $recovery['otp_expires_at']);

        $request->session()->put(self::SESSION_KEY, $recovery);

        return redirect()->route('password.reset');
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $recovery = $request->session()->get(self::SESSION_KEY);

        if (! is_array($recovery)
            || ($recovery['role'] ?? null) !== User::ROLE_JAMAAH
            || empty($recovery['user_id'])
        ) {
            return redirect()->route('password.request', ['portal' => 'jamaah']);
        }

        $user = User::query()
            ->whereKey($recovery['user_id'])
            ->where('role', User::ROLE_JAMAAH)
            ->first();

        if (! $user) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('password.request', ['portal' => 'jamaah']);
        }

        $this->storeAndSendOtp($request, $user);

        return redirect()->route('password.otp.form')
            ->with('status', 'password-otp-sent');
    }

    private function verifyAdmin(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
            'recovery_code' => trim((string) $request->input('recovery_code')),
        ]);

        $validated = $request->validate([
            'email' => ['required', 'email:rfc'],
            'recovery_code' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $validated['email'])
            ->where('role', User::ROLE_ADMIN)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'recovery_code' => 'Email Admin atau Recovery Code tidak sesuai.',
            ]);
        }

        try {
            $activeCode = User::normalizeRecoveryCode(
                (string) ($user->recovery_code ?? '')
            );
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'recovery_code' => 'Recovery Code Admin tidak dapat dibaca. Silakan periksa konfigurasi APP_KEY FINUS.',
            ]);
        }

        if ($activeCode === '') {
            throw ValidationException::withMessages([
                'recovery_code' => 'Recovery Code Admin belum tersedia pada akun ini. Masuk ke akun Admin dan buat/ganti Recovery Code melalui Pengaturan Akun.',
            ]);
        }

        if (! hash_equals($activeCode, (string) $validated['recovery_code'])) {
            throw ValidationException::withMessages([
                'recovery_code' => 'Email Admin atau Recovery Code tidak sesuai.',
            ]);
        }

        $this->markRecoveryVerified($request, $user, 'admin');

        return redirect()->route('password.reset');
    }

    private function verifyPegawai(Request $request): RedirectResponse
    {
        $request->merge([
            'nip' => trim((string) $request->input('nip')),
            'recovery_code' => User::normalizeRecoveryCode(
                (string) $request->input('recovery_code')
            ),
        ]);

        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:100'],
            'recovery_code' => ['required', 'string', 'max:100'],
        ]);

        $pegawai = Pegawai::query()
            ->where('nip', $validated['nip'])
            ->first();

        $user = $pegawai?->user;

        if (! $pegawai || ! $pegawai->is_verified || ! $user || $user->role !== User::ROLE_PEGAWAI) {
            throw ValidationException::withMessages([
                'recovery_code' => 'NIP atau Recovery Code tidak sesuai.',
            ]);
        }

        try {
            $activeCode = User::normalizeRecoveryCode((string) $user->recovery_code);
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'recovery_code' => 'Recovery Code tidak dapat dibaca. Hubungi Admin FINUS.',
            ]);
        }

        if ($activeCode === '' || ! hash_equals($activeCode, $validated['recovery_code'])) {
            throw ValidationException::withMessages([
                'recovery_code' => 'NIP atau Recovery Code tidak sesuai.',
            ]);
        }

        $this->markRecoveryVerified($request, $user, 'pegawai');

        return redirect()->route('password.reset');
    }

    private function sendJamaahOtp(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'email' => ['required', 'email:rfc'],
        ]);

        $user = User::query()
            ->where('email', $validated['email'])
            ->where('role', User::ROLE_JAMAAH)
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email Jamaah tidak ditemukan.',
            ]);
        }

        $this->storeAndSendOtp($request, $user);

        return redirect()->route('password.otp.form')
            ->with('status', 'password-otp-sent');
    }

    private function storeAndSendOtp(Request $request, User $user): void
    {
        $code = (string) random_int(100000, 999999);

        $request->session()->put(self::SESSION_KEY, [
            'user_id' => $user->id,
            'role' => User::ROLE_JAMAAH,
            'portal' => 'jamaah',
            'otp_hash' => Hash::make($code),
            'otp_expires_at' => now()->addSeconds(self::OTP_EXPIRES_SECONDS)->timestamp,
            'otp_verified' => false,
            'verified' => false,
        ]);

        Mail::to($user->email)->send(
            new PasswordResetOtpJamaah(
                $code,
                $user->name,
                self::OTP_EXPIRES_SECONDS
            )
        );
    }

    private function markRecoveryVerified(Request $request, User $user, string $portal): void
    {
        $request->session()->put(self::SESSION_KEY, [
            'user_id' => $user->id,
            'role' => $user->role,
            'portal' => $portal,
            'verified' => true,
            'reset_expires_at' => now()
                ->addMinutes(self::RESET_EXPIRES_MINUTES)
                ->timestamp,
        ]);
    }

    private function hasJamaahOtpSession(mixed $recovery): bool
    {
        return is_array($recovery)
            && ($recovery['role'] ?? null) === User::ROLE_JAMAAH
            && ! empty($recovery['user_id'])
            && ! empty($recovery['otp_hash'])
            && ! empty($recovery['otp_expires_at']);
    }

    private function normalizePortal(string $portal): string
    {
        return match (strtolower(trim($portal))) {
            'admin' => 'admin',
            'pegawai', 'staff' => 'pegawai',
            default => 'jamaah',
        };
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        if ($domain === '') {
            return $email;
        }

        $visible = mb_substr($local, 0, min(2, mb_strlen($local)));
        $stars = str_repeat('*', max(3, mb_strlen($local) - mb_strlen($visible)));

        return $visible . $stars . '@' . $domain;
    }
}

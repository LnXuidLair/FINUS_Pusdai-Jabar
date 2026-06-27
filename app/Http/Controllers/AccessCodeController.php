<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AccessCodeController extends Controller
{
    private const MAX_ATTEMPTS = 3;
    private const COOLDOWN_SECONDS = 30;

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:admin,staff'],
            'code' => ['required', 'string', 'min:6', 'max:100'],
        ]);

        if ($request->user()) {
            return response()->json([
                'status' => 'already_logged_in',
                'message' => 'Anda sudah masuk.',
                'redirect' => $this->dashboardFor($request->user()),
                'current_role' => $request->user()->role,
                'requested_type' => $validated['type'],
                'logout_url' => route('logout'),
            ]);
        }

        $type = $validated['type'];
        $code = trim($validated['code']);

        $expectedCode = $type === 'admin'
            ? config('finus.access_code_admin', env('ACCESS_CODE_ADMIN'))
            : config('finus.access_code_staff', env('ACCESS_CODE_STAFF'));

        if (!is_string($expectedCode) || trim($expectedCode) === '') {
            return response()->json([
                'status' => 'misconfigured',
                'message' => 'Kode akses belum dikonfigurasi.',
            ]);
        }

        $attemptKey = "access_code_attempts:{$request->ip()}:{$type}";
        $cooldownKey = "access_code_cooldown:{$request->ip()}:{$type}";

        $cooldownUntil = (int) Cache::get($cooldownKey, 0);

        if ($cooldownUntil > now()->timestamp) {
            return response()->json([
                'status' => 'cooldown',
                'message' => 'Terlalu banyak percobaan.',
                'remaining' => $cooldownUntil - now()->timestamp,
            ]);
        }

        if ($cooldownUntil > 0) {
            Cache::forget($cooldownKey);
        }

        if (hash_equals(trim($expectedCode), $code)) {
            Cache::forget($attemptKey);
            Cache::forget($cooldownKey);

            return response()->json([
                'status' => 'success',
                'redirect' => $this->accessDestination($type),
            ]);
        }

        $attempts = (int) Cache::get($attemptKey, 0) + 1;

        Cache::put(
            $attemptKey,
            $attempts,
            now()->addSeconds(self::COOLDOWN_SECONDS)
        );

        if ($attempts >= self::MAX_ATTEMPTS) {
            $cooldownUntil = now()->timestamp + self::COOLDOWN_SECONDS;

            Cache::put(
                $cooldownKey,
                $cooldownUntil,
                now()->addSeconds(self::COOLDOWN_SECONDS)
            );

            Cache::forget($attemptKey);

            return response()->json([
                'status' => 'locked',
                'message' => 'Percobaan habis. Tunggu 30 detik.',
                'remaining' => self::COOLDOWN_SECONDS,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Kode akses salah.',
            'attempts' => $attempts,
            'max_attempts' => self::MAX_ATTEMPTS,
        ]);
    }

    private function accessDestination(string $type): string
    {
        if ($type === 'staff') {
            return route('login.staff');
        }

        return User::where('role', User::ROLE_ADMIN)->exists()
            ? route('login.admin')
            : route('register.admin');
    }

    private function dashboardFor(User $user): string
    {
        return match ($user->role) {
            User::ROLE_ADMIN => route('dashboard'),
            User::ROLE_PEGAWAI => route('pegawai.dashboard'),
            User::ROLE_JAMAAH => route('jamaah.dashboard'),
            default => route('home'),
        };
    }
}
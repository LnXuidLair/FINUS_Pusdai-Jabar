<?php

namespace App\Support;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class OrganizationContext
{
    public static function currentOrganizationId(bool $allowSingleOrganizationFallback = true): ?int
    {
        $route = request()->route();
        $routeName = $route?->getName();
        $middleware = $route?->gatherMiddleware() ?? [];

        if (in_array('auth:admin', $middleware, true) || (is_string($routeName) && str_starts_with($routeName, 'admin.'))) {
            return self::organizationIdForGuard(User::ROLE_ADMIN);
        }

        if (in_array('auth:pegawai', $middleware, true) || (is_string($routeName) && str_starts_with($routeName, 'pegawai.'))) {
            return self::organizationIdForGuard(User::ROLE_PEGAWAI);
        }

        // Jika route sudah dikenali tetapi bukan portal Admin/Pegawai
        // (misalnya Jamaah atau halaman publik), jangan mengambil organization
        // hanya karena guard Admin/Pegawai kebetulan juga aktif di browser yang sama.
        if ($route) {
            if (! $allowSingleOrganizationFallback) {
                return null;
            }

            $ids = Organization::query()
                ->active()
                ->limit(2)
                ->pluck('id');

            return $ids->count() === 1 ? (int) $ids->first() : null;
        }

        $adminId = self::organizationIdForGuard(User::ROLE_ADMIN);
        $pegawaiId = self::organizationIdForGuard(User::ROLE_PEGAWAI);

        if ($adminId && $pegawaiId) {
            // Di luar HTTP route, jangan menebak jika dua guard aktif
            // dari organization berbeda.
            return $adminId === $pegawaiId ? $adminId : null;
        }

        if ($adminId) {
            return $adminId;
        }

        if ($pegawaiId) {
            return $pegawaiId;
        }

        if (! $allowSingleOrganizationFallback) {
            return null;
        }

        $ids = Organization::query()
            ->active()
            ->limit(2)
            ->pluck('id');

        return $ids->count() === 1 ? (int) $ids->first() : null;
    }

    public static function currentActor(): ?Authenticatable
    {
        $route = request()->route();
        $routeName = $route?->getName();
        $middleware = $route?->gatherMiddleware() ?? [];

        if (in_array('auth:admin', $middleware, true) || (is_string($routeName) && str_starts_with($routeName, 'admin.'))) {
            return Auth::guard(User::ROLE_ADMIN)->user();
        }

        if (in_array('auth:pegawai', $middleware, true) || (is_string($routeName) && str_starts_with($routeName, 'pegawai.'))) {
            return Auth::guard(User::ROLE_PEGAWAI)->user();
        }

        return Auth::guard(User::ROLE_ADMIN)->user()
            ?? Auth::guard(User::ROLE_PEGAWAI)->user();
    }

    private static function organizationIdForGuard(string $guard): ?int
    {
        $organizationId = Auth::guard($guard)->user()?->organization_id;

        return $organizationId ? (int) $organizationId : null;
    }
}

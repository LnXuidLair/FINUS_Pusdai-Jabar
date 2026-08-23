<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePegawaiAccess
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$allowedAccessRoles
    ): Response {
        $user = $request->user('pegawai');
        $pegawai = $user?->pegawai;
        $accessRole = $pegawai?->akses_role ?? 'umum';

        if (! $user || ! $pegawai) {
            abort(403, 'Data pegawai belum terhubung dengan akun ini.');
        }

        if (! in_array($accessRole, $allowedAccessRoles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}

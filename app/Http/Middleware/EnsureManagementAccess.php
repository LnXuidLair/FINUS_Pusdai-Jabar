<?php
namespace App\Http\Middleware;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class EnsureManagementAccess
{
    /*
     * Access Code berlaku 15 menit agar cukup untuk proses login/aktivasi,
     * tetapi tidak tersimpan sepanjang session browser.
     */
    private const ACCESS_TTL_SECONDS = 900;
    public function handle(
        Request $request,
        Closure $next,
        string $type
    ): Response{
        abort_unless(in_array($type, ['admin', 'staff'], true), 403);
        $sessionKey = "management_access.{$type}_verified_at";
        $verifiedAt = (int) $request->session()->get($sessionKey, 0);
        $isValid = $verifiedAt > 0
            && (now()->timestamp - $verifiedAt) <= self::ACCESS_TTL_SECONDS;

        if(! $isValid){
            $request->session()->forget($sessionKey);
            return redirect()
                ->route('management.access')
                ->with(
                    'access_error',
                    'Silakan verifikasi kode akses terlebih dahulu.'
                );
        }
        $response = $next($request);
        /*
         * Setelah login berhasil, bukti Access Code dihapus.
         * Login berikutnya harus melewati /management-access lagi.
         */
        if($request->isMethod('post')){
            $guard = $type === 'admin'
                ? User::ROLE_ADMIN
                : User::ROLE_PEGAWAI;

            if(Auth::guard($guard)->check()){
                $request->session()->forget($sessionKey);
            }
        }
        return $response;
    }
}
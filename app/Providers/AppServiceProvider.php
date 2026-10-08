<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        /*
         * Admin dan Pegawai bekerja di dalam satu organization (masjid), jadi
         * sidebar mereka menampilkan organization masing-masing. Jamaah tidak
         * memakai composer ini karena akun Jamaah bersifat publik/global dan
         * dapat memilih banyak masjid tujuan transaksi.
         */
        View::composer([
            'layouts.sidebar-admin',
            'layouts.sidebar-staff',
        ], function ($view): void {
            $mosqueName = 'Masjid';
            $organization = null;

            try {
                if (Schema::hasTable('organizations') && Schema::hasColumn('users', 'organization_id')) {
                    $user = Auth::guard(User::ROLE_ADMIN)->user()
                        ?? Auth::guard(User::ROLE_PEGAWAI)->user();

                    if ($user?->organization_id) {
                        $organization = DB::table('organizations')
                            ->where('id', $user->organization_id)
                            ->first();
                    }

                    if (! $organization) {
                        $organization = DB::table('organizations')
                            ->where('is_active', true)
                            ->orderBy('id')
                            ->first();
                    }

                    if ($organization && trim((string) $organization->name) !== '') {
                        $mosqueName = trim((string) $organization->name);
                    }
                } elseif (Schema::hasTable('users')) {
                    // Kompatibilitas sebelum migration organizations dijalankan.
                    $columns = ['name'];
                    if (Schema::hasColumn('users', 'nama_masjid')) {
                        $columns[] = 'nama_masjid';
                    }

                    $admin = DB::table('users')
                        ->where('role', User::ROLE_ADMIN)
                        ->first($columns);

                    if ($admin) {
                        $legacy = trim((string) ($admin->nama_masjid ?? ''));
                        if ($legacy === '') {
                            $legacy = preg_replace('/^Admin\s+/iu', '', trim((string) $admin->name)) ?: '';
                        }
                        if ($legacy !== '') {
                            $mosqueName = $legacy;
                        }
                    }
                }
            } catch (\Throwable) {
                // Sidebar tetap dapat dirender saat instalasi/migrasi belum selesai.
            }

            $view->with([
                'finusMosqueName' => $mosqueName,
                'finusOrganization' => $organization,
            ]);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'nama_masjid')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('nama_masjid')->nullable()->after('name');
            });
        }

        /*
         * Backfill instalasi FINUS lama. Pada registrasi versi lama nama masjid
         * belum disimpan terpisah dan biasanya berada pada "Admin {Nama Masjid}".
         * Data baru tidak memerlukan backfill karena RegisterController langsung
         * menyimpan nama_masjid dari formulir pembuatan Admin.
         */
        DB::table('users')
            ->where('role', 'admin')
            ->whereNull('nama_masjid')
            ->orderBy('id')
            ->get(['id', 'name', 'email'])
            ->each(function (object $admin): void {
                $name = trim((string) $admin->name);
                $mosqueName = '';

                if (preg_match('/^Admin\s+(.+)$/iu', $name, $matches) === 1) {
                    $mosqueName = trim((string) ($matches[1] ?? ''));
                }

                /*
                 * Jika nama Admin sudah pernah diedit sebelum migration ini,
                 * gunakan domain email registrasi sebagai fallback agar nama
                 * pribadi Admin tidak dianggap sebagai nama masjid.
                 */
                if ($mosqueName === '' && preg_match('/^admin@([^.]+)\.finus\.id$/i', (string) $admin->email, $matches) === 1) {
                    $mosqueName = ucfirst(trim((string) ($matches[1] ?? '')));
                }

                if ($mosqueName === '') {
                    $mosqueName = 'Masjid';
                }

                DB::table('users')
                    ->where('id', $admin->id)
                    ->update(['nama_masjid' => $mosqueName]);
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'nama_masjid')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('nama_masjid');
            });
        }
    }
};

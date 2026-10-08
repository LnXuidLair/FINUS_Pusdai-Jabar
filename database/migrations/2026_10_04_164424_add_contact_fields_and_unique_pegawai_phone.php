<?php

use App\Support\PhoneNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'no_telp')) {
                $table->string('no_telp', 20)->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable()->after('no_telp');
            }
        });

        /*
         * Rapikan format nomor lama sebelum unique index dibuat tanpa mengasumsikan
         * kode negara tertentu. Nomor internasional dipertahankan sebagai E.164;
         * data legacy tanpa country code tidak dipaksa menjadi +62.
         */
        $seen = [];
        DB::table('pegawai')
            ->select(['id', 'no_telp'])
            ->orderBy('id')
            ->get()
            ->each(function ($pegawai) use (&$seen): void {
                $normalized = PhoneNumber::normalize($pegawai->no_telp);

                if ($normalized === null) {
                    DB::table('pegawai')->where('id', $pegawai->id)->update(['no_telp' => null]);
                    return;
                }

                if (isset($seen[$normalized])) {
                    DB::table('pegawai')->where('id', $pegawai->id)->update(['no_telp' => null]);
                    return;
                }

                $seen[$normalized] = true;
                DB::table('pegawai')->where('id', $pegawai->id)->update(['no_telp' => $normalized]);
            });

        Schema::table('pegawai', function (Blueprint $table): void {
            $table->unique('no_telp', 'pegawai_no_telp_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table): void {
            $table->dropUnique('pegawai_no_telp_unique');
        });

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'alamat')) {
                $table->dropColumn('alamat');
            }
            if (Schema::hasColumn('users', 'no_telp')) {
                $table->dropColumn('no_telp');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pegawai') && ! Schema::hasColumn('pegawai', 'akses_role')) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->enum('akses_role', ['umum', 'dkm', 'keuangan'])
                    ->default('umum')
                    ->after('jabatan')
                    ->index();
            });

            DB::table('pegawai')
                ->whereRaw('LOWER(jabatan) LIKE ?', ['%dkm%'])
                ->update(['akses_role' => 'dkm']);

            DB::table('pegawai')
                ->whereRaw('LOWER(jabatan) LIKE ?', ['%keuangan%'])
                ->update(['akses_role' => 'keuangan']);
        }

        if (! Schema::hasTable('gaji_jabatan_riwayat')) {
            Schema::create('gaji_jabatan_riwayat', function (Blueprint $table) {
                $table->id();
                $table->foreignId('gaji_jabatan_id')
                    ->constrained('gaji_jabatan')
                    ->cascadeOnDelete();
                $table->unsignedBigInteger('gaji_perhari')->default(0);
                $table->date('berlaku_mulai');
                $table->date('berlaku_sampai')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['gaji_jabatan_id', 'berlaku_mulai']);
            });

            if (Schema::hasTable('gaji_jabatan')) {
                DB::table('gaji_jabatan')
                    ->orderBy('id')
                    ->get()
                    ->each(function ($jabatan): void {
                        DB::table('gaji_jabatan_riwayat')->insert([
                            'gaji_jabatan_id' => $jabatan->id,
                            'gaji_perhari' => $jabatan->gaji_perhari ?? 0,
                            'berlaku_mulai' => now()->toDateString(),
                            'berlaku_sampai' => null,
                            'created_by' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gaji_jabatan_riwayat');

        if (Schema::hasTable('pegawai') && Schema::hasColumn('pegawai', 'akses_role')) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->dropColumn('akses_role');
            });
        }
    }
};

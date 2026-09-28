<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kebijakan_zakat_versions', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 150);
            $table->date('berlaku_mulai');
            $table->date('berlaku_sampai')->nullable();
            $table->string('status', 20)->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('sumber_version_id')->nullable()
                ->constrained('kebijakan_zakat_versions')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'berlaku_mulai']);
            $table->index(['berlaku_mulai', 'berlaku_sampai']);
        });

        Schema::table('zakat_settings', function (Blueprint $table) {
            $table->foreignId('kebijakan_zakat_version_id')->nullable()
                ->after('id')->constrained('kebijakan_zakat_versions')->nullOnDelete();
            $table->dropUnique('zakat_settings_tahun_unique');
            $table->unique(
                ['kebijakan_zakat_version_id', 'tahun'],
                'zakat_settings_version_tahun_unique'
            );
        });

        Schema::table('kebijakan_amil', function (Blueprint $table) {
            $table->foreignId('kebijakan_zakat_version_id')->nullable()
                ->after('id')->constrained('kebijakan_zakat_versions')->nullOnDelete();
            $table->index(
                ['kebijakan_zakat_version_id', 'jenis_sumber'],
                'kebijakan_amil_version_sumber_index'
            );
        });

        Schema::table('kebijakan_mustahik', function (Blueprint $table) {
            $table->foreignId('kebijakan_zakat_version_id')->nullable()
                ->after('id')->constrained('kebijakan_zakat_versions')->nullOnDelete();
            $table->dropUnique('kebijakan_mustahik_asnaf_mulai_unique');
            $table->unique(
                ['kebijakan_zakat_version_id', 'asnaf'],
                'kebijakan_mustahik_version_asnaf_unique'
            );
        });

        $hasRules = DB::table('zakat_settings')->exists()
            || DB::table('kebijakan_amil')->exists()
            || DB::table('kebijakan_mustahik')->exists();

        if ($hasRules) {
            $start = collect([
                DB::table('zakat_settings')->min('berlaku_mulai'),
                DB::table('kebijakan_amil')->min('berlaku_mulai'),
                DB::table('kebijakan_mustahik')->min('berlaku_mulai'),
            ])->filter()->min() ?: now()->toDateString();

            $versionId = DB::table('kebijakan_zakat_versions')->insertGetId([
                'kode' => 'KZ-AWAL',
                'nama' => 'Kebijakan Zakat Awal',
                'berlaku_mulai' => $start,
                'berlaku_sampai' => null,
                'status' => 'aktif',
                'catatan' => 'Versi awal yang dibentuk otomatis dari kebijakan yang sudah ada.',
                'created_at' => now(),
                'updated_at' => now(),
                'approved_at' => now(),
            ]);

            DB::table('zakat_settings')->whereNull('kebijakan_zakat_version_id')
                ->update(['kebijakan_zakat_version_id' => $versionId]);
            DB::table('kebijakan_amil')->whereNull('kebijakan_zakat_version_id')
                ->update(['kebijakan_zakat_version_id' => $versionId]);
            DB::table('kebijakan_mustahik')->whereNull('kebijakan_zakat_version_id')
                ->update(['kebijakan_zakat_version_id' => $versionId]);
        }
    }

    public function down(): void
    {
        Schema::table('kebijakan_mustahik', function (Blueprint $table) {
            $table->dropUnique('kebijakan_mustahik_version_asnaf_unique');
            $table->dropConstrainedForeignId('kebijakan_zakat_version_id');
            $table->unique(['asnaf', 'berlaku_mulai'], 'kebijakan_mustahik_asnaf_mulai_unique');
        });

        Schema::table('kebijakan_amil', function (Blueprint $table) {
            $table->dropIndex('kebijakan_amil_version_sumber_index');
            $table->dropConstrainedForeignId('kebijakan_zakat_version_id');
        });

        Schema::table('zakat_settings', function (Blueprint $table) {
            $table->dropUnique('zakat_settings_version_tahun_unique');
            $table->dropConstrainedForeignId('kebijakan_zakat_version_id');
            $table->unique('tahun');
        });

        Schema::dropIfExists('kebijakan_zakat_versions');
    }
};

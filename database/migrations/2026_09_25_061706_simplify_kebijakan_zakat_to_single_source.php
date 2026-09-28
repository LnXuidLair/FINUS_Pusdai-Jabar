<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan kolom nilai acuan & kebijakan ke ketentuan_pokok_zakat
        if (!Schema::hasColumn('ketentuan_pokok_zakat', 'nisab_rupiah')) {
            Schema::table('ketentuan_pokok_zakat', function (Blueprint $table) {
                $table->decimal('nisab_rupiah', 15, 2)->nullable()->after('nisab_pokok');
                $table->decimal('persentase_amil', 5, 2)->default(12.50)->after('berat_fitrah_liter');
                $table->json('target_mustahik')->nullable()->after('persentase_amil');
            });
        }

        // 2. Sesuaikan ziswaf_penerimaan (Hapus foreign key yang lama)
        if (Schema::hasTable('ziswaf_penerimaan')) {
            Schema::table('ziswaf_penerimaan', function (Blueprint $table) {
                if (Schema::hasColumn('ziswaf_penerimaan', 'kebijakan_version_id')) {
                    $table->dropConstrainedForeignId('kebijakan_version_id');
                }
                if (Schema::hasColumn('ziswaf_penerimaan', 'zakat_setting_id')) {
                    $table->dropConstrainedForeignId('zakat_setting_id');
                }
                if (Schema::hasColumn('ziswaf_penerimaan', 'kebijakan_amil_id')) {
                    $table->dropColumn('kebijakan_amil_id');
                }
            });
        }

        // 3. Drop tabel-tabel versi lama
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('kebijakan_mustahik');
        Schema::dropIfExists('kebijakan_amil');
        Schema::dropIfExists('zakat_settings');
        Schema::dropIfExists('kebijakan_zakat_versions');
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::table('ketentuan_pokok_zakat', function (Blueprint $table) {
            $table->dropColumn(['nisab_rupiah', 'persentase_amil', 'target_mustahik']);
        });
    }
};

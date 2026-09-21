<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kebijakan_mustahik', function (Blueprint $table) {
            $table->id();
            $table->string('asnaf', 50);
            $table->unsignedTinyInteger('prioritas')->default(1);
            $table->decimal('target_persentase', 5, 2)->nullable();
            $table->unsignedBigInteger('batas_bantuan')->nullable();
            $table->string('bentuk_penyaluran', 20)->default('keduanya');
            $table->text('kriteria');
            $table->string('dasar_aturan', 255)->nullable();
            $table->date('berlaku_mulai');
            $table->date('berlaku_sampai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['asnaf', 'aktif']);
            $table->index(['berlaku_mulai', 'berlaku_sampai']);
            $table->unique(['asnaf', 'berlaku_mulai'], 'kebijakan_mustahik_asnaf_mulai_unique');
        });

        if (Schema::hasTable('ziswaf_penyaluran')) {
            Schema::table('ziswaf_penyaluran', function (Blueprint $table) {
                $table->foreignId('kebijakan_mustahik_id')
                    ->nullable()
                    ->after('asnaf')
                    ->constrained('kebijakan_mustahik')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('ziswaf_penyaluran')
            && Schema::hasColumn('ziswaf_penyaluran', 'kebijakan_mustahik_id')
        ) {
            Schema::table('ziswaf_penyaluran', function (Blueprint $table) {
                $table->dropConstrainedForeignId('kebijakan_mustahik_id');
            });
        }

        Schema::dropIfExists('kebijakan_mustahik');
    }
};

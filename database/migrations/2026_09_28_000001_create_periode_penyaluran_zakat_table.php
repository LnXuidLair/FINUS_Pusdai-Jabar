<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_penyaluran_zakat', function (Blueprint $table): void {
            $table->id();
            $table->char('periode', 7)->unique();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('persentase_amil', 5, 2)->default(0);
            $table->json('target_asnaf')->nullable();
            $table->unsignedBigInteger('saldo_sebelum_penyaluran')->default(0);
            $table->unsignedBigInteger('total_disalurkan')->default(0);
            $table->unsignedBigInteger('saldo_akhir')->default(0);
            $table->string('status', 24)->default('aktif');
            $table->timestamp('ditutup_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ditutup_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'tanggal_selesai']);
        });

        Schema::table('pengeluaran', function (Blueprint $table): void {
            $table->foreignId('periode_penyaluran_zakat_id')
                ->nullable()
                ->after('restriction_type')
                ->constrained('periode_penyaluran_zakat')
                ->nullOnDelete();
            $table->string('nomor_batch', 30)->nullable()->unique()->after('periode_penyaluran_zakat_id');
            $table->unique('periode_penyaluran_zakat_id', 'pengeluaran_satu_batch_periode_zakat_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table): void {
            $table->dropUnique('pengeluaran_satu_batch_periode_zakat_unique');
            $table->dropUnique('pengeluaran_nomor_batch_unique');
            $table->dropConstrainedForeignId('periode_penyaluran_zakat_id');
            $table->dropColumn('nomor_batch');
        });

        Schema::dropIfExists('periode_penyaluran_zakat');
    }
};

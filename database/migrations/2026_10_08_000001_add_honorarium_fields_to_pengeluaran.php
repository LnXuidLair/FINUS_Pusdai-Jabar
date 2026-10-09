<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table): void {
            if (! Schema::hasColumn('pengeluaran', 'id_pegawai')) {
                $table->foreignId('id_pegawai')
                    ->nullable()
                    ->after('id_penggajian')
                    ->constrained('pegawai')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('pengeluaran', 'bukti_surat_tugas')) {
                $table->string('bukti_surat_tugas')
                    ->nullable()
                    ->after('bukti_pembayaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table): void {
            if (Schema::hasColumn('pengeluaran', 'id_pegawai')) {
                $table->dropConstrainedForeignId('id_pegawai');
            }

            if (Schema::hasColumn('pengeluaran', 'bukti_surat_tugas')) {
                $table->dropColumn('bukti_surat_tugas');
            }
        });
    }
};

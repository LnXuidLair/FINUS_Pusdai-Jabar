<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambahkan nilai 'dibatalkan' ke ENUM status_verifikasi,
     * lalu update data lama yang dibatalkan jamaah.
     */
    public function up(): void
    {
        // 1. Perluas ENUM agar menerima nilai 'dibatalkan'
        DB::statement("
            ALTER TABLE ziswaf_penerimaan
            MODIFY status_verifikasi ENUM('pending','diterima','ditolak','dibatalkan') NULL DEFAULT 'pending'
        ");

        // 2. Update data lama: transaksi yang dibatalkan jamaah
        DB::table('ziswaf_penerimaan')
            ->where('catatan_verifikasi', 'Dibatalkan oleh jamaah.')
            ->where('status_verifikasi', 'ditolak')
            ->update(['status_verifikasi' => 'dibatalkan']);
    }

    public function down(): void
    {
        // Kembalikan data terlebih dahulu sebelum menyempitkan ENUM
        DB::table('ziswaf_penerimaan')
            ->where('status_verifikasi', 'dibatalkan')
            ->update(['status_verifikasi' => 'ditolak']);

        DB::statement("
            ALTER TABLE ziswaf_penerimaan
            MODIFY status_verifikasi ENUM('pending','diterima','ditolak') NULL DEFAULT 'pending'
        ");
    }
};

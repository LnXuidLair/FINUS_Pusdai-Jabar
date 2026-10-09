<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ketentuan_pokok_zakat') && Schema::hasColumn('ketentuan_pokok_zakat', 'persentase_amil')) {
            DB::table('ketentuan_pokok_zakat')->update(['persentase_amil' => 0, 'updated_at' => now()]);
        }

        $accounts = DB::table('coa')->where('kode_akun', '4301')->get(['id']);
        foreach ($accounts as $account) {
            if (! DB::table('jurnal_detail')->where('coa_id', $account->id)->exists()) {
                DB::table('coa')->where('id', $account->id)->delete();
            }
        }
    }

    public function down(): void
    {
        // Histori persentase dan akun tidak direka ulang saat rollback.
    }
};

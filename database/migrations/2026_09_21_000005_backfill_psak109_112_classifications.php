<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ziswaf_penerimaan')) {
            DB::table('ziswaf_penerimaan')
                ->whereNull('restriction_type')
                ->where(function ($query): void {
                    $query->where('jenis_ziswaf', 'like', '%infaq%')
                        ->orWhere('jenis_ziswaf', 'like', '%infak%')
                        ->orWhere('jenis_ziswaf', 'like', '%sedekah%');
                })
                ->update(['restriction_type' => 'mutlaqah']);

            DB::table('ziswaf_penerimaan')
                ->whereNull('wakaf_type')
                ->where('jenis_ziswaf', 'like', '%wakaf%')
                ->update(['wakaf_type' => 'permanen']);
        }

        if (Schema::hasTable('jurnal_detail')) {
            DB::table('jurnal_detail')
                ->whereNull('restriction_type')
                ->whereIn('jenis_dana', ['infak', 'infak_sedekah'])
                ->update(['restriction_type' => 'mutlaqah']);

            DB::table('jurnal_detail')
                ->whereNull('psak_reference')
                ->whereIn('jenis_dana', ['zakat', 'infak', 'infak_sedekah'])
                ->update(['psak_reference' => 'PSAK 109']);

            DB::table('jurnal_detail')
                ->whereNull('psak_reference')
                ->whereIn('jenis_dana', ['wakaf', 'wakaf_temporer', 'nazhir'])
                ->update(['psak_reference' => 'PSAK 112']);
        }
    }

    public function down(): void
    {
        // Klasifikasi historis dipertahankan agar jejak pelaporan tidak hilang.
    }
};

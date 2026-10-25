<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        foreach (['pengeluaran', 'jurnal_detail'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->string('zakat_source', 20)->nullable()->index());
        }
        Schema::table('periode_penyaluran_zakat', fn (Blueprint $t) => $t->index('organization_id', 'periode_zakat_org_index'));
        Schema::table('periode_penyaluran_zakat', function (Blueprint $t) {
            $t->dropUnique('periode_zakat_org_periode_unique');
            $t->string('zakat_source', 20)->default('lainnya');
            $t->unique(['organization_id', 'periode', 'zakat_source'], 'periode_zakat_org_source_unique');
        });
        DB::table('jurnal_detail')->where('jenis_dana', 'zakat')->update(['zakat_source' => 'lainnya']);
        DB::table('pengeluaran')->where('jenis_dana', 'zakat')->update(['zakat_source' => 'lainnya']);
        // Only receipts have reliable historical identification of fitrah.
        DB::table('ziswaf_penerimaan')->where('jenis_ziswaf', 'zakat_fitrah')->whereNotNull('jurnal_id')->orderBy('id')->chunkById(200, function ($rows) {
            DB::table('jurnal_detail')->whereIn('jurnal_id', $rows->pluck('jurnal_id'))->where('jenis_dana', 'zakat')->update(['zakat_source' => 'fitrah']);
        });
    }
    public function down(): void {
        // Refuse a rollback that would merge two existing period records.
        if (DB::table('periode_penyaluran_zakat')->select('organization_id', 'periode')->groupBy('organization_id', 'periode')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Periode zakat fitrah dan lainnya harus direkonsiliasi sebelum rollback.');
        }
        Schema::table('periode_penyaluran_zakat', function (Blueprint $t) {
            $t->dropUnique('periode_zakat_org_source_unique');
            $t->dropColumn('zakat_source');
            $t->unique(['organization_id', 'periode'], 'periode_zakat_org_periode_unique');
        });
        foreach (['pengeluaran', 'jurnal_detail'] as $table) Schema::table($table, fn (Blueprint $t) => $t->dropColumn('zakat_source'));
    }
};

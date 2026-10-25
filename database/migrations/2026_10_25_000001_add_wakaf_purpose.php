<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        foreach (['ziswaf_penerimaan', 'pengeluaran', 'jurnal_detail'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->string('wakaf_purpose', 30)->nullable()->index());
        }
    }
    public function down(): void {
        foreach (['ziswaf_penerimaan', 'pengeluaran', 'jurnal_detail'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->dropColumn('wakaf_purpose'));
        }
    }
};

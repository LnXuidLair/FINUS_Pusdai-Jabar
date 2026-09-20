<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ziswaf_penyaluran') && ! Schema::hasColumn('ziswaf_penyaluran', 'jumlah_penerima')) {
            Schema::table('ziswaf_penyaluran', function (Blueprint $table): void {
                $table->unsignedInteger('jumlah_penerima')->default(1)->after('penerima_manfaat');
            });
        }

        if (! Schema::hasTable('coa')) {
            return;
        }

        DB::transaction(function (): void {
            $targetId = DB::table('coa')->where('kode_akun', '5210')->value('id');

            if (! $targetId) {
                $targetId = DB::table('coa')->insertGetId([
                    'header_akun' => 5,
                    'kode_akun' => '5210',
                    'nama_akun' => 'Penyaluran Zakat',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $legacyAccounts = DB::table('coa')
                ->whereIn('kode_akun', ['5211', '5212', '5213', '5214', '5215', '5216', '5217', '5218'])
                ->get();

            foreach ($legacyAccounts as $legacy) {
                if (Schema::hasTable('jurnal_detail')) {
                    $details = DB::table('jurnal_detail')->where('coa_id', $legacy->id);

                    if ($legacy->kode_akun === '5213' && Schema::hasColumn('jurnal_detail', 'asnaf')) {
                        (clone $details)->whereNull('asnaf')->update(['asnaf' => 'amil']);
                    }

                    $details->update(['coa_id' => $targetId]);
                }

                if (Schema::hasTable('pengeluaran') && Schema::hasColumn('pengeluaran', 'coa_debit_id')) {
                    DB::table('pengeluaran')
                        ->where('coa_debit_id', $legacy->id)
                        ->update([
                            'coa_debit_id' => $targetId,
                            'kategori' => 'Penyaluran Zakat',
                            'updated_at' => now(),
                        ]);
                }

                DB::table('coa')->where('id', $legacy->id)->delete();
            }
        });
    }

    public function down(): void
    {
        // Akun tidak dipecah kembali agar referensi jurnal tetap konsisten.
        if (Schema::hasTable('ziswaf_penyaluran') && Schema::hasColumn('ziswaf_penyaluran', 'jumlah_penerima')) {
            Schema::table('ziswaf_penyaluran', function (Blueprint $table): void {
                $table->dropColumn('jumlah_penerima');
            });
        }
    }
};

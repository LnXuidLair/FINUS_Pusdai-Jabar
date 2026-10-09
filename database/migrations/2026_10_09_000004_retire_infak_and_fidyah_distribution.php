<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transaction_categories')) {
            DB::table('transaction_categories')
                ->where('transaction_type', 'pengeluaran')
                ->where(function ($query): void {
                    $query->whereIn('code', [
                        'PENYALURAN-INFAK',
                        'PENYALURAN-FIDYAH',
                        'INFAK',
                        'FIDYAH',
                    ])->orWhereIn('name', [
                        'Penyaluran Infak dan Sedekah',
                        'Penyaluran Fidyah',
                    ]);
                })
                ->orderBy('id')
                ->get(['id'])
                ->each(function (object $category): void {
                    $hasHistory = Schema::hasTable('pengeluaran')
                        && Schema::hasColumn('pengeluaran', 'transaction_category_id')
                        && DB::table('pengeluaran')->where('transaction_category_id', $category->id)->exists();

                    if ($hasHistory) {
                        DB::table('transaction_categories')->where('id', $category->id)->update([
                            'is_active' => false,
                            'updated_at' => now(),
                        ]);

                        return;
                    }

                    DB::table('transaction_categories')->where('id', $category->id)->delete();
                });
        }

        if (! Schema::hasTable('coa')) {
            return;
        }

        DB::table('coa')
            ->whereIn('kode_akun', ['4108', '4302', '5311', '5312', '5511'])
            ->orderBy('id')
            ->get(['id'])
            ->each(function (object $account): void {
                if ($this->accountHasReferences((int) $account->id)) {
                    return;
                }

                DB::table('coa')->where('id', $account->id)->delete();
            });
    }

    public function down(): void
    {
        // Master lama tidak dibuat ulang karena dapat membuka kembali alur transaksi
        // yang sudah dihentikan. Histori yang pernah dipakai tetap dipertahankan oleh up().
    }

    private function accountHasReferences(int $accountId): bool
    {
        $references = [
            ['jurnal_detail', 'coa_id'],
            ['pengeluaran', 'coa_debit_id'],
            ['pengeluaran', 'coa_kredit_id'],
            ['ziswaf_penerimaan', 'coa_id'],
            ['transaction_categories', 'coa_id'],
            ['barang_zakat', 'coa_persediaan_id'],
        ];

        foreach ($references as [$table, $column]) {
            if (
                Schema::hasTable($table)
                && Schema::hasColumn($table, $column)
                && DB::table($table)->where($column, $accountId)->exists()
            ) {
                return true;
            }
        }

        return false;
    }
};

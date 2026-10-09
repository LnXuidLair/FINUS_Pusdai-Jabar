<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rules = config('transaction_categories.fund_source_rules', []);
        uksort($rules, fn (string $left, string $right): int => strlen($right) <=> strlen($left));

        DB::table('transaction_categories')
            ->join('coa', 'coa.id', '=', 'transaction_categories.coa_id')
            ->select('transaction_categories.id', 'coa.kode_akun')
            ->orderBy('transaction_categories.id')
            ->get()
            ->each(function (object $category) use ($rules): void {
                $fundType = null;
                foreach ($rules as $prefix => $candidate) {
                    if (str_starts_with((string) $category->kode_akun, $prefix)) {
                        $fundType = $candidate;
                        break;
                    }
                }

                if (! $fundType) {
                    return;
                }

                DB::table('transaction_categories')
                    ->where('id', $category->id)
                    ->update([
                        'group' => $fundType,
                        'default_fund_type' => $fundType,
                        'allowed_fund_types' => json_encode([$fundType]),
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        // Nilai lama tidak dapat direkonstruksi karena sebelumnya diatur manual.
    }
};

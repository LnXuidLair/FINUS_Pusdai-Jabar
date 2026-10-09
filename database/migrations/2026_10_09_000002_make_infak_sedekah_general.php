<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('transaction_categories')
            ->where('requires_restriction', true)
            ->update([
                'requires_restriction' => false,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('transaction_categories')
            ->where('form_type', 'infak_distribution')
            ->update([
                'requires_restriction' => true,
                'updated_at' => now(),
            ]);
    }
};

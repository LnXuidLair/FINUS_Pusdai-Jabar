<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name', 150);
            $table->string('transaction_type', 20)->default('pengeluaran');
            $table->string('group', 30);
            $table->foreignId('coa_id')->constrained('coa')->restrictOnDelete();
            $table->string('default_fund_type', 30);
            $table->json('allowed_fund_types');
            $table->string('form_type', 40)->default('general');
            $table->boolean('requires_asnaf')->default(false);
            $table->boolean('requires_restriction')->default(false);
            $table->boolean('requires_employee')->default(false);
            $table->boolean('requires_assignment_proof')->default(false);
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['organization_id', 'code']);
            $table->index(['organization_id', 'transaction_type', 'is_active'], 'transaction_categories_lookup_idx');
        });

        Schema::table('pengeluaran', function (Blueprint $table): void {
            $table->foreignId('transaction_category_id')
                ->nullable()
                ->after('id_pegawai')
                ->constrained('transaction_categories')
                ->nullOnDelete();
            $table->string('jenis_dana', 30)->nullable()->after('kategori');
        });

        $definitions = collect(config('transaction_categories.defaults', []));
        $resolveFundType = static function (string $accountCode): ?string {
            $rules = config('transaction_categories.fund_source_rules', []);
            uksort($rules, fn (string $left, string $right): int => strlen($right) <=> strlen($left));

            foreach ($rules as $prefix => $fundType) {
                if (str_starts_with($accountCode, $prefix)) {
                    return $fundType;
                }
            }

            return null;
        };
        $organizationIds = DB::table('coa')->whereNotNull('organization_id')->distinct()->pluck('organization_id');

        foreach ($organizationIds as $organizationId) {
            foreach ($definitions as $definition) {
                $coaId = DB::table('coa')
                    ->where('organization_id', $organizationId)
                    ->where('kode_akun', $definition['coa_code'])
                    ->value('id');
                if (! $coaId) {
                    continue;
                }
                $fundType = $resolveFundType($definition['coa_code']);
                if (! $fundType) {
                    continue;
                }

                DB::table('transaction_categories')->updateOrInsert(
                    ['organization_id' => $organizationId, 'code' => $definition['code']],
                    [
                        'name' => $definition['name'],
                        'transaction_type' => 'pengeluaran',
                        'group' => $fundType,
                        'coa_id' => $coaId,
                        'default_fund_type' => $fundType,
                        'allowed_fund_types' => json_encode([$fundType]),
                        'form_type' => $definition['form_type'] ?? 'general',
                        'requires_asnaf' => $definition['requires_asnaf'] ?? false,
                        'requires_restriction' => $definition['requires_restriction'] ?? false,
                        'requires_employee' => $definition['requires_employee'] ?? false,
                        'requires_assignment_proof' => $definition['requires_assignment_proof'] ?? false,
                        'is_system' => true,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        DB::table('pengeluaran')
            ->whereNotNull('organization_id')
            ->whereNotNull('coa_debit_id')
            ->orderBy('id')
            ->each(function ($expense): void {
                $category = DB::table('transaction_categories')
                    ->where('organization_id', $expense->organization_id)
                    ->where('name', $expense->kategori)
                    ->first();
                $category ??= DB::table('transaction_categories')
                    ->where('organization_id', $expense->organization_id)
                    ->where('coa_id', $expense->coa_debit_id)
                    ->first();
                if ($category) {
                    DB::table('pengeluaran')->where('id', $expense->id)->update([
                        'transaction_category_id' => $category->id,
                        'jenis_dana' => $category->default_fund_type,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('transaction_category_id');
            $table->dropColumn('jenis_dana');
        });

        Schema::dropIfExists('transaction_categories');
    }
};

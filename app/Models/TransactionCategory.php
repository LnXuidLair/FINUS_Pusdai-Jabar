<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionCategory extends Model
{
    use BelongsToOrganization, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'code',
        'name',
        'transaction_type',
        'group',
        'coa_id',
        'default_fund_type',
        'allowed_fund_types',
        'form_type',
        'requires_asnaf',
        'requires_restriction',
        'requires_employee',
        'requires_assignment_proof',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'allowed_fund_types' => 'array',
        'requires_asnaf' => 'boolean',
        'requires_restriction' => 'boolean',
        'requires_employee' => 'boolean',
        'requires_assignment_proof' => 'boolean',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function pengeluaran(): HasMany
    {
        return $this->hasMany(Pengeluaran::class, 'transaction_category_id');
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('transaction_type', 'pengeluaran');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function allowsFund(string $fundType): bool
    {
        return $this->resolvedFundType() === $fundType;
    }

    public function resolvedFundType(): ?string
    {
        $accountCode = $this->relationLoaded('coa')
            ? $this->coa?->kode_akun
            : $this->coa()->value('kode_akun');

        return static::fundTypeForAccountCode((string) $accountCode);
    }

    public static function fundTypeForAccountCode(string $accountCode): ?string
    {
        $rules = config('transaction_categories.fund_source_rules', []);
        $prefixes = array_keys($rules);
        usort($prefixes, fn (string $left, string $right): int => strlen($right) <=> strlen($left));

        foreach ($prefixes as $prefix) {
            if (str_starts_with($accountCode, $prefix)) {
                return $rules[$prefix];
            }
        }

        return null;
    }

    public static function ensureDefaults(): void
    {
        foreach (config('transaction_categories.defaults', []) as $definition) {
            Coa::query()
                ->where('kode_akun', $definition['coa_code'])
                ->get()
                ->each(function (Coa $coa) use ($definition): void {
                    $category = static::withTrashed()->firstOrNew([
                        'organization_id' => $coa->organization_id,
                        'code' => $definition['code'],
                    ]);
                    // Kategori bawaan yang sengaja dihapus tidak dibuat ulang.
                    if ($category->trashed()) {
                        return;
                    }
                    $mappedCoa = $category->exists
                        ? $category->coa()->first()
                        : $coa;
                    $fundType = static::fundTypeForAccountCode((string) $mappedCoa?->kode_akun);
                    if (! $fundType) {
                        return;
                    }

                    if (! $category->exists) {
                        $category->fill([
                            'name' => $definition['name'],
                            'transaction_type' => 'pengeluaran',
                            'coa_id' => $coa->id,
                            'form_type' => $definition['form_type'] ?? 'general',
                            'requires_asnaf' => $definition['requires_asnaf'] ?? false,
                            'requires_restriction' => $definition['requires_restriction'] ?? false,
                            'requires_employee' => $definition['requires_employee'] ?? false,
                            'requires_assignment_proof' => $definition['requires_assignment_proof'] ?? false,
                            'is_system' => true,
                            'is_active' => true,
                        ]);
                    }

                    $category->fill([
                        'group' => $fundType,
                        'default_fund_type' => $fundType,
                        'allowed_fund_types' => [$fundType],
                    ])->save();
                });
        }
    }
}

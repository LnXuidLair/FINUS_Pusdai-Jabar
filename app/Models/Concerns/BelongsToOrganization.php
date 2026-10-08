<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use App\Support\OrganizationContext;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder): void {
            $organizationId = OrganizationContext::currentOrganizationId();

            if ($organizationId) {
                $builder->where(
                    $builder->getModel()->qualifyColumn('organization_id'),
                    $organizationId
                );
            } elseif (OrganizationContext::requiresOrganization()) {
                // Fail closed: portal Admin/Pegawai tanpa organisasi tidak boleh
                // mendapatkan seluruh data dari berbagai masjid.
                $builder->whereRaw('1 = 0');
            }
        });

        static::creating(function ($model): void {
            $organizationId = OrganizationContext::currentOrganizationId();
            $required = OrganizationContext::requiresOrganization();

            if (! empty($model->organization_id)) {
                // Cegah pengisian organization_id masjid lain dari portal internal.
                if ($required && (int) $model->organization_id !== $organizationId) {
                    throw new AuthorizationException('Tidak dapat membuat data untuk organisasi lain.');
                }

                return;
            }

            if ($organizationId) {
                $model->organization_id = $organizationId;
            } elseif ($required) {
                throw new AuthorizationException('Organisasi akun tidak tersedia. Data tidak dapat dibuat.');
            }
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function scopeForOrganization(Builder $query, int|Organization $organization): Builder
    {
        $organizationId = $organization instanceof Organization
            ? (int) $organization->getKey()
            : $organization;

        return $query
            ->withoutGlobalScope('organization')
            ->where($query->qualifyColumn('organization_id'), $organizationId);
    }
}
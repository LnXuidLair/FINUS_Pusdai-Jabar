<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use App\Support\OrganizationContext;
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
            }
        });

        static::creating(function ($model): void {
            if (! empty($model->organization_id)) {
                return;
            }

            $organizationId = OrganizationContext::currentOrganizationId();

            if ($organizationId) {
                $model->organization_id = $organizationId;
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

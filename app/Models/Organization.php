<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    protected $table = 'organizations';

    protected $fillable = [
        'public_id',
        'name',
        'slug',
        'legal_name',
        'address',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'country_code',
        'phone',
        'email',
        'logo_path',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'organization_id');
    }

    public function admins(): HasMany
    {
        return $this->users()->where('role', User::ROLE_ADMIN);
    }

    public function staffUsers(): HasMany
    {
        return $this->users()->where('role', User::ROLE_PEGAWAI);
    }

    public function pegawais(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'organization_id');
    }


    public function gajiJabatans(): HasMany
    {
        return $this->hasMany(GajiJabatan::class, 'organization_id');
    }

    public function coas(): HasMany
    {
        return $this->hasMany(Coa::class, 'organization_id');
    }

    public function transactionCategories(): HasMany
    {
        return $this->hasMany(TransactionCategory::class, 'organization_id');
    }

    public function pengeluarans(): HasMany
    {
        return $this->hasMany(Pengeluaran::class, 'organization_id');
    }

    public function ziswafPenerimaans(): HasMany
    {
        return $this->hasMany(ZiswafPenerimaan::class, 'organization_id');
    }

    public function paymentChannels(): HasMany
    {
        return $this->hasMany(OrganizationPaymentChannel::class, 'organization_id');
    }

    public function settings(): HasOne
    {
        return $this->hasOne(OrganizationSetting::class, 'organization_id');
    }
}

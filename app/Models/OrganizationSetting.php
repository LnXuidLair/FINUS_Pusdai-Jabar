<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;

class OrganizationSetting extends Model
{
    use BelongsToOrganization;

    protected $table = 'organization_settings';

    protected $fillable = [
        'organization_id',
        'timezone',
        'currency',
        'zakat_enabled',
        'infaq_enabled',
        'wakaf_enabled',
        'settings',
    ];

    protected $casts = [
        'zakat_enabled' => 'boolean',
        'infaq_enabled' => 'boolean',
        'wakaf_enabled' => 'boolean',
        'settings' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;

class OrganizationPaymentChannel extends Model
{
    use BelongsToOrganization;

    protected $table = 'organization_payment_channels';

    protected $fillable = [
        'organization_id',
        'type',
        'provider',
        'account_name',
        'account_number',
        'qr_image_path',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}

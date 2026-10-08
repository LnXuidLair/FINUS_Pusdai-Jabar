<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GajiJabatanRiwayat extends Model
{
    use BelongsToOrganization;
    protected $table = 'gaji_jabatan_riwayat';

    protected $fillable = [
        'organization_id',
        'gaji_jabatan_id',
        'gaji_perhari',
        'berlaku_mulai',
        'berlaku_sampai',
        'created_by',
    ];

    protected $casts = [
        'berlaku_mulai' => 'date',
        'berlaku_sampai' => 'date',
    ];

    public function gajiJabatan()
    {
        return $this->belongsTo(GajiJabatan::class, 'gaji_jabatan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

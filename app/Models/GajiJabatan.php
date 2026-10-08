<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GajiJabatan extends Model
{
    use BelongsToOrganization;
    use HasFactory;

    protected $table = 'gaji_jabatan';

    protected $fillable = [
        'organization_id',
        'jabatan',
        'gaji_perhari',
        'created_by',
        'updated_by',
    ];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'jabatan', 'jabatan');
    }

    public function riwayat()
    {
        return $this->hasMany(GajiJabatanRiwayat::class, 'gaji_jabatan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

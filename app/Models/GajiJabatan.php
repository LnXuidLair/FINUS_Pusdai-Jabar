<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiJabatan extends Model
{
    protected $table = 'gaji_jabatan';

    protected $fillable = [
        'jabatan',
        'gaji_perhari',
    ];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'jabatan', 'jabatan');
    }

    public function riwayat()
    {
        return $this->hasMany(GajiJabatanRiwayat::class, 'gaji_jabatan_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GajiJabatanRiwayat extends Model
{
    protected $table = 'gaji_jabatan_riwayat';

    protected $fillable = [
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
}

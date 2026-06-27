<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranSiswa extends Model
{
    protected $guarded = [];
    protected $casts = ['tanggal_bayar' => 'date'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}

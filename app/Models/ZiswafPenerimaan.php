<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZiswafPenerimaan extends Model
{
    protected $table = 'ziswaf_penerimaan';

    protected $fillable = [
        'muzakki_id',
        'id_pegawai',
        'tanggal',
        'jenis_ziswaf',
        'nominal',
        'metode_pembayaran',
        'keterangan',
        'coa_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'integer',
    ];

    public function muzakki()
    {
        return $this->belongsTo(User::class, 'muzakki_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }
}

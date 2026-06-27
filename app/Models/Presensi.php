<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'id_pegawai',
        'tanggal',
        'status',
        'keterangan',
        'bukti_kehadiran',
        'is_approved',
        'approved_by',
        'approved_at'
    ];
    protected $casts = [
    'tanggal' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
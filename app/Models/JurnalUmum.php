<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    protected $table = 'jurnal_umum';

    protected $guarded = [];

    protected $casts = [
        'tgl' => 'date',
        'tanggal' => 'date',
    ];

    public function detail()
    {
        return $this->hasMany(JurnalDetail::class, 'jurnal_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
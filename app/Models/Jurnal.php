<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $guarded = [];
    protected $casts = ['tanggal' => 'date'];
}

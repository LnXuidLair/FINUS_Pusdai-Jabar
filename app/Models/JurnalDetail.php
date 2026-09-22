<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    protected $table = 'jurnal_detail';

    protected $guarded = [];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function jurnalUmum()
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function getLabelJenisDanaAttribute(): string
    {
        return match ($this->jenis_dana) {
            'zakat' => 'Dana Zakat',
            'infak_sedekah', 'infak' => 'Dana Infak/Sedekah',
            'amil' => 'Dana Amil',
            'non_halal' => 'Dana Non-Halal',
            'wakaf' => 'Dana Wakaf',
            'wakaf_temporer' => 'Liabilitas Wakaf Temporer',
            'nazhir' => 'Dana Nazhir',
            default => ucfirst(str_replace('_', ' ', $this->jenis_dana ?? 'amil')),
        };
    }

    public function getWarnaJenisDanaAttribute(): string
    {
        return match ($this->jenis_dana) {
            'zakat' => 'emerald',
            'infak_sedekah', 'infak' => 'blue',
            'amil' => 'amber',
            'non_halal' => 'red',
            'wakaf' => 'purple',
            'wakaf_temporer' => 'indigo',
            'nazhir' => 'teal',
            default => 'gray',
        };
    }

    public function scopeZakat($query)
    {
        return $query->where('jenis_dana', 'zakat');
    }

    public function scopeInfak($query)
    {
        return $query->whereIn('jenis_dana', ['infak', 'infak_sedekah']);
    }

    public function scopeAmil($query)
    {
        return $query->where('jenis_dana', 'amil');
    }
}

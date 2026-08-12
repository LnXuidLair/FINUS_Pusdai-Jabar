<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AgendaKegiatan extends Model
{
    protected $table = 'agenda_kegiatan';

    protected $fillable = [
        'judul',
        'kategori',
        'hari',
        'waktu',
        'lokasi',
        'deskripsi',
        'is_aktif',
        'urutan',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'urutan'   => 'integer',
    ];

    /**
     * Hanya agenda yang aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Label tampilan kategori.
     */
    public static function kategoriLabels(): array
    {
        return [
            'kajian'     => 'Kajian',
            'sosial'     => 'Sosial',
            'pendidikan' => 'Pendidikan',
            'ibadah'     => 'Ibadah',
        ];
    }

    public function getKategoriLabelAttribute(): string
    {
        return self::kategoriLabels()[$this->kategori] ?? ucfirst($this->kategori);
    }
}

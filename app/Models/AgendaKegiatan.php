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
        'tipe_jadwal',
        'hari_rutin',
        'tanggal',
        'hari',
        'waktu',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'deskripsi',
        'is_aktif',
        'urutan',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'urutan'   => 'integer',
        'tanggal'  => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Format waktu: "HH.MM - HH.MM"
            if ($model->waktu_mulai && $model->waktu_selesai) {
                $mulai = str_replace(':', '.', substr($model->waktu_mulai, 0, 5));
                $selesai = str_replace(':', '.', substr($model->waktu_selesai, 0, 5));
                $model->waktu = "{$mulai} - {$selesai}";
            }

            // Format hari/jadwal
            if ($model->tipe_jadwal === 'rutin') {
                $model->hari = 'Setiap ' . $model->hari_rutin;
            } elseif ($model->tipe_jadwal === 'sekali' && $model->tanggal) {
                // Gunakan Carbon untuk menerjemahkan ke format hari tanggal Indonesia
                $model->hari = \Carbon\Carbon::parse($model->tanggal)->translatedFormat('l, d F Y');
            }
        });
    }

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

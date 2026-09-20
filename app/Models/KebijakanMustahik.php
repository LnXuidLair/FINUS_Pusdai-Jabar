<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KebijakanMustahik extends Model
{
    public const ASNAF = [
        'fakir' => 'Fakir',
        'miskin' => 'Miskin',
        'amil' => 'Amil',
        'muallaf' => 'Muallaf',
        'riqab' => 'Riqab',
        'gharim' => 'Gharim',
        'fisabilillah' => 'Fisabilillah',
        'ibnu_sabil' => 'Ibnu Sabil',
    ];

    public const BENTUK_PENYALURAN = [
        'uang' => 'Uang',
        'barang' => 'Barang',
        'keduanya' => 'Uang dan Barang',
    ];

    protected $table = 'kebijakan_mustahik';

    protected $fillable = [
        'asnaf',
        'prioritas',
        'target_persentase',
        'batas_bantuan',
        'bentuk_penyaluran',
        'kriteria',
        'dasar_aturan',
        'berlaku_mulai',
        'berlaku_sampai',
        'aktif',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'prioritas' => 'integer',
        'target_persentase' => 'decimal:2',
        'batas_bantuan' => 'integer',
        'berlaku_mulai' => 'date',
        'berlaku_sampai' => 'date',
        'aktif' => 'boolean',
    ];

    public function scopeBerlakuPada(Builder $query, $tanggal = null): Builder
    {
        $date = $tanggal
            ? (is_string($tanggal) ? $tanggal : $tanggal->format('Y-m-d'))
            : now()->toDateString();

        return $query
            ->where('aktif', true)
            ->whereDate('berlaku_mulai', '<=', $date)
            ->where(function (Builder $builder) use ($date): void {
                $builder->whereNull('berlaku_sampai')
                    ->orWhereDate('berlaku_sampai', '>=', $date);
            });
    }

    public static function aturanUntuk(string $asnaf, $tanggal = null): ?self
    {
        return static::query()
            ->where('asnaf', $asnaf)
            ->berlakuPada($tanggal)
            ->latest('berlaku_mulai')
            ->first();
    }
}

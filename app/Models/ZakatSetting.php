<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ZakatSetting extends Model
{
    protected $table = 'zakat_settings';

    protected $fillable = [
        'tahun',
        'nisab_penghasilan_tahunan',
        'nisab_penghasilan_bulanan',
        'nisab_maal',
        'persentase_zakat',
        'zakat_fitrah_per_jiwa',
        'beras_fitrah_kg',
        'beras_fitrah_liter',
        'sumber',
        'berlaku_mulai',
        'berlaku_sampai',
        'aktif',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'nisab_penghasilan_tahunan' => 'integer',
        'nisab_penghasilan_bulanan' => 'integer',
        'nisab_maal' => 'integer',
        'persentase_zakat' => 'decimal:2',
        'zakat_fitrah_per_jiwa' => 'integer',
        'beras_fitrah_kg' => 'decimal:2',
        'beras_fitrah_liter' => 'decimal:2',
        'berlaku_mulai' => 'date',
        'berlaku_sampai' => 'date',
        'aktif' => 'boolean',
    ];

    public function scopeBerlaku(Builder $query): Builder
    {
        return $query
            ->where('aktif', true)
            ->whereDate('berlaku_mulai', '<=', now()->toDateString())
            ->where(function (Builder $builder): void {
                $builder->whereNull('berlaku_sampai')
                    ->orWhereDate(
                        'berlaku_sampai',
                        '>=',
                        now()->toDateString()
                    );
            });
    }

    public static function pengaturanAktif(): self
    {
        return static::query()
            ->berlaku()
            ->latest('tahun')
            ->firstOrFail();
    }
}
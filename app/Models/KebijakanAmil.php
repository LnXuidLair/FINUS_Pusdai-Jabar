<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KebijakanAmil extends Model
{
    protected $table = 'kebijakan_amil';

    protected $fillable = [
        'jenis_sumber',
        'persentase_amil',
        'berlaku_mulai',
        'berlaku_sampai',
        'dasar_aturan',
        'potong_infak_terikat',
        'aktif',
    ];

    protected $casts = [
        'persentase_amil' => 'decimal:2',
        'berlaku_mulai' => 'date',
        'berlaku_sampai' => 'date',
        'potong_infak_terikat' => 'boolean',
        'aktif' => 'boolean',
    ];

    public function scopeBerlakuPada(Builder $query, $tanggal): Builder
    {
        $dateStr = $tanggal ? (is_string($tanggal) ? $tanggal : $tanggal->format('Y-m-d')) : now()->toDateString();

        return $query
            ->where('aktif', true)
            ->whereDate('berlaku_mulai', '<=', $dateStr)
            ->where(function (Builder $builder) use ($dateStr): void {
                $builder->whereNull('berlaku_sampai')
                    ->orWhereDate('berlaku_sampai', '>=', $dateStr);
            });
    }

    public static function ambilKebijakan(string $jenisSumber, $tanggal = null): ?self
    {
        return static::query()
            ->where('jenis_sumber', $jenisSumber)
            ->berlakuPada($tanggal)
            ->latest('berlaku_mulai')
            ->first();
    }
}

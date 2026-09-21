<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HargaBarangZakat extends Model
{
    public const STATUS = [
        'draft' => 'Draft',
        'disetujui' => 'Disetujui',
    ];

    protected $table = 'harga_barang_zakat';

    protected $fillable = [
        'barang_zakat_id',
        'wilayah',
        'harga_per_satuan',
        'berlaku_mulai',
        'berlaku_sampai',
        'sumber_harga',
        'status',
        'catatan',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'harga_per_satuan' => 'integer',
        'berlaku_mulai' => 'date',
        'berlaku_sampai' => 'date',
        'approved_at' => 'datetime',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangZakat::class, 'barang_zakat_id');
    }

    public function scopeBerlakuPada(Builder $query, $tanggal = null): Builder
    {
        $date = $tanggal
            ? (is_string($tanggal) ? $tanggal : $tanggal->format('Y-m-d'))
            : now()->toDateString();

        return $query
            ->where('status', 'disetujui')
            ->whereDate('berlaku_mulai', '<=', $date)
            ->where(function (Builder $builder) use ($date): void {
                $builder->whereNull('berlaku_sampai')
                    ->orWhereDate('berlaku_sampai', '>=', $date);
            });
    }
}

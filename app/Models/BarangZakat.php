<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BarangZakat extends Model
{
    public const KATEGORI = [
        'makanan_pokok' => 'Makanan Pokok',
        'hasil_pertanian' => 'Hasil Pertanian',
        'peternakan' => 'Peternakan',
        'logam_mulia' => 'Logam Mulia',
        'lainnya' => 'Barang Lainnya',
    ];

    public const SATUAN = [
        'kg' => 'Kilogram',
        'gram' => 'Gram',
        'liter' => 'Liter',
        'ekor' => 'Ekor',
        'unit' => 'Unit',
    ];

    public const METODE_PENILAIAN = [
        'harga_pasar' => 'Harga Pasar',
        'harga_resmi' => 'Harga Resmi Lembaga',
        'penilaian_petugas' => 'Penilaian Petugas',
    ];

    protected $table = 'barang_zakat';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'satuan_dasar',
        'metode_penilaian',
        'coa_persediaan_id',
        'aktif',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function coaPersediaan(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_persediaan_id');
    }

    public function harga(): HasMany
    {
        return $this->hasMany(HargaBarangZakat::class, 'barang_zakat_id');
    }

    public function hargaTerbaru(): HasOne
    {
        return $this->hasOne(HargaBarangZakat::class, 'barang_zakat_id')
            ->where('status', 'disetujui')
            ->latestOfMany('berlaku_mulai');
    }
}

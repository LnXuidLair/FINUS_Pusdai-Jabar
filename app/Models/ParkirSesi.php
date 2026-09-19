<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkirSesi extends Model
{
    use HasFactory;

    protected $table = 'parkir_sesi';

    protected $fillable = [
        'plat_nomor',
        'jenis_kendaraan',
        'nominal',
        'metode_pembayaran',
        'status',
        'waktu_masuk',
        'waktu_keluar',
        'keterangan',
        'input_by',
        'ziswaf_penerimaan_id',
    ];

    protected $casts = [
        'waktu_masuk'  => 'datetime',
        'waktu_keluar' => 'datetime',
        'nominal'      => 'integer',
    ];

    /* ── Relasi ── */
    public function penerimaan()
    {
        return $this->belongsTo(ZiswafPenerimaan::class, 'ziswaf_penerimaan_id');
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    /* ── Scopes ── */
    public function scopeHariIni($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    /* ── Helper label ── */
    public static function tarifDefault(): array
    {
        return [
            'motor' => 3000,
            'mobil' => 5000,
            'lain'  => 3000,
        ];
    }

    public static function labelJenis(): array
    {
        return [
            'motor' => 'Motor',
            'mobil' => 'Mobil',
            'lain'  => 'Lainnya',
        ];
    }
}

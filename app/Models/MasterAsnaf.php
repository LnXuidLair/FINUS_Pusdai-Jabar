<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Master Asnaf — delapan golongan penerima zakat sesuai QS At-Taubah:60.
 *
 * Data ini bersifat tetap dan tidak berubah. Tidak boleh ditambah
 * atau dikurangi karena bersumber langsung dari Al-Qur'an.
 *
 * Target distribusi operasional disimpan bersama ketentuan pokok zakat.
 */
class MasterAsnaf extends Model
{
    protected $table = 'master_asnaf';

    protected $fillable = [
        'kode',
        'nama',
        'urutan',
        'definisi',
        'dasar_hukum',
        'aktif',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'aktif' => 'boolean',
    ];

    /**
     * Ambil semua asnaf yang aktif, berurutan sesuai QS At-Taubah:60.
     */
    public static function semuaAktif()
    {
        return static::query()
            ->where('aktif', true)
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Label key => value untuk select/dropdown.
     */
    public static function labels(): array
    {
        return static::query()
            ->where('aktif', true)
            ->orderBy('urutan')
            ->pluck('nama', 'kode')
            ->toArray();
    }
}

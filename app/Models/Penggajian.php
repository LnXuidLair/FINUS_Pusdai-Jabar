<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    protected $table = 'penggajian';

    protected $fillable = [
        'id_pegawai',
        'periode',
        'jumlah_hari',
        'gaji_perhari',
        'total_gaji',
        'status_penggajian',
        'tanggal',
        'id_jurnal',
        'jumlah_kehadiran',
        'bukti_pembayaran',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($penggajian) {

            // === 1. Ambil otomatis gaji per hari dari gaji_jabatan ===
            if (empty($penggajian->gaji_perhari)) {
                $penggajian->gaji_perhari =
                    $penggajian->pegawai->gajiJabatan->gaji_perhari ?? 0;
            }

            // === 2. Hitung jumlah kehadiran otomatis dari tabel presensi ===
            if (empty($penggajian->jumlah_kehadiran)) {
                $penggajian->jumlah_kehadiran =
                    $penggajian->pegawai->hitungKehadiran($penggajian->periode);
            }

            // === 3. Hitung total gaji otomatis ===
            if (empty($penggajian->total_gaji)) {
                $penggajian->total_gaji =
                    $penggajian->gaji_perhari * $penggajian->jumlah_kehadiran;
            }
        });
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function jurnal()
    {
        return $this->belongsTo(JurnalUmum::class, 'id_jurnal');
    }

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'id_penggajian');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZiswafPenyaluran extends Model
{
    protected $table = 'ziswaf_penyaluran';

    protected $fillable = [
        'tanggal',
        'kategori_program',
        'penerima_manfaat',
        'jumlah_penerima',
        'nominal',
        'jenis_ziswaf_asal',
        'bukti_penyaluran',
        'keterangan',
        'id_pengeluaran',
        'asnaf',
        'nama_penerima',
        'alamat_penerima',
        'nik_penerima',
        'no_hp_penerima',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_penerima' => 'integer',
        'nominal' => 'integer',
    ];

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'id_pengeluaran');
    }
}

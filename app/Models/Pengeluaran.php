<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'id_penggajian',
        'kategori',
        'deskripsi',
        'jumlah',
        'tanggal',
        'bukti_pembayaran',
        'jenis',
        'referensi_penggajian_id',
        'coa_debit_id',
        'coa_kredit_id',
        'nominal',
        'keterangan',
        'status_verifikasi',
        'catatan_verifikasi',
        'nominal_ocr',
        'tanggal_ocr',
    ];

    public function penggajian()
    {
        return $this->belongsTo(Penggajian::class, 'id_penggajian');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    protected $table = 'jurnal_umum';

    protected $guarded = [];

    protected $casts = [
        'tgl' => 'date',
        'tanggal' => 'date',
    ];

    public function detail()
    {
        return $this->hasMany(JurnalDetail::class, 'jurnal_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePemasukan($query)
    {
        return $query->where(function ($q) {
            $q->where('sumber_tabel', 'ziswaf_penerimaan')
              ->orWhereHas('detail', function ($dq) {
                  $dq->where('debit', '>', 0)
                     ->whereHas('coa', function ($cq) {
                         $cq->whereIn('kode_akun', ['1101', '1102'])
                            ->orWhere('nama_akun', 'like', '%Kas%')
                            ->orWhere('nama_akun', 'like', '%Bank%');
                     });
              });
        });
    }

    public function scopePengeluaran($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('sumber_tabel', ['pengeluaran', 'penggajian', 'ziswaf_penyaluran'])
              ->orWhereHas('detail', function ($dq) {
                  $dq->where('credit', '>', 0)
                     ->whereHas('coa', function ($cq) {
                         $cq->whereIn('kode_akun', ['1101', '1102'])
                            ->orWhere('nama_akun', 'like', '%Kas%')
                            ->orWhere('nama_akun', 'like', '%Bank%');
                     });
              });
        });
    }
}
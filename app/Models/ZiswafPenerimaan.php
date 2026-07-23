<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZiswafPenerimaan extends Model
{
    protected $table = 'ziswaf_penerimaan';

    protected $fillable = [
        'order_id',
        'snap_token',
        'payment_gateway',
        'muzakki_id',
        'id_pegawai',
        'tanggal',
        'jenis_ziswaf',
        'nominal',
        'metode_pembayaran',
        'payment_status',
        'payment_type',
        'transaction_id',
        'fraud_status',
        'bukti_pembayaran',
        'status_verifikasi',
        'catatan_verifikasi',
        'verified_by',
        'verified_at',
        'paid_at',
        'keterangan',
        'rincian_perhitungan',
        'nisab_digunakan',
        'persentase_zakat',
        'coa_id',
        'jurnal_id',
        'zakat_setting_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'integer',
        'verified_at' => 'datetime',
        'paid_at' => 'datetime',
        'rincian_perhitungan' => 'array',
        'persentase_zakat' => 'decimal:2',
    ];

    public function muzakki()
    {
        return $this->belongsTo(User::class, 'muzakki_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function zakatSetting()
    {
        return $this->belongsTo(ZakatSetting::class, 'zakat_setting_id');
    }
}
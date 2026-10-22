<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;

class ZiswafPenerimaan extends Model
{
    use BelongsToOrganization;
    protected $table = 'ziswaf_penerimaan';

    protected $fillable = [
        'organization_id',
        'order_id',
        'snap_token',
        'payment_gateway',
        'muzakki_id',
        'id_pegawai',
        'tanggal',
        'jenis_ziswaf',
        'restriction_type',
        'wakaf_type',
        'wakaf_return_date',
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
        'persentase_amil',
        'nominal_amil',
        'persentase_nazhir',
        'nominal_nazhir',
        'snapshot_kebijakan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'wakaf_return_date' => 'date',
        'nominal' => 'integer',
        'verified_at' => 'datetime',
        'paid_at' => 'datetime',
        'rincian_perhitungan' => 'array',
        'persentase_zakat' => 'decimal:2',
        'persentase_amil' => 'decimal:2',
        'persentase_nazhir' => 'decimal:2',
        'snapshot_kebijakan' => 'array',
    ];

    public function scopeAwaitingManualVerification($query)
    {
        return $query->where(fn ($q) => $q->whereNull('status_verifikasi')->orWhere('status_verifikasi', 'pending'))
            ->whereIn('metode_pembayaran', ['manual_transfer', 'qris_manual', 'tunai', 'kas'])
            ->where(fn ($q) => $q->whereNull('payment_status')->orWhere('payment_status', 'manual_pending'));
    }

    public function getCanVerifyManuallyAttribute(): bool
    {
        return in_array($this->status_verifikasi, [null, 'pending'], true)
            && in_array($this->metode_pembayaran, ['manual_transfer', 'qris_manual', 'tunai', 'kas'], true)
            && in_array($this->payment_status, [null, 'manual_pending'], true);
    }

    public function muzakki()
    {
        return $this->belongsTo(User::class, 'muzakki_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function jurnal()
    {
        return $this->belongsTo(JurnalUmum::class, 'jurnal_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }
}

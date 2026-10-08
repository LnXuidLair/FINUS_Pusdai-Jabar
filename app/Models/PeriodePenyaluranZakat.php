<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PeriodePenyaluranZakat extends Model
{
    use BelongsToOrganization;
    public const STATUS_AKTIF = 'aktif';

    public const STATUS_DITUTUP = 'ditutup';

    protected $table = 'periode_penyaluran_zakat';

    protected $fillable = [
        'organization_id',
        'periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'persentase_amil',
        'target_asnaf',
        'saldo_sebelum_penyaluran',
        'total_disalurkan',
        'saldo_akhir',
        'status',
        'ditutup_at',
        'created_by',
        'ditutup_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'persentase_amil' => 'decimal:2',
        'target_asnaf' => 'array',
        'saldo_sebelum_penyaluran' => 'integer',
        'total_disalurkan' => 'integer',
        'saldo_akhir' => 'integer',
        'ditutup_at' => 'datetime',
    ];

    public function pengeluaran(): HasOne
    {
        return $this->hasOne(Pengeluaran::class, 'periode_penyaluran_zakat_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ditutupBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditutup_by');
    }
}

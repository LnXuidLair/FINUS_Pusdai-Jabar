<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use BelongsToOrganization;

    public const STATUS_HADIR = 'hadir';
    public const STATUS_IZIN = 'izin';
    public const STATUS_SAKIT = 'sakit';
    public const STATUS_TIDAK_HADIR = 'tidak hadir';

    public const KONDISI_NORMAL = 'normal';
    public const KONDISI_PULANG_AWAL = 'pulang_awal';
    public const KONDISI_TUGAS_DINAS = 'tugas_dinas';
    public const KONDISI_LEMBUR = 'lembur';

    protected $table = 'presensi';

    protected $fillable = [
        'organization_id',
        'id_pegawai',
        'tanggal',
        'status',
        'kondisi',
        'jam_datang',
        'jam_pulang',
        'keterangan',
        // Kolom lama dipertahankan agar data presensi sebelum fitur Datang–Pulang tetap terbaca.
        'bukti_kehadiran',
        'bukti_datang',
        'bukti_pulang',
        'bukti_status',
        'input_datang_by',
        'input_datang_role',
        'input_pulang_by',
        'input_pulang_role',
        'input_status_by',
        'input_status_role',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function inputDatangBy()
    {
        return $this->belongsTo(User::class, 'input_datang_by');
    }

    public function inputPulangBy()
    {
        return $this->belongsTo(User::class, 'input_pulang_by');
    }

    public function inputStatusBy()
    {
        return $this->belongsTo(User::class, 'input_status_by');
    }

    /**
     * Record dianggap lengkap jika bukti yang diwajibkan sudah ada.
     * Hanya record lengkap yang boleh di-ACC dan masuk perhitungan gaji.
     */
    public function isComplete(): bool
    {
        if ($this->status === self::STATUS_HADIR) {
            // Data baru Datang–Pulang harus lengkap pada kedua checkpoint.
            if (filled($this->jam_datang) || filled($this->jam_pulang) || filled($this->bukti_datang) || filled($this->bukti_pulang)) {
                return filled($this->jam_datang)
                    && filled($this->jam_pulang)
                    && filled($this->bukti_datang)
                    && filled($this->bukti_pulang);
            }

            // Kompatibilitas data lama sebelum fitur Datang–Pulang.
            return filled($this->bukti_kehadiran);
        }

        if (in_array($this->status, [self::STATUS_IZIN, self::STATUS_SAKIT], true)) {
            return filled($this->keterangan)
                && (filled($this->bukti_status) || filled($this->bukti_kehadiran));
        }

        return $this->status === self::STATUS_TIDAK_HADIR;
    }

    public function isLegacyAttendance(): bool
    {
        return blank($this->jam_datang)
            && blank($this->jam_pulang)
            && filled($this->bukti_kehadiran);
    }

    public function kondisiLabel(): string
    {
        return match ($this->kondisi) {
            self::KONDISI_PULANG_AWAL => 'Pulang Lebih Awal',
            self::KONDISI_TUGAS_DINAS => 'Tugas / Panggilan Dinas',
            self::KONDISI_LEMBUR => 'Lembur',
            default => 'Normal',
        };
    }
}
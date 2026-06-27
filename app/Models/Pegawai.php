<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'nip',
        'nama_pegawai',
        'jabatan',
        'email',
        'alamat',
        'is_verified',
        'gender',
        'no_telp',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function gajiJabatan()
    {
        return $this->belongsTo(GajiJabatan::class, 'jabatan', 'jabatan');
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class, 'id_pegawai');
    }

    public function penggajians()
    {
        return $this->hasMany(Penggajian::class, 'id_pegawai');
    }

    public function hitungKehadiran($periode)
    {
        return $this->presensis()
            ->whereMonth('tanggal', date('m', strtotime($periode)))
            ->whereYear('tanggal', date('Y', strtotime($periode)))
            ->where('status', 'hadir')
            ->count();
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }

    public function getJabatanSlugAttribute(): string
    {
        return str($this->jabatan ?: 'pegawai')
            ->lower()
            ->slug()
            ->toString();
    }

    public function getDashboardProfileAttribute(): array
    {
        $jabatan = $this->jabatan ?: 'Pegawai';

        $colors = [
            '#14532d',
            '#0f766e',
            '#0369a1',
            '#1d4ed8',
            '#4338ca',
            '#7e22ce',
            '#9f1239',
            '#c2410c',
            '#475569',
        ];

        $colorIndex = (int) sprintf('%u', crc32($this->jabatan_slug)) % count($colors);

        return [
            'jabatan' => $jabatan,
            'slug' => $this->jabatan_slug,
            'color' => $colors[$colorIndex],
            'subtitle' => "Dashboard khusus {$jabatan}.",
            'focus' => [
                "Pelaksanaan tugas {$jabatan}",
                'Presensi dan aktivitas pribadi',
                'Koordinasi dengan admin dan pengurus',
            ],
        ];
    }
}
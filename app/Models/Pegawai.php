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

    public const AKSES_UMUM = 'umum';
    public const AKSES_DKM = 'dkm';
    public const AKSES_KEUANGAN = 'keuangan';

    public const ACCESS_LABELS = [
        self::AKSES_UMUM => 'Pegawai Umum',
        self::AKSES_DKM => 'DKM',
        self::AKSES_KEUANGAN => 'Keuangan',
    ];

    private const ACCESS_KEYWORDS = [
        self::AKSES_KEUANGAN => [
            'keuangan',
            'bendahara',
        ],
        self::AKSES_DKM => [
            'dkm',
            'dewan kemakmuran',
            'dewan kemakmuran masjid',
        ],
    ];

    /**
     * Email institusi Pegawai selalu disimpan dalam huruf kecil.
     */
    public function setEmailAttribute(?string $value): void
    {
        $this->attributes['email'] = strtolower(trim((string) $value));
    }

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
        $accessRole = $this->akses_role;

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

        $subtitle = "Dashboard khusus {$jabatan}.";
        $focus = [
            "Pelaksanaan tugas {$jabatan}",
            'Presensi dan aktivitas pribadi',
            'Koordinasi dengan admin dan pengurus',
        ];

        if ($accessRole === self::AKSES_DKM) {
            $subtitle = 'Dashboard DKM untuk pemantauan laporan keuangan FINUS.';
            $focus = [
                'Memantau jurnal umum dan arus kas',
                'Meninjau transparansi laporan keuangan',
                'Koordinasi keputusan dengan pengurus dan keuangan',
            ];
        }

        if ($accessRole === self::AKSES_KEUANGAN) {
            $subtitle = 'Dashboard Keuangan untuk pencatatan pengeluaran dan penggajian.';
            $focus = [
                'Input pengeluaran operasional',
                'Mengelola status penggajian pegawai',
                'Meninjau laporan keuangan dan arus kas',
            ];
        }

        return [
            'jabatan' => $jabatan,
            'slug' => $this->jabatan_slug,
            'color' => $colors[$colorIndex],
            'subtitle' => $subtitle,
            'focus' => $focus,
        ];
    }

    public static function resolveAksesRoleFromJabatan(?string $jabatan): string
    {
        $normalizedJabatan = str($jabatan ?? '')
            ->ascii()
            ->lower()
            ->squish()
            ->toString();

        foreach (self::ACCESS_KEYWORDS as $accessRole => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($normalizedJabatan, $keyword)) {
                    return $accessRole;
                }
            }
        }

        return self::AKSES_UMUM;
    }

    public function getAksesRoleAttribute(?string $value): string
    {
        return self::resolveAksesRoleFromJabatan($this->attributes['jabatan'] ?? null);
    }

    public function getAksesRoleLabelAttribute(): string
    {
        return self::ACCESS_LABELS[$this->akses_role] ?? self::ACCESS_LABELS[self::AKSES_UMUM];
    }

    public function hasAksesRole(string ...$accessRoles): bool
    {
        return in_array($this->akses_role, $accessRoles, true);
    }
}

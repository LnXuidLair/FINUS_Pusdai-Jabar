<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PEGAWAI = 'pegawai';
    public const ROLE_JAMAAH = 'jamaah';

    private const RECOVERY_DIGITS = '0123456789';
    private const RECOVERY_UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const RECOVERY_LOWER = 'abcdefghijklmnopqrstuvwxyz';
    private const RECOVERY_SYMBOLS = '!@#$%&*;';
    private const RECOVERY_ALPHABET = self::RECOVERY_DIGITS
        . self::RECOVERY_UPPER
        . self::RECOVERY_LOWER
        . self::RECOVERY_SYMBOLS;

    /*
     * Delapan karakter pertama dari 16 karakter random-part membawa signature
     * unik berbasis users.id. Delapan karakter terakhir tetap acak dan selalu
     * berisi minimal satu angka, huruf besar, huruf kecil, dan simbol.
     *
     * Karena users.id unik, signature ini juga unik. Pengecekan seluruh kode
     * tetap dilakukan sebagai lapisan tambahan untuk data legacy.
     */
    private const RECOVERY_SIGNATURE_LENGTH = 8;
    private const RECOVERY_RANDOM_LENGTH = 8;
    private const RECOVERY_LOCK_NAME = 'finus_pegawai_recovery_code_unique';

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'recovery_code',
        'password_changed_at',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'recovery_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_verification_code_expires_at' => 'datetime',
        'password_changed_at' => 'datetime',
        // Database menyimpan ciphertext. Saat dibaca melalui model User,
        // Laravel otomatis mendekripsinya menjadi plaintext.
        'recovery_code' => 'encrypted',
    ];

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'email', 'email');
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isPegawai(): bool
    {
        return $this->hasRole(self::ROLE_PEGAWAI);
    }

    public function isJamaah(): bool
    {
        return $this->hasRole(self::ROLE_JAMAAH);
    }

    public function getFirstNameAttribute(): string
    {
        return explode(' ', trim($this->name))[0];
    }


    /**
     * Email akun selalu disimpan dalam huruf kecil agar Admin dan Pegawai
     * tidak memiliki variasi kapitalisasi pada alamat yang sama.
     */
    public function setEmailAttribute(?string $value): void
    {
        $this->attributes['email'] = strtolower(trim((string) $value));
    }

    /**
     * Generator umum untuk Recovery Code Admin.
     * Menghasilkan 16 karakter acak (4 x 4) dan menjamin minimal satu angka,
     * huruf besar, huruf kecil, dan simbol.
     */
    public static function generateRecoveryCode(): string
    {
        $chars = [
            self::randomCharacter(self::RECOVERY_DIGITS),
            self::randomCharacter(self::RECOVERY_UPPER),
            self::randomCharacter(self::RECOVERY_LOWER),
            self::randomCharacter(self::RECOVERY_SYMBOLS),
        ];

        while (count($chars) < 16) {
            $chars[] = self::randomCharacter(self::RECOVERY_ALPHABET);
        }

        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }

        return 'FINUS-' . implode('-', str_split(implode('', $chars), 4));
    }

    public function ziswafPenerimaans()
    {
        return $this->hasMany(ZiswafPenerimaan::class, 'muzakki_id');
    }

    /**
     * Mengganti Recovery Code Pegawai dengan kode baru yang WAJIB unik.
     *
     * Jaminan keunikan di aplikasi FINUS:
     * 1. setiap User memiliki users.id yang unik;
     * 2. 8 karakter pertama dikonstruksi dari users.id dengan transformasi
     *    satu-ke-satu, sehingga berbeda untuk setiap Pegawai;
     * 3. sebelum disimpan, seluruh Recovery Code aktif tetap diperiksa;
     * 4. MySQL advisory lock mencegah dua proses recovery-code berjalan
     *    bersamaan pada database yang sama;
     * 5. jika kandidat bentrok dengan data legacy, kandidat TIDAK disimpan
     *    dan sistem terus membuat kandidat baru tanpa batas percobaan.
     */
    public function rotateRecoveryCode(): string
    {
        abort_unless($this->role === self::ROLE_PEGAWAI, 500);

        $lockResult = DB::selectOne(
            'SELECT GET_LOCK(?, 15) AS acquired',
            [self::RECOVERY_LOCK_NAME]
        );

        if ((int) ($lockResult->acquired ?? 0) !== 1) {
            throw new RuntimeException(
                'FINUS tidak dapat memperoleh lock pembuatan Recovery Code Pegawai.'
            );
        }

        try {
            /*
             * User baru harus memiliki ID terlebih dahulu karena ID tersebut
             * menjadi komponen jaminan keunikan Recovery Code.
             */
            if (! $this->exists) {
                $this->save();
            }

            $userId = (int) $this->getKey();
            $currentCode = '';

            try {
                $currentCode = self::normalizeRecoveryCode(
                    (string) ($this->recovery_code ?? '')
                );
            } catch (DecryptException) {
                $currentCode = '';
            }

            while (true) {
                $code = self::makeRecoveryCodeForUserId($userId);

                // Recovery Code harus benar-benar berubah saat dirotasi.
                if ($currentCode !== '' && hash_equals($currentCode, $code)) {
                    continue;
                }

                // Lapisan kompatibilitas data lama/legacy.
                if (self::recoveryCodeExists($code, $userId)) {
                    continue;
                }

                $this->recovery_code = $code;
                $this->save();

                /*
                 * Verifikasi sekali lagi saat lock masih dipegang. Jika data
                 * legacy atau penulisan lama menyebabkan konflik, jangan pernah
                 * lepaskan kode bentrok sebagai kode aktif.
                 */
                if (self::recoveryCodeExists($code, $userId)) {
                    continue;
                }

                return $code;
            }
        } finally {
            DB::selectOne(
                'SELECT RELEASE_LOCK(?) AS released',
                [self::RECOVERY_LOCK_NAME]
            );
        }
    }

    public static function normalizeRecoveryCode(string $code): string
    {
        // Case-sensitive: A dan a adalah karakter yang berbeda.
        return trim($code);
    }

    private static function recoveryCodeExists(string $code, ?int $ignoreId = null): bool
    {
        $normalized = self::normalizeRecoveryCode($code);

        return static::query()
            ->where('role', self::ROLE_PEGAWAI)
            ->whereNotNull('recovery_code')
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->get(['id', 'recovery_code'])
            ->contains(function (User $user) use ($normalized): bool {
                try {
                    $existing = self::normalizeRecoveryCode((string) $user->recovery_code);
                } catch (DecryptException) {
                    return false;
                }

                return $existing !== '' && hash_equals($existing, $normalized);
            });
    }

    private static function makeRecoveryCodeForUserId(int $userId): string
    {
        $signature = self::makeUniqueUserSignature($userId);
        $random = self::makeRandomRecoverySuffix();
        $raw = $signature . $random;

        return 'FINUS-' . implode('-', str_split($raw, 4));
    }

    /**
     * Menghasilkan signature fixed-length yang satu-ke-satu terhadap users.id.
     * Per-position Caesar shift berbasis APP_KEY hanya mengaburkan representasi
     * ID; transformasinya tetap bijektif sehingga dua ID berbeda tidak dapat
     * menghasilkan signature yang sama selama ID masih muat dalam 8 digit base-70.
     */
    private static function makeUniqueUserSignature(int $userId): string
    {
        if ($userId < 1) {
            throw new RuntimeException('User Pegawai belum memiliki ID yang valid.');
        }

        $base = strlen(self::RECOVERY_ALPHABET); // 70
        $value = $userId;
        $digits = [];

        for ($i = 0; $i < self::RECOVERY_SIGNATURE_LENGTH; $i++) {
            $digits[] = $value % $base;
            $value = intdiv($value, $base);
        }

        if ($value > 0) {
            throw new RuntimeException(
                'ID User melebihi kapasitas Recovery Code FINUS.'
            );
        }

        $keyMaterial = (string) config('app.key');
        $mask = hash('sha256', 'FINUS|RECOVERY|SIGNATURE|' . $keyMaterial, true);
        $signature = '';

        /*
         * Reverse posisi digit agar perubahan ID tidak selalu terlihat pada
         * karakter pertama. Shift tiap posisi tetap satu-ke-satu modulo 70.
         */
        $digits = array_reverse($digits);

        foreach ($digits as $i => $digit) {
            $shift = ord($mask[$i]) % $base;
            $signature .= self::RECOVERY_ALPHABET[($digit + $shift) % $base];
        }

        return $signature;
    }

    private static function makeRandomRecoverySuffix(): string
    {
        $chars = [
            self::randomCharacter(self::RECOVERY_DIGITS),
            self::randomCharacter(self::RECOVERY_UPPER),
            self::randomCharacter(self::RECOVERY_LOWER),
            self::randomCharacter(self::RECOVERY_SYMBOLS),
        ];

        while (count($chars) < self::RECOVERY_RANDOM_LENGTH) {
            $chars[] = self::randomCharacter(self::RECOVERY_ALPHABET);
        }

        // Fisher-Yates menggunakan random_int agar seluruh posisi acak.
        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }

        return implode('', $chars);
    }

    private static function randomCharacter(string $characters): string
    {
        return $characters[random_int(0, strlen($characters) - 1)];
    }
}

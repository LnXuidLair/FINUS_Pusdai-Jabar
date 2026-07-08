<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PublicEmailDomain implements ValidationRule
{
    private array $allowedDomains = [
        'gmail.com',
        'googlemail.com',

        'yahoo.com',
        'yahoo.co.id',
        'ymail.com',
        'rocketmail.com',

        'outlook.com',
        'hotmail.com',
        'live.com',
        'msn.com',

        'icloud.com',
        'me.com',
        'mac.com',

        'proton.me',
        'protonmail.com',

        'aol.com',
        'mail.com',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = strtolower(trim((string) $value));

        if (! str_contains($email, '@')) {
            $fail('Alamat email tidak valid.');
            return;
        }

        $domain = substr(strrchr($email, '@'), 1);

        if (! in_array($domain, $this->allowedDomains, true)) {
            $fail('Jamaah hanya boleh menggunakan email pribadi umum seperti Gmail, Yahoo, Outlook, Hotmail, iCloud, atau Proton. Email instansi, perusahaan, sekolah, kampus, atau organisasi tidak diperbolehkan.');
        }
    }
}
<?php

namespace App\Support;

final class PhoneNumber
{
    /**
     * Merapikan nomor telepon tanpa menebak negara pengguna.
     *
     * Nomor dari intl-tel-input disimpan dalam format internasional E.164,
     * misalnya +6281234567890, +60123456789, atau +6591234567.
     * Prefix 00 juga diterima dan diubah menjadi +. Nomor legacy yang belum
     * memiliki country code tidak dipaksa menjadi +62 agar tetap aman untuk
     * pengguna dari negara lain.
     */
    public static function normalize(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $isInternational = str_starts_with($value, '+');
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return null;
        }

        if (! $isInternational && str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
            $isInternational = true;
        }

        return $isInternational ? '+'.$digits : $digits;
    }
}

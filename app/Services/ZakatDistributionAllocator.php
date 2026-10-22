<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class ZakatDistributionAllocator
{
    public function allocate(int $amount, array $percentages): array
    {
        $weights = [];
        foreach ($percentages as $asnaf => $percentage) {
            if (! is_numeric($percentage) || $percentage < 0 || $percentage > 100
                || abs((float) $percentage * 100 - round((float) $percentage * 100)) > 0.00001) {
                throw ValidationException::withMessages(['target_asnaf' => 'Persentase harus 0–100 dengan maksimal dua angka desimal.']);
            }
            $weights[$asnaf] = (int) round((float) $percentage * 100);
        }
        if (array_sum($weights) !== 10000) {
            throw ValidationException::withMessages(['target_asnaf' => 'Total persentase penyaluran asnaf harus tepat 100%.']);
        }
        $result = [];
        $cumulative = 0;
        $allocated = 0;
        foreach ($weights as $asnaf => $weight) {
            $cumulative += $weight;
            $subtotal = intdiv($amount * $cumulative, 10000);
            $result[$asnaf] = $subtotal - $allocated;
            $allocated = $subtotal;
            if ($weight > 0 && $result[$asnaf] < 1) {
                throw ValidationException::withMessages(['target_asnaf' => 'Nominal setiap asnaf yang dipilih minimal Rp1.']);
            }
        }
        return $result;
    }
}

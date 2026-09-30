<?php

namespace Tests\Unit;

use App\Models\KetentuanPokokZakat;
use App\Services\ZakatCalculatorService;
use PHPUnit\Framework\TestCase;

class ZakatCalculatorServiceTest extends TestCase
{
    public function test_monthly_income_nisab_is_one_twelfth_of_the_annual_nisab(): void
    {
        $result = (new ZakatCalculatorService)->penghasilan([
            'periode_penghasilan' => 'bulanan',
            'penghasilan_bersih' => 10_000_000,
        ], $this->incomePolicy(96_000_000));

        $this->assertSame(96_000_000, $result['nisab_tahunan']);
        $this->assertSame(8_000_000, $result['nisab_bulanan']);
        $this->assertSame(8_000_000, $result['nisab']);
        $this->assertSame(10_000_000, $result['dasar_zakat']);
        $this->assertSame(250_000, $result['jumlah_zakat']);
    }

    public function test_annual_income_calculation_deducts_verified_prior_payments(): void
    {
        $result = (new ZakatCalculatorService)->penghasilan([
            'periode_penghasilan' => 'tahunan',
            'penghasilan_bersih' => 120_000_000,
            'zakat_sudah_dibayar' => 1_000_000,
        ], $this->incomePolicy(120_000_000));

        $this->assertSame(120_000_000, $result['nisab']);
        $this->assertSame(3_000_000, $result['kewajiban_zakat']);
        $this->assertSame(1_000_000, $result['zakat_sudah_dibayar']);
        $this->assertSame(2_000_000, $result['jumlah_zakat']);
    }

    private function incomePolicy(int $annualNisab): KetentuanPokokZakat
    {
        return new KetentuanPokokZakat([
            'jenis' => 'penghasilan',
            'kadar_persentase' => 2.5,
            'nisab_rupiah' => $annualNisab,
        ]);
    }
}

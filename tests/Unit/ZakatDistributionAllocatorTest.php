<?php

namespace Tests\Unit;

use App\Services\ZakatDistributionAllocator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ZakatDistributionAllocatorTest extends TestCase
{
    public function test_allocates_percentages_with_exact_total_after_rounding(): void
    {
        $allocator = new ZakatDistributionAllocator;
        $this->assertSame(['fakir' => 3_000_000, 'miskin' => 4_500_000],
            $allocator->allocate(7_500_000, ['fakir' => 40, 'miskin' => 60]));
        $result = $allocator->allocate(101, ['fakir' => 33.33, 'miskin' => 33.33, 'fisabilillah' => 33.34]);
        $this->assertSame(101, array_sum($result));
        $this->assertSame([33, 34, 34], array_values($result));
    }

    public function test_rejects_a_total_other_than_one_hundred_percent(): void
    {
        $this->expectException(ValidationException::class);
        (new ZakatDistributionAllocator)->allocate(7_500_000, ['fakir' => 40, 'miskin' => 50]);
    }
}

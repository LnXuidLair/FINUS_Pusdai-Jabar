<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\ZiswafPenerimaan;
use App\Services\Accounting\MidtransReceiptJournalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class MidtransHistoricalJournalRepairTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_paid_receipt_is_posted_once_and_repeated_repair_does_not_duplicate(): void
    {
        $org = Organization::create([
            'public_id' => (string) str()->ulid(),
            'name' => 'Masjid Uji Midtrans',
            'slug' => 'midtrans-uji',
            'country_code' => 'ID',
            'is_active' => true,
        ]);
        DB::table('coa')->whereNull('organization_id')->update(['organization_id' => $org->id]);
        $receipt = ZiswafPenerimaan::create([
            'organization_id' => $org->id,
            'order_id' => 'MIDTRANS-TEST-LEGACY-50K',
            'tanggal' => '2026-10-15',
            'jenis_ziswaf' => 'infaq',
            'nominal' => 50_000,
            'metode_pembayaran' => 'virtual_account',
            'payment_gateway' => 'midtrans',
            'payment_status' => 'settlement',
            'status_verifikasi' => 'diterima',
        ]);
        $service = app(MidtransReceiptJournalService::class);
        $this->assertSame('created', $service->repair($receipt->id));
        $journalId = $receipt->fresh()->jurnal_id;
        $this->assertNotNull($journalId);
        $this->assertSame(2, $receipt->fresh()->jurnal->detail()->count());
        $this->assertSame('2026-10-15', $receipt->fresh()->jurnal->tanggal->format('Y-m-d'));
        $this->assertSame('already_posted', $service->repair($receipt->id));
        $this->assertSame($journalId, $receipt->fresh()->jurnal_id);
        $this->assertSame(2, $receipt->fresh()->jurnal->detail()->count());
    }

    public function test_pending_payment_cannot_be_force_posted(): void
    {
        $org = Organization::create([
            'public_id' => (string) str()->ulid(),
            'name' => 'Masjid Uji Midtrans',
            'slug' => 'midtrans-uji-pending',
            'country_code' => 'ID',
            'is_active' => true,
        ]);
        $receipt = ZiswafPenerimaan::create([
            'organization_id' => $org->id,
            'order_id' => 'MIDTRANS-TEST-PENDING',
            'tanggal' => '2026-10-15',
            'jenis_ziswaf' => 'infaq',
            'nominal' => 50_000,
            'metode_pembayaran' => 'virtual_account',
            'payment_gateway' => 'midtrans',
            'payment_status' => 'pending',
            'status_verifikasi' => 'pending',
        ]);
        $this->expectException(RuntimeException::class);
        app(MidtransReceiptJournalService::class)->repair($receipt->id);
    }
}

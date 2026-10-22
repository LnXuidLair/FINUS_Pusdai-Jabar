<?php

namespace Tests\Feature;

use App\Models\{JurnalUmum, Organization, User, ZiswafPenerimaan};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UnifiedPemasukanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $org = Organization::create(['slug' => 'unified-receipts', 'public_id' => (string) str()->ulid(), 'name' => 'Masjid Test', 'country_code' => 'ID', 'is_active' => true]);
        DB::table('coa')->whereNull('organization_id')->update(['organization_id' => $org->id]);
        $admin = User::create(['organization_id' => $org->id, 'name' => 'Admin', 'email' => 'unified-admin@finus.test', 'role' => User::ROLE_ADMIN, 'password' => bcrypt('password123'), 'email_verified_at' => now()]);
        $this->actingAs($admin, 'admin');
    }

    private function receipt(string $order, array $attributes = []): ZiswafPenerimaan
    {
        return ZiswafPenerimaan::create(array_merge(['order_id' => $order, 'jenis_ziswaf' => 'infaq', 'nominal' => 100000, 'tanggal' => today(), 'metode_pembayaran' => 'manual_transfer', 'payment_status' => 'manual_pending', 'status_verifikasi' => 'pending'], $attributes));
    }

    public function test_tabs_include_all_statuses_but_manual_queue_excludes_gateway_and_cancelled(): void
    {
        $manual = $this->receipt('MANUAL-QUEUE');
        $gateway = $this->receipt('GATEWAY-UNPAID', ['metode_pembayaran' => 'qris', 'payment_status' => 'pending']);
        $cancelled = $this->receipt('CANCELLED-RECORD', ['payment_status' => 'cancel', 'status_verifikasi' => 'dibatalkan']);
        $expired = $this->receipt('EXPIRED-RECORD', ['payment_status' => 'expire', 'status_verifikasi' => 'ditolak']);
        $all = $this->get(route('admin.pemasukan.index'))->assertOk()->assertSee('Semua Penerimaan')->assertSee('1102')->assertSee('CANCELLED-RECORD')->assertSee('GATEWAY-UNPAID')->assertDontSee('>Transaksi ZISWAF<', false);
        $this->assertCount(4, $all->viewData('transaksi'));
        $pending = $this->get(route('admin.pemasukan.index', ['tab' => 'pending']))->assertOk()->assertSee('MANUAL-QUEUE')->assertDontSee('GATEWAY-UNPAID')->assertDontSee('CANCELLED-RECORD');
        $this->assertEquals(1, $pending->viewData('summaryTotal')['pending']);
        $this->get(route('admin.pemasukan.index', ['tab' => 'history']))->assertOk()->assertSee('CANCELLED-RECORD')->assertSee('EXPIRED-RECORD')->assertDontSee('MANUAL-QUEUE')->assertDontSee('GATEWAY-UNPAID');
        $this->get(route('admin.pemasukan.index', ['tab' => 'history', 'payment_status' => 'expire']))->assertOk()->assertSee('EXPIRED-RECORD')->assertDontSee('CANCELLED-RECORD');
    }

    public function test_manual_acceptance_posts_once_and_rejection_is_preserved_in_history(): void
    {
        $receipt = $this->receipt('ACCEPT-ONCE');
        $url = route('admin.pemasukan.verifikasi', $receipt);
        $this->patch($url, ['action' => 'terima'])->assertSessionHasNoErrors();
        $receipt->refresh();
        $this->assertEquals('diterima', $receipt->status_verifikasi);
        $this->assertNotNull($receipt->jurnal_id);
        $this->assertEquals(1, JurnalUmum::where('sumber_tabel', 'ziswaf_penerimaan')->where('sumber_id', $receipt->id)->count());
        $this->patch($url, ['action' => 'terima'])->assertSessionHasErrors('action');
        $this->assertEquals(1, JurnalUmum::where('sumber_tabel', 'ziswaf_penerimaan')->where('sumber_id', $receipt->id)->count());
        $this->get(route('admin.pemasukan.index', ['tab' => 'history']))->assertOk()->assertSee('ACCEPT-ONCE')->assertSee($receipt->jurnal->no_referensi);
        $rejected = $this->receipt('REJECTED-MANUAL');
        $this->patch(route('admin.pemasukan.verifikasi', $rejected), ['action' => 'tolak', 'catatan_verifikasi' => 'Bukti tidak sesuai'])->assertSessionHasNoErrors();
        $this->assertEquals('manual_rejected', $rejected->fresh()->payment_status);
        $this->assertNull($rejected->fresh()->jurnal_id);
    }

    public function test_gateway_pending_cannot_be_manually_accepted_and_old_links_redirect(): void
    {
        $receipt = $this->receipt('WAIT-GATEWAY', ['metode_pembayaran' => 'qris', 'payment_status' => 'pending']);
        $this->patch(route('admin.pemasukan.verifikasi', $receipt), ['action' => 'terima'])->assertSessionHasErrors('action');
        $this->patch(route('admin.ziswaf.transaksi.terima', $receipt), [])->assertSessionHasErrors('action');
        $this->assertNull($receipt->fresh()->jurnal_id);
        $this->get(route('admin.ziswaf.transaksi.index', ['jenis' => 'wakaf', 'q' => 'old']))->assertRedirect(route('admin.pemasukan.index', ['q' => 'old', 'golongan' => 'wakaf']));
    }

    public function test_posting_failure_rolls_back_verification(): void
    {
        $receipt = $this->receipt('ROLLBACK-RECEIPT');
        $service = \Mockery::mock(\App\Services\Accounting\Psak109PostingService::class);
        $service->shouldReceive('postPenerimaan')->once()->andThrow(new \RuntimeException('Posting failed for test'));
        $this->app->instance(\App\Services\Accounting\Psak109PostingService::class, $service);
        $this->patch(route('admin.pemasukan.verifikasi', $receipt), ['action' => 'terima'])->assertServerError();
        $this->assertEquals('pending', $receipt->fresh()->status_verifikasi);
        $this->assertNull($receipt->fresh()->jurnal_id);
    }
}

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

    public function test_wakaf_receipts_require_purpose_and_expenses_use_only_that_purpose_balance(): void
    {
        $data = ['jenis_ziswaf' => 'wakaf', 'nominal' => 100000, 'tanggal' => today()->toDateString(), 'metode_pembayaran' => 'tunai'];
        $this->post(route('admin.pemasukan.store'), $data)->assertSessionHasErrors('wakaf_purpose');
        $this->post(route('admin.pemasukan.store'), $data + ['wakaf_purpose' => 'lainnya'])->assertSessionHasErrors('wakaf_purpose');
        $this->post(route('admin.pemasukan.store'), $data + ['wakaf_purpose' => 'pembangunan'])->assertSessionHasNoErrors();
        $receipt = ZiswafPenerimaan::where('jenis_ziswaf', 'wakaf')->firstOrFail();
        $this->assertSame('permanen', $receipt->wakaf_type);
        $this->assertSame('pembangunan', $receipt->wakaf_purpose);
        // Post the receipt to represent completed verification.
        app(\App\Services\Accounting\Psak109PostingService::class)->postPenerimaan($receipt);
        \App\Models\TransactionCategory::ensureDefaults();
        $category = \App\Models\TransactionCategory::where('code', 'PENYALURAN-WAKAF')->firstOrFail();
        $expense = ['transaction_category_id' => $category->id, 'coa_kredit_id' => \App\Models\Coa::where('kode_akun', '1101')->firstOrFail()->id, 'deskripsi' => 'Pembelian material', 'jumlah' => 50000, 'tanggal' => today()->toDateString()];
        $this->post(route('admin.pengeluaran.store'), $expense)->assertSessionHasErrors('wakaf_purpose');
        $this->post(route('admin.pengeluaran.store'), $expense + ['wakaf_purpose' => 'mushaf'])->assertSessionHasErrors('jumlah');
        $this->assertDatabaseCount('pengeluaran', 0);
        $this->post(route('admin.pengeluaran.store'), $expense + ['wakaf_purpose' => 'pembangunan'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pengeluaran', ['wakaf_purpose' => 'pembangunan', 'jumlah' => 50000]);
        $this->assertSame(50000.0, app(\App\Services\Accounting\Psak109PostingService::class)->getRekapWakaf()['pembangunan']['saldo']);
        $this->get(route('admin.pengeluaran.create'))->assertOk()->assertSee('Peruntukan Wakaf')->assertSee('Mushaf Al-Qur’an');
    }

    public function test_fitrah_calculation_for_admin_and_finance_uses_active_rice_price_and_saves_snapshot(): void
    {
        $policy = \App\Models\KetentuanPokokZakat::untukJenis('fitrah');
        $policy->update(['berat_fitrah_kg' => 2.5]);
        $rice = \App\Models\BarangZakat::firstOrCreate(['nama' => 'Beras'], ['kode' => 'BERAS', 'kategori' => 'makanan_pokok', 'satuan_dasar' => 'kg', 'metode_penilaian' => 'harga_pasar', 'aktif' => true]);
        $rice->update(['aktif' => true]);
        $rice->harga()->delete();
        $price = $rice->harga()->create(['wilayah' => 'Bandung', 'harga_per_satuan' => 16000, 'berlaku_mulai' => today()->subDay(), 'status' => 'disetujui', 'sumber_harga' => 'Uji']);
        // A newer draft must not replace the approved price.
        $rice->harga()->create(['wilayah' => 'Bandung', 'harga_per_satuan' => 99000, 'berlaku_mulai' => today(), 'status' => 'draft', 'sumber_harga' => 'Draft']);
        $organizationId = auth('admin')->user()->organization_id;
        foreach (['admin', 'pegawai'] as $role) {
            if ($role === 'pegawai') {
                $staff = User::create(['organization_id' => $organizationId, 'name' => 'Keuangan', 'email' => 'fitrah-keuangan@finus.test', 'role' => User::ROLE_PEGAWAI, 'password' => bcrypt('password123'), 'email_verified_at' => now(), 'jabatan' => 'Keuangan']);
                \App\Models\Pegawai::create(['organization_id' => $organizationId, 'nip' => 'FITRAH-001', 'nama_pegawai' => 'Keuangan', 'email' => $staff->email, 'jabatan' => 'Keuangan', 'akses_role' => 'keuangan', 'is_verified' => true]);
                $this->actingAs($staff, 'pegawai');
                auth('admin')->logout();
            }
            $prefix = $role === 'admin' ? 'admin' : 'pegawai.keuangan';
            $data = ['jenis_ziswaf' => 'zakat_fitrah', 'nominal' => 160000, 'tanggal' => today()->toDateString(), 'metode_pembayaran' => 'tunai', 'jumlah_jiwa' => 4];
            $this->get(route($prefix.'.pemasukan.index'))->assertOk()->assertSee('Jumlah Jiwa')->assertSee('Zakat Fitrah');
            $this->post(route($prefix.'.pemasukan.store'), array_replace($data, ['jumlah_jiwa' => 1.5]))->assertSessionHasErrors('jumlah_jiwa');
            $this->post(route($prefix.'.pemasukan.store'), array_replace($data, ['nominal' => 1000]))->assertSessionHasErrors('nominal');
            $this->post(route($prefix.'.pemasukan.store'), $data)->assertSessionHasNoErrors();
        }
        $receipts = ZiswafPenerimaan::where('jenis_ziswaf', 'zakat_fitrah')->get();
        $this->assertCount(2, $receipts);
        foreach ($receipts as $receipt) {
            $this->assertSame(160000, $receipt->nominal);
            $this->assertSame(4, $receipt->rincian_perhitungan['jumlah_jiwa']);
            $this->assertSame(16000, $receipt->snapshot_kebijakan['harga_beras']['harga_per_kg']);
            $this->assertNotNull($receipt->jurnal_id);
        }
        $price->update(['harga_per_satuan' => 18000]);
        $this->assertSame(160000, $receipts->first()->fresh()->nominal);
        $this->assertSame(16000, $receipts->first()->fresh()->snapshot_kebijakan['harga_beras']['harga_per_kg']);
        $price->update(['berlaku_sampai' => today()->subDay()]);
        $this->post(route('pegawai.keuangan.pemasukan.store'), $data)->assertSessionHasErrors('jumlah_jiwa');
        $this->assertSame(2, ZiswafPenerimaan::where('jenis_ziswaf', 'zakat_fitrah')->count());
    }

    public function test_zakat_expenses_separate_sources_and_periods_and_check_cash_account(): void
    {
        \App\Models\TransactionCategory::ensureDefaults();
        $category = \App\Models\TransactionCategory::where('code', 'PENYALURAN-ZAKAT')->firstOrFail();
        $posting = app(\App\Services\Accounting\Psak109PostingService::class);
        foreach (['zakat_fitrah' => 200000, 'zakat_maal' => 500000] as $type => $amount) {
            $receipt = $this->receipt('SRC-'.$type, ['jenis_ziswaf' => $type, 'nominal' => $amount, 'metode_pembayaran' => 'tunai', 'status_verifikasi' => 'diterima', 'payment_status' => 'manual_paid']);
            $posting->postPenerimaan($receipt);
        }
        $this->assertSame(['fitrah' => 200000.0, 'lainnya' => 500000.0], $posting->getSaldoZakatSources());
        $cash = \App\Models\Coa::where('kode_akun', '1101')->firstOrFail();
        $bank = \App\Models\Coa::where('kode_akun', '1102')->firstOrFail();
        $data = ['transaction_category_id' => $category->id, 'coa_kredit_id' => $cash->id, 'deskripsi' => 'Penyaluran zakat', 'jumlah' => 300000, 'tanggal' => '2026-09-30', 'periode_zakat' => '2026-09', 'target_asnaf' => ['fakir' => 100], 'zakat_details' => [['asnaf' => 'fakir', 'nama_penerima' => 'Penerima Uji']]];
        $this->post(route('admin.pengeluaran.store'), $data)->assertSessionHasErrors('zakat_source');
        $this->post(route('admin.pengeluaran.store'), $data + ['zakat_source' => 'fitrah'])->assertSessionHasErrors('jumlah');
        $this->post(route('admin.pengeluaran.store'), array_replace($data, ['zakat_source' => 'fitrah', 'jumlah' => 100000, 'coa_kredit_id' => $bank->id]))->assertSessionHasErrors();
        // Fitrah can be distributed on its actual date, before month end.
        $this->post(route('admin.pengeluaran.store'), array_replace($data, ['zakat_source' => 'fitrah', 'jumlah' => 100000, 'tanggal' => '2026-09-20']))->assertSessionHasNoErrors();
        $this->post(route('admin.pengeluaran.store'), $data + ['zakat_source' => 'lainnya'])->assertSessionHasNoErrors();
        $this->assertSame(['fitrah' => 100000.0, 'lainnya' => 200000.0], $posting->getSaldoZakatSources());
        $this->assertSame(300000.0, $posting->getSaldoDana()['zakat']);
        $this->assertDatabaseCount('periode_penyaluran_zakat', 2);
        $this->assertDatabaseHas('pengeluaran', ['zakat_source' => 'fitrah', 'jumlah' => 100000]);
        $this->assertDatabaseHas('jurnal_detail', ['jenis_dana' => 'zakat', 'zakat_source' => 'fitrah', 'credit' => 100000]);
        $this->get(route('admin.pengeluaran.create'))->assertOk()->assertSee('Sumber Dana Zakat')->assertSee('Dana Zakat Fitrah');
        // Moving fitrah preserves its source and does not move other zakat funds.
        config(['cash_transfers.enabled' => true]);
        $transfer = app(\App\Services\Accounting\CashTransferService::class);
        $journal = $transfer->transfer(['fund' => 'zakat_fitrah', 'source' => $cash->id, 'target' => $bank->id, 'amount' => 50000, 'date' => '2026-09-30', 'description' => 'Pindah fitrah']);
        $this->assertSame(50000.0, $posting->getSaldoZakatSources($bank)['fitrah']);
        $this->assertSame(0.0, $posting->getSaldoZakatSources($bank)['lainnya']);
        $transfer->cancel($journal->id);
        $this->assertSame(0.0, $posting->getSaldoZakatSources($bank)['fitrah']);
    }

    public function test_temporary_waqf_is_disabled_for_new_receipts_and_expense_categories(): void
    {
        $this->post(route('admin.pemasukan.store'), [
            'jenis_ziswaf' => 'wakaf', 'wakaf_type' => 'temporer',
            'wakaf_return_date' => today()->addYear()->toDateString(),
            'nominal' => 100000, 'tanggal' => today()->toDateString(), 'metode_pembayaran' => 'tunai',
        ])->assertSessionHasErrors('wakaf_type');
        \App\Models\TransactionCategory::ensureDefaults();
        $category = \App\Models\TransactionCategory::where('code', 'PENGEMBALIAN-WAKAF')->firstOrFail();
        $this->assertFalse($category->is_active);
        $this->assertFalse(\App\Models\TransactionCategory::active()->whereKey($category->id)->exists());
        $this->get(route('admin.pemasukan.index'))->assertOk()->assertDontSee('<option value="temporer"', false);
        $this->get(route('admin.pengeluaran.create'))->assertOk()->assertDontSee('Pengembalian Pokok Wakaf Temporer');
        $old = $this->receipt('OLD-TEMPORARY-WAQF', ['jenis_ziswaf' => 'wakaf', 'wakaf_type' => 'temporer', 'status_verifikasi' => 'diterima', 'payment_status' => 'manual_paid']);
        $this->get(route('admin.pemasukan.index'))->assertOk()->assertSee('OLD-TEMPORARY-WAQF')->assertSee('Wakaf Temporer');
        $this->assertEquals('temporer', $old->fresh()->wakaf_type);
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

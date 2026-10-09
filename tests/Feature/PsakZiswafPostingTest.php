<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\KetentuanPokokZakat;
use App\Models\Organization;
use App\Models\Pengeluaran;
use App\Models\TransactionCategory;
use App\Models\ZiswafPenerimaan;
use App\Services\Accounting\Psak109PostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PsakZiswafPostingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $organization = Organization::create([
            'public_id' => (string) str()->ulid(),
            'name' => 'PUSDAI Uji Posting',
            'slug' => 'pusdai-uji-posting',
            'country_code' => 'ID',
            'is_active' => true,
        ]);
        DB::table('coa')
            ->whereNull('organization_id')
            ->update(['organization_id' => $organization->id]);
    }

    public function test_zakat_is_kept_in_full_without_amil_allocation(): void
    {
        $ketentuan = KetentuanPokokZakat::untukJenis('maal');
        $ketentuan->update(['persentase_amil' => 11]);

        $receipt = $this->receipt([
            'jenis_ziswaf' => 'zakat_maal',
            'nominal' => 1_000_000,
        ]);

        app(Psak109PostingService::class)->postPenerimaan($receipt);
        $receipt->refresh();

        $this->assertSame(0, (int) $receipt->nominal_amil);
        $this->assertSame(0.0, (float) $ketentuan->fresh()->persentase_amil);
        $this->assertDatabaseMissing('jurnal_detail', [
            'jurnal_id' => $receipt->jurnal_id,
            'jenis_dana' => 'amil',
        ]);
        $this->assertSame(1_000_000.0, app(Psak109PostingService::class)->getSaldoDana()['zakat']);
    }

    public function test_infak_is_always_posted_as_general_fund(): void
    {
        $receipt = $this->receipt([
            'jenis_ziswaf' => 'infaq',
            'restriction_type' => 'muqayyadah',
            'nominal' => 1_000_000,
        ]);

        app(Psak109PostingService::class)->postPenerimaan($receipt);
        $receipt->refresh();

        $this->assertSame(0, (int) $receipt->nominal_amil);
        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $receipt->jurnal_id,
            'jenis_dana' => 'operasional',
            'restriction_type' => null,
            'psak_reference' => 'PSAK 109',
            'debit' => 1_000_000,
        ]);
        $this->assertDatabaseMissing('jurnal_detail', [
            'jurnal_id' => $receipt->jurnal_id,
            'jenis_dana' => 'amil',
        ]);
        $this->assertSame(1_000_000.0, app(Psak109PostingService::class)->getSaldoDana()['operasional']);
    }

    public function test_posting_rejects_missing_master_account_without_creating_it(): void
    {
        $account = $this->account('4105');
        $account->delete();
        $receipt = $this->receipt([
            'jenis_ziswaf' => 'zakat_maal',
            'nominal' => 100_000,
        ]);

        try {
            app(Psak109PostingService::class)->postPenerimaan($receipt);
            $this->fail('Posting seharusnya ditolak ketika akun master tidak tersedia.');
        } catch (\DomainException $exception) {
            $this->assertStringContainsString('4105', $exception->getMessage());
        }

        $this->assertDatabaseMissing('coa', ['kode_akun' => '4105']);
        $this->assertNull($receipt->fresh()->jurnal_id);
    }

    public function test_temporary_wakaf_is_recognized_as_a_liability(): void
    {
        $receipt = $this->receipt([
            'jenis_ziswaf' => 'wakaf',
            'wakaf_type' => 'temporer',
            'wakaf_return_date' => '2027-09-21',
            'nominal' => 2_000_000,
        ]);

        app(Psak109PostingService::class)->postPenerimaan($receipt);
        $receipt->refresh();

        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $receipt->jurnal_id,
            'coa_id' => $this->account('2201')->id,
            'jenis_dana' => 'wakaf_temporer',
            'psak_reference' => 'PSAK 112',
            'credit' => 2_000_000,
        ]);
        $this->assertDatabaseMissing('jurnal_detail', [
            'jurnal_id' => $receipt->jurnal_id,
            'coa_id' => $this->account('4107')->id,
        ]);
    }

    public function test_permanent_wakaf_is_separate_from_temporary_wakaf(): void
    {
        $receipt = $this->receipt([
            'jenis_ziswaf' => 'wakaf',
            'wakaf_type' => 'permanen',
            'nominal' => 3_000_000,
        ]);

        app(Psak109PostingService::class)->postPenerimaan($receipt);
        $receipt->refresh();

        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $receipt->jurnal_id,
            'coa_id' => $this->account('4107')->id,
            'jenis_dana' => 'wakaf',
            'psak_reference' => 'PSAK 112',
            'credit' => 3_000_000,
        ]);
    }

    public function test_nazhir_share_is_only_allocated_from_realized_wakaf_management_results(): void
    {
        $receipt = $this->receipt([
            'jenis_ziswaf' => 'wakaf',
            'wakaf_type' => 'hasil_pengelolaan',
            'persentase_nazhir' => 10,
            'nominal' => 1_000_000,
        ]);

        app(Psak109PostingService::class)->postPenerimaan($receipt);
        $receipt->refresh();

        $this->assertSame(100_000, (int) $receipt->nominal_nazhir);
        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $receipt->jurnal_id,
            'coa_id' => $this->account('4109')->id,
            'credit' => 1_000_000,
        ]);
        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $receipt->jurnal_id,
            'coa_id' => $this->account('5412')->id,
            'jenis_dana' => 'wakaf',
            'debit' => 100_000,
        ]);
        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $receipt->jurnal_id,
            'coa_id' => $this->account('4310')->id,
            'jenis_dana' => 'nazhir',
            'credit' => 100_000,
        ]);
    }

    public function test_return_of_temporary_wakaf_principal_debits_the_liability(): void
    {
        TransactionCategory::ensureDefaults();
        $category = TransactionCategory::query()
            ->where('code', 'PENGEMBALIAN-WAKAF')
            ->firstOrFail();

        $expense = Pengeluaran::create([
            'transaction_category_id' => $category->id,
            'kategori' => 'Liabilitas Wakaf Temporer',
            'jenis_dana' => 'wakaf_temporer',
            'deskripsi' => 'Pengembalian pokok wakaf temporer',
            'jumlah' => 750_000,
            'nominal' => 750_000,
            'tanggal' => '2026-09-21',
            'jenis' => 'operasional',
            'status_verifikasi' => 'diterima',
            'coa_debit_id' => $this->account('2201')->id,
            'coa_kredit_id' => $this->account('1101')->id,
        ]);

        app(Psak109PostingService::class)->postPengeluaran($expense);
        $expense->refresh();

        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $expense->jurnal_id,
            'coa_id' => $this->account('2201')->id,
            'jenis_dana' => 'wakaf_temporer',
            'psak_reference' => 'PSAK 112',
            'debit' => 750_000,
        ]);
        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $expense->jurnal_id,
            'coa_id' => $this->account('1101')->id,
            'credit' => 750_000,
        ]);
    }

    private function receipt(array $attributes): ZiswafPenerimaan
    {
        return ZiswafPenerimaan::create(array_merge([
            'tanggal' => '2026-09-21',
            'jenis_ziswaf' => 'zakat_maal',
            'nominal' => 100_000,
            'metode_pembayaran' => 'tunai',
            'status_verifikasi' => 'diterima',
        ], $attributes));
    }

    private function account(string $code): Coa
    {
        return Coa::where('kode_akun', $code)->firstOrFail();
    }
}

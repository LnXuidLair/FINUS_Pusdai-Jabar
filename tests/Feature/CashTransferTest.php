<?php

namespace Tests\Feature;

use App\Models\{Coa, JurnalUmum, Organization, User};
use App\Services\Accounting\CashTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CashTransferTest extends TestCase
{
    use RefreshDatabase;

    private function prepareAccounts(): array
    {
        $org = Organization::create(['slug' => 'transfer-test', 'public_id' => (string) str()->ulid(), 'name' => 'Test', 'country_code' => 'ID', 'is_active' => true]);
        DB::table('coa')->whereNull('organization_id')->update(['organization_id' => $org->id]);
        $admin = User::create(['organization_id' => $org->id, 'role' => User::ROLE_ADMIN, 'name' => 'Admin Uji', 'email' => 'cash-transfer@finus.test', 'email_verified_at' => now(), 'password' => bcrypt('password123')]);
        $this->actingAs($admin, 'admin');
        $kas = Coa::where('kode_akun', '1101')->firstOrFail();
        $bank = Coa::where('kode_akun', '1102')->firstOrFail();
        $journal = JurnalUmum::create(['tanggal' => today(), 'tgl' => today(), 'sumber_tabel' => 'ziswaf_penerimaan']);
        $journal->detail()->create(['coa_id' => $bank->id, 'jenis_dana' => 'zakat', 'debit' => 1000000, 'credit' => 0]);
        return [$kas, $bank];
    }

    public function test_transfer_and_cancellation_preserve_fund_and_are_not_income_or_expense(): void
    {
        [$kas, $bank] = $this->prepareAccounts();
        $this->get(route('admin.cash-transfers.index'))->assertOk()->assertSee('Saldo dana');
        $payload = ['source' => $bank->id, 'target' => $kas->id, 'fund' => 'zakat', 'amount' => 200000, 'date' => today()->toDateString(), 'description' => 'Penarikan'];
        $this->post(route('admin.cash-transfers.store'), $payload)->assertSessionHasNoErrors();
        $journal = JurnalUmum::where('sumber_tabel', 'kas_bank_transfer')->firstOrFail();
        $this->assertEquals(200000, $journal->detail->sum('debit'));
        $this->assertEquals(200000, $journal->detail->sum('credit'));
        $this->assertFalse(JurnalUmum::pemasukan()->whereKey($journal->id)->exists());
        $this->assertFalse(JurnalUmum::pengeluaran()->whereKey($journal->id)->exists());
        $this->assertEquals(['1102' => 800000, '1101' => 200000], app(CashTransferService::class)->balances()['zakat']);
        $this->delete(route('admin.cash-transfers.destroy', $journal->id))->assertSessionHasNoErrors();
        $this->assertEquals(1000000, app(CashTransferService::class)->balances()['zakat']['1102']);
        $this->assertEquals(0, app(CashTransferService::class)->balances()['zakat']['1101']);
        $this->delete(route('admin.cash-transfers.destroy', $journal->id))->assertSessionHasErrors();
    }

    public function test_cancellation_is_rejected_after_destination_balance_is_spent(): void
    {
        [$kas, $bank] = $this->prepareAccounts();
        $service = app(CashTransferService::class);
        $journal = $service->transfer(['source' => $bank->id, 'target' => $kas->id, 'fund' => 'zakat', 'amount' => 200000, 'date' => today()->toDateString(), 'description' => 'Penarikan']);
        $spent = JurnalUmum::create(['tanggal' => today(), 'tgl' => today(), 'sumber_tabel' => 'pengeluaran']);
        $spent->detail()->create(['coa_id' => $kas->id, 'jenis_dana' => 'zakat', 'credit' => 150000, 'debit' => 0]);
        $this->delete(route('admin.cash-transfers.destroy', $journal->id))->assertSessionHasErrors();
        $this->assertSame(0, JurnalUmum::where('sumber_tabel', 'kas_bank_reversal')->count());
        $this->assertEquals(50000, $service->balances()['zakat']['1101']);
    }

    public function test_expense_uses_selected_fund_at_source_and_switch_restores_previous_rule(): void
    {
        [$kas, $bank] = $this->prepareAccounts();
        $receipt = JurnalUmum::create(['tanggal' => today(), 'tgl' => today(), 'sumber_tabel' => 'ziswaf_penerimaan']);
        $receipt->detail()->create(['coa_id' => $kas->id, 'jenis_dana' => 'operasional', 'debit' => 100000, 'credit' => 0]);
        \App\Models\TransactionCategory::ensureDefaults();
        $category = \App\Models\TransactionCategory::where('code', 'KEBERSIHAN')->firstOrFail();
        $payload = ['transaction_category_id' => $category->id, 'coa_kredit_id' => $bank->id, 'deskripsi' => 'Uji sumber golongan', 'jumlah' => 50000, 'tanggal' => today()->toDateString()];
        $this->post(route('admin.pengeluaran.store'), $payload)->assertSessionHasErrors('coa_kredit_id');
        $this->assertDatabaseMissing('pengeluaran', ['deskripsi' => $payload['deskripsi']]);
        $payload['coa_kredit_id'] = $kas->id;
        $this->from(route('admin.pengeluaran.create'))->post(route('admin.pengeluaran.store'), $payload)->assertSessionHasNoErrors()->assertRedirect(route('admin.pengeluaran.index'));
        $this->assertEquals(50000, app(CashTransferService::class)->balances()['operasional']['1101']);
        config(['cash_transfers.enabled' => false]);
        $payload['coa_kredit_id'] = $bank->id;
        $payload['deskripsi'] = 'Uji perilaku lama';
        $this->post(route('admin.pengeluaran.store'), $payload)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pengeluaran', ['deskripsi' => $payload['deskripsi']]);
    }

    public function test_transfer_cannot_use_another_organizations_account(): void
    {
        [$kas, $bank] = $this->prepareAccounts();
        $other = Organization::create(['slug' => 'other-transfer', 'public_id' => (string) str()->ulid(), 'name' => 'Other', 'country_code' => 'ID', 'is_active' => true]);
        $account = $bank->replicate();
        $account->organization_id = $other->id;
        // Simulasikan akun yang telah dibuat organisasi lain tanpa melewati portal saat ini.
        $id = DB::table('coa')->insertGetId($account->getAttributes());
        $this->post(route('admin.cash-transfers.store'), ['source' => $id, 'target' => $kas->id, 'fund' => 'zakat', 'amount' => 100, 'date' => today()->toDateString(), 'description' => 'Uji isolasi'])->assertSessionHasErrors('source');
        $this->assertSame(0, JurnalUmum::where('sumber_tabel', 'kas_bank_transfer')->count());
    }

    public function test_allocation_offers_separate_zakat_sources_and_rejects_other_funds(): void
    {
        [$kas, $bank] = $this->prepareAccounts();
        $this->get(route('admin.cash-transfers.index'))->assertOk()
            ->assertViewHas('labels', ['operasional' => 'Dana Operasional', 'wakaf' => 'Dana Wakaf', 'zakat' => 'Dana Zakat Lainnya', 'zakat_fitrah' => 'Dana Zakat Fitrah'])
            ->assertDontSee('value="amil"', false)->assertDontSee('value="wakaf_temporer"', false)
            ->assertDontSee('Dana Infak/Sedekah')->assertDontSee('Dana Nazhir');
        foreach (['infak_sedekah', 'amil', 'nazhir', 'wakaf_temporer', 'non_halal'] as $fund) {
            $this->post(route('admin.cash-transfers.store'), ['source' => $bank->id, 'target' => $kas->id, 'fund' => $fund, 'amount' => 100, 'date' => today()->toDateString(), 'description' => 'Uji batas golongan'])->assertSessionHasErrors('fund');
        }
        $this->assertSame(0, JurnalUmum::where('sumber_tabel', 'kas_bank_transfer')->count());
    }

    public function test_wrong_fund_insufficient_balance_and_feature_switch(): void
    {
        [$kas, $bank] = $this->prepareAccounts();
        $payload = ['source' => $bank->id, 'target' => $kas->id, 'fund' => 'operasional', 'amount' => 100, 'date' => today()->toDateString(), 'description' => 'Uji'];
        $this->post(route('admin.cash-transfers.store'), $payload)->assertSessionHasErrors('coa_kredit_id');
        $payload['fund'] = 'zakat'; $payload['amount'] = 1000001;
        $this->post(route('admin.cash-transfers.store'), $payload)->assertSessionHasErrors();
        $this->assertSame(0, JurnalUmum::where('sumber_tabel', 'kas_bank_transfer')->count());
        config(['cash_transfers.enabled' => false]);
        $this->get(route('admin.cash-transfers.index'))->assertNotFound();
    }
}

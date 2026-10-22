<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BukuBesarTest extends TestCase
{
    use RefreshDatabase;

    public function test_ledger_balances_continue_across_pages_and_exclude_other_organizations(): void
    {
        $org = Organization::create(['public_id' => (string) str()->ulid(), 'name' => 'Ledger Test', 'slug' => 'ledger-test', 'country_code' => 'ID', 'is_active' => true]);
        $other = Organization::create(['public_id' => (string) str()->ulid(), 'name' => 'Other', 'slug' => 'ledger-other', 'country_code' => 'ID', 'is_active' => true]);
        $admin = User::factory()->create(['organization_id' => $org->id, 'role' => User::ROLE_ADMIN]);
        $account = Coa::create(['organization_id' => $org->id, 'kode_akun' => '1199', 'nama_akun' => 'Bank Uji', 'header_akun' => 1]);
        $make = function ($date, $debit, $credit, $organization, $fund = 'zakat') use ($account): void {
            $journal = JurnalUmum::withoutEvents(fn () => JurnalUmum::create(['organization_id' => $organization, 'tanggal' => $date, 'tgl' => $date, 'no_referensi' => 'LEDGER-TEST', 'deskripsi' => 'Uji buku besar']));
            JurnalDetail::create(['jurnal_id' => $journal->id, 'coa_id' => $account->id, 'debit' => $debit, 'credit' => $credit, 'jenis_dana' => $fund]);
        };
        $make('2026-09-30', 100, 0, $org->id);
        for ($i = 0; $i < 30; $i++) {
            $make('2026-10-01', 10, 0, $org->id);
        }
        $make('2026-10-02', 0, 40, $org->id);
        $make('2026-11-01', 999, 0, $org->id);
        $make('2026-10-01', 777, 0, $other->id);
        $make('2026-10-01', 555, 0, $org->id, 'operasional');

        $this->actingAs($admin, 'admin')->get(route('admin.laporan.buku-besar', [
            'coa_id' => $account->id, 'tanggal_dari' => '2026-10-01', 'tanggal_sampai' => '2026-10-31', 'dana' => 'zakat', 'page' => 2,
        ]))->assertOk()->assertViewHas('opening', 100.0)->assertViewHas('closing', 360.0)
            ->assertViewHas('entries', fn ($entries) => $entries->total() === 31
                && $entries->first()->running_balance === 360.0
                && $entries->last()->running_balance === 360.0);

        $journalCount = JurnalUmum::withoutGlobalScopes()->count();
        $this->get(route('admin.laporan.buku-besar.cetak', [
            'coa_id' => $account->id, 'tanggal_dari' => '2026-10-01', 'tanggal_sampai' => '2026-10-31', 'dana' => 'zakat', 'page' => 2,
        ]))->assertOk()->assertViewIs('admin.laporan.buku-besar-cetak')
            ->assertViewHas('entries', fn ($entries) => $entries->count() === 31
                && $entries->last()->running_balance === 360.0)
            ->assertSee('Waktu cetak:');
        $this->assertSame($journalCount, JurnalUmum::withoutGlobalScopes()->count());

        $foreignAccount = Coa::create(['organization_id' => $other->id, 'kode_akun' => '1199', 'nama_akun' => 'Other Bank', 'header_akun' => 1]);
        $this->get(route('admin.laporan.buku-besar', ['coa_id' => $foreignAccount->id]))->assertNotFound();
        $this->get(route('admin.laporan.buku-besar.cetak', ['coa_id' => $foreignAccount->id]))->assertNotFound();
    }
}

<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterCoaPengeluaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_form_groups_general_expenses_and_zakat_distribution(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pengeluaran.create'))
            ->assertOk()
            ->assertSee('Beban Operasional')
            ->assertSee('Penyaluran Zakat kepada Mustahik')
            ->assertSee('5108 - Beban Kebersihan')
            ->assertSee('5210 - Penyaluran Zakat')
            ->assertSee('Rincian Penerima Zakat')
            ->assertSee('Jumlah Penerima');
    }

    public function test_expense_is_saved_using_selected_coa_id(): void
    {
        $account = Coa::where('kode_akun', '5108')->firstOrFail();

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.pengeluaran.store'), [
                'coa_debit_id' => $account->id,
                'deskripsi' => 'Pembelian alat kebersihan',
                'jumlah' => 150000,
                'tanggal' => '2026-09-21',
            ])
            ->assertSessionHasNoErrors();

        $expense = Pengeluaran::latest('id')->firstOrFail();

        $this->assertSame($account->id, $expense->coa_debit_id);
        $this->assertSame('Beban Kebersihan', $expense->kategori);
        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $expense->jurnal_id,
            'coa_id' => $account->id,
            'jenis_dana' => 'amil',
            'debit' => 150000,
        ]);
    }

    public function test_zakat_distribution_uses_zakat_fund_dimension(): void
    {
        $account = Coa::where('kode_akun', '5210')->firstOrFail();

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.pengeluaran.store'), [
                'coa_debit_id' => $account->id,
                'deskripsi' => 'Penyaluran kepada mustahik miskin',
                'jumlah' => 500000,
                'tanggal' => '2026-09-21',
                'zakat_details' => [
                    [
                        'asnaf' => 'fakir',
                        'jumlah_penerima' => 3,
                        'nominal' => 300000,
                    ],
                    [
                        'asnaf' => 'miskin',
                        'jumlah_penerima' => 2,
                        'nominal' => 200000,
                    ],
                ],
            ])
            ->assertSessionHasNoErrors();

        $expense = Pengeluaran::latest('id')->firstOrFail();
        $debits = JurnalDetail::where('jurnal_id', $expense->jurnal_id)
            ->where('debit', '>', 0)
            ->orderBy('asnaf')
            ->get();

        $this->assertCount(2, $debits);
        $this->assertSame(['fakir', 'miskin'], $debits->pluck('asnaf')->all());
        $this->assertTrue($debits->every(fn (JurnalDetail $detail) => $detail->jenis_dana === 'zakat'));
        $this->assertDatabaseHas('ziswaf_penyaluran', [
            'id_pengeluaran' => $expense->id,
            'asnaf' => 'fakir',
            'jumlah_penerima' => 3,
            'nominal' => 300000,
        ]);

        $this->get(route('admin.laporan.jurnal-pengeluaran'))
            ->assertOk()
            ->assertSee('Penyaluran zakat - Fakir (3 orang)')
            ->assertSee('Penyaluran zakat - Miskin (2 orang)');
    }

    public function test_zakat_distribution_detail_must_match_expense_total(): void
    {
        $account = Coa::where('kode_akun', '5210')->firstOrFail();

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.pengeluaran.store'), [
                'coa_debit_id' => $account->id,
                'deskripsi' => 'Penyaluran zakat tidak seimbang',
                'jumlah' => 100000,
                'tanggal' => '2026-09-21',
                'zakat_details' => [[
                    'asnaf' => 'fakir',
                    'jumlah_penerima' => 1,
                    'nominal' => 90000,
                ]],
            ])
            ->assertSessionHasErrors('zakat_details');
    }

    public function test_master_coa_and_cash_flow_report_use_normalized_accounts(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.coa.index'))
            ->assertOk()
            ->assertSee('Beban Administrasi dan ATK')
            ->assertSee('Penyaluran Zakat');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.laporan.arus-kas'))
            ->assertOk()
            ->assertSee('Beban Kebersihan');
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin COA',
            'email' => 'admin-coa-'.uniqid().'@finus.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}

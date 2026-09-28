<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\Pengeluaran;
use App\Models\PeriodePenyaluranZakat;
use App\Models\User;
use App\Models\ZiswafPenerimaan;
use App\Services\Accounting\Psak109PostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ZakatPeriodDistributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_end_of_month_batch_supports_a_single_mustahik_and_closes_the_period(): void
    {
        $this->postZakatReceipt(1_000_000);
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pengeluaran.store'), $this->batchPayload())
            ->assertSessionHasNoErrors();

        $period = PeriodePenyaluranZakat::where('periode', '2026-09')->firstOrFail();
        $expense = Pengeluaran::where('periode_penyaluran_zakat_id', $period->id)->firstOrFail();

        $this->assertSame(PeriodePenyaluranZakat::STATUS_DITUTUP, $period->status);
        $this->assertSame(875_000, $period->saldo_sebelum_penyaluran);
        $this->assertSame(500_000, $period->total_disalurkan);
        $this->assertSame(375_000, $period->saldo_akhir);
        $this->assertStringStartsWith('ZKT-202609-', $expense->nomor_batch);
        $this->assertDatabaseHas('ziswaf_penyaluran', [
            'id_pengeluaran' => $expense->id,
            'nama_penerima' => 'Ahmad',
            'nik_penerima' => '3273000000000001',
            'asnaf' => 'fakir',
            'jumlah_penerima' => 1,
            'nominal' => 500_000,
        ]);
        $this->assertTrue(
            JurnalDetail::where('jurnal_id', $expense->jurnal_id)
                ->where('asnaf', 'fakir')
                ->where('debit', 500_000)
                ->exists()
        );
        $this->actingAs($admin, 'admin')
            ->get(route('admin.pengeluaran.index'))
            ->assertOk()
            ->assertSee($expense->nomor_batch)
            ->assertSee('Periode September 2026');
    }

    public function test_regular_zakat_batch_must_use_the_last_day_of_the_period(): void
    {
        $this->postZakatReceipt(1_000_000);
        $payload = $this->batchPayload(['tanggal' => '2026-09-29']);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.pengeluaran.store'), $payload)
            ->assertSessionHasErrors('tanggal');

        $this->assertDatabaseMissing('periode_penyaluran_zakat', ['periode' => '2026-09']);
    }

    public function test_a_period_cannot_have_more_than_one_distribution_batch(): void
    {
        $this->postZakatReceipt(1_000_000);
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pengeluaran.store'), $this->batchPayload())
            ->assertSessionHasNoErrors();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pengeluaran.store'), $this->batchPayload([
                'jumlah' => 100_000,
                'zakat_details' => [[
                    'nama_penerima' => 'Siti',
                    'asnaf' => 'miskin',
                    'jumlah_penerima' => 1,
                    'nominal' => 100_000,
                ]],
            ]))
            ->assertSessionHasErrors('periode_zakat');

        $this->assertSame(1, Pengeluaran::whereNotNull('periode_penyaluran_zakat_id')->count());
    }

    public function test_distribution_cannot_exceed_the_available_zakat_balance(): void
    {
        $this->postZakatReceipt(100_000);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.pengeluaran.store'), $this->batchPayload())
            ->assertSessionHasErrors('jumlah');

        $this->assertDatabaseMissing('periode_penyaluran_zakat', ['periode' => '2026-09']);
    }

    public function test_asnaf_targets_must_total_one_hundred_percent_when_used(): void
    {
        $this->postZakatReceipt(1_000_000);

        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.pengeluaran.store'), $this->batchPayload([
                'target_asnaf' => ['fakir' => 50, 'miskin' => 40],
            ]))
            ->assertSessionHasErrors('target_asnaf');
    }

    private function batchPayload(array $overrides = []): array
    {
        return array_replace_recursive([
            'coa_debit_id' => Coa::where('kode_akun', '5210')->firstOrFail()->id,
            'deskripsi' => 'Batch penyaluran zakat September 2026',
            'jumlah' => 500_000,
            'tanggal' => '2026-09-30',
            'periode_zakat' => '2026-09',
            'target_asnaf' => ['fakir' => 60, 'miskin' => 40],
            'zakat_details' => [[
                'nama_penerima' => 'Ahmad',
                'nik_penerima' => '3273000000000001',
                'alamat_penerima' => 'Bandung',
                'no_hp_penerima' => '081234567890',
                'asnaf' => 'fakir',
                'jumlah_penerima' => 1,
                'nominal' => 500_000,
            ]],
        ], $overrides);
    }

    private function postZakatReceipt(int $nominal): void
    {
        $receipt = ZiswafPenerimaan::create([
            'tanggal' => '2026-09-15',
            'jenis_ziswaf' => 'zakat_maal',
            'nominal' => $nominal,
            'metode_pembayaran' => 'tunai',
            'status_verifikasi' => 'diterima',
        ]);

        app(Psak109PostingService::class)->postPenerimaan($receipt);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Penyaluran Zakat',
            'email' => 'admin-zakat-period-'.uniqid().'@finus.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}

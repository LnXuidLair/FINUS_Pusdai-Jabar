<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\JurnalDetail;
use App\Models\Organization;
use App\Models\Pegawai;
use App\Models\Pengeluaran;
use App\Models\TransactionCategory;
use App\Models\User;
use App\Models\ZiswafPenerimaan;
use App\Services\Accounting\Psak109PostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MasterCoaPengeluaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_form_groups_general_expenses_and_zakat_distribution(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pengeluaran.create'))
            ->assertOk()
            ->assertSee('Dana Operasional')
            ->assertSee('Penyaluran Zakat kepada Mustahik')
            ->assertSee('Peralatan dan Bahan Kebersihan')
            ->assertSee('Pengembalian Pokok Wakaf Temporer')
            ->assertSee('Sumber Dana')
            ->assertSee('Ditentukan otomatis dari kode akun kategori.')
            ->assertSee('Pratinjau Jurnal')
            ->assertDontSee('Kategori (Akun COA)')
            ->assertSee('Rincian Penerima Zakat')
            ->assertSee('Jumlah Penerima')
            ->assertSee('Batch Akhir Periode')
            ->assertSee('Target Mustahik Per Asnaf')
            ->assertDontSee('Penyaluran Infak dan Sedekah')
            ->assertDontSee('Penyaluran Fidyah')
            ->assertDontSee('Sifat Infak/Sedekah');
    }

    public function test_expense_form_exposes_employee_honorarium_and_payment_source(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pengeluaran.create'))
            ->assertOk()
            ->assertSee('Honorarium Pegawai')
            ->assertSee('Data Honorarium Pegawai')
            ->assertSee('Pegawai Penerima')
            ->assertSee('Bukti Surat Tugas')
            ->assertSee('Sumber Pembayaran')
            ->assertSee('Kas')
            ->assertSee('Bank');
    }

    public function test_honorarium_is_linked_to_employee_and_credited_to_selected_bank(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $organizationId = $admin->organization_id;
        $category = $this->category($admin, 'HONORARIUM');

        $pegawai = Pegawai::create([
            'organization_id' => $organizationId,
            'nip' => 'PGW-HON-001',
            'nama_pegawai' => 'Pegawai Honorarium',
            'jabatan' => 'Petugas Kegiatan',
            'email' => 'pegawai-honorarium@finus.test',
            'is_verified' => true,
        ]);
        $pegawaiUser = User::create([
            'organization_id' => $organizationId,
            'name' => $pegawai->nama_pegawai,
            'email' => $pegawai->email,
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_PEGAWAI,
        ]);

        $penerimaan = ZiswafPenerimaan::create([
            'tanggal' => '2026-10-01',
            'jenis_ziswaf' => 'parkir',
            'nominal' => 1_000_000,
            'metode_pembayaran' => 'transfer',
            'status_verifikasi' => 'diterima',
        ]);
        app(Psak109PostingService::class)->postPenerimaan($penerimaan);

        $account = $category->coa;
        $bank = Coa::where('kode_akun', '1102')->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pengeluaran.store'), [
                'transaction_category_id' => $category->id,
                'jenis_dana' => 'operasional',
                'coa_kredit_id' => $bank->id,
                'id_pegawai' => $pegawai->id,
                'deskripsi' => 'Honorarium petugas kegiatan masjid',
                'jumlah' => 250000,
                'tanggal' => '2026-10-08',
                'bukti_surat_tugas' => UploadedFile::fake()->create('surat-tugas.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasNoErrors();

        $expense = Pengeluaran::latest('id')->firstOrFail();

        $this->assertSame($pegawai->id, $expense->id_pegawai);
        $this->assertSame('Honorarium Pegawai', $expense->kategori);
        $this->assertSame($account->id, $expense->coa_debit_id);
        $this->assertSame($bank->id, $expense->coa_kredit_id);
        Storage::disk('public')->assertExists($expense->bukti_surat_tugas);
        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $expense->jurnal_id,
            'coa_id' => $bank->id,
            'jenis_dana' => 'operasional',
            'credit' => 250000,
        ]);

        $this->actingAs($pegawaiUser, 'pegawai')
            ->get(route('pegawai.laporan-gaji.index'))
            ->assertOk()
            ->assertSee('Riwayat Honorarium')
            ->assertSee('Honorarium petugas kegiatan masjid')
            ->assertSee('Bank');
    }

    public function test_expense_is_saved_using_selected_coa_id(): void
    {
        $admin = $this->admin();
        $category = $this->category($admin, 'KEBERSIHAN');
        $account = $category->coa;
        $receipt = ZiswafPenerimaan::create([
            'tanggal' => '2026-09-20',
            'jenis_ziswaf' => 'parkir',
            'nominal' => 500000,
            'metode_pembayaran' => 'tunai',
            'status_verifikasi' => 'diterima',
        ]);
        app(Psak109PostingService::class)->postPenerimaan($receipt);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pengeluaran.store'), [
                'transaction_category_id' => $category->id,
                'jenis_dana' => 'operasional',
                'deskripsi' => 'Pembelian alat kebersihan',
                'jumlah' => 150000,
                'tanggal' => '2026-09-21',
            ])
            ->assertSessionHasNoErrors();

        $expense = Pengeluaran::latest('id')->firstOrFail();

        $this->assertSame($account->id, $expense->coa_debit_id);
        $this->assertSame('Peralatan dan Bahan Kebersihan', $expense->kategori);
        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $expense->jurnal_id,
            'coa_id' => $account->id,
            'jenis_dana' => 'operasional',
            'debit' => 150000,
        ]);
    }

    public function test_zakat_distribution_uses_zakat_fund_dimension(): void
    {
        $admin = $this->admin();
        $category = $this->category($admin, 'PENYALURAN-ZAKAT');
        $account = $category->coa;
        $receipt = ZiswafPenerimaan::create([
            'tanggal' => '2026-09-15',
            'jenis_ziswaf' => 'zakat_maal',
            'nominal' => 1_000_000,
            'metode_pembayaran' => 'tunai',
            'status_verifikasi' => 'diterima',
        ]);
        app(Psak109PostingService::class)->postPenerimaan($receipt);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pengeluaran.store'), [
                'transaction_category_id' => $category->id,
                'jenis_dana' => 'zakat',
                'deskripsi' => 'Penyaluran kepada mustahik miskin',
                'jumlah' => 500000,
                'tanggal' => '2026-09-30',
                'periode_zakat' => '2026-09',
                'zakat_details' => [
                    [
                        'nama_penerima' => 'Program Fakir',
                        'asnaf' => 'fakir',
                        'jumlah_penerima' => 3,
                        'nominal' => 300000,
                    ],
                    [
                        'nama_penerima' => 'Program Miskin',
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
            'nama_penerima' => 'Program Fakir',
        ]);

        $this->get(route('admin.laporan.jurnal-pengeluaran'))
            ->assertOk()
            ->assertSee('Penyaluran zakat - Fakir - Program Fakir (3 orang)')
            ->assertSee('Penyaluran zakat - Miskin - Program Miskin (2 orang)');
    }

    public function test_zakat_distribution_detail_must_match_expense_total(): void
    {
        $admin = $this->admin();
        $category = $this->category($admin, 'PENYALURAN-ZAKAT');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pengeluaran.store'), [
                'transaction_category_id' => $category->id,
                'jenis_dana' => 'zakat',
                'deskripsi' => 'Penyaluran zakat tidak seimbang',
                'jumlah' => 100000,
                'tanggal' => '2026-09-30',
                'periode_zakat' => '2026-09',
                'zakat_details' => [[
                    'nama_penerima' => 'Mustahik Tunggal',
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

    public function test_category_account_code_overrides_submitted_fund_type(): void
    {
        $admin = $this->admin();
        $category = $this->category($admin, 'KEBERSIHAN');
        $receipt = ZiswafPenerimaan::create([
            'tanggal' => '2026-10-01',
            'jenis_ziswaf' => 'parkir',
            'nominal' => 100000,
            'metode_pembayaran' => 'tunai',
            'status_verifikasi' => 'diterima',
        ]);
        app(Psak109PostingService::class)->postPenerimaan($receipt);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pengeluaran.store'), [
                'transaction_category_id' => $category->id,
                'jenis_dana' => 'zakat',
                'deskripsi' => 'Sumber dana mengikuti akun kebersihan',
                'jumlah' => 1000,
                'tanggal' => '2026-10-08',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pengeluaran', [
            'transaction_category_id' => $category->id,
            'jenis_dana' => 'operasional',
        ]);
    }

    public function test_admin_can_open_transaction_category_master(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.transaction-categories.index'))
            ->assertOk()
            ->assertSee('Kategori Transaksi')
            ->assertSee('Honorarium Pegawai')
            ->assertSee('Penyaluran Zakat kepada Mustahik')
            ->assertSee('Beban Gaji dan Honorarium');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.transaction-categories.create'))
            ->assertOk()
            ->assertSee('Sumber dana ditentukan otomatis dari kode akun.')
            ->assertDontSee('name="group"', false)
            ->assertDontSee('name="default_fund_type"', false)
            ->assertDontSee('name="allowed_fund_types[]"', false);
    }

    public function test_default_sync_preserves_a_category_disabled_by_admin(): void
    {
        $admin = $this->admin();
        $category = $this->category($admin, 'KEBERSIHAN');
        $category->update(['is_active' => false]);

        TransactionCategory::ensureDefaults();

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_retired_infak_and_fidyah_distribution_masters_are_not_recreated(): void
    {
        $this->admin();

        TransactionCategory::ensureDefaults();

        $this->assertDatabaseMissing('transaction_categories', ['code' => 'PENYALURAN-INFAK']);
        $this->assertDatabaseMissing('transaction_categories', ['code' => 'PENYALURAN-FIDYAH']);
        $this->assertDatabaseMissing('coa', ['kode_akun' => '4108']);
        $this->assertDatabaseMissing('coa', ['kode_akun' => '4302']);
        $this->assertDatabaseMissing('coa', ['kode_akun' => '5311']);
        $this->assertDatabaseMissing('coa', ['kode_akun' => '5312']);
        $this->assertDatabaseMissing('coa', ['kode_akun' => '5511']);
    }

    public function test_psak_specific_receipt_fields_and_report_labels_are_visible(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.pemasukan.index'))
            ->assertOk()
            ->assertDontSee('Sifat Infak/Sedekah')
            ->assertSee('Jenis Penerimaan Wakaf')
            ->assertSee('Tanggal Pengembalian Pokok')
            ->assertSee('Imbalan Nazhir');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.laporan.jurnal-pemasukan'))
            ->assertOk()
            ->assertSeeText('Jurnal Pemasukan')
            ->assertDontSee('PSAK 109')
            ->assertDontSee('PSAK 112');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.laporan.jurnal-pengeluaran'))
            ->assertOk()
            ->assertSeeText('Jurnal Pengeluaran')
            ->assertDontSee('PSAK 109')
            ->assertDontSee('PSAK 112');
    }

    public function test_zakat_policy_screen_only_exposes_the_current_single_source_settings(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.kebijakan-zakat.index', ['tab' => 'periode']))
            ->assertOk();
    }

    private function admin(): User
    {
        $organization = Organization::query()->firstOrCreate(
            ['slug' => 'pusdai-test'],
            [
                'public_id' => (string) str()->ulid(),
                'name' => 'PUSDAI Test',
                'country_code' => 'ID',
                'is_active' => true,
            ]
        );
        DB::table('coa')
            ->whereNull('organization_id')
            ->update(['organization_id' => $organization->id]);

        return User::create([
            'organization_id' => $organization->id,
            'name' => 'Admin COA',
            'email' => 'admin-coa-'.uniqid().'@finus.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);
    }

    private function category(User $admin, string $code): TransactionCategory
    {
        $this->actingAs($admin, 'admin');
        TransactionCategory::ensureDefaults();

        return TransactionCategory::query()->with('coa')->where('code', $code)->firstOrFail();
    }
}

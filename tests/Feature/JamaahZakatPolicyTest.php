<?php

namespace Tests\Feature;

use App\Models\BarangZakat;
use App\Models\HargaBarangZakat;
use App\Models\KetentuanPokokZakat;
use App\Models\Organization;
use App\Models\User;
use App\Models\ZiswafPenerimaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JamaahZakatPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.midtrans.enabled' => false]);
        Organization::query()->firstOrCreate(
            ['slug' => 'pusdai-jamaah-test'],
            [
                'public_id' => (string) str()->ulid(),
                'name' => 'PUSDAI Jamaah Test',
                'country_code' => 'ID',
                'is_active' => true,
            ]
        );
    }

    public function test_jamaah_zakat_page_uses_the_same_active_policy_as_admin(): void
    {
        KetentuanPokokZakat::untukJenis('maal')->update([
            'kadar_persentase' => 3,
            'persentase_amil' => 10,
        ]);
        KetentuanPokokZakat::untukJenis('penghasilan')->update([
            'kadar_persentase' => 2.75,
        ]);

        $beras = BarangZakat::query()->where('nama', 'Beras')->firstOrFail();
        $beras->harga()->delete();
        HargaBarangZakat::create([
            'barang_zakat_id' => $beras->id,
            'wilayah' => 'Jawa Barat',
            'harga_per_satuan' => 16_000,
            'berlaku_mulai' => now()->subDay()->toDateString(),
            'sumber_harga' => 'Survei pasar',
            'status' => 'disetujui',
        ]);
        $this->createActiveGoldPrice(1_300_000);

        $response = $this->actingAs($this->jamaah(), User::ROLE_JAMAAH)
            ->get(route('jamaah.transaksi.create', 'zakat'));

        $response
            ->assertOk()
            ->assertSee('Ketentuan Zakat Aktif')
            ->assertSee('Zakat Fitrah')
            ->assertSee('16.000')
            ->assertSee('Kategori Harta / Barang')
            ->assertSee('Simpanan dan Uang Tunai')
            ->assertDontSee('Piutang Tertagih')
            ->assertDontSee('Harta Halal Lainnya')
            ->assertSee('Berat Emas yang Dimiliki')
            ->assertSee('1.300.000')
            ->assertSee('110.500.000')
            ->assertSee('Kategori Penghasilan')
            ->assertSee('Honorarium atau Jasa Profesional')
            ->assertSee('Penghasilan/Pendapatan Bersih')
            ->assertDontSee('Penghasilan Utama')
            ->assertDontSee('Penghasilan Lain')
            ->assertDontSee('Pengurang/Kebutuhan Pokok')
            ->assertSee('Tanggal Mulai Kepemilikan Harta / Barang')
            ->assertSee('hitung terpisah per kelompok tanggal kepemilikan')
            ->assertViewHas('zakatPolicies', function (array $policies): bool {
                return (float) $policies['zakat_maal']['kadar_persentase'] === 3.0
                    && (float) $policies['zakat_penghasilan']['kadar_persentase'] === 2.75;
            })
            ->assertViewHas('hargaBeras', fn ($harga): bool => (int) $harga->harga_per_satuan === 16_000);
    }

    public function test_agriculture_is_presented_as_one_zakat_type_with_an_irrigation_method(): void
    {
        $response = $this->actingAs($this->jamaah(), User::ROLE_JAMAAH)
            ->get(route('jamaah.transaksi.create', 'zakat'));

        $response
            ->assertOk()
            ->assertSee('data-value="zakat_pertanian"', false)
            ->assertDontSee('data-value="zakat_pertanian_berbiaya"', false)
            ->assertDontSee('data-value="zakat_pertanian_alami"', false)
            ->assertSee('Metode Pengairan')
            ->assertSee('Irigasi / menggunakan biaya')
            ->assertSee('Tadah hujan / alami tanpa biaya')
            ->assertDontSee('Jagung')
            ->assertDontSee('Hasil Pertanian Lainnya')
            ->assertSee('Berat Bersih Hasil Panen');
    }

    public function test_removed_maal_categories_are_rejected_by_the_server(): void
    {
        Storage::fake('public');
        $jamaah = $this->jamaah();

        foreach (['piutang_tertagih', 'harta_lainnya'] as $category) {
            $this->actingAs($jamaah, User::ROLE_JAMAAH)
                ->post(route('jamaah.transaksi.store', 'zakat'), [
                    'jenis_ziswaf' => 'zakat_maal',
                    'nominal' => 250_000,
                    'kategori_perhitungan' => $category,
                    'tanggal_mulai_kepemilikan' => now()->subYear()->subDay()->toDateString(),
                    'metode_pembayaran' => 'manual_transfer',
                    'bukti_pembayaran' => UploadedFile::fake()->create(
                        'bukti-transfer.jpg',
                        100,
                        'image/jpeg'
                    ),
                ])
                ->assertSessionHasErrors('kategori_perhitungan');
        }

        $this->assertDatabaseCount('ziswaf_penerimaan', 0);
    }

    public function test_agriculture_transaction_uses_the_selected_irrigation_policy(): void
    {
        Storage::fake('public');
        $this->createActiveAgriculturalPrice('GABAH', 'Gabah', 10_000);

        $response = $this->actingAs($this->jamaah(), User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'zakat'), [
                'jenis_ziswaf' => 'zakat_pertanian_berbiaya',
                'nominal' => 1_000,
                'kategori_perhitungan' => 'padi_gabah',
                'berat_panen_kg' => 700,
                'metode_pembayaran' => 'manual_transfer',
                'bukti_pembayaran' => UploadedFile::fake()->create(
                    'bukti-transfer.jpg',
                    100,
                    'image/jpeg'
                ),
            ]);

        $response->assertRedirect(route('jamaah.riwayat.index'));

        $transaction = ZiswafPenerimaan::query()->latest('id')->firstOrFail();
        $this->assertSame('zakat_pertanian_berbiaya', $transaction->jenis_ziswaf);
        $this->assertSame('padi_gabah', $transaction->rincian_perhitungan['kategori_perhitungan']);
        $this->assertSame('Padi / Gabah', $transaction->rincian_perhitungan['kategori_perhitungan_label']);
        $this->assertSame(5.0, (float) $transaction->persentase_zakat);
        $this->assertSame('pertanian_berbiaya', $transaction->snapshot_kebijakan['jenis']);
        $this->assertSame(350_000, (int) $transaction->nominal);
        $this->assertSame(700.0, (float) $transaction->rincian_perhitungan['berat_panen_kg']);
        $this->assertSame(10_000, (int) $transaction->rincian_perhitungan['harga_per_kg']);
        $this->assertSame(7_000_000, (int) $transaction->rincian_perhitungan['nilai_panen_rupiah']);
        $this->assertSame(653.0, (float) $transaction->rincian_perhitungan['nisab_panen_kg']);
        $this->assertSame(35.0, (float) $transaction->rincian_perhitungan['zakat_panen_kg']);
    }

    public function test_agriculture_rejects_removed_categories_and_harvest_below_nisab(): void
    {
        Storage::fake('public');
        $this->createActiveAgriculturalPrice('GABAH', 'Gabah', 10_000);
        $jamaah = $this->jamaah();

        $basePayload = [
            'jenis_ziswaf' => 'zakat_pertanian_alami',
            'nominal' => 1_000,
            'berat_panen_kg' => 700,
            'metode_pembayaran' => 'manual_transfer',
            'bukti_pembayaran' => UploadedFile::fake()->create('bukti-transfer.jpg', 100, 'image/jpeg'),
        ];

        $this->actingAs($jamaah, User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'zakat'), array_merge($basePayload, [
                'kategori_perhitungan' => 'jagung',
            ]))
            ->assertSessionHasErrors('kategori_perhitungan');

        $this->actingAs($jamaah, User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'zakat'), array_merge($basePayload, [
                'kategori_perhitungan' => 'padi_gabah',
                'berat_panen_kg' => 652,
                'bukti_pembayaran' => UploadedFile::fake()->create('bukti-transfer-2.jpg', 100, 'image/jpeg'),
            ]))
            ->assertSessionHasErrors('berat_panen_kg');

        $this->assertDatabaseCount('ziswaf_penerimaan', 0);
    }

    public function test_jamaah_zakat_transaction_keeps_the_policy_snapshot_used_at_creation(): void
    {
        Storage::fake('public');

        $policy = KetentuanPokokZakat::untukJenis('maal');
        $policy->update([
            'kadar_persentase' => 3.25,
            'persentase_amil' => 11,
            'haul' => '6 bulan',
        ]);
        $this->createActiveGoldPrice(1_200_000);

        $response = $this->actingAs($this->jamaah(), User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'zakat'), [
                'jenis_ziswaf' => 'zakat_maal',
                'nominal' => 250_000,
                'kategori_perhitungan' => 'simpanan_uang_tunai',
                'tanggal_mulai_kepemilikan' => now()->subMonths(7)->toDateString(),
                'metode_pembayaran' => 'manual_transfer',
                'bukti_pembayaran' => UploadedFile::fake()->create(
                    'bukti-transfer.jpg',
                    100,
                    'image/jpeg'
                ),
                'keterangan' => 'Zakat maal jamaah',
            ]);

        $response->assertRedirect(route('jamaah.riwayat.index'));

        $transaction = ZiswafPenerimaan::query()->latest('id')->firstOrFail();

        $this->assertSame($policy->id, $transaction->snapshot_kebijakan['ketentuan_pokok_id']);
        $this->assertSame(3.25, (float) $transaction->snapshot_kebijakan['kadar_persentase']);
        $this->assertSame(0.0, (float) $transaction->snapshot_kebijakan['persentase_amil']);
        $this->assertSame(102_000_000, (int) $transaction->nisab_digunakan);
        $this->assertSame(3.25, (float) $transaction->persentase_zakat);
        $this->assertSame(0.0, (float) $transaction->persentase_amil);
        $this->assertTrue($transaction->rincian_perhitungan['memenuhi_haul']);
        $this->assertSame(
            now()->subMonths(7)->toDateString(),
            $transaction->rincian_perhitungan['tanggal_mulai_kepemilikan']
        );
        $this->assertSame('6 bulan', $transaction->snapshot_kebijakan['haul']);
        $this->assertSame('simpanan_uang_tunai', $transaction->rincian_perhitungan['kategori_perhitungan']);
        $this->assertSame('Simpanan dan Uang Tunai', $transaction->rincian_perhitungan['kategori_perhitungan_label']);
    }

    public function test_jamaah_cannot_submit_zakat_maal_before_the_goods_meet_haul(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->jamaah(), User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'zakat'), [
                'jenis_ziswaf' => 'zakat_maal',
                'nominal' => 250_000,
                'kategori_perhitungan' => 'simpanan_uang_tunai',
                'tanggal_mulai_kepemilikan' => now()->subMonths(6)->toDateString(),
                'metode_pembayaran' => 'manual_transfer',
                'bukti_pembayaran' => UploadedFile::fake()->create(
                    'bukti-transfer.jpg',
                    100,
                    'image/jpeg'
                ),
            ]);

        $response->assertSessionHasErrors('tanggal_mulai_kepemilikan');
        $this->assertDatabaseCount('ziswaf_penerimaan', 0);
    }

    public function test_gold_zakat_converts_grams_to_rupiah_and_sets_the_required_payment(): void
    {
        Storage::fake('public');
        $this->createActiveGoldPrice(1_200_000);

        $response = $this->actingAs($this->jamaah(), User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'zakat'), [
                'jenis_ziswaf' => 'zakat_maal',
                'nominal' => 1_000,
                'kategori_perhitungan' => 'emas_logam_mulia',
                'berat_emas_gram' => 100,
                'tanggal_mulai_kepemilikan' => now()->subYear()->subDay()->toDateString(),
                'metode_pembayaran' => 'manual_transfer',
                'bukti_pembayaran' => UploadedFile::fake()->create(
                    'bukti-transfer.jpg',
                    100,
                    'image/jpeg'
                ),
            ]);

        $response->assertRedirect(route('jamaah.riwayat.index'));

        $transaction = ZiswafPenerimaan::query()->latest('id')->firstOrFail();
        $calculation = $transaction->rincian_perhitungan;

        $this->assertSame(3_000_000, (int) $transaction->nominal);
        $this->assertSame(100.0, (float) $calculation['berat_emas_gram']);
        $this->assertSame(1_200_000, (int) $calculation['harga_emas_per_gram']);
        $this->assertSame(120_000_000, (int) $calculation['nilai_emas_rupiah']);
        $this->assertSame(85.0, (float) $calculation['nisab_emas_gram']);
        $this->assertSame(102_000_000, (int) $calculation['nisab_emas_rupiah']);
        $this->assertSame(3_000_000, (int) $calculation['jumlah_zakat_dihitung']);
        $this->assertSame(102_000_000, (int) $transaction->nisab_digunakan);
    }

    public function test_gold_below_eighty_five_grams_is_rejected(): void
    {
        Storage::fake('public');
        $this->createActiveGoldPrice(1_200_000);

        $this->actingAs($this->jamaah(), User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'zakat'), [
                'jenis_ziswaf' => 'zakat_maal',
                'nominal' => 1_000,
                'kategori_perhitungan' => 'emas_logam_mulia',
                'berat_emas_gram' => 84,
                'tanggal_mulai_kepemilikan' => now()->subYear()->subDay()->toDateString(),
                'metode_pembayaran' => 'manual_transfer',
                'bukti_pembayaran' => UploadedFile::fake()->create(
                    'bukti-transfer.jpg',
                    100,
                    'image/jpeg'
                ),
            ])
            ->assertSessionHasErrors('berat_emas_gram');

        $this->assertDatabaseCount('ziswaf_penerimaan', 0);
    }

    public function test_jamaah_income_zakat_stores_the_selected_income_category(): void
    {
        Storage::fake('public');
        KetentuanPokokZakat::untukJenis('penghasilan')->update([
            'kadar_persentase' => 2.5,
        ]);
        $this->createActiveGoldPrice(1_200_000);

        $response = $this->actingAs($this->jamaah(), User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'zakat'), [
                'jenis_ziswaf' => 'zakat_penghasilan',
                'nominal' => 250_000,
                'kategori_perhitungan' => 'honorarium_jasa',
                'periode_penghasilan' => 'bulanan',
                'bulan_penghasilan' => now()->format('Y-m'),
                'tahun_penghasilan' => now()->year,
                'penghasilan_bersih' => 10_000_000,
                'metode_pembayaran' => 'manual_transfer',
                'bukti_pembayaran' => UploadedFile::fake()->create(
                    'bukti-transfer.jpg',
                    100,
                    'image/jpeg'
                ),
            ]);

        $response->assertRedirect(route('jamaah.riwayat.index'));

        $transaction = ZiswafPenerimaan::query()->latest('id')->firstOrFail();
        $this->assertSame('honorarium_jasa', $transaction->rincian_perhitungan['kategori_perhitungan']);
        $this->assertSame(
            'Honorarium atau Jasa Profesional',
            $transaction->rincian_perhitungan['kategori_perhitungan_label']
        );
        $this->assertSame('bulanan', $transaction->rincian_perhitungan['periode_penghasilan']);
        $this->assertSame(now()->format('Y-m'), $transaction->rincian_perhitungan['bulan_penghasilan']);
        $this->assertSame(102_000_000, (int) $transaction->rincian_perhitungan['nisab_tahunan']);
        $this->assertSame(8_500_000, (int) $transaction->rincian_perhitungan['nisab_bulanan']);
        $this->assertSame(8_500_000, (int) $transaction->nisab_digunakan);
        $this->assertSame(10_000_000, (int) $transaction->rincian_perhitungan['dasar_zakat']);
        $this->assertSame('penghasilan_bersih', $transaction->rincian_perhitungan['metode_dasar_zakat']);
        $this->assertSame(250_000, (int) $transaction->nominal);
        $this->assertNull($transaction->rincian_perhitungan['memenuhi_haul']);
    }

    public function test_annual_income_zakat_reconciles_verified_payments_from_the_same_year(): void
    {
        Storage::fake('public');
        $jamaah = $this->jamaah();
        KetentuanPokokZakat::untukJenis('penghasilan')->update([
            'kadar_persentase' => 2.5,
        ]);
        $this->createActiveGoldPrice(1_200_000);

        ZiswafPenerimaan::create([
            'muzakki_id' => $jamaah->id,
            'tanggal' => now()->startOfYear()->addMonth()->toDateString(),
            'jenis_ziswaf' => 'zakat_penghasilan',
            'nominal' => 1_000_000,
            'metode_pembayaran' => 'manual_transfer',
            'status_verifikasi' => 'diterima',
            'rincian_perhitungan' => [
                'periode_penghasilan' => 'bulanan',
                'bulan_penghasilan' => now()->startOfYear()->addMonth()->format('Y-m'),
                'tahun_penghasilan' => now()->year,
            ],
        ]);

        $response = $this->actingAs($jamaah, User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'zakat'), [
                'jenis_ziswaf' => 'zakat_penghasilan',
                'nominal' => 9_999_999,
                'kategori_perhitungan' => 'gaji_upah',
                'periode_penghasilan' => 'tahunan',
                'tahun_penghasilan' => now()->year,
                'penghasilan_bersih' => 120_000_000,
                'metode_pembayaran' => 'manual_transfer',
                'bukti_pembayaran' => UploadedFile::fake()->create(
                    'bukti-transfer-tahunan.jpg',
                    100,
                    'image/jpeg'
                ),
            ]);

        $response->assertRedirect(route('jamaah.riwayat.index'));

        $transaction = ZiswafPenerimaan::query()->latest('id')->firstOrFail();
        $calculation = $transaction->rincian_perhitungan;

        $this->assertSame('tahunan', $calculation['periode_penghasilan']);
        $this->assertSame(120_000_000, (int) $calculation['dasar_zakat']);
        $this->assertSame(3_000_000, (int) $calculation['kewajiban_zakat']);
        $this->assertSame(1_000_000, (int) $calculation['zakat_sudah_dibayar']);
        $this->assertSame(2_000_000, (int) $calculation['jumlah_zakat']);
        $this->assertSame(2_000_000, (int) $transaction->nominal);
        $this->assertSame(102_000_000, (int) $transaction->nisab_digunakan);
    }

    public function test_income_zakat_rejects_a_maal_category(): void
    {
        Storage::fake('public');

        $this->actingAs($this->jamaah(), User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'zakat'), [
                'jenis_ziswaf' => 'zakat_penghasilan',
                'nominal' => 250_000,
                'kategori_perhitungan' => 'emas_logam_mulia',
                'metode_pembayaran' => 'manual_transfer',
                'bukti_pembayaran' => UploadedFile::fake()->create(
                    'bukti-transfer.jpg',
                    100,
                    'image/jpeg'
                ),
            ])
            ->assertSessionHasErrors('kategori_perhitungan');

        $this->assertDatabaseCount('ziswaf_penerimaan', 0);
    }

    public function test_inactive_zakat_policy_is_not_offered_to_jamaah(): void
    {
        KetentuanPokokZakat::untukJenis('fitrah')->update(['aktif' => false]);

        $this->actingAs($this->jamaah(), User::ROLE_JAMAAH)
            ->get(route('jamaah.transaksi.create', 'zakat'))
            ->assertOk()
            ->assertViewHas('config', fn (array $config): bool => ! array_key_exists(
                'zakat_fitrah',
                $config['jenisOptions']
            ));
    }

    public function test_infak_form_has_no_restriction_and_ignores_submitted_restriction(): void
    {
        Storage::fake('public');
        $jamaah = $this->jamaah();
        $organization = Organization::query()->firstOrFail();

        $this->actingAs($jamaah, User::ROLE_JAMAAH)
            ->get(route('jamaah.transaksi.create', 'infak'))
            ->assertOk()
            ->assertDontSee('Sifat Infak/Sedekah')
            ->assertDontSee('name="restriction_type"', false);

        $this->actingAs($jamaah, User::ROLE_JAMAAH)
            ->post(route('jamaah.transaksi.store', 'infak'), [
                'organization_id' => $organization->id,
                'jenis_ziswaf' => 'infaq',
                'restriction_type' => 'muqayyadah',
                'nominal' => 50000,
                'metode_pembayaran' => 'manual_transfer',
                'bukti_pembayaran' => UploadedFile::fake()->create(
                    'bukti-infak.jpg',
                    100,
                    'image/jpeg'
                ),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('ziswaf_penerimaan', [
            'muzakki_id' => $jamaah->id,
            'jenis_ziswaf' => 'infaq',
            'restriction_type' => null,
        ]);
    }

    private function jamaah(): User
    {
        return User::create([
            'name' => 'Jamaah Uji',
            'email' => 'jamaah-uji-'.uniqid().'@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_JAMAAH,
        ]);
    }

    private function createActiveGoldPrice(int $pricePerGram): HargaBarangZakat
    {
        $emas = BarangZakat::create([
            'kode' => 'EMAS',
            'nama' => 'Emas',
            'kategori' => 'logam_mulia',
            'satuan_dasar' => 'gram',
            'metode_penilaian' => 'harga_pasar',
            'aktif' => true,
        ]);

        return HargaBarangZakat::create([
            'barang_zakat_id' => $emas->id,
            'wilayah' => 'Jawa Barat',
            'harga_per_satuan' => $pricePerGram,
            'berlaku_mulai' => now()->subDay()->toDateString(),
            'sumber_harga' => 'Harga emas harian',
            'status' => 'disetujui',
        ]);
    }

    private function createActiveAgriculturalPrice(string $code, string $name, int $pricePerKg): HargaBarangZakat
    {
        $commodity = BarangZakat::create([
            'kode' => $code,
            'nama' => $name,
            'kategori' => 'hasil_pertanian',
            'satuan_dasar' => 'kg',
            'metode_penilaian' => 'harga_pasar',
            'aktif' => true,
        ]);

        return HargaBarangZakat::create([
            'barang_zakat_id' => $commodity->id,
            'wilayah' => 'Jawa Barat',
            'harga_per_satuan' => $pricePerKg,
            'berlaku_mulai' => now()->subDay()->toDateString(),
            'sumber_harga' => 'Harga gabah harian',
            'status' => 'disetujui',
        ]);
    }
}

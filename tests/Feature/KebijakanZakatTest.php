<?php

namespace Tests\Feature;

use App\Models\BarangZakat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KebijakanZakatTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_kebijakan_zakat_page(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.kebijakan-zakat.index'))
            ->assertOk()
            ->assertSee('Kebijakan Zakat')
            ->assertSee('Barang & Harga', false)
            ->assertSee('data-kz-modal-open="kz-modal-barang"', false)
            ->assertSee('id="kz-modal-barang"', false)
            ->assertSee('Muzakki')
            ->assertSee('Hak Amil')
            ->assertSee('Mustahik');
    }

    public function test_admin_can_store_barang_and_approved_price(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.barang.store'), [
                'kode' => 'GBH',
                'nama' => 'Gabah Uji',
                'kategori' => 'hasil_pertanian',
                'satuan_dasar' => 'kg',
                'metode_penilaian' => 'harga_pasar',
                'aktif' => 1,
            ])
            ->assertSessionHasNoErrors();

        $barang = BarangZakat::where('kode', 'GBH')->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.harga-barang.store'), [
                'barang_zakat_id' => $barang->id,
                'wilayah' => 'Jawa Barat',
                'harga_per_satuan' => 7500,
                'berlaku_mulai' => '2095-01-01',
                'berlaku_sampai' => '2095-12-31',
                'sumber_harga' => 'Survei pasar tahun 2095',
                'status' => 'disetujui',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('harga_barang_zakat', [
            'barang_zakat_id' => $barang->id,
            'wilayah' => 'Jawa Barat',
            'harga_per_satuan' => 7500,
            'status' => 'disetujui',
            'approved_by' => $admin->id,
        ]);
    }

    public function test_approved_item_prices_cannot_have_overlapping_periods(): void
    {
        $admin = $this->admin();
        $beras = BarangZakat::where('kode', 'BRAS')->firstOrFail();

        $base = [
            'barang_zakat_id' => $beras->id,
            'wilayah' => 'Jawa Barat',
            'harga_per_satuan' => 17000,
            'berlaku_mulai' => '2094-01-01',
            'berlaku_sampai' => '2094-12-31',
            'sumber_harga' => 'Ketetapan harga tahun 2094',
            'status' => 'disetujui',
        ];

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.harga-barang.store'), $base)
            ->assertSessionHasNoErrors();

        $base['berlaku_mulai'] = '2094-06-01';

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.harga-barang.store'), $base)
            ->assertSessionHasErrors('berlaku_mulai');
    }

    public function test_admin_can_store_mustahik_policy(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.mustahik.store'), [
                'asnaf' => 'fakir',
                'prioritas' => 1,
                'target_persentase' => 35,
                'batas_bantuan' => 2500000,
                'bentuk_penyaluran' => 'keduanya',
                'kriteria' => 'Tidak memiliki penghasilan untuk memenuhi kebutuhan dasar.',
                'dasar_aturan' => 'Keputusan pengurus tahun 2099',
                'berlaku_mulai' => '2099-01-01',
                'berlaku_sampai' => '2099-12-31',
                'aktif' => 1,
            ])
            ->assertRedirect(route('admin.kebijakan-zakat.index', ['tab' => 'mustahik']));

        $this->assertDatabaseHas('kebijakan_mustahik', [
            'asnaf' => 'fakir',
            'prioritas' => 1,
            'batas_bantuan' => 2500000,
            'aktif' => 1,
            'created_by' => $admin->id,
        ]);
    }

    public function test_admin_can_store_muzakki_and_amil_policies(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.muzakki.store'), [
                'tahun' => 2098,
                'nisab_penghasilan_tahunan' => 120000000,
                'nisab_penghasilan_bulanan' => 10000000,
                'nisab_maal' => 120000000,
                'persentase_zakat' => 2.5,
                'zakat_fitrah_per_jiwa' => 60000,
                'beras_fitrah_kg' => 2.5,
                'beras_fitrah_liter' => 3.5,
                'sumber' => 'Keputusan pengurus tahun 2098',
                'berlaku_mulai' => '2098-01-01',
                'berlaku_sampai' => '2098-12-31',
                'aktif' => 1,
            ])
            ->assertSessionHasNoErrors();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.amil.store'), [
                'jenis_sumber' => 'zakat',
                'persentase_amil' => 10,
                'berlaku_mulai' => '2098-01-01',
                'berlaku_sampai' => '2098-12-31',
                'dasar_aturan' => 'Keputusan pengurus tahun 2098',
                'potong_infak_terikat' => 0,
                'aktif' => 0,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('zakat_settings', [
            'tahun' => 2098,
            'zakat_fitrah_per_jiwa' => 60000,
        ]);

        $this->assertDatabaseHas('kebijakan_amil', [
            'jenis_sumber' => 'zakat',
            'persentase_amil' => 10,
            'berlaku_mulai' => '2098-01-01',
            'aktif' => 0,
        ]);
    }

    public function test_overlapping_mustahik_policy_is_rejected(): void
    {
        $admin = $this->admin();

        $payload = [
            'asnaf' => 'miskin',
            'prioritas' => 1,
            'target_persentase' => 40,
            'bentuk_penyaluran' => 'uang',
            'kriteria' => 'Belum mampu memenuhi kebutuhan dasar.',
            'berlaku_mulai' => '2097-01-01',
            'berlaku_sampai' => '2097-12-31',
            'aktif' => 1,
        ];

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.mustahik.store'), $payload)
            ->assertSessionHasNoErrors();

        $payload['berlaku_mulai'] = '2097-06-01';

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.mustahik.store'), $payload)
            ->assertSessionHasErrors('berlaku_mulai');
    }

    public function test_overlapping_targets_cannot_exceed_one_hundred_percent(): void
    {
        $admin = $this->admin();

        $base = [
            'prioritas' => 1,
            'bentuk_penyaluran' => 'keduanya',
            'kriteria' => 'Memenuhi hasil verifikasi kelayakan.',
            'berlaku_mulai' => '2096-01-01',
            'berlaku_sampai' => '2096-12-31',
            'aktif' => 1,
        ];

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.mustahik.store'), $base + [
                'asnaf' => 'fakir',
                'target_persentase' => 60,
            ])
            ->assertSessionHasNoErrors();

        $this->actingAs($admin, 'admin')
            ->post(route('admin.kebijakan-zakat.mustahik.store'), $base + [
                'asnaf' => 'miskin',
                'target_persentase' => 50,
            ])
            ->assertSessionHasErrors('target_persentase');
    }

    public function test_mustahik_policy_rejects_unknown_asnaf(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->from(route('admin.kebijakan-zakat.index', ['tab' => 'mustahik']))
            ->post(route('admin.kebijakan-zakat.mustahik.store'), [
                'asnaf' => 'umum',
                'prioritas' => 1,
                'bentuk_penyaluran' => 'uang',
                'kriteria' => 'Kriteria uji.',
                'berlaku_mulai' => '2099-01-01',
                'aktif' => 1,
            ])
            ->assertSessionHasErrors('asnaf');
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Kebijakan',
            'email' => 'admin-kebijakan-'.uniqid().'@finus.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}

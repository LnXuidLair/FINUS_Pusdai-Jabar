<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePemasukanPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_uses_standalone_create_form_and_index_links_to_it(): void
    {
        $org = Organization::create([
            'slug' => 'pemasukan-create-test',
            'public_id' => (string) str()->ulid(),
            'name' => 'Masjid Test',
            'country_code' => 'ID',
            'is_active' => true,
        ]);
        $admin = User::create([
            'organization_id' => $org->id,
            'name' => 'Admin Pengujian',
            'email' => 'pemasukan-create@finus.test',
            'password' => bcrypt('test123456'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);
        $this->actingAs($admin, 'admin');

        $this->get(route('admin.pemasukan.index'))
            ->assertOk()
            ->assertSee(route('admin.pemasukan.create'), false)
            ->assertDontSee('pmTambahOverlay', false);

        $this->get(route('admin.pemasukan.create'))
            ->assertOk()
            ->assertSee('id="pmTambahForm"', false)
            ->assertSee('name="bukti_pembayaran"', false)
            ->assertSee('id="pmParkingFields"', false)
            ->assertSee('name="wakaf_type"', false)
            ->assertSee('name="nominal_qris"', false)
            ->assertSee('name="nominal_tunai"', false)
            ->assertSee(route('admin.pemasukan.store'), false);
    }
}

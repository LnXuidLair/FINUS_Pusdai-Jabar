<?php

namespace Tests\Feature;

use App\Models\Coa;
use App\Models\GajiJabatan;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class MultiOrganizationIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_job_title_can_exist_in_different_organizations_and_creator_is_recorded(): void
    {
        [$organizationA, $adminA] = $this->makeOrganizationWithAdmin('Masjid A', 'admin-a@masjida.finus.id');
        [$organizationB, $adminB] = $this->makeOrganizationWithAdmin('Masjid B', 'admin-b@masjidb.finus.id');

        $this->actingAs($adminA, User::ROLE_ADMIN)
            ->post(route('admin.gaji-jabatan.store'), [
                'jabatan' => 'Bendahara',
                'gaji_perhari' => 100000,
            ])
            ->assertRedirect(route('admin.gaji-jabatan.index'));

        $this->assertDatabaseHas('gaji_jabatan', [
            'organization_id' => $organizationA->id,
            'jabatan' => 'Bendahara',
            'gaji_perhari' => 100000,
            'created_by' => $adminA->id,
        ]);

        Auth::guard(User::ROLE_ADMIN)->logout();

        $this->actingAs($adminB, User::ROLE_ADMIN)
            ->post(route('admin.gaji-jabatan.store'), [
                'jabatan' => 'Bendahara',
                'gaji_perhari' => 150000,
            ])
            ->assertRedirect(route('admin.gaji-jabatan.index'));

        $this->assertDatabaseHas('gaji_jabatan', [
            'organization_id' => $organizationB->id,
            'jabatan' => 'Bendahara',
            'gaji_perhari' => 150000,
            'created_by' => $adminB->id,
        ]);
    }

    public function test_same_job_title_is_rejected_inside_the_same_organization(): void
    {
        [, $admin] = $this->makeOrganizationWithAdmin('Masjid A', 'admin-a@masjida.finus.id');

        $this->actingAs($admin, User::ROLE_ADMIN)
            ->post(route('admin.gaji-jabatan.store'), [
                'jabatan' => 'Keuangan',
                'gaji_perhari' => 100000,
            ]);

        $this->post(route('admin.gaji-jabatan.store'), [
            'jabatan' => 'Keuangan',
            'gaji_perhari' => 200000,
        ])->assertSessionHasErrors('jabatan');
    }

    public function test_admin_route_only_sees_its_organization_even_if_staff_guard_from_another_org_is_also_logged_in(): void
    {
        [$organizationA, $adminA] = $this->makeOrganizationWithAdmin('Masjid A', 'admin-a@masjida.finus.id');
        [$organizationB] = $this->makeOrganizationWithAdmin('Masjid B', 'admin-b@masjidb.finus.id');

        $staffB = User::create([
            'name' => 'Pegawai Masjid B',
            'organization_id' => $organizationB->id,
            'email' => 'staff@masjidb.finus.id',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_PEGAWAI,
        ]);

        GajiJabatan::withoutGlobalScope('organization')->create([
            'organization_id' => $organizationA->id,
            'jabatan' => 'Bendahara Masjid A',
            'gaji_perhari' => 100000,
            'created_by' => $adminA->id,
        ]);

        GajiJabatan::withoutGlobalScope('organization')->create([
            'organization_id' => $organizationB->id,
            'jabatan' => 'Bendahara Masjid B',
            'gaji_perhari' => 200000,
        ]);

        $this->actingAs($adminA, User::ROLE_ADMIN);
        Auth::guard(User::ROLE_PEGAWAI)->login($staffB);

        $this->get(route('admin.gaji-jabatan.index'))
            ->assertOk()
            ->assertSee('Bendahara Masjid A')
            ->assertDontSee('Bendahara Masjid B');
    }

    public function test_coa_code_and_name_can_be_reused_by_different_organizations(): void
    {
        [$organizationA] = $this->makeOrganizationWithAdmin('Masjid A', 'admin-a@masjida.finus.id');
        [$organizationB] = $this->makeOrganizationWithAdmin('Masjid B', 'admin-b@masjidb.finus.id');

        Coa::withoutGlobalScope('organization')->create([
            'organization_id' => $organizationA->id,
            'header_akun' => 1,
            'kode_akun' => '1101',
            'nama_akun' => 'Kas',
        ]);

        Coa::withoutGlobalScope('organization')->create([
            'organization_id' => $organizationB->id,
            'header_akun' => 1,
            'kode_akun' => '1101',
            'nama_akun' => 'Kas',
        ]);

        $this->assertSame(
            2,
            Coa::withoutGlobalScope('organization')->where('kode_akun', '1101')->count()
        );
    }

    private function makeOrganizationWithAdmin(string $name, string $email): array
    {
        $organization = Organization::create([
            'public_id' => (string) Str::ulid(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'country_code' => 'ID',
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Admin '.$name,
            'organization_id' => $organization->id,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);

        return [$organization, $admin];
    }
}

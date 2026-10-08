<?php

namespace Tests\Feature;

use App\Models\GajiJabatan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_assign_phone_number_used_by_another_employee(): void
    {
        $admin = User::create([
            'name' => 'Admin Pusdai',
            'email' => 'admin@pusdai.finus.id',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);

        GajiJabatan::create([
            'jabatan' => 'Keuangan',
            'gaji_perhari' => 100000,
        ]);

        $this->createEmployee('0001', 'Pegawai Satu', 'pegawaisatu0001@staffpusdai.finus.id', '+6281234567890');
        $pegawaiDua = $this->createEmployee('0002', 'Pegawai Dua', 'pegawaidua0002@staffpusdai.finus.id', '+6281355566677');

        $this->actingAs($admin, User::ROLE_ADMIN)
            ->patch(route('admin.pegawai.update', $pegawaiDua), [
                'nip' => '0002',
                'nama_pegawai' => 'Pegawai Dua',
                'jabatan' => 'Keuangan',
                'gender' => 'L',
                'no_telp' => '+62 812-3456-7890',
                'alamat' => 'Bandung',
            ])
            ->assertSessionHasErrors('no_telp');

        $this->assertSame('+6281355566677', $pegawaiDua->fresh()->no_telp);
    }

    public function test_employee_cannot_use_phone_number_owned_by_another_employee(): void
    {
        $this->createEmployee('0001', 'Pegawai Satu', 'satu@staff.finus.id', '+6281234567890');
        $pegawaiDua = $this->createEmployee('0002', 'Pegawai Dua', 'dua@staff.finus.id', '+6281355566677');
        $userDua = User::where('email', $pegawaiDua->email)->firstOrFail();

        $this->actingAs($userDua, User::ROLE_PEGAWAI)
            ->patch(route('pegawai.contact.update'), [
                'no_telp' => '+62 812 3456 7890',
                'alamat' => 'Bandung',
            ])
            ->assertSessionHasErrors('no_telp');

        $this->assertSame('+6281355566677', $pegawaiDua->fresh()->no_telp);
    }

    public function test_employee_can_update_own_phone_and_address_from_settings(): void
    {
        $pegawai = $this->createEmployee('0001', 'Pegawai Satu', 'satu@staff.finus.id', null);
        $user = User::where('email', $pegawai->email)->firstOrFail();

        $this->actingAs($user, User::ROLE_PEGAWAI)
            ->patch(route('pegawai.contact.update'), [
                'no_telp' => '+62 813-1111-2222',
                'alamat' => 'Jl. Diponegoro No. 63, Bandung',
            ])
            ->assertRedirect(route('pegawai.settings'))
            ->assertSessionHas('status', 'contact-updated');

        $pegawai->refresh();
        $this->assertSame('+6281311112222', $pegawai->no_telp);
        $this->assertSame('Jl. Diponegoro No. 63, Bandung', $pegawai->alamat);
    }

    public function test_jamaah_can_update_phone_and_address_from_settings(): void
    {
        $jamaah = User::create([
            'name' => 'Jamaah FINUS',
            'email' => 'jamaah@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_JAMAAH,
        ]);

        $this->actingAs($jamaah, User::ROLE_JAMAAH)
            ->patch(route('jamaah.contact.update'), [
                'no_telp' => '+62 812-0000-9999',
                'alamat' => 'Bandung, Jawa Barat',
            ])
            ->assertRedirect(route('jamaah.settings'))
            ->assertSessionHas('status', 'contact-updated');

        $jamaah->refresh();
        $this->assertSame('+6281200009999', $jamaah->no_telp);
        $this->assertSame('Bandung, Jawa Barat', $jamaah->alamat);
    }


    public function test_same_national_digits_with_different_country_codes_are_not_treated_as_same_employee_phone(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->createEmployee('0001', 'Pegawai Indonesia', 'indonesia@staff.finus.id', '+6281234567890');
        $pegawaiMalaysia = $this->createEmployee('0002', 'Pegawai Malaysia', 'malaysia@staff.finus.id', '+601234567890');

        $this->actingAs($admin, User::ROLE_ADMIN)
            ->put(route('admin.pegawai.update', $pegawaiMalaysia->id), [
                'nip' => $pegawaiMalaysia->nip,
                'nama_pegawai' => $pegawaiMalaysia->nama_pegawai,
                'jabatan' => $pegawaiMalaysia->jabatan,
                'gender' => $pegawaiMalaysia->gender,
                'no_telp' => '+601234567890',
                'alamat' => $pegawaiMalaysia->alamat,
            ])
            ->assertSessionDoesntHaveErrors('no_telp');

        $this->assertSame('+601234567890', $pegawaiMalaysia->fresh()->no_telp);
    }

    private function createEmployee(
        string $nip,
        string $name,
        string $email,
        ?string $phone
    ): Pegawai {
        $pegawai = Pegawai::create([
            'nip' => $nip,
            'nama_pegawai' => $name,
            'jabatan' => 'Keuangan',
            'email' => $email,
            'alamat' => null,
            'is_verified' => true,
            'gender' => 'L',
            'no_telp' => $phone,
        ]);

        User::create([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_PEGAWAI,
        ]);

        return $pegawai;
    }
}

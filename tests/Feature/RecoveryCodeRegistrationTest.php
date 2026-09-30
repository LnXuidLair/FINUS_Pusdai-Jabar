<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RecoveryCodeRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_registration_displays_and_stores_recovery_code(): void
    {
        $recoveryCode = 'AdminRecovery!2026';

        $this->withSession($this->managementAccessSession('admin'))
            ->get(route('register.admin'))
            ->assertOk()
            ->assertSee('Recovery Code Admin')
            ->assertSee(route('register.admin.recovery-code.generate'), false);

        $this->withSession($this->managementAccessSession('admin'))
            ->post(route('register.admin.post'), [
                'nama_masjid' => 'Pusdai Jabar',
                'password' => 'StrongPass!123',
                'password_confirmation' => 'StrongPass!123',
                'recovery_code' => $recoveryCode,
            ])
            ->assertRedirect(route('login.admin'));

        $admin = User::query()->where('role', User::ROLE_ADMIN)->firstOrFail();

        $this->assertSame($recoveryCode, $admin->recovery_code);
        $this->assertNotSame($recoveryCode, $admin->getRawOriginal('recovery_code'));
    }

    public function test_admin_can_generate_a_recovery_code_during_registration(): void
    {
        $response = $this->withSession($this->managementAccessSession('admin'))
            ->postJson(route('register.admin.recovery-code.generate'))
            ->assertOk()
            ->assertJsonStructure(['recovery_code']);

        $code = (string) $response->json('recovery_code');

        $this->assertMatchesRegularExpression('/[a-z]/', $code);
        $this->assertMatchesRegularExpression('/[A-Z]/', $code);
        $this->assertMatchesRegularExpression('/[0-9]/', $code);
        $this->assertMatchesRegularExpression('/[^A-Za-z0-9]/', $code);
    }

    public function test_employee_receives_recovery_code_once_after_activation(): void
    {
        User::query()->create([
            'name' => 'Admin Pusdai',
            'email' => 'admin@pusdai.finus.id',
            'email_verified_at' => now(),
            'password' => Hash::make('StrongPass!123'),
            'recovery_code' => 'AdminRecovery!2026',
            'role' => User::ROLE_ADMIN,
        ]);

        $pegawai = Pegawai::query()->create([
            'nip' => 'PGW-2026-001',
            'nama_pegawai' => 'Budi Santoso',
            'jabatan' => 'Pegawai Umum',
            'email' => 'budisantoso6001@staffpusdai.finus.id',
            'is_verified' => false,
        ]);

        $recoveryCode = 'StaffRecovery!2026';
        $user = User::query()->create([
            'name' => $pegawai->nama_pegawai,
            'email' => $pegawai->email,
            'email_verified_at' => now(),
            'password' => Hash::make('TemporaryPass!123'),
            'recovery_code' => $recoveryCode,
            'role' => User::ROLE_PEGAWAI,
        ]);

        $this->withSession(array_merge(
            $this->managementAccessSession('staff'),
            [
                'staff_activation' => [
                    'pegawai_id' => $pegawai->getKey(),
                    'expires_at' => now()->addMinutes(10)->timestamp,
                ],
            ]
        ))->post(route('register.staff.post'), [
            'password' => 'NewStrongPass!123',
            'password_confirmation' => 'NewStrongPass!123',
        ])->assertRedirect(route('register.staff.success'));

        $pegawai->refresh();
        $user->refresh();

        $this->assertTrue($pegawai->is_verified);
        $this->assertTrue(Hash::check('NewStrongPass!123', $user->password));
        $this->assertSame($recoveryCode, $user->recovery_code);

        $this->get(route('register.staff.success'))
            ->assertOk()
            ->assertSee($pegawai->email)
            ->assertSee($recoveryCode);

        $this->get(route('register.staff.success'))
            ->assertRedirect(route('login.staff'));
    }

    private function managementAccessSession(string $portal): array
    {
        return ["management_access.{$portal}_verified_at" => now()->timestamp];
    }
}

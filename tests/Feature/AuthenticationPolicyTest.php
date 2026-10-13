<?php

namespace Tests\Feature;

use App\Mail\VerifyCodeJamaah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Bypass CSRF only in this feature test; production web routes retain
     * the normal VerifyCsrfToken middleware.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_jamaah_must_use_allowed_public_email_and_receive_otp(): void
    {
        Mail::fake();

        $response = $this->post(route('register.jamaah.post'), [
            'name' => 'Jamaah FINUS',
            'email' => 'jamaah@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('users', ['email' => 'jamaah@example.com']);

        $this->post(route('register.jamaah.post'), [
            'name' => 'Jamaah FINUS',
            'email' => 'jamaah@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('verification.notice'));

        $jamaah = User::where('email', 'jamaah@gmail.com')->firstOrFail();
        $this->assertSame(User::ROLE_JAMAAH, $jamaah->role);
        $this->assertFalse($jamaah->hasVerifiedEmail());
        $this->assertNotEmpty($jamaah->email_verification_code);

        Mail::assertSent(VerifyCodeJamaah::class, function (VerifyCodeJamaah $mail) use ($jamaah): bool {
            return $mail->hasTo($jamaah->email);
        });
    }

    public function test_registration_rejects_second_admin_in_current_one_admin_phase(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@finus.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->withSession([
            'management_access.admin_verified_at' => now()->timestamp,
        ])->post(route('register.admin.post'), [
            'nama_masjid' => 'Masjid Pengujian Kedua',
            'alamat_masjid' => 'Bandung',
            'password' => 'StrongPass!123',
            'password_confirmation' => 'StrongPass!123',
            'recovery_code' => User::generateRecoveryCode(),
        ]);
        $response->assertSessionHasErrors('nama_masjid');

        $this->assertSame(1, User::where('role', User::ROLE_ADMIN)->count());
    }

    public function test_unverified_jamaah_cannot_open_dashboard(): void
    {
        $jamaah = User::create([
            'name' => 'Jamaah',
            'email' => 'jamaah@gmail.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_JAMAAH,
        ]);

        $this->actingAs($jamaah, User::ROLE_JAMAAH)
            ->get(route('jamaah.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_admin_is_logged_out_after_fifteen_minutes_idle(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@finus.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin, User::ROLE_ADMIN)
            ->withSession([
                'last_activity_at.admin' => now()->subMinutes(16)->timestamp,
            ])
            ->get(route('dashboard'))
            ->assertRedirect(route('management.access'));

        $this->assertGuest(User::ROLE_ADMIN);
    }
}
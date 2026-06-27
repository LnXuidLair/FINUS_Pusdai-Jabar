<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_jamaah_must_register_with_gmail_and_verify_email(): void
    {
        Notification::fake();

        $this->post(route('register.jamaah.post'), [
            'name' => 'Jamaah FINUS',
            'email' => 'jamaah@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');

        $response = $this->post(route('register.jamaah.post'), [
            'name' => 'Jamaah FINUS',
            'email' => 'jamaah@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $jamaah = User::where('email', 'jamaah@gmail.com')->firstOrFail();

        $this->assertSame(User::ROLE_JAMAAH, $jamaah->role);
        $this->assertFalse($jamaah->hasVerifiedEmail());
        Notification::assertSentTo($jamaah, VerifyEmail::class);
    }

    public function test_only_one_admin_can_be_registered(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@finus.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->post(route('register.admin.post'), [
            'email' => 'admin2@finus.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');

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

        $this->actingAs($jamaah)
            ->get(route('jamaah.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_authenticated_user_is_logged_out_after_fifteen_minutes_idle(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@finus.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->withSession(['last_activity_at' => now()->subMinutes(16)->timestamp])
            ->get(route('dashboard'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}

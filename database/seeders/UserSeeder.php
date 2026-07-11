<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Pusdai',
                'email' => 'admin.pusdai@adminfinuspusdai.org',
                'password' => 'AdMnFINUS2026',
                'role' => 'admin',
            ],
        ];

        foreach ($users as $user) {
            $existingUser = DB::table('users')
                ->where('email', $user['email'])
                ->first();

            if ($existingUser) {
                DB::table('users')
                    ->where('email', $user['email'])
                    ->update([
                        'name' => $user['name'],
                        'password' => Hash::make($user['password']),
                        'role' => $user['role'],
                        'email_verified_at' => now(),
                        'email_verification_code' => null,
                        'email_verification_code_expires_at' => null,
                        'password_changed_at' => null,
                        'password_changed' => false,
                        'remember_token' => null,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('users')->insert([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'email_verified_at' => now(),
                    'email_verification_code' => null,
                    'email_verification_code_expires_at' => null,
                    'password' => Hash::make($user['password']),
                    'password_changed_at' => null,
                    'role' => $user['role'],
                    'password_changed' => false,
                    'remember_token' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
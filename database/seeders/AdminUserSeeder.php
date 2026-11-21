<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Creates the first admin from ADMIN_EMAIL / ADMIN_PASSWORD in .env.
 * No credentials are stored in source code. If ADMIN_PASSWORD is empty,
 * a random one is generated and printed once in the terminal.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');

        if (! $email) {
            $this->command->warn('ADMIN_EMAIL is not set in .env - admin account was NOT created.');

            return;
        }

        $existing = User::where('email', $email)->first();

        if ($existing) {
            $existing->forceFill(['is_admin' => true])->save();
            $this->command->info("Admin {$email} already exists (password unchanged).");

            return;
        }

        $password = env('ADMIN_PASSWORD');
        $generated = false;

        if (! $password) {
            $password = Str::password(16);
            $generated = true;
        }

        User::forceCreate([
            'name' => env('ADMIN_NAME', 'Administrator'),
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info("Admin account created for {$email}.");

        if ($generated) {
            $this->command->warn("Generated password (shown once, save it now): {$password}");
        }
    }
}

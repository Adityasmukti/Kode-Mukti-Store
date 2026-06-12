<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = getenv('ADMIN_EMAIL');
        $password = getenv('ADMIN_PASSWORD');

        if (empty($email) || empty($password)) {
            fwrite(STDERR, "WARNING: ADMIN_EMAIL and ADMIN_PASSWORD env vars must be set to seed admin user. Skipping.\n");
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);
    }
}

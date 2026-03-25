<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $username = env('SUPER_ADMIN_USERNAME');
        $password = env('SUPER_ADMIN_PASSWORD');

        if (empty($username) || empty($password)) {
            $this->command?->warn('SUPER_ADMIN_USERNAME or SUPER_ADMIN_PASSWORD is not set. Skipping Super Admin seed.');
            return;
        }

        User::updateOrCreate(
            ['username' => $username],
            [
                'password'    => Hash::make($password),
                'role'        => User::ROLE_SUPER_ADMIN,
                'chapter_id'  => null,
                'first_login' => true,
                'status'      => 'active',
            ]
        );
    }
}

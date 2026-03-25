<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetSuperAdminPassword extends Command
{
    protected $signature = 'superadmin:reset-password
                            {username? : Super Admin username}
                            {--password= : New password (optional, will prompt if missing)}';

    protected $description = 'Reset Super Admin password and force change on next login.';

    public function handle(): int
    {
        $username = $this->argument('username')
            ?? env('SUPER_ADMIN_USERNAME')
            ?? $this->ask('Super Admin username');

        $password = $this->option('password')
            ?? $this->secret('New password');

        if (! $username || ! $password) {
            $this->error('Username and password are required.');
            return self::FAILURE;
        }

        $user = User::where('username', $username)->first();

        if (! $user) {
            $this->error("User not found: {$username}");
            return self::FAILURE;
        }

        if ($user->role !== User::ROLE_SUPER_ADMIN) {
            $this->error('Target user is not a Super Admin.');
            return self::FAILURE;
        }

        $user->update([
            'password'    => Hash::make($password),
            'first_login' => true,
        ]);

        $this->info('Super Admin password updated. User must change password on next login.');
        return self::SUCCESS;
    }
}

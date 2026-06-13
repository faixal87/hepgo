<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@hep.test'],
            [
                'name' => 'Pentadbir Sistem',
                'password' => 'password',
                'status' => UserStatus::ACTIVE,
            ],
        );

        $admin->assignRole('super_admin');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@atlticket.exchange'],
            [
                'name' => 'Admin',
                'email' => 'admin@atlticket.exchange',
                'password' => Hash::make('changeme123'),
                'username' => 'admin',
                'is_admin' => true,
                'is_super_admin' => true,
                'probation' => false,
                'email_verified_at' => now(),
            ]
        );
    }
}

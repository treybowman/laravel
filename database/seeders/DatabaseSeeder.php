<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            VenueSeeder::class,
            CreditPackageSeeder::class,
            PageSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}

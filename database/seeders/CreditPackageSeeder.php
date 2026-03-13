<?php

namespace Database\Seeders;

use App\Models\CreditPackage;
use Illuminate\Database\Seeder;

class CreditPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter',
                'credits' => 3,
                'price_cents' => 300,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Value Pack',
                'credits' => 6,
                'price_cents' => 500,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro Pack',
                'credits' => 12,
                'price_cents' => 900,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $package) {
            CreditPackage::updateOrCreate(['name' => $package['name']], $package);
        }
    }
}

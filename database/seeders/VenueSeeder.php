<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            [
                'name' => 'Truist Park',
                'slug' => 'truist-park',
                'address' => '1 Ballpark Pkwy, Cumberland, GA 30339',
                'sort_order' => 1,
            ],
            [
                'name' => 'State Farm Arena',
                'slug' => 'state-farm-arena',
                'address' => '1 State Farm Drive, Atlanta, GA 30303',
                'sort_order' => 2,
            ],
            [
                'name' => 'Mercedes-Benz Stadium',
                'slug' => 'mercedes-benz-stadium',
                'address' => '1 AMB Drive NW, Atlanta, GA 30313',
                'sort_order' => 3,
            ],
            [
                'name' => 'Fox Theatre',
                'slug' => 'fox-theatre',
                'address' => '660 Peachtree St NE, Atlanta, GA 30308',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gas South Arena',
                'slug' => 'gas-south-arena',
                'address' => '6400 Sugarloaf Pkwy, Duluth, GA 30097',
                'sort_order' => 5,
            ],
            [
                'name' => 'Ameris Bank Amphitheatre',
                'slug' => 'ameris-bank-amphitheatre',
                'address' => '2200 Encore Pkwy, Alpharetta, GA 30009',
                'sort_order' => 6,
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'address' => 'Atlanta, GA',
                'sort_order' => 99,
            ],
        ];

        foreach ($venues as $venue) {
            Venue::updateOrCreate(['slug' => $venue['slug']], $venue);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Deal;
use Illuminate\Database\Seeder;

class DealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($deal = 1; $deal <= 50; $deal++) {
            Deal::insert([
                'name' => 'Weekly Deal',
                'discount_percentage' => rand(20,50),
                'start_date' => '2022-02-01',
                'end_date' => '2022-02-15',
                'image_url' => '/storage/dealImages/',
                'is_published' => rand(0,1),
                'original_price' => rand(15, 50),
                'discounted_price' => rand(10, 40),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}

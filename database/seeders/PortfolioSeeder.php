<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($user = 1; $user <= 100;) {
            for ($portfolio = 1; $portfolio <= 4; $portfolio++) {
                Portfolio::insert([
                    'artist_id' => $user + 100,
                    'title' => 'Sample Service',
                    'image_url' => 'storage/portfolio/download-0' . rand(1,4) . '.jpg',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $user++;
        }
    }
}

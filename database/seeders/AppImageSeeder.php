<?php

namespace Database\Seeders;

use App\Models\AppImage;
use Illuminate\Database\Seeder;

class AppImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($image = 1; $image <= 4; $image++) {
            AppImage::insert([
                'carousal_images' => 'storage/carousals/image_0' . $image,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}

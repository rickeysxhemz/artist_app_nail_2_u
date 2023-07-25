<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($artist = 101; $artist <= 200;) {
            for ($service = 1; $service <= 5; $service++) {
                Service::insert([
                    'artist_id' => $artist,
                    'name' => $this->getRandomService(),
                    'price' => rand(100, 500),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $artist++;
        }
    }

    // private function getArtistId()
    // {
    //     $artist_id = DB::table('model_has_roles')
    //         ->where('role_id', 3)
    //         ->inRandomOrder()
    //         ->first();

    //     return $artist_id->model_id;
    // }

    private function getRandomService()
    {
        $services = [
            'Makeup for Film/TV',
            'Bridal Beauty',
            'Makeup for Red Carpet',
            'Speaking Engagement',
            'Makeup for Professional Photo Shoot',
            'Runway',
            'Beauty Consultation',
            'Hair Cut',
            'Nail Cleaning',
            'Wexing'
        ];

        return $services[array_rand($services)];
    }
}

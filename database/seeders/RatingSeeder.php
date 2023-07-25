<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($user = 1; $user <= 200; ) {
            for ($rating = 1; $rating <= 10; $rating++) {
                Rating::insert([
                    'artist_id' => rand(101, 200),
                    'client_id' => rand(1, 100),
                    'rating' => rand(1, 5),
                    'review' => $faker->sentence(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $user++;
        }
    }

    // private function getArtistId()
    // {
    //     $artist_id = UserRole::where('role_id', 2)
    //         ->inRandomOrder()
    //         ->first('user_id');

    //     return $artist_id->user_id;
    // }

    // private function getClientId()
    // {
    //     $artist_id = DB::table('model_has_roles')
    //         ->where('role_id', 3)
    //         ->inRandomOrder()
    //         ->first();

    //     return $artist_id->model_id;
    // }
}

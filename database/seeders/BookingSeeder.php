<?php

namespace Database\Seeders;

use App\Models\Booking;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            'new',
            'in-process',
            'done',
            'pending',
            'cancel',
            'assign_to_other'
        ];

        for ($status = 0; $status <= 5;) {
            for ($artist = 101; $artist <= 200;) {
                for ($service = 1; $service <= 5; $service++) {
                    Booking::insert([
                        'artist_id' => $artist,
                        'client_id' => rand(4, 100),
                        'started_at' => 1,
                        'ended_at' => now(),
                        'status' => $statuses[$status],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                $artist++;
            }
            $status++;
        }
    }

    public function getDate()
    {
        $period = CarbonPeriod::create("2020-5-20", "2020-5-30");
        foreach ($period as $date) {
            // Insert Dates into listOfDates Array
            $listOfDates[] = $date->format('Y-m-d H:i:s');
        }

        // Now You Can Review This Array
        dd($listOfDates);
    }

    // private function getRandomStatus()
    // {
    //     $statuses = [
    //         'new',
    //         'in-process',
    //         'done',
    //         'pending',
    //         'cancel',
    //         'assign_to_other'
    //     ];

    //     return $statuses[array_rand($statuses)];
    // }
}

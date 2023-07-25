<?php

namespace Database\Seeders;

use App\Models\Scheduler;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('schedulers')->insert(
            [
                ['name' => 'time', 'time' => '10:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '10:30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '11:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '11:30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '12:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '12:30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '01:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '02:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '02:30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '03:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '03:30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '04:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '04:30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '05:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '05:30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '06:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '06:30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '07:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '07:30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '08:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '08:30', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
                ['name' => 'time', 'time' => '09:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ]
        );
    }
}

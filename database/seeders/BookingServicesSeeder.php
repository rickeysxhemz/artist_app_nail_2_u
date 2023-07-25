<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services_count = Service::count();
        $bookings_count = Booking::count();

        for ($service = 1; $service <= $services_count;) {
            for ($booking = 1; $booking <= $bookings_count;) {
                DB::table('booking_services')->insert([
                    'service_id' => $service,
                    'booking_id' => $booking,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $booking++;
            }
            $service++;
        }
    }
}

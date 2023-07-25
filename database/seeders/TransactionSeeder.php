<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Transaction;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($transaction = 1; $transaction <= 200; $transaction++) {
            Transaction::insert([
                'sender_id' => $this->getClientId(),
                'receiver_id' => 1,
                'payment_method_id' => rand(1, 2),
                'amount' => rand(100, 500),
                'transaction_status' => rand(0, 2),
                'booking_id' => $this->getBookingId(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function getClientId()
    {
        $user_id = DB::table('model_has_roles')
            ->where('role_id', 2)
            ->inRandomOrder()
            ->first();

        return $user_id->model_id;
    }

    private function getBookingId()
    {
        $bookings = Booking::inRandomOrder()
            ->first('id');
        return $bookings->id;
    }
}

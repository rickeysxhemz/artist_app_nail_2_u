<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($rating = 1; $rating <= 300; $rating++) {
            Setting::insert([
                'user_id' => $rating,
                'private_account' => 0,
                'secure_payment' => 1,
                'sync_contact_no' => 0,
                'app_notification' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}

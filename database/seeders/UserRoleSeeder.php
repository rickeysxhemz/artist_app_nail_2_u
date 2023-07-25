<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_1 = User::find(205);
        $admin_2 = User::find(206);

        $admin_role = Role::findByName('admin');
        $admin_role->users()->attach($admin_1);
        $admin_role->users()->attach($admin_2);


        //seed users
        for ($i = 1; $i <= 100; $i++) {
            DB::table('model_has_roles')->insert(
                ['role_id' => 2, 'model_type' => 'App\Models\User', 'model_id' => $i]
            );
        }

        //seed artist
        for ($i = 101; $i <= 200; $i++) {
            DB::table('model_has_roles')->insert(
                ['role_id' => 3, 'model_type' => 'App\Models\User', 'model_id' => $i]
            );
        }
    }
}

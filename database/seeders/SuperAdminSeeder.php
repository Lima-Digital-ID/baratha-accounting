<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = new \App\Models\User;

        $superAdmin->name = 'Super Admin';
        $superAdmin->email = 'superadmin@baratha.com';
        $superAdmin->password = \Hash::make('superadmin');
        $superAdmin->akses = 'Super Admin';

        $superAdmin->save();
    }
}

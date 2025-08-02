<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {   
        \Artisan::call('db:wipe');
        \Artisan::call('migrate');

        $this->call(SuperAdminSeeder::class);
        $this->call(DebugAdminSeeder::class);
        $this->call(MembershipSeeder::class);
        $this->call(LandingSectionSeeder::class);
        $this->call(SettingSeeder::class);
    }

}

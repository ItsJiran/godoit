<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting; // Pastikan mengimpor model User

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'slug' => 'free_member_comission_percentage',
            'value' => 10, 
        ]);

        Setting::create([
            'slug' => 'premium_member_comission_percentage',
            'value' => 20, 
        ]);

        Setting::create([
            'slug' => 'premium_downline',
            'value' => 2, 
        ]);
    }
}

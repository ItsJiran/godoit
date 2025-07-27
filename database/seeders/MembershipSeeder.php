<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str; 

use App\Models\Membership; 
use App\Models\Product; 

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lifetimeMembershipName = 'Premium Lifetime Membership';
        $lifetimeMembershipSlug = Str::slug($lifetimeMembershipName);

        // Check if a 'Premium Lifetime' membership already exists to prevent duplicates
        if (!Membership::where('slug', $lifetimeMembershipSlug)->exists()) {
            Membership::create([
                'name' => $lifetimeMembershipName,
                'slug' => $lifetimeMembershipSlug,
                'duration_type' => 'lifetime', // Set duration type to lifetime
                'duration_unit' => 'lifetime', // Set duration unit to lifetime
                'duration_value' => 0,          // Set value to 0 for indefinite duration
            ]);

            $this->command->info($lifetimeMembershipName . ' created successfully!');
        } else {
            $this->command->warn($lifetimeMembershipName . ' already exists. Skipping.');
        }

        // Get the new membership and create it
        if (Membership::where('slug', $lifetimeMembershipSlug)->exists()) {
            
        } else {
            $this->command->warn($lifetimeMembershipName . ' not exists for creating membership product. Skipping.');
        }
    }
}

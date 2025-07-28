<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str; 

use App\Enums\Product\ProductStatus;

use App\Services\Acquisition\UserAcquisitionService;
use App\Models\UserAcquisition; 

use App\Models\User;
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

        $premium_membership = Membership::where('slug', $lifetimeMembershipSlug)->first();

        // Get the new membership and create it
        if (Membership::where('slug', $lifetimeMembershipSlug)->exists() && !Product::where([
            ['productable_id','=', $premium_membership->id],
            ['productable_type','=', $premium_membership::class]
        ])->exists()) {

            $product = Product::create([
                'creator_id' => 1, // defualt membership product is superadmin..
                'productable_id' => $premium_membership->id,   // from morphs('productable')
                'productable_type' => $premium_membership::class, // from morphs('productable')

                'slug' => Product::determineNextSequenceSlug($premium_membership::class, Product::determineNextSequenceNumber($premium_membership::class)),
                'sequence_number' => Product::determineNextSequenceNumber($premium_membership::class), // Add sequence_number to fillable
                'title' => 'Premium Membership',
                'description' => 'Akses Premium Membership',
                'price' => 100000,
                'currency' => 'IDR',
                'status' => ProductStatus::PUBLISHED,
                'published_at' => now(),
            ]);

            if ( !UserAcquisition::userHasActiveProductAcquisition(1,$product->id) ){
                UserAcquisitionService::grantAcquisition(
                    User::where('id',1)->first(),
                    $product,
                    null,
                    null,
                    'Auto Granted By System..'
                );
            };

            if ( !UserAcquisition::userHasActiveProductAcquisition(2,$product->id) ){
                UserAcquisitionService::grantAcquisition(
                    User::where('id',2)->first(),
                    $product,
                    null,
                    null,
                    'Auto Granted By System..'
                );
            };
        } else {
            $this->command->warn($lifetimeMembershipName . ' not exists for creating membership product. Skipping.');
        }

        // Grant acquistion for the premium_membersip product for the current superadmin
    }
}

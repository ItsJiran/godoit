<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;

use App\Models\User;
use App\Models\Membership;
use App\Models\Product;
use App\Enums\Product\ProductType;

class PremiumMembershipController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $premium_membership_product = Product::where('productable_type',Membership::class)->first();

        if($user && $user->activeMembershipPremium()) return view('welcome');
        
        // Pass products and the current user's ID to the view
        return view('product.index', [
            'product' => $premium_membership_product,
            'currentUserId' => $user ? $user->id : null, // Pass user ID for comparison in view
            'productEventFinished' => false,
        ]);
    }



}
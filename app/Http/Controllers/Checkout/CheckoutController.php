<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CheckoutController extends Controller
{
    
    public function showCheckoutForm()
    {
        return view('dashboard.demo.checkout');
    }

}

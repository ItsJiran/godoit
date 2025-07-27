<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // Make sure this is present

class Controller extends BaseController // This is crucial
{
    use AuthorizesRequests, ValidatesRequests;
}
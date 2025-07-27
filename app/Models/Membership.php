<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membership extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Define the prefix for the sequential code.
     * @var string
     */
    public static $slugPrefix = 'M-'; // <-- Define the prefix here
}

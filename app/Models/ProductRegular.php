<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ProductRegular extends Model
{
    use SoftDeletes;

    protected $table = 'products_regular';

    /**
     * Define the prefix for the sequential code.
     * @var string
     */
    public static $slugPrefix = 'R-'; // <-- Define the prefix here

    protected $fillable = [
        'timestamp',
    ];


    protected $casts = [
        'timestamp' => 'datetime',
        // 'productable_type' => ProductType::class, // Tambahkan casting ini
    ];
}

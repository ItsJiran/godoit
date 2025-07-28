<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketingKit extends Model
{
    use HasFactory;
    protected $fillable = ['judul', 'gambar', 'konten'];
}

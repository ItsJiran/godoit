<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class UserBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'atas_nama',
        'nama_bank',
        'no_rek'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
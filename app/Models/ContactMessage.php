<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['judul', 'nama', 'email', 'whatsapp', 'pesan'];
}

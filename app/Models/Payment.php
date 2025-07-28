<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments'; // pastikan ini sesuai dengan nama tabel di database

    protected $fillable = [
        'user_id',
        'order_id',
        'id_customer',
        'id_order',
        'snap_token',
        'transaction_details',
        'customer_details',
        'product_details',
        'status',
        'payment_type',
    ];

    protected $casts = [
        'transaction_details' => 'array',
        'customer_details' => 'array',
        'product_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship jika ingin menghubungkan dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}

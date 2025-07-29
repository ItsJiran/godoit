<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Import Str facade for string manipulation

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'slug',
        'total_price',
        'status', // Assuming 'status' is also fillable as it's set in the controller
        // Add other fillable attributes as per your table schema
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'status' => OrderStatus::class, // If you have an OrderStatus Enum and want to cast it
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate a unique slug for the order.
     * This method can be called before saving a new order.
     *
     * @return string The generated unique slug.
     */
    public static function generateUniqueSlug(): string
    {
        do {
            // Generate a slug based on current timestamp and a random string
            $slug = Str::random(8) . '-' . now()->timestamp;
        } while (self::where('slug', $slug)->exists()); // Ensure uniqueness

        return $slug;
    }

    /**
     * Boot method to hook into model events.
     * Automatically generates a slug before creating a new order.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->slug)) {
                $order->slug = self::generateUniqueSlug();
            }
        });
    }
}
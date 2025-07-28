<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

use App\Enums\Product\ProductType; 
use App\Enums\Product\ProductStatus; 

class Product extends Model
{
    use HasFactory, SoftDeletes; // Use the SoftDeletes trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'creator_id',
        'productable_id',   // from morphs('productable')
        'productable_type', // from morphs('productable')
        'sequence_number', // Add sequence_number to fillable
        'title',
        'slug',
        'description',
        'price',
        'currency',
        'status',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2', // Cast price to decimal with 2 decimal places
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime', // Cast deleted_at to datetime
        'status' => ProductStatus::class,
        // 'productable_type' => ProductType::class, // Tambahkan casting ini
    ];

    /**
     * The number of digits for the sequential part of the slug.
     * This defines the padding for NEWLY generated slugs.
     * @var int
     */
    protected static int $slugCodeDigits = 4;

    /**
     * The "booting" method of the model.
     * This is where you register model event listeners.
     */
    protected static function boot(): void
    {
        parent::boot(); // Always call the parent boot method first
    }

    /**
     * Determines the next sequential number for a given productable type.
     * This method is the source of truth for the sequence, using the dedicated column.
     *
     * @param string $productableTypeClass The fully qualified class name of the productable model (e.g., 'App\Models\Membership').
     * @return int The next sequential number.
     * @throws \RuntimeException If the productable class does not define a static $slugPrefix (though check is redundant here).
     */
    public static function determineNextSequenceNumber(string $productableTypeClass): int
    {
        // Query for the last record of this productable_type based on the sequence_number
        $lastRecord = static::query()
            ->where('productable_type', $productableTypeClass)
            ->orderByDesc('sequence_number') // Order by the actual sequence_number field
            ->first();

        // If a last record exists, increment its sequence_number; otherwise, start from 1
        return ($lastRecord ? $lastRecord->sequence_number : 0) + 1;
    }

     /**
     * Determines the next sequential number for a given productable type.
     * This method is the source of truth for the sequence, using the dedicated column.
     *
     * @param string $productableTypeClass The fully qualified class name of the productable model (e.g., 'App\Models\Membership').
     * @return int The next sequential number.
     * @throws \RuntimeException If the productable class does not define a static $slugPrefix (though check is redundant here).
     */
    public static function determineNextSequenceSlug($productableClass, $nextSequenceNumber)
    {
        $paddedNumber = str_pad($nextSequenceNumber, static::$slugCodeDigits, '0', STR_PAD_LEFT);
        return $productableClass::$slugPrefix . $paddedNumber;
    }


    /**
     * Get the user (creator/publisher) that owns the product.
     */
    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the productable model (e.g., Membership, Course, DigitalDownload).
     */
    public function productable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    // Example of a scope to get only published products
    public function scopePublished($query)
    {
        return $query->where('status', ProductStatus::Published)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

        /**
     * Create a new Product record from the provided validated data.
     *
     * @param array $validatedData The validated data from the Form Request.
     * @param \App\Models\User $user The authenticated user creating this product.
     * @param \Illuminate\Database\Eloquent\Model|null $productable The related productable model instance.
     * @return static Returns the newly created Product model instance.
     *
     * @throws \InvalidArgumentException If the productable model type is invalid or missing slugPrefix.
     */
    public static function storeRecord(array $validatedData, User $user, $productable = null): static
    {
        // Data yang sudah divalidasi dari Form Request
        $data = $validatedData;

        // Tambahkan user_id (creator_id jika itu nama kolomnya)
        $data['creator_id'] = $user->id; 
        $data['productable_id'] = $productable->id;
        $data['productable_type'] = $productable::class;
        $data['sequence_number'] = self::determineNextSequenceNumber($productable::class);
        $data['slug'] = self::determineNextSequenceSlug($productable::class,$data['sequence_number']);
        
        // Buat produk
        return self::create($data);
    }


    /**
     * Update an existing Product record based on the provided validated data.
     *
     * @param array $validatedData The validated data from the Form Request.
     * @param \App\Models\Product $product The product instance to update.
     * @param \Illuminate\Database\Eloquent\Model|null $productable The related productable model instance if it needs to be updated.
     * @return static Returns the updated Product model instance.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the product with the given ID is not found.
     */
    public static function updateRecord(array $validatedData, Product $product, $productable = null): static
    {
        // Data yang sudah divalidasi dari Form Request
        $data = $validatedData;

        // Jika $productable diteruskan, update productable_id dan productable_type
        if (!is_null($productable)) {
            $data['productable_id'] = $productable->id;
            $data['productable_type'] = $productable::class;
        }

        // Perbarui produk
        $product->update($data);
        return $product;
    }

}
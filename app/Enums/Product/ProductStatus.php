<?php

namespace App\Enums\Product;

use App\Traits\HasValues; // Assuming you have this trait for easy value retrieval

/**
 * @method static string DRAFT()
 * @method static string PUBLISHED()
 * @method static string ARCHIVED()
 * @method static string PENDING_REVIEW()
 * @method static string REJECTED()
 * @method static string OUT_OF_STOCK()
 * @method static string DISCONTINUED()
 */
enum ProductStatus: string
{
    use HasValues; // This trait (if you have it) would provide methods like ProductStatus::values()

    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    /**
     * Get a human-readable label for the status.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    /**
     * Check if the status indicates the product is public/visible.
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this === self::Published;
    }

    /**
     * Check if the status indicates the product is in a draft state.
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this === self::Draft;
    }

    // /**
    //  * Check if the status indicates the product is no longer available for sale.
    //  *
    //  * @return bool
    //  */
    // public function isUnavailable(): bool
    // {
    //     return in_array($this, [self::Archived, self::OutofStock, self::Discontinued, self::Rejected]);
    // }
}
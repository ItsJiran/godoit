<?php

namespace App\Enums\Order;

use App\Traits\HasValues; // Assuming you have this trait for easy value retrieval

/**
 * @method static string PENDING()
 * @method static string COMPLETED()
 * @method static string CANCELLED()
 * @method static string REFUNDED()
 * @method static string FAILED()
 */
enum OrderStatus: string
{
    use HasValues; // This trait (if you have it) would provide methods like OrderStatus::values()

    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';

    /**
     * Get a human-readable label for the order status.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::FAILED => 'Failed',
        };
    }

    /**
     * Check if the order status indicates a completed state.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    /**
     * Check if the order status indicates a pending state.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if the order status indicates a failed or cancelled state.
     *
     * @return bool
     */
    public function isUnsuccessful(): bool
    {
        return in_array($this, [self::CANCELLED, self::FAILED]);
    }
}
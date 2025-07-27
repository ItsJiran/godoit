<?php

namespace App\Enums\Product;

use App\Traits\BasicEnumTrait; 

// models product type
use App\Models\Membership;

enum ProductType: string
{
    use BasicEnumTrait;

    case MEMBERSHIP = 'membership'; // For premium account upgrades or access levels

    /**
     * Get a human-readable label for the product type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::MEMBERSHIP => 'Membership / Premium Access',
        };
    }

    public function model()
    {
        return match ($this) {
            self::MEMBERSHIP => Membership::class,
        };
    }

        /**
     * Get an associative array of enum values mapped to their labels for select options.
     *
     * @return array<string, string>
     */
    public static function toSelectArray(): array
    {
        return collect(self::cases())->mapWithKeys(function (self $type) {
            return [$type->value => $type->label()];
        })->toArray();
    }
}
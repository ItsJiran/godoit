<?php

namespace App\Traits;

use BackedEnum; // Import BackedEnum for type hinting if your enum is backed

trait BasicEnumTrait
{
    /**
     * Compares the current enum case with another GatewayProvider enum case or a string value.
     *
     * @param self|string $other The enum case or string value to compare against.
     * @return bool True if the values are equal, false otherwise.
     */
    public function equals(self|string $other): bool
    {
        // If the other value is an instance of the same enum, compare instances directly.
        // Otherwise, compare the backed value of the current enum with the string.
        return $other instanceof self
            ? $this === $other
            : ($this instanceof BackedEnum ? $this->value === $other : $this->name === $other);
    }

    /**
     * Normalizes a mixed value into an enum instance.
     *
     * If the value is already an enum instance, it's returned as is.
     * Otherwise, it attempts to create an enum instance from the value using tryFrom().
     *
     * @param mixed $value The value to normalize.
     * @return self|null The enum instance if successful, or null if conversion fails.
     */
    public static function normalize(mixed $value): ?self
    {
        // If the value is already an instance of this enum, return it directly.
        if ($value instanceof self) {
            return $value;
        }

        // If the enum is a BackedEnum, try to create an instance from its value.
        // If it's a Pure Enum, try to create from its name.
        // tryFrom() is suitable as it returns null on failure.
        if (self::isBackedEnum()) {
            return self::tryFrom($value);
        } else {
            // For Pure Enums, try to find by name
            foreach (self::cases() as $case) {
                if ($case->name === $value) {
                    return $case;
                }
            }
            return null;
        }
    }

    /**
     * Helper to determine if the enum is a BackedEnum.
     *
     * @return bool
     */
    private static function isBackedEnum(): bool
    {
        // Check if the enum implements the BackedEnum interface
        return is_subclass_of(self::class, BackedEnum::class);
    }
}

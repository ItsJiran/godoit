<?php

namespace App\Traits;

trait HasValues
{
    /**
     * Get an array of all the enum case values.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get an array of all the enum case names.
     *
     * @return array
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get an associative array of enum names mapped to their values.
     *
     * @return array
     */
    public static function toArray(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->name] = $case->value;
            return $carry;
        }, []);
    }
}
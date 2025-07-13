<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * Trait providing common enum helper methods.
 *
 * This trait provides implementations for values() and labels() methods,
 * while requiring the implementing enum to define the label() method.
 */
trait HasEnumHelpers
{
    /**
     * Get all enum values as an array.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all enum labels as an array.
     *
     * @return array<int, string>
     */
    public static function labels(): array
    {
        return array_map(fn ($case): string => $case->label(), self::cases());
    }

    /**
     * Get the human-readable label for this enum case.
     *
     * Default implementation converts the case name to title case.
     * Override this method in your enum for custom labels.
     */
    public function label(): string
    {
        return ucwords(str_replace('_', ' ', mb_strtolower($this->name)));
    }
}

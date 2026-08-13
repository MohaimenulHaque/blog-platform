<?php

namespace App\Enums;

enum CategoryStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    /**
     * Human readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
        };
    }

    /**
     * Badge variant used across the admin UI.
     */
    public function badge(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'neutral',
        };
    }

    /**
     * The available statuses for the admin UI.
     *
     * @return array<int, self>
     */
    public static function options(): array
    {
        return [
            self::Active,
            self::Inactive,
        ];
    }
}

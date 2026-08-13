<?php

namespace App\Enums;

enum PostVisibility: string
{
    case Public = 'public';
    case Private = 'private';

    /**
     * Human readable label for the visibility.
     */
    public function label(): string
    {
        return match ($this) {
            self::Public => 'Public',
            self::Private => 'Private',
        };
    }

    /**
     * Badge variant used across the admin UI.
     */
    public function badge(): string
    {
        return match ($this) {
            self::Public => 'success',
            self::Private => 'neutral',
        };
    }

    /**
     * The available visibilities for the admin UI.
     *
     * @return array<int, self>
     */
    public static function options(): array
    {
        return [
            self::Public,
            self::Private,
        ];
    }
}

<?php

namespace App\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Published = 'published';
    case Scheduled = 'scheduled';
    case Archived = 'archived';

    /**
     * Human readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Published => 'Published',
            self::Scheduled => 'Scheduled',
            self::Archived => 'Archived',
        };
    }

    /**
     * Badge variant used across the admin UI.
     */
    public function badge(): string
    {
        return match ($this) {
            self::Draft => 'neutral',
            self::Pending => 'warning',
            self::Published => 'success',
            self::Scheduled => 'info',
            self::Archived => 'outline',
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
            self::Draft,
            self::Pending,
            self::Published,
            self::Scheduled,
            self::Archived,
        ];
    }
}

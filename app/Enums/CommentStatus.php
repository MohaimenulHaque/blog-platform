<?php

namespace App\Enums;

enum CommentStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Spam = 'spam';

    /**
     * Human readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Spam => 'Spam',
        };
    }

    /**
     * Badge variant used across the UI.
     */
    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Spam => 'neutral',
        };
    }

    /**
     * The statuses that are publicly visible.
     *
     * @return array<int, self>
     */
    public static function visible(): array
    {
        return [
            self::Approved,
        ];
    }

    /**
     * The available statuses for the moderation UI.
     *
     * @return array<int, self>
     */
    public static function options(): array
    {
        return [
            self::Pending,
            self::Approved,
            self::Rejected,
            self::Spam,
        ];
    }
}

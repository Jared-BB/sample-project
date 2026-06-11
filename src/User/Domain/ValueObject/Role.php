<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

enum Role: string
{
    case ADMIN = 'ADMIN';
    case AGENT = 'AGENT';

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}

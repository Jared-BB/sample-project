<?php

namespace App\Access\Domain\GroupPermission\ValueObject;

enum Context: string
{
    case USER = 'USER';
    case GROUP = 'GROUP';

    public const array VALUES = [
        'USER',
        'GROUP',
    ];

    public static function values(): array
    {
        return self::VALUES;
    }
}

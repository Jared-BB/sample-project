<?php

namespace App\Access\Domain\GroupPermission\ValueObject;

enum Permission: string
{
    case MANAGE = 'MANAGE';
    case CREATE = 'CREATE';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
    case LIST = 'LIST';
    case READ = 'READ';

    public const array VALUES = [
        'MANAGE',
        'CREATE',
        'UPDATE',
        'DELETE',
        'LIST',
        'READ',
    ];

    public static function values(): array
    {
        return self::VALUES;
    }
}

<?php

declare(strict_types=1);

namespace App\Access\Domain\Exception;

use App\Shared\Domain\Exception\ConflictException;

final class MissingContextException extends ConflictException
{
    public static function create(): self
    {
        return new self('Permission Context its required');
    }

    public function errorCode(): string
    {
        return 'MISSING_CONTEXT';
    }
}

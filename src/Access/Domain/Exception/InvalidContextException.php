<?php

declare(strict_types=1);

namespace App\Access\Domain\Exception;

use App\Shared\Domain\Exception\ConflictException;

final class InvalidContextException extends ConflictException
{
    public static function create(): self
    {
        return new self('The Permission Context its invalid');
    }

    public function errorCode(): string
    {
        return 'INVALID_CONTEXT_ERROR';
    }
}

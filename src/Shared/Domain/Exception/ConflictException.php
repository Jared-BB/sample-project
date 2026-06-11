<?php

namespace App\Shared\Domain\Exception;

use RuntimeException;

abstract class ConflictException extends RuntimeException implements ApiExceptionInterface
{
    public function statusCode(): int
    {
        return 409;
    }
}

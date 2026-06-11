<?php

namespace App\Shared\Domain\Exception;

use RuntimeException;

abstract class BadRequestException extends RuntimeException implements ApiExceptionInterface
{
    public function statusCode(): int
    {
        return 400;
    }
}

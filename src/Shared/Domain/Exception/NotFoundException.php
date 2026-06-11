<?php

namespace App\Shared\Domain\Exception;

use RuntimeException;

abstract class NotFoundException extends RuntimeException implements ApiExceptionInterface
{
    public function statusCode(): int
    {
        return 404;
    }
}

<?php

namespace App\Shared\Domain\Exception;

interface ApiExceptionInterface
{
    public function errorCode(): string;

    public function statusCode(): int;
}

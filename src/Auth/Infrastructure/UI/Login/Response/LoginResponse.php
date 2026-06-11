<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\UI\Login\Response;

final readonly class LoginResponse
{
    public function __construct(
        public string $token,
    ) {
    }
}

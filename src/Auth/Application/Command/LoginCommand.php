<?php

declare(strict_types=1);

namespace App\Auth\Application\Command;

use App\User\Domain\ValueObject\Email;

final readonly class LoginCommand
{
    public function __construct(
        public Email $email,
        public string $password,
    ) {
    }
}

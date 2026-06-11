<?php

declare(strict_types=1);

namespace App\Auth\Application\Command;

use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;

final readonly class LoginCommand
{
    public function __construct(
        public Email $email,
        public Password $password,
    ) {
    }
}

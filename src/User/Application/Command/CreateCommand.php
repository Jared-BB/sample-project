<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Role;

final readonly class CreateCommand
{
    public function __construct(
        public Email $email,
        public Role $role,
    ) {
    }
}

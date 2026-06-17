<?php

namespace App\User\Application\Command;

use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use App\User\Domain\ValueObject\Role;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateCommand
{
    public function __construct(
        public Uuid $userId,
        public ?Email $email,
        public ?Password $password,
        public ?Role $role,
    ) {
    }
}

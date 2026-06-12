<?php

namespace App\Access\Application\Command;

use Symfony\Component\Uid\Uuid;

final readonly class CreateDefaultUserPermissionsCommand
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}

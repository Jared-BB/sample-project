<?php

namespace App\User\Application\Command;

use App\Shared\Application\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateUserProjectionCommand implements AsyncCommandInterface
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}

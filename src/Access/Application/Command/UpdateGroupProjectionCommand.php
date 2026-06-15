<?php

namespace App\Access\Application\Command;

use App\Shared\Application\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

final readonly class UpdateGroupProjectionCommand implements AsyncCommandInterface
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}

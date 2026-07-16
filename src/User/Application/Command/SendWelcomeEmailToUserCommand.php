<?php

namespace App\User\Application\Command;

use App\Shared\Application\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

final readonly class SendWelcomeEmailToUserCommand implements AsyncCommandInterface
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}

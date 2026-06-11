<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use Symfony\Component\Uid\Uuid;

final readonly class DeleteCommand
{
    public function __construct(
        public Uuid $userId,
    ) {
    }
}

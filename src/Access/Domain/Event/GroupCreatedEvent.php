<?php

declare(strict_types=1);

namespace App\Access\Domain\Event;

use App\Shared\Domain\EventInterface;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final class GroupCreatedEvent implements EventInterface
{
    public function __construct(
        public Uuid $id,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}

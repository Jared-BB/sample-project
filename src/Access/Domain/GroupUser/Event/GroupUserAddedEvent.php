<?php

declare(strict_types=1);

namespace App\Access\Domain\GroupUser\Event;

use App\Shared\Domain\EventInterface;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final class GroupUserAddedEvent implements EventInterface
{
    public function __construct(
        public Uuid $groupId,
        public Uuid $userId,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}

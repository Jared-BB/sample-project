<?php

declare(strict_types=1);

namespace App\Access\Domain\GroupUser;

use App\Access\Domain\Group;
use App\Access\Domain\GroupUser\Event\GroupUserAddedEvent;
use App\Shared\Domain\EventStore;
use Symfony\Component\Uid\Uuid;

class GroupUser
{
    private Group $group;
    private Uuid $userId;

    private function __construct(Group $group, Uuid $userId)
    {
        $this->group = $group;
        $this->userId = $userId;
    }

    public static function create(Group $group, Uuid $userId): self
    {
        $groupUser = new self(
            group: $group,
            userId: $userId,
        );

        EventStore::addEvent(
            new GroupUserAddedEvent(
                groupId: $group->id(),
                userId: $userId,
            )
        );

        return $groupUser;
    }

    public function group(): Group
    {
        return $this->group;
    }

    public function userId(): Uuid
    {
        return $this->userId;
    }
}

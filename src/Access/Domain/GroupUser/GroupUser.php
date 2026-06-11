<?php

declare(strict_types=1);

namespace App\Access\Domain\GroupUser;

use App\Access\Domain\Group;
use App\Access\Domain\GroupUser\Event\GroupUserAddedEvent;
use App\Shared\Domain\EventStore;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'access_group_user')]
class GroupUser
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'access_group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    #[ORM\Id]
    #[ORM\Column(name: 'user_id', type: 'uuid', nullable: false)]
    private Uuid $userId;

    public function __construct(Group $group, Uuid $userId)
    {
        $this->group = $group;
        $this->userId = $userId;

        EventStore::addEvent(
            new GroupUserAddedEvent(
                groupId: $group->id(),
                userId: $userId,
            )
        );
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

<?php

declare(strict_types=1);

namespace App\Access\Domain;

use App\Access\Domain\Event\GroupCreatedEvent;
use App\Access\Domain\GroupPermission\DTO\GroupPermissionCollection;
use App\Access\Domain\GroupPermission\GroupPermission;
use App\Access\Domain\GroupUser\Event\GroupUserDeletedEvent;
use App\Access\Domain\GroupUser\GroupUser;
use App\Access\Domain\ValueObject\Name;
use App\Shared\Domain\EventStore;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

class Group
{
    private readonly Uuid $id;
    private Name $name;
    private bool $enabled = true;
    private DateTimeImmutable $createdAt;
    private Collection $permissions;
    private Collection $users;

    private function __construct(Uuid $id, Name $name, GroupPermissionCollection $permissionCollection)
    {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();
        $this->permissions = new ArrayCollection();
        $this->users = new ArrayCollection();

        foreach ($permissionCollection->items() as $permissionDto) {
            $this->permissions->add(
                GroupPermission::create(
                    id: Uuid::v7(),
                    group: $this,
                    context: $permissionDto->context,
                    permission: $permissionDto->permission,
                    objectId: $permissionDto->objectId,
                ),
            );
        }
    }

    public static function create(
        Uuid $id,
        Name $name,
        GroupPermissionCollection $permissionCollection,
    ): self {
        $group = new self(
            id: $id,
            name: $name,
            permissionCollection: $permissionCollection,
        );

        EventStore::addEvent(
            new GroupCreatedEvent(
                id: $id,
            )
        );

        return $group;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return GroupUser[]
     */
    public function users(): array
    {
        return $this->users->toArray();
    }

    /**
     * @return GroupPermission[]
     */
    public function permissions(): array
    {
        return $this->permissions->toArray();
    }

    public function addUser(Uuid $userId): void
    {
        if (array_any(
            $this->users->toArray(),
            fn (GroupUser $groupUser) => $groupUser->userId()->equals($userId),
        )) {
            return;
        }

        $this->users->add(
            GroupUser::create(
                group: $this,
                userId: $userId,
            ),
        );
    }

    public function removeUser(Uuid $userId): void
    {
        foreach ($this->users as $groupUser) {
            if ($groupUser->userId()->equals($userId)) {
                $this->users->removeElement($groupUser);

                EventStore::addEvent(
                    new GroupUserDeletedEvent(
                        groupId: $this->id(),
                        userId: $userId,
                    )
                );

                return;
            }
        }
    }
}

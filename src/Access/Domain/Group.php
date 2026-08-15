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
use Symfony\Component\Uid\Uuid;

class Group
{
    private readonly Uuid $id;
    private Name $name;
    private bool $enabled = true;
    private DateTimeImmutable $createdAt;

    /** @var GroupPermission[] */
    private array $permissions = [];

    /** @var GroupUser[] */
    private array $users = [];

    private function __construct(Uuid $id, Name $name, GroupPermissionCollection $permissionCollection)
    {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();

        foreach ($permissionCollection->items() as $permissionDto) {
            $this->permissions[] = GroupPermission::create(
                id: Uuid::v7(),
                group: $this,
                context: $permissionDto->context,
                permission: $permissionDto->permission,
                objectId: $permissionDto->objectId,
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
        return $this->users;
    }

    /**
     * @return GroupPermission[]
     */
    public function permissions(): array
    {
        return $this->permissions;
    }

    public function addUser(Uuid $userId): void
    {
        if (array_any($this->users, fn ($groupUser) => $groupUser->userId()->equals($userId))) {
            return;
        }

        $this->users[] = GroupUser::create(
            group: $this,
            userId: $userId,
        );
    }

    public function removeUser(Uuid $userId): void
    {
        foreach ($this->users as $index => $groupUser) {
            if ( ! $groupUser->userId()->equals($userId)) {
                continue;
            }

            unset($this->users[$index]);
            $this->users = array_values($this->users);

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

<?php

declare(strict_types=1);

namespace App\Access\Domain;

use App\Access\Application\DTO\GroupPermissionCollection;
use App\Access\Domain\Event\GroupCreatedEvent;
use App\Access\Domain\Event\GroupUpdatedEvent;
use App\Access\Domain\GroupPermission\GroupPermission;
use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupUser\Event\GroupUserDeletedEvent;
use App\Access\Domain\GroupUser\GroupUser;
use App\Access\Domain\ValueObject\Name;
use App\Shared\Domain\EventStore;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'access_group')]
class Group
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true, nullable: false)]
    private Uuid $id;

    #[ORM\Embedded(class: Name::class, columnPrefix: false)]
    private Name $name;

    #[ORM\Column(name: 'enabled', type: Types::BOOLEAN, nullable: false)]
    private bool $enabled = true;

    #[ORM\Column(name: 'created_at', type: Types::DATETIMETZ_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\OneToMany(targetEntity: GroupPermission::class, mappedBy: 'group', cascade: ['persist', 'remove'], fetch: 'LAZY', orphanRemoval: true)]
    private Collection $permissions;

    #[ORM\OneToMany(targetEntity: GroupUser::class, mappedBy: 'group', cascade: ['persist', 'remove'], fetch: 'LAZY', orphanRemoval: true)]
    private Collection $users;

    public function __construct(Uuid $id, Name $name, GroupPermissionCollection $permissionCollection)
    {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = new DateTimeImmutable();
        $this->users = new ArrayCollection();

        $this->permissions = new ArrayCollection();
        foreach ($permissionCollection->items() as $permissionDto) {
            $this->permissions->add(
                new GroupPermission(
                    id: Uuid::v7(),
                    group: $this,
                    context: $permissionDto->context,
                    permission: $permissionDto->permission,
                    objectId: $permissionDto->objectId,
                )
            );
        }

        EventStore::addEvent(
            new GroupCreatedEvent(
                id: $id,
            )
        );
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
     * @return GroupPermission[]
     */
    public function permissionsByContext(Context $context): array
    {
        $permissions = [];
        /** @var GroupPermission $groupPermission */
        foreach ($this->permissions->toArray() as $groupPermission) {
            if ($groupPermission->context()->value === $context->value) {
                $permissions[] = $groupPermission;
            }
        }

        return $permissions;
    }

    /**
     * @return GroupPermission[]
     */
    public function permissionsByObject(Uuid $objectId): array
    {
        $permissions = [];
        /** @var GroupPermission $groupPermission */
        foreach ($this->permissions->toArray() as $groupPermission) {
            if ($groupPermission->objectId()?->equals($objectId)) {
                $permissions[] = $groupPermission;
            }
        }

        return $permissions;
    }

    public function users(): array
    {
        return $this->users->toArray();
    }

    public function addUser(Uuid $userId): void
    {
        foreach ($this->users as $groupUser) {
            if ($groupUser->userId()->equals($userId)) {
                return;
            }
        }

        $this->users->add(
            new GroupUser(
                group: $this,
                userId: $userId,
            )
        );
    }

    public function removeUser(Uuid $userId): void
    {
        /** @var GroupUser $groupUser */
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

    public function update(Name $name, GroupPermissionCollection $permissionCollection): void
    {
        $this->name = $name;

        $this->permissions->clear();
        $this->addPermissions($permissionCollection);
    }

    public function addPermissions(GroupPermissionCollection $permissionCollection): void
    {
        foreach ($permissionCollection->items() as $permissionDto) {
            $this->permissions->add(
                new GroupPermission(
                    id: Uuid::v7(),
                    group: $this,
                    context: $permissionDto->context,
                    permission: $permissionDto->permission,
                    objectId: $permissionDto->objectId,
                )
            );
        }

        EventStore::addEvent(
            new GroupUpdatedEvent(
                id: $this->id(),
            )
        );
    }
}

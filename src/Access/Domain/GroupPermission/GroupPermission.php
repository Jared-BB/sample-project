<?php

declare(strict_types=1);

namespace App\Access\Domain\GroupPermission;

use App\Access\Domain\Group;
use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use Symfony\Component\Uid\Uuid;

class GroupPermission
{
    private readonly Uuid $id;
    private Group $group;
    private Context $context;
    private Permission $permission;
    private ?Uuid $objectId;

    private function __construct(
        Uuid $id,
        Group $group,
        Context $context,
        Permission $permission,
        ?Uuid $objectId,
    ) {
        $this->id = $id;
        $this->group = $group;
        $this->context = $context;
        $this->permission = $permission;
        $this->objectId = $objectId;
    }

    public static function create(
        Uuid $id,
        Group $group,
        Context $context,
        Permission $permission,
        ?Uuid $objectId,
    ): self {
        return new self(
            id: $id,
            group: $group,
            context: $context,
            permission: $permission,
            objectId: $objectId,
        );
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function group(): Group
    {
        return $this->group;
    }

    public function context(): Context
    {
        return $this->context;
    }

    public function permission(): Permission
    {
        return $this->permission;
    }

    public function objectId(): ?Uuid
    {
        return $this->objectId;
    }
}

<?php

declare(strict_types=1);

namespace App\Access\Domain\GroupPermission;

use App\Access\Domain\Group;
use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'access_group_permission')]
class GroupPermission
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true, nullable: false)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'permissions')]
    #[ORM\JoinColumn(name: 'access_group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    #[ORM\Column(name: 'context', type: Types::STRING, length: 128, nullable: false, enumType: Context::class)]
    private Context $context;

    #[ORM\Column(name: 'permission', type: Types::STRING, length: 128, nullable: false, enumType: Permission::class)]
    private Permission $permission;

    #[ORM\Column(name: 'object_id', type: 'uuid', length: 128, nullable: true)]
    private ?Uuid $objectId;

    public function __construct(Uuid $id, Group $group, Context $context, Permission $permission, ?Uuid $objectId)
    {
        $this->id = $id;
        $this->group = $group;
        $this->context = $context;
        $this->permission = $permission;
        $this->objectId = $objectId;
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

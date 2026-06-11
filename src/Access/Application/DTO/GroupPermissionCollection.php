<?php

namespace App\Access\Application\DTO;

use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use Symfony\Component\Uid\Uuid;

final class GroupPermissionCollection
{
    private array $items = [];

    public function add(GroupPermissionDto $dto): void
    {
        $this->items[] = $dto;
    }

    /** @return GroupPermissionDto[] */
    public function items(): array
    {
        return $this->items;
    }

    public static function fromRequest(array $permissions): self
    {
        $collection = new self();

        foreach ($permissions as $data) {
            $collection->add(
                new GroupPermissionDto(
                    context: Context::from($data['context']),
                    permission: Permission::from($data['permission']),
                    objectId: ! empty($data['objectId']) ? Uuid::fromString($data['objectId']) : null,
                )
            );
        }

        return $collection;
    }
}

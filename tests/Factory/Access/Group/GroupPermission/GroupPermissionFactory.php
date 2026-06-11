<?php

declare(strict_types=1);

namespace App\Tests\Factory\Access\Group\GroupPermission;

use App\Access\Domain\GroupPermission\GroupPermission;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class GroupPermissionFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return GroupPermission::class;
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function initialize(): static
    {
        return $this->instantiateWith(function (array $a): GroupPermission {
            return new GroupPermission(
                id: Uuid::v7(),
                group: $a['group'],
                context: $a['context'],
                permission: $a['permission'],
                objectId: $a['objectId'],
            );
        });
    }
}

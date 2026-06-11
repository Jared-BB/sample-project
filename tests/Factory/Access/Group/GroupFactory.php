<?php

declare(strict_types=1);

namespace App\Tests\Factory\Access\Group;

use App\Access\Domain\Group;
use App\Access\Domain\ValueObject\Name;
use AttendoPolar\Shared\Application\DTO\Access\GroupPermissionCollection;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class GroupFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Group::class;
    }

    protected function defaults(): array
    {
        return [
            'name' => self::faker()->name(),
            'permissionCollection' => new GroupPermissionCollection(),
        ];
    }

    protected function initialize(): static
    {
        return $this->instantiateWith(function (array $a): Group {
            return new Group(
                id: Uuid::v7(),
                name: new Name((string) $a['name']),
                permissionCollection: $a['permissionCollection'],
            );
        });
    }
}

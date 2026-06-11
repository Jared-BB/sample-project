<?php

declare(strict_types=1);

namespace App\Tests\Factory\Access\Group\GroupUser;

use App\Access\Domain\GroupUser\GroupUser;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class GroupUserFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return GroupUser::class;
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function initialize(): static
    {
        return $this->instantiateWith(function (array $a): GroupUser {
            return new GroupUser(
                group: $a['group'],
                userId: $a['userId'],
            );
        });
    }
}

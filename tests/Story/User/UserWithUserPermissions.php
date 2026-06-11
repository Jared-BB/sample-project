<?php

declare(strict_types=1);

namespace App\Tests\Story\User;

use App\Access\Application\DTO\GroupPermissionCollection;
use App\Access\Application\DTO\GroupPermissionDto;
use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\Tests\Factory\Access\Group\GroupFactory;
use App\Tests\Factory\Access\Group\GroupUser\GroupUserFactory;
use App\Tests\Factory\User\UserFactory;
use Zenstruck\Foundry\Story;

class UserWithUserPermissions extends Story
{
    public function build(): void
    {
        $user = UserFactory::createOne(['email' => 'test@test.com', 'password' => 'PasswordOk1']);
        $this->addState('user', $user);

        $permissionCollection = new GroupPermissionCollection();
        $permissionCollection->add(
            new GroupPermissionDto(context: Context::USER, permission: Permission::MANAGE),
        );

        $group = GroupFactory::createOne(['name' => 'TEST DEV', 'permissionCollection' => $permissionCollection]);
        $this->addState('group', $group);

        $groupUser = GroupUserFactory::createOne([
            'userId' => $user->id(),
            'group' => $group,
        ]);
        $this->addState('groupUser', $groupUser);
    }
}

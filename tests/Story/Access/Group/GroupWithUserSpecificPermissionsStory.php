<?php

declare(strict_types=1);

namespace App\Tests\Story\Access\Group;

use App\Access\Application\DTO\GroupPermissionCollection;
use App\Access\Application\DTO\GroupPermissionDto;
use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\Tests\Factory\Access\Group\GroupFactory;
use App\Tests\Factory\Access\Group\GroupUser\GroupUserFactory;
use App\Tests\Factory\User\UserFactory;
use App\User\Domain\ValueObject\Role;
use Zenstruck\Foundry\Story;

class GroupWithUserSpecificPermissionsStory extends Story
{
    public function build(): void
    {
        $user = UserFactory::createOne(['email' => 'test@test.com', 'password' => 'PasswordOk1', 'role' => Role::AGENT->value]);
        $this->addState('user', $user);

        $user2 = UserFactory::createOne(['email' => 'test2@test.com', 'password' => 'PasswordOk1']);
        $this->addState('user_2', $user2);

        $permissionCollection = new GroupPermissionCollection();
        $permissionCollection->add(new GroupPermissionDto(context: Context::USER, permission: Permission::UPDATE, objectId: $user2->id()));

        $group = GroupFactory::createOne(['name' => 'TEST DEV', 'permissionCollection' => $permissionCollection]);
        $this->addState('group', $group);

        $groupUser = GroupUserFactory::createOne([
            'userId' => $user->id(),
            'group' => $group,
        ]);

        $this->addState('group', $group);
        $this->addState('groupUser', $groupUser);
    }
}

<?php

namespace App\Tests\Story\User;

use App\Access\Application\DTO\GroupPermissionCollection;
use App\Access\Application\DTO\GroupPermissionDto;
use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\Tests\Factory\Access\Group\GroupFactory;
use App\Tests\Factory\Access\Group\GroupUser\GroupUserFactory;
use App\Tests\Factory\User\UserFactory;
use App\User\Domain\ValueObject\Role;
use Zenstruck\Foundry\Story;

class ListUsersStory extends Story
{
    public function build(): void
    {
        $user1 = UserFactory::createOne(['email' => 'admin@test.com', 'password' => 'PasswordOk1', 'role' => Role::ADMIN->value]);
        $this->addState('admin_user', $user1);

        $user2 = UserFactory::createOne(['email' => 'agent_1@test.com', 'password' => 'PasswordOk1', 'role' => Role::AGENT->value]);
        $this->addState('agent_user_1', $user2);

        $permissionCollection = new GroupPermissionCollection();
        $permissionCollection->add(
            new GroupPermissionDto(context: Context::USER, permission: Permission::MANAGE),
        );

        $group = GroupFactory::createOne(['name' => 'TEST DEV', 'permissionCollection' => $permissionCollection]);
        $this->addState('group', $group);

        $groupUser = GroupUserFactory::createOne([
            'userId' => $user2->id(),
            'group' => $group,
        ]);
        $this->addState('groupUser', $groupUser);

        $user3 = UserFactory::createOne(['email' => 'agent_2@test.com', 'password' => 'PasswordOk1', 'role' => Role::AGENT->value]);
        $this->addState('agent_user_2', $user3);

        $user4 = UserFactory::createOne(['email' => 'agent_3@test.com', 'password' => 'PasswordOk1', 'role' => Role::AGENT->value, 'enabled' => false]);
        $this->addState('agent_user_3', $user4);
    }
}

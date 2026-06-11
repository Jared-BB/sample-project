<?php

declare(strict_types=1);

namespace App\Tests\Story\Access\Group;

use App\Tests\Factory\Access\Group\GroupFactory;
use App\Tests\Factory\Access\Group\GroupUser\GroupUserFactory;
use App\Tests\Factory\User\UserFactory;
use AttendoPolar\Shared\Application\DTO\Access\GroupPermissionCollection;
use AttendoPolar\Shared\Application\DTO\Access\GroupPermissionDto;
use AttendoPolar\Shared\Domain\GroupPermission\Context;
use AttendoPolar\Shared\Domain\GroupPermission\Permission;
use Zenstruck\Foundry\Story;

class GroupWithUserStory extends Story
{
    public function build(): void
    {
        $user = UserFactory::createOne(['email' => 'test@attendo.com', 'password' => 'PasswordOk1']);
        $this->addState('user', $user);

        $user2 = UserFactory::createOne(['email' => 'test2@attendo.com', 'password' => 'PasswordOk1']);
        $this->addState('user_2', $user2);

        $permissionCollection = new GroupPermissionCollection();
        $permissionCollection->add(new GroupPermissionDto(context: Context::USER, permission: Permission::MANAGE));
        $permissionCollection->add(new GroupPermissionDto(context: Context::GROUP, permission: Permission::MANAGE));
        $permissionCollection->add(new GroupPermissionDto(context: Context::TABLE_DESIGNER, permission: Permission::MANAGE));
        $permissionCollection->add(new GroupPermissionDto(context: Context::QUERY_DESIGNER, permission: Permission::MANAGE));
        $permissionCollection->add(new GroupPermissionDto(context: Context::SCREEN_DESIGNER, permission: Permission::MANAGE));

        $group = GroupFactory::createOne(['name' => 'Attendo DEV', 'permissionCollection' => $permissionCollection]);
        $this->addState('group', $group);

        $groupUser = GroupUserFactory::createOne([
            'userId' => $user->id(),
            'group' => $group,
        ]);

        $this->addState('group', $group);
        $this->addState('groupUser', $groupUser);
    }
}

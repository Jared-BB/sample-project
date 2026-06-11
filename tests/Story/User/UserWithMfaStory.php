<?php

declare(strict_types=1);

namespace App\Tests\Story\User;

use App\Tests\Factory\Access\Group\GroupFactory;
use App\Tests\Factory\Access\Group\GroupUser\GroupUserFactory;
use App\Tests\Factory\User\UserFactory;
use AttendoPolar\Shared\Application\DTO\Access\GroupPermissionCollection;
use AttendoPolar\Shared\Application\DTO\Access\GroupPermissionDto;
use AttendoPolar\Shared\Domain\GroupPermission\Context;
use AttendoPolar\Shared\Domain\GroupPermission\Permission;
use Zenstruck\Foundry\Story;

class UserWithMfaStory extends Story
{
    public function build(): void
    {
        $user = UserFactory::createOne(['email' => 'test@attendo.com', 'password' => 'PasswordOk1', 'mfa_code' => '123456']);
        $this->addState('user', $user);

        $permissionCollection = new GroupPermissionCollection();
        $permissionCollection->add(
            new GroupPermissionDto(context: Context::USER, permission: Permission::MANAGE),
        );

        $group = GroupFactory::createOne(['name' => 'Attendo DEV', 'permissionCollection' => $permissionCollection]);
        $this->addState('group', $group);

        $groupUser = GroupUserFactory::createOne([
            'userId' => $user->id(),
            'group' => $group,
        ]);
        $this->addState('groupUser', $groupUser);
    }
}

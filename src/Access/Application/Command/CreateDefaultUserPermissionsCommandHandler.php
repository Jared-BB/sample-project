<?php

namespace App\Access\Application\Command;

use App\Access\Application\DTO\GroupPermissionCollection;
use App\Access\Application\DTO\GroupPermissionDto;
use App\Access\Domain\Group;
use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\Access\Domain\GroupRepository;
use App\Access\Domain\ValueObject\Name;
use Symfony\Component\Uid\Uuid;

final readonly class CreateDefaultUserPermissionsCommandHandler
{
    public function __construct(
        private GroupRepository $groupRepository,
    ) {
    }

    public function __invoke(CreateDefaultUserPermissionsCommand $command): void
    {
        $permissionCollection = new GroupPermissionCollection();
        $permissionCollection->add(
            new GroupPermissionDto(
                context: Context::USER,
                permission: Permission::MANAGE,
                objectId: $command->userId,
            )
        );

        $group = new Group(
            id: Uuid::v7(),
            name: new Name('user_' . $command->userId->toString()),
            permissionCollection: $permissionCollection,
        );

        $group->addUser($command->userId);

        $this->groupRepository->save($group);
    }
}

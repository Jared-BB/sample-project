<?php

namespace App\Access\Application\DTO;

use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use Symfony\Component\Uid\Uuid;

final readonly class GroupPermissionDto
{
    public function __construct(
        public Context $context,
        public Permission $permission,
        public ?Uuid $objectId = null,
    ) {
    }
}

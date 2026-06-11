<?php

declare(strict_types=1);

namespace App\Access\Infrastructure\Security;

use App\Access\Domain\GroupPermission\ValueObject\Permission;
use Symfony\Component\Uid\Uuid;

final readonly class PermissionDto
{
    public function __construct(
        public Permission $permission,
        public ?Uuid $objectId,
    ) {
    }
}

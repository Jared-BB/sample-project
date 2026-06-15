<?php

declare(strict_types=1);

namespace App\Access\Domain;

use App\Access\Application\DTO\GroupPermissionCollection;
use Symfony\Component\Uid\Uuid;

interface GroupRepository
{
    public function userHasAnyPermission(
        Uuid $userId,
        GroupPermissionCollection $permissionCollection,
    ): bool;

    /**
     * @return Group[]
     */
    public function findByUserId(Uuid $userId): array;

    public function save(Group $group): void;
}

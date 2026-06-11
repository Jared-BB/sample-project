<?php

declare(strict_types=1);

namespace App\Access\Domain;

use Symfony\Component\Uid\Uuid;

interface GroupRepository
{
    public function findByIdOrFail(Uuid $groupId): Group;

    public function findByGroupAndUserOrFail(Uuid $groupId, Uuid $userId): Group;

    public function findOneByUserOrFail(Uuid $userId): Group;

    /**
     * @return Group[]
     */
    public function findByObjectId(Uuid $objectId): array;

    /**
     * @return Group[]
     */
    public function findByUser(Uuid $userId): array;

    /**
     * @return Group[]
     */
    public function findActive(int $page): array;

    public function countActive(): int;

    public function save(Group $group): void;
}

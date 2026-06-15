<?php

namespace App\Access\Domain;

use Symfony\Component\Uid\Uuid;

interface GroupReadRepository
{
    public function findByUserId(Uuid $userId): array;

    /**
     * @param Group[] $groups
     */
    public function saveForUser(Uuid $userId, array $groups): void;

    public function deleteForUser(Uuid $userId): void;
}

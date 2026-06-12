<?php

declare(strict_types=1);

namespace App\Access\Domain;

use Symfony\Component\Uid\Uuid;

interface GroupRepository
{
    /**
     * @return Group[]
     */
    public function findByUser(Uuid $userId): array;
}

<?php

declare(strict_types=1);

namespace App\Access\Infrastructure;

use Symfony\Component\Uid\Uuid;

final class GroupContext
{
    private Uuid $groupId;

    public function setContext(Uuid $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function getGroupId(): Uuid
    {
        return $this->groupId;
    }
}

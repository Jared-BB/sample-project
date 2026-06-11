<?php

declare(strict_types=1);

namespace App\Access\Domain\Exception;

use App\Shared\Domain\Exception\NotFoundException;
use Symfony\Component\Uid\Uuid;

final class GroupNotFoundException extends NotFoundException
{
    public static function byId(Uuid $groupId): self
    {
        return new self("Group {$groupId->toString()} not found");
    }

    public static function byUserId(Uuid $userId): self
    {
        return new self("Group not found for user {$userId->toString()}");
    }

    public function errorCode(): string
    {
        return 'GROUP_NOT_FOUND';
    }
}

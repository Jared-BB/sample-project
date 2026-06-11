<?php

declare(strict_types=1);

namespace App\User\Domain\Exception;

use App\Shared\Domain\Exception\NotFoundException;
use App\User\Domain\ValueObject\Email;
use Symfony\Component\Uid\Uuid;

final class UserNotFoundException extends NotFoundException
{
    public static function byId(Uuid $userId): self
    {
        return new self("User {$userId->toString()} not found");
    }

    public static function byEmail(Email $email): self
    {
        return new self("User {$email->asString()} not found");
    }

    public function errorCode(): string
    {
        return 'USER_NOT_FOUND';
    }
}

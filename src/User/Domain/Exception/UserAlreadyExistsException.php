<?php

declare(strict_types=1);

namespace App\User\Domain\Exception;

use App\Shared\Domain\Exception\ConflictException;
use App\User\Domain\ValueObject\Email;

final class UserAlreadyExistsException extends ConflictException
{
    public static function byEmail(Email $email): self
    {
        return new self("User {$email->asString()} already exists");
    }

    public function errorCode(): string
    {
        return 'USER_ALREADY_EXISTS';
    }
}

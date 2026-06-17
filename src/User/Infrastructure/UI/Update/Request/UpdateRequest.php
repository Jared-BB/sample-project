<?php

namespace App\User\Infrastructure\UI\Update\Request;

use App\User\Domain\ValueObject\Role;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateRequest
{
    #[Assert\Email]
    public ?string $email = null;

    public ?string $password = null;

    #[Assert\Choice(callback: [Role::class, 'values'])]
    public ?string $role = null;
}

<?php

declare(strict_types=1);

namespace App\User\Infrastructure\UI\Create\Request;

use App\User\Domain\ValueObject\Role;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    public string $password;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [Role::class, 'values'])]
    public string $role;
}

<?php

namespace App\User\Infrastructure\UI\List\Response;

use App\User\Domain\User;

final readonly class UserItemResponse
{
    public string $id;
    public string $email;
    public string $role;

    public function __construct(User $user)
    {
        $this->id = $user->id()->toString();
        $this->email = $user->email()->asString();
        $this->role = $user->role()->value;
    }
}

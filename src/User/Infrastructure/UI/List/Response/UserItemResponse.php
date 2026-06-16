<?php

namespace App\User\Infrastructure\UI\List\Response;

use App\User\Application\DTO\UserDto;

final readonly class UserItemResponse
{
    public string $id;
    public string $email;
    public string $role;

    public function __construct(UserDto $user)
    {
        $this->id = $user->id;
        $this->email = $user->email;
        $this->role = $user->role;
    }
}

<?php

namespace App\User\Domain;

use App\User\Application\DTO\UserCollection;

interface UserReadRepository
{
    public function searchUsers(?string $search, int $page): UserCollection;

    public function save(User $user): void;

    public function deleteUser(User $user): void;
}

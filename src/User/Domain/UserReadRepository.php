<?php

namespace App\User\Domain;

use App\User\Application\DTO\UserCollection;

interface UserReadRepository
{
    public function searchUser(string $search, int $page): UserCollection;

    public function save(User $user): void;

    public function deleteUser(User $user): void;
}

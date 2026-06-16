<?php

namespace App\User\Domain;

interface UserReadRepository
{
    public function save(User $user): void;
}

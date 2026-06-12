<?php

namespace App\User\Domain;

use Symfony\Component\Uid\Uuid;

interface LoggedUserProviderInterface
{
    public function requireUser(): User;

    public function requireUserId(): Uuid;
}

<?php

namespace App\Shared\Application\Security;

use App\User\Domain\User;
use Symfony\Component\Uid\Uuid;

interface LoggedUserProviderInterface
{
    public function requireUser(): User;

    public function requireUserId(): Uuid;
}

<?php

namespace App\User\Application\Port;

use Symfony\Component\Uid\Uuid;

interface AccessProvisioner
{
    public function provisionForUser(Uuid $userId): void;
}

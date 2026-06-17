<?php

namespace App\User\Application\Command\Updater;

use App\User\Application\Command\UpdateCommand;
use App\User\Domain\User;

interface UserFieldUpdater
{
    public function supports(UpdateCommand $command): bool;

    public function update(User $user, UpdateCommand $command): void;
}

<?php

namespace App\User\Application\Command\Updater;

use App\User\Application\Command\UpdateCommand;
use App\User\Domain\User;

final readonly class EmailUpdater implements UserFieldUpdater
{
    public function supports(UpdateCommand $command): bool
    {
        return $command->email !== null;
    }

    public function update(User $user, UpdateCommand $command): void
    {
        $user->changeEmail($command->email);
    }
}

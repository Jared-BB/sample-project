<?php

namespace App\User\Application\Command\Updater;

use App\User\Application\Command\UpdateCommand;
use App\User\Domain\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class PasswordUpdater implements UserFieldUpdater
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function supports(UpdateCommand $command): bool
    {
        return $command->password !== null;
    }

    public function update(User $user, UpdateCommand $command): void
    {
        $user->changePassword(
            hasher: $this->hasher,
            password: $command->password,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\User\Domain\UserRepository;

final readonly class DeleteCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(DeleteCommand $command): void
    {
        $user = $this->userRepository->findByIdOrFail($command->userId);
        $user->delete();
        $this->userRepository->save($user);
    }
}

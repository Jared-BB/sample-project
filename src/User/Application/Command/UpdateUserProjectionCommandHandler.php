<?php

namespace App\User\Application\Command;

use App\User\Domain\UserReadRepository;
use App\User\Domain\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'commands.bus')]
final readonly class UpdateUserProjectionCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserReadRepository $userReadRepository,
    ) {
    }

    public function __invoke(UpdateUserProjectionCommand $command): void
    {
        $this->userReadRepository->save(
            user: $this->userRepository->findByIdOrFail($command->userId),
        );
    }
}

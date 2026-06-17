<?php

namespace App\User\Application\Command;

use App\User\Application\Command\Updater\UserFieldUpdater;
use App\User\Domain\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'commands.bus')]
final readonly class UpdateCommandHandler
{
    /**
     * @param iterable<UserFieldUpdater> $updaters
     */
    public function __construct(
        private UserRepository $userRepository,
        private iterable $updaters,
    ) {
    }

    public function __invoke(UpdateCommand $command): void
    {
        $user = $this->userRepository->findByIdOrFail($command->userId);

        foreach ($this->updaters as $updater) {
            if ($updater->supports($command)) {
                $updater->update($user, $command);
            }
        }

        $this->userRepository->save($user);
    }
}

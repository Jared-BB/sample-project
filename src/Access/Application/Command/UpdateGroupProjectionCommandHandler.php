<?php

namespace App\Access\Application\Command;

use App\Access\Domain\GroupReadRepository;
use App\Access\Domain\GroupRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'commands.bus')]
final readonly class UpdateGroupProjectionCommandHandler
{
    public function __construct(
        private GroupRepository $groupRepository,
        private GroupReadRepository $groupReadRepository,
    ) {
    }

    public function __invoke(UpdateGroupProjectionCommand $command): void
    {
        $this->groupReadRepository->saveForUser(
            userId: $command->userId,
            groups: $this->groupRepository->findByUserId($command->userId),
        );
    }
}

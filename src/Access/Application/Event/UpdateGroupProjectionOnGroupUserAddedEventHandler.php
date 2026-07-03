<?php

namespace App\Access\Application\Event;

use App\Access\Application\Command\UpdateGroupProjectionCommand;
use App\Access\Domain\GroupUser\Event\GroupUserAddedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class UpdateGroupProjectionOnGroupUserAddedEventHandler
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(GroupUserAddedEvent $event): void
    {
        $this->commandBus->dispatch(
            new UpdateGroupProjectionCommand(
                userId: $event->userId,
            )
        );
    }
}

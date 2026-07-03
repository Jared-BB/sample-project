<?php

namespace App\User\Application\Event;

use App\User\Application\Command\UpdateUserProjectionCommand;
use App\User\Domain\Event\UserDeletedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class UpdateUserProjectionOnUserDeletedEventHandler
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(UserDeletedEvent $event): void
    {
        $this->commandBus->dispatch(
            new UpdateUserProjectionCommand(
                userId: $event->id,
            )
        );
    }
}

<?php

namespace App\User\Application\Event;

use App\User\Application\Command\UpdateUserProjectionCommand;
use App\User\Domain\Event\UserUpdatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'events.bus')]
final readonly class UpdateUserProjectionOnUserUpdatedEventHandler
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(UserUpdatedEvent $event): void
    {
        $this->commandBus->dispatch(
            new UpdateUserProjectionCommand(
                userId: $event->id,
            )
        );
    }
}

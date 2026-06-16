<?php

namespace App\User\Application\Event;

use App\User\Application\Command\UpdateUserProjectionCommand;
use App\User\Domain\Event\UserCreatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'events.bus')]
final readonly class UpdateUserProjectionOnUserCreatedEventHandler
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(UserCreatedEvent $event): void
    {
        $this->commandBus->dispatch(
            new UpdateUserProjectionCommand(
                userId: $event->id,
            )
        );
    }
}

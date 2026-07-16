<?php

namespace App\User\Application\Event;

use App\User\Application\Command\SendWelcomeEmailToUserCommand;
use App\User\Domain\Event\UserCreatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SendWelcomeEmailToUserOnUserCreatedEventHandler
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(UserCreatedEvent $event): void
    {
        $this->commandBus->dispatch(
            new SendWelcomeEmailToUserCommand(
                userId: $event->id,
            )
        );
    }
}

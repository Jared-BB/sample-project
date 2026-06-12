<?php

namespace App\Shared\Infrastructure\Bridge\UserAccess;

use App\Access\Application\Command\CreateDefaultUserPermissionsCommand;
use App\User\Application\Port\AccessProvisioner;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

final readonly class MessengerAccessProvisioner implements AccessProvisioner
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function provisionForUser(Uuid $userId): void
    {
        $this->commandBus->dispatch(
            new CreateDefaultUserPermissionsCommand(
                userId: $userId,
            )
        );
    }
}

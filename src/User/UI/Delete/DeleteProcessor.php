<?php

namespace App\User\UI\Delete;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\User\Application\Command\DeleteCommand;
use App\User\Infrastructure\Security\AccessGuard;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class DeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private AccessGuard $accessGuard,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): null
    {
        $userId = $uriVariables['id'];

        $this->accessGuard->isGranted(
            actionPermission: Permission::DELETE,
            objectId: $userId,
        );

        try {
            $this->commandBus->dispatch(
                new DeleteCommand(
                    userId: $userId,
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }

        return null;
    }
}

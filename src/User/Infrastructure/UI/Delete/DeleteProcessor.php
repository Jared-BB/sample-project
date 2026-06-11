<?php

namespace App\User\Infrastructure\UI\Delete;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\User\Application\Command\DeleteCommand;
use App\User\Infrastructure\Security\AccessGuard;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeleteProcessor implements ProcessorInterface
{
    use HandleTrait;

    public function __construct(
        private readonly AccessGuard $accessGuard,
        private readonly MessageBusInterface $commandBus,
    ) {
        $this->messageBus = $commandBus;
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

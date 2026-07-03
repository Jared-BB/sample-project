<?php

namespace App\User\UI\Update;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\User\Application\Command\UpdateCommand;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use App\User\Domain\ValueObject\Role;
use App\User\Infrastructure\Security\AccessGuard;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class UpdateProcessor implements ProcessorInterface
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
            actionPermission: Permission::UPDATE,
            objectId: $userId,
        );

        try {
            $this->commandBus->dispatch(
                new UpdateCommand(
                    userId: $userId,
                    email: $data->email ? new Email($data->email) : null,
                    password: $data->password ? new Password($data->password) : null,
                    role: $data->role ? Role::from($data->role) : null,
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }

        return null;
    }
}

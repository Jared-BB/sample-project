<?php

declare(strict_types=1);

namespace App\User\Infrastructure\UI\Create;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\User\Application\Command\CreateCommand;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use App\User\Domain\ValueObject\Role;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateProcessor implements ProcessorInterface
{
    use HandleTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
    ) {
        $this->messageBus = $commandBus;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): null
    {
        try {
            $this->commandBus->dispatch(
                new CreateCommand(
                    email: new Email($data->email),
                    password: new Password($data->password),
                    role: Role::from($data->role),
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }

        return null;
    }
}

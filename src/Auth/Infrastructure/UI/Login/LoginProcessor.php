<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\UI\Login;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Auth\Application\Command\LoginCommand;
use App\Auth\Infrastructure\UI\Login\Response\LoginResponse;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class LoginProcessor implements ProcessorInterface
{
    use HandleTrait;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
    ) {
        $this->messageBus = $commandBus;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): LoginResponse
    {
        try {
            $token = $this->handle(
                new LoginCommand(
                    email: new Email($data->email),
                    password: new Password($data->password),
                )
            );
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }

        return new LoginResponse(
            token: $token,
        );
    }
}

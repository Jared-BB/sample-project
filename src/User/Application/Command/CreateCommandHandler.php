<?php

declare(strict_types=1);

namespace App\User\Application\Command;

use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\User;
use App\User\Domain\UserRepository;
use App\User\Domain\ValueObject\Password;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler(bus: 'commands.bus')]
final readonly class CreateCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function __invoke(CreateCommand $command): void
    {
        if ($this->userRepository->findByEmail($command->email)) {
            throw UserAlreadyExistsException::byEmail($command->email);
        }

        $user = new User(
            id: Uuid::v7(),
            email: $command->email,
            role: $command->role,
        );

        $user->addPassword(
            password: new Password(
                $this->hasher->hashPassword(
                    user: $user,
                    plainPassword: bin2hex(random_bytes(8)),
                ),
            ),
        );

        $this->userRepository->save($user);
    }
}

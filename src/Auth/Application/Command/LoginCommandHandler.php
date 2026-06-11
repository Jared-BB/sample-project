<?php

declare(strict_types=1);

namespace App\Auth\Application\Command;

use App\Auth\Application\Service\JwtService;
use App\User\Domain\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

#[AsMessageHandler(bus: 'commands.bus')]
final readonly class LoginCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $hasher,
        private JwtService $jwtService,
    ) {
    }

    public function __invoke(LoginCommand $command): string
    {
        $user = $this->userRepository->findByEmailOrFail($command->email);

        if ( ! $this->hasher->isPasswordValid($user, $command->password->asString())) {
            throw new BadCredentialsException();
        }

        return $this->jwtService->generate($user);
    }
}

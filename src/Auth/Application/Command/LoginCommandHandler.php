<?php

declare(strict_types=1);

namespace App\Auth\Application\Command;

use App\Auth\Application\Service\JwtCreator;
use App\User\Domain\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

final readonly class LoginCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $hasher,
        private JwtCreator $jwtService,
    ) {
    }

    public function __invoke(LoginCommand $command): string
    {
        $user = $this->userRepository->findByEmailOrFail($command->email);

        if ( ! $this->hasher->isPasswordValid($user, $command->password)) {
            throw new BadCredentialsException();
        }

        return $this->jwtService->generate($user);
    }
}

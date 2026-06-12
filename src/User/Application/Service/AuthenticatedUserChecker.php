<?php

namespace App\User\Application\Service;

use App\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class AuthenticatedUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if ( ! $user instanceof User) {
            return;
        }

        if ($user->isDeleted()) {
            throw new CustomUserMessageAccountStatusException('User deleted.');
        }

        if ( ! $user->isEnabled()) {
            throw new CustomUserMessageAccountStatusException('User disabled.');
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
    }
}

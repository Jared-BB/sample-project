<?php

declare(strict_types=1);

namespace App\User\Application\Service;

use App\User\Domain\LoggedUserProviderInterface;
use App\User\Domain\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Uid\Uuid;

final readonly class AuthenticatedUserProvider implements LoggedUserProviderInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function requireUser(): User
    {
        $user = $this->security->getUser();

        if ( ! $user instanceof User || ! $user->isEnabled() || $user->isDeleted()) {
            throw new AccessDeniedHttpException('User not logged');
        }

        return $user;
    }

    public function requireUserId(): Uuid
    {
        $user = $this->requireUser();

        return $user->id();
    }
}

<?php

declare(strict_types=1);

namespace App\Access\Infrastructure\Security;

use App\Access\Application\DTO\GroupPermissionCollection;
use App\Access\Domain\GroupRepository;
use App\Shared\Infrastructure\Security\VoterName;
use App\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class GroupPermissionVoter extends Voter
{
    public function __construct(
        private readonly GroupRepository $groupRepository,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === VoterName::GROUP_PERMISSION_VOTER->value && $subject instanceof GroupPermissionCollection;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if ( ! $user instanceof User) {
            return false;
        }

        if ($user->role()->isAdmin()) {
            return true;
        }

        return $this->groupRepository->userHasAnyPermission($user->id(), $subject);
    }
}

<?php

declare(strict_types=1);

namespace App\Access\Infrastructure\Security;

use App\Access\Domain\Exception\InvalidContextException;
use App\Access\Domain\Exception\MissingContextException;
use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\Access\Domain\GroupRepository;
use App\Shared\Infrastructure\Security\VoterName;
use App\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Uid\Uuid;

final class GroupPermissionVoter extends Voter
{
    public const string ATTRIBUTE = VoterName::GROUP_PERMISSION_VOTER->value;

    public function __construct(
        private readonly GroupRepository $groupRepository,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ATTRIBUTE && \is_array($subject);
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

        $context = null;
        $requiredPermissions = [];
        foreach ($subject as $permission) {
            $parts = explode(';', $permission);

            if ($context && $context !== Context::from($parts[0])) {
                throw InvalidContextException::create();
            }

            $context = Context::from($parts[0]);
            $requiredPermissions[] = new PermissionDto(
                permission: Permission::from($parts[1]),
                objectId: isset($parts[2]) ? Uuid::fromString($parts[2]) : null,
            );
        }

        if ( ! $context) {
            throw MissingContextException::create();
        }

        $userGroup = $this->groupRepository->findOneByUserOrFail($user->id());

        foreach ($userGroup->permissionsByContext($context) as $permissionData) {
            foreach ($requiredPermissions as $requiredPermission) {
                if (
                    $permissionData->permission()->value === $requiredPermission->permission->value
                    && $permissionData->objectId()?->toString() === $requiredPermission->objectId?->toString()
                ) {
                    return true;
                }
            }
        }

        return false;
    }
}

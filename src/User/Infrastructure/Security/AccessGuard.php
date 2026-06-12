<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use App\Access\Application\DTO\GroupPermissionCollection;
use App\Access\Application\DTO\GroupPermissionDto;
use App\Access\Domain\GroupPermission\ValueObject\Context;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\Shared\Infrastructure\Security\VoterName;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class AccessGuard
{
    public function __construct(
        private AuthorizationCheckerInterface $auth,
    ) {
    }

    public function isGranted(Permission $actionPermission, ?Uuid $objectId = null): void
    {
        $permissions = new GroupPermissionCollection();
        $permissions->add(
            new GroupPermissionDto(
                context: Context::USER,
                permission: Permission::MANAGE,
            )
        );
        $permissions->add(
            new GroupPermissionDto(
                context: Context::USER,
                permission: $actionPermission,
            )
        );

        if ($objectId) {
            $permissions->add(
                new GroupPermissionDto(
                    context: Context::USER,
                    permission: $actionPermission,
                    objectId: $objectId,
                )
            );
        }

        if ( ! $this->auth->isGranted(VoterName::GROUP_PERMISSION_VOTER->value, $permissions)) {
            throw new AccessDeniedHttpException();
        }
    }
}

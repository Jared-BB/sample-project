<?php

declare(strict_types=1);

namespace App\Access\Infrastructure\Persistence;

use App\Access\Application\DTO\GroupPermissionCollection;
use App\Access\Domain\Group;
use App\Access\Domain\GroupPermission\GroupPermission;
use App\Access\Domain\GroupRepository;
use App\Access\Domain\GroupUser\GroupUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class PostgresqlGroupRepository extends ServiceEntityRepository implements GroupRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function userHasAnyPermission(
        Uuid $userId,
        GroupPermissionCollection $permissionCollection,
    ): bool {
        if ($permissionCollection->items() === []) {
            return false;
        }

        $qb = $this->createQueryBuilder('g')
            ->select('1')
            ->innerJoin(GroupUser::class, 'gu', 'WITH', 'gu.group = g.id')
            ->innerJoin(GroupPermission::class, 'gp', 'WITH', 'gp.group = g.id')
            ->where('gu.userId = :userId')
            ->andWhere('g.enabled = true')
            ->andWhere('gp.context = :context')
            ->setParameter('userId', $userId->toString())
            ->setParameter('context', $permissionCollection->context()->value)
            ->setMaxResults(1);

        $orX = $qb->expr()->orX();

        foreach ($permissionCollection->items() as $index => $permissionDto) {
            $permissionParam = 'permission_' . $index;
            $objectIdParam = 'objectId_' . $index;

            if ($permissionDto->objectId === null) {
                $orX->add(
                    $qb->expr()->andX(
                        "gp.permission = :$permissionParam",
                        'gp.objectId IS NULL'
                    )
                );
            } else {
                $orX->add(
                    $qb->expr()->andX(
                        "gp.permission = :$permissionParam",
                        "gp.objectId = :$objectIdParam"
                    )
                );

                $qb->setParameter($objectIdParam, $permissionDto->objectId->toString());
            }

            $qb->setParameter($permissionParam, $permissionDto->permission->value);
        }

        $qb->andWhere($orX);

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }
}

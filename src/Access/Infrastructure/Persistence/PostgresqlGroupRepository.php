<?php

declare(strict_types=1);

namespace App\Access\Infrastructure\Persistence;

use App\Access\Domain\Exception\GroupNotFoundException;
use App\Access\Domain\Group;
use App\Access\Domain\GroupPermission\GroupPermission;
use App\Access\Domain\GroupRepository;
use App\Access\Domain\GroupUser\GroupUser;
use App\Shared\Domain\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class PostgresqlGroupRepository extends ServiceEntityRepository implements GroupRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function findByIdOrFail(Uuid $groupId): Group
    {
        $group = $this->findOneBy(['id' => $groupId, 'enabled' => true]);
        if ($group === null) {
            throw GroupNotFoundException::byId($groupId);
        }

        return $group;
    }

    public function findByGroupAndUserOrFail(Uuid $groupId, Uuid $userId): Group
    {
        $qb = $this->createQueryBuilder('g')
            ->innerJoin(GroupUser::class, 'gu', 'WITH', 'gu.group = g.id')
            ->where('g.id = :groupId')
            ->andWhere('gu.userId = :userId')
            ->andWhere('g.enabled = true')
            ->setMaxResults(1)
            ->setParameter('groupId', $groupId)
            ->setParameter('userId', $userId);

        $group = $qb->getQuery()->getOneOrNullResult();

        if ($group === null) {
            throw GroupNotFoundException::byId($groupId);
        }

        return $group;
    }

    public function findOneByUserOrFail(Uuid $userId): Group
    {
        $qb = $this->createQueryBuilder('g')
            ->innerJoin(GroupUser::class, 'gu', 'WITH', 'gu.group = g.id')
            ->where('gu.userId = :userId')
            ->andWhere('g.enabled = true')
            ->orderBy('g.createdAt', 'ASC')
            ->setMaxResults(1)
            ->setParameter('userId', $userId->toString());

        $group = $qb->getQuery()->getOneOrNullResult();

        if ($group === null) {
            throw GroupNotFoundException::byUserId($userId);
        }

        return $group;
    }

    public function findByObjectId(Uuid $objectId): array
    {
        $qb = $this->createQueryBuilder('g')
            ->innerJoin(GroupPermission::class, 'gp', 'WITH', 'gp.group = g.id')
            ->where('gp.objectId = :objectId')
            ->andWhere('g.enabled = true')
            ->setParameter('objectId', $objectId->toString());

        return $qb->getQuery()->getResult();
    }

    public function findByUser(Uuid $userId): array
    {
        $qb = $this->createQueryBuilder('g')
            ->innerJoin(GroupUser::class, 'gu', 'WITH', 'gu.group = g.id')
            ->where('gu.userId = :userId')
            ->andWhere('g.enabled = true')
            ->setParameter('userId', $userId->toString());

        return $qb->getQuery()->getResult();
    }

    public function findActive(int $page): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * Pagination::LIMIT;

        return $this->findBy(
            criteria: ['enabled' => true],
            orderBy: ['createdAt' => 'ASC'],
            limit: Pagination::LIMIT,
            offset: $offset,
        );
    }

    public function countActive(): int
    {
        return $this->count(['enabled' => true]);
    }

    public function save(Group $group): void
    {
        $this->getEntityManager()->persist($group);
        $this->getEntityManager()->flush();
    }
}

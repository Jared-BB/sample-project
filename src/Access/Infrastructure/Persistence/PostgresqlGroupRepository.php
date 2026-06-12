<?php

declare(strict_types=1);

namespace App\Access\Infrastructure\Persistence;

use App\Access\Domain\Group;
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

    public function findByUser(Uuid $userId): array
    {
        $qb = $this->createQueryBuilder('g')
            ->innerJoin(GroupUser::class, 'gu', 'WITH', 'gu.group = g.id')
            ->where('gu.userId = :userId')
            ->andWhere('g.enabled = true')
            ->setParameter('userId', $userId->toString());

        return $qb->getQuery()->getResult();
    }
}

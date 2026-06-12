<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistence;

use App\Shared\Domain\Pagination;
use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\User;
use App\User\Domain\UserRepository;
use App\User\Domain\ValueObject\Email;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class PostgresqlUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByIdOrFail(Uuid $userId): User
    {
        $user = $this->findOneBy(['id' => $userId, 'deleted' => false]);
        if ($user === null) {
            throw UserNotFoundException::byId($userId);
        }

        return $user;
    }

    public function findByEmail(Email $email): ?User
    {
        return $this->findOneBy(['email' => $email->asString(), 'enabled' => true, 'deleted' => false]);
    }

    public function findByEmailOrFail(Email $email): User
    {
        $user = $this->findByEmail($email);
        if ($user === null) {
            throw UserNotFoundException::byEmail($email);
        }

        return $user;
    }

    public function findActive(?string $search, int $page): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * Pagination::LIMIT;

        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.enabled = true')
            ->orderBy('u.createdAt', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults(Pagination::LIMIT);

        if ($search) {
            $qb
                ->andWhere('LOWER(u.email) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function countActive(?string $search): int
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.enabled = true');

        if ($search) {
            $qb
                ->andWhere('LOWER(u.email) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $search . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}

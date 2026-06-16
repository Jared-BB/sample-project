<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Persistence;

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

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}

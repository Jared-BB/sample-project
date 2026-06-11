<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\User\Domain\ValueObject\Email;
use Symfony\Component\Uid\Uuid;

interface UserRepository
{
    public function findByIdOrFail(Uuid $userId): User;

    public function findByEmail(Email $email): ?User;

    public function findByEmailOrFail(Email $email): User;

    /**
     * @return User[]
     */
    public function findActive(int $page): array;

    public function countActive(): int;

    public function save(User $user): void;
}

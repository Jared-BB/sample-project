<?php

declare(strict_types=1);

namespace App\Tests\Factory\User;

use App\User\Domain\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Role;
use ReflectionProperty;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use DateTimeImmutable;

final class UserFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'password' => self::faker()->password(),
            'role' => Role::AGENT->value,
            'enabled' => true,
            'createdAt' => new DateTimeImmutable(),
        ];
    }

    protected function initialize(): static
    {
        return $this->instantiateWith(function (array $a): User {
            $user = new User(
                id: Uuid::v7(),
                email: new Email((string) $a['email']),
                role: Role::from($a['role']),
            );

            if ( ! $a['enabled']) {
                $user->disable();
            }

            $factory = new PasswordHasherFactory([
                User::class => ['algorithm' => 'auto'],
            ]);

            $hashedPassword = $factory
                ->getPasswordHasher($user)
                ->hash((string) $a['password']);

            $reflection = new ReflectionProperty(User::class, 'password');
            $reflection->setValue($user, $hashedPassword);

            $reflection = new ReflectionProperty(User::class, 'createdAt');
            $reflection->setValue($user, $a['createdAt']);

            return $user;
        });
    }
}

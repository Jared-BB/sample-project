<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\Shared\Domain\EventStore;
use App\User\Domain\Event\UserCreatedEvent;
use App\User\Domain\Event\UserDeletedEvent;
use App\User\Domain\Event\UserDisabledEvent;
use App\User\Domain\Event\UserUpdatedEvent;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use App\User\Domain\ValueObject\Role;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private bool $updated = false;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true, nullable: false)]
    private Uuid $id;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 254, nullable: false)]
    private string $email;

    #[ORM\Column(name: 'password', type: Types::STRING, length: 254, nullable: false)]
    private string $password;

    #[ORM\Column(name: 'role', type: Types::STRING, length: 50, nullable: false, enumType: Role::class)]
    private Role $role;

    #[ORM\Column(name: 'enabled', type: Types::BOOLEAN, nullable: false)]
    private bool $enabled = true;

    #[ORM\Column(name: 'deleted', type: Types::BOOLEAN, nullable: false)]
    private bool $deleted = false;

    #[ORM\Column(name: 'created_at', type: Types::DATETIMETZ_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $id,
        Email $email,
        Role $role,
    ) {
        $this->id = $id;
        $this->email = $email->asString();
        $this->role = $role;
        $this->createdAt = new DateTimeImmutable();

        EventStore::addEvent(
            new UserCreatedEvent(
                id: $id,
            )
        );
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function email(): Email
    {
        return new Email($this->email);
    }

    public function getRoles(): array
    {
        return [$this->role->value];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function addPassword(UserPasswordHasherInterface $hasher, Password $password): void
    {
        $this->password = $hasher->hashPassword(
            user: $this,
            plainPassword: $password->asString(),
        );
    }

    public function delete(): void
    {
        $this->deleted = true;

        EventStore::addEvent(
            new UserDeletedEvent(
                id: $this->id(),
            )
        );
    }

    public function disable(): void
    {
        $this->enabled = false;

        EventStore::addEvent(
            new UserDisabledEvent(
                id: $this->id(),
            )
        );
    }

    public function changeEmail(Email $email): void
    {
        $this->email = $email->asString();
        $this->markUpdated();
    }

    public function changeRole(Role $role): void
    {
        $this->role = $role;
        $this->markUpdated();
    }

    public function changePassword(UserPasswordHasherInterface $hasher, Password $password): void
    {
        $this->password = $hasher->hashPassword(
            user: $this,
            plainPassword: $password->asString(),
        );
        $this->markUpdated();
    }

    private function markUpdated(): void
    {
        if ($this->updated) {
            return;
        }

        $this->updated = true;

        EventStore::addEvent(
            new UserUpdatedEvent(
                id: $this->id,
            )
        );
    }
}

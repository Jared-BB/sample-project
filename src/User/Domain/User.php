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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private bool $updated = false;

    private readonly Uuid $id;
    private string $email;
    private string $password;
    private Role $role;
    private bool $enabled = true;
    private bool $deleted = false;
    private DateTimeImmutable $createdAt;

    private function __construct(
        Uuid $id,
        Email $email,
        Role $role,
    ) {
        $this->id = $id;
        $this->email = $email->asString();
        $this->role = $role;
        $this->createdAt = new DateTimeImmutable();
    }

    public static function create(
        Uuid $id,
        Email $email,
        Role $role,
    ): self {
        $user = new self(
            id: $id,
            email: $email,
            role: $role,
        );

        EventStore::addEvent(
            new UserCreatedEvent(
                id: $id,
            )
        );

        return $user;
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

    public function addPassword(
        UserPasswordHasherInterface $hasher,
        Password $password,
    ): void {
        $this->password = $hasher->hashPassword(
            user: $this,
            plainPassword: $password->asString(),
        );
    }

    public function changePassword(
        UserPasswordHasherInterface $hasher,
        Password $password,
    ): void {
        $this->addPassword(
            hasher: $hasher,
            password: $password,
        );

        $this->markUpdated();
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

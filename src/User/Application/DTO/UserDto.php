<?php

namespace App\User\Application\DTO;

final readonly class UserDto
{
    public function __construct(
        public string $id,
        public string $email,
        public string $role,
        public bool $enabled,
        public bool $deleted,
        public string $createdAt,
    ) {
    }
}

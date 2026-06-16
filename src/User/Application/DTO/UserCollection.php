<?php

namespace App\User\Application\DTO;

final class UserCollection
{
    private array $items = [];

    public function add(UserDto $dto): void
    {
        $this->items[] = $dto;
    }

    /** @return UserDto[] */
    public function items(): array
    {
        return $this->items;
    }
}

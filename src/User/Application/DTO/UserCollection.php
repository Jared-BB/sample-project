<?php

namespace App\User\Application\DTO;

final class UserCollection
{
    private array $items = [];
    private int $total = 0;

    public function add(UserDto $dto): void
    {
        $this->items[] = $dto;
    }

    /** @return UserDto[] */
    public function items(): array
    {
        return $this->items;
    }

    public function addTotal(int $total): void
    {
        $this->total = $total;
    }

    public function total(): int
    {
        return $this->total;
    }
}

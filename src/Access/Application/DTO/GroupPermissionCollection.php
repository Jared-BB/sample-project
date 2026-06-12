<?php

namespace App\Access\Application\DTO;

use App\Access\Domain\Exception\InvalidContextException;
use App\Access\Domain\GroupPermission\ValueObject\Context;

final class GroupPermissionCollection
{
    private array $items = [];
    private ?Context $context = null;

    public function add(GroupPermissionDto $dto): void
    {
        if ($this->context && $this->context !== $dto->context) {
            throw InvalidContextException::create();
        }

        $this->items[] = $dto;
        $this->context = $dto->context;
    }

    /** @return GroupPermissionDto[] */
    public function items(): array
    {
        return $this->items;
    }

    public function context(): Context
    {
        if ( ! $this->context) {
            throw InvalidContextException::create();
        }

        return $this->context;
    }
}

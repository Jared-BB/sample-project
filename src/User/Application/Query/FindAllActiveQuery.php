<?php

declare(strict_types=1);

namespace App\User\Application\Query;

final readonly class FindAllActiveQuery
{
    public function __construct(
        public ?string $search,
        public int $page,
    ) {
    }
}

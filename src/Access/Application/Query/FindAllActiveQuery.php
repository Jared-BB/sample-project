<?php

namespace App\Access\Application\Query;

use Symfony\Component\Uid\Uuid;

final readonly class FindAllActiveQuery
{
    public function __construct(
        public Uuid $userId,
        public int $page,
    ) {
    }
}

<?php

namespace App\Shared\Domain;

use DateTimeImmutable;

interface EventInterface
{
    public function occurredAt(): DateTimeImmutable;
}

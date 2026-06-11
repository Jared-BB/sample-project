<?php

declare(strict_types=1);

namespace App\Access\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Name
{
    public const int MIN_LENGTH = 3;
    public const int MAX_LENGTH = 128;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 128, nullable: false)]
    private string $name;

    public function __construct(string $name)
    {
        Assert::lengthBetween($name, self::MIN_LENGTH, self::MAX_LENGTH);

        $this->name = $name;
    }

    public function asString(): string
    {
        return $this->name;
    }
}

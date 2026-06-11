<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class Password
{
    public const int MIN_LENGTH = 3;
    public const int MAX_LENGTH = 128;

    #[ORM\Column(name: 'password', type: Types::STRING, length: 128, nullable: false)]
    private string $password;

    public function __construct(string $password)
    {
        Assert::lengthBetween($password, self::MIN_LENGTH, self::MAX_LENGTH);

        $this->password = $password;
    }

    public function asString(): string
    {
        return $this->password;
    }
}

<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class Email
{
    public const int MIN_LENGTH = 3;
    public const int MAX_LENGTH = 30;

    private string $email;

    public function __construct(string $email)
    {
        $email = trim($email);
        $email = mb_strtolower($email);

        Assert::lengthBetween($email, self::MIN_LENGTH, self::MAX_LENGTH);
        Assert::email($email);

        $this->email = $email;
    }

    public function asString(): string
    {
        return $this->email;
    }
}

<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class Password
{
    public const int MIN_LENGTH = 8;
    public const int MAX_LENGTH = 128;

    private string $password;

    public function __construct(string $password)
    {
        Assert::lengthBetween($password, self::MIN_LENGTH, self::MAX_LENGTH);
        Assert::regex(
            $password,
            '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'Password must contain at least one lowercase letter, one uppercase letter and one number.'
        );

        $this->password = $password;
    }

    public function asString(): string
    {
        return $this->password;
    }
}

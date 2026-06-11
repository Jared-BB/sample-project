<?php

declare(strict_types=1);

namespace App\Auth\Application\Service;

use App\User\Domain\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final readonly class JwtService
{
    public function __construct(
        private JWTTokenManagerInterface $jwt,
    ) {
    }

    public function generate(User $user): string
    {
        return $this->jwt->createFromPayload($user);
    }
}

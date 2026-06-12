<?php

declare(strict_types=1);

namespace App\User\Application\Query;

use App\User\Domain\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class FindAllActiveQueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(FindAllActiveQuery $query): array
    {
        return [
            $this->userRepository->findActive($query->search, $query->page),
            $this->userRepository->countActive($query->search),
        ];
    }
}

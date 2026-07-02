<?php

declare(strict_types=1);

namespace App\User\Application\Query;

use App\User\Application\DTO\UserCollection;
use App\User\Domain\UserReadRepository;

final readonly class FindAllActiveQueryHandler
{
    public function __construct(
        private UserReadRepository $userRepository,
    ) {
    }

    public function __invoke(FindAllActiveQuery $query): UserCollection
    {
        return $this->userRepository->searchUsers(
            search: $query->search,
            page: $query->page,
        );
    }
}

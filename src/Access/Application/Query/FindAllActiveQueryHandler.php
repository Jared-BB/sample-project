<?php

namespace App\Access\Application\Query;

use App\Access\Domain\GroupReadRepository;
use App\Shared\Domain\Pagination;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class FindAllActiveQueryHandler
{
    public function __construct(
        private GroupReadRepository $groupRepository,
    ) {
    }

    public function __invoke(FindAllActiveQuery $query): array
    {
        $groups = $this->groupRepository->findByUserId($query->userId);

        $offset = ($query->page - 1) * Pagination::LIMIT;

        return [
            array_slice($groups, $offset, Pagination::LIMIT),
            count($groups),
        ];
    }
}

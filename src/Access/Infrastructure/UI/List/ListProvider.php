<?php

namespace App\Access\Infrastructure\UI\List;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Access\Application\Query\FindAllActiveQuery;
use App\Shared\Domain\Pagination;
use App\User\Domain\LoggedUserProviderInterface;
use ArrayIterator;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class ListProvider implements ProviderInterface
{
    use HandleTrait;

    public function __construct(
        private readonly MessageBusInterface $queryBus,
        private readonly LoggedUserProviderInterface $loggedUserProvider,
    ) {
        $this->messageBus = $queryBus;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): TraversablePaginator
    {
        $page = isset($context['filters']['page']) ? (int) $context['filters']['page'] : 1;

        [$items, $total] = $this->handle(
            new FindAllActiveQuery(
                userId: $this->loggedUserProvider->requireUserId(),
                page: $page,
            ),
        );

        return new TraversablePaginator(
            traversable: new ArrayIterator($items),
            currentPage: $page,
            itemsPerPage: Pagination::LIMIT,
            totalItems: $total
        );
    }
}

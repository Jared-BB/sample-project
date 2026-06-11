<?php

declare(strict_types=1);

namespace App\User\Infrastructure\UI\List;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\Shared\Domain\Pagination;
use App\User\Infrastructure\UI\List\Response\UserItemResponse;
use App\User\Application\Query\FindAllActiveQuery;
use App\User\Domain\User;
use App\User\Infrastructure\Security\AccessGuard;
use ArrayIterator;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class ListProvider implements ProviderInterface
{
    use HandleTrait;

    public function __construct(
        private readonly AccessGuard $accessGuard,
        private readonly MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): TraversablePaginator
    {
        $this->accessGuard->isGranted(actionPermission: Permission::LIST);

        $page = isset($context['filters']['page']) ? (int) $context['filters']['page'] : 1;
        $search = $context['filters']['search'] ?? null;

        [$items, $total] = $this->handle(
            new FindAllActiveQuery(
                search: $search,
                page: $page,
            ),
        );

        return new TraversablePaginator(
            traversable: new ArrayIterator($this->transform($items)),
            currentPage: $page,
            itemsPerPage: Pagination::LIMIT,
            totalItems: $total
        );
    }

    private function transform(array $users): array
    {
        $response = [];
        /** @var User $user */
        foreach ($users as $user) {
            $response[] = new UserItemResponse($user);
        }

        return $response;
    }
}

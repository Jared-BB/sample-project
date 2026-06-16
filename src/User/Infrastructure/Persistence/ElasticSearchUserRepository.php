<?php

namespace App\User\Infrastructure\Persistence;

use App\User\Domain\User;
use App\User\Domain\UserReadRepository;
use DateTimeInterface;
use Elastic\Elasticsearch\Client;

final readonly class ElasticSearchUserRepository implements UserReadRepository
{
    private const string INDEX = 'users';

    public function __construct(
        private Client $client,
    ) {
    }

    public function save(User $user): void
    {
        /** @var array<string, mixed> $document */
        $document = [
            'id' => (string) $user->id(),
            'email' => $user->email(),
            'role' => $user->role(),
            'enabled' => $user->isEnabled(),
            'deleted' => $user->isDeleted(),
            'created_at' => $user->createdAt()->format(DateTimeInterface::ATOM),
        ];

        $this->client->index([
            'index' => self::INDEX,
            'id' => (string) $user->id(),
            'body' => $document,
        ]);
    }
}

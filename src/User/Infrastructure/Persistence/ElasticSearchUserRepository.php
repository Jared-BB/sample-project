<?php

namespace App\User\Infrastructure\Persistence;

use App\User\Application\DTO\UserCollection;
use App\User\Application\DTO\UserDto;
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

    public function searchUser(string $search): UserCollection
    {
        $response = $this->client->search([
            'index' => self::INDEX,
            'body' => [
                'query' => [
                    'wildcard' => [
                        'email.keyword' => [
                            'value' => '*' . strtolower($search) . '*',
                            'case_insensitive' => true,
                        ],
                    ],
                ],
            ],
        ]);

        return $this->transform($response->asArray());
    }

    public function save(User $user): void
    {
        /** @var array<string, mixed> $document */
        $document = [
            'id' => $user->id()->toString(),
            'email' => $user->email()->asString(),
            'role' => $user->role()->value,
            'enabled' => $user->isEnabled(),
            'deleted' => $user->isDeleted(),
            'created_at' => $user->createdAt()->format(DateTimeInterface::ATOM),
        ];

        $this->client->index([
            'index' => self::INDEX,
            'id' => $user->id()->toString(),
            'body' => $document,
            'refresh' => 'true',
        ]);
    }

    public function deleteUser(User $user): void
    {
        $this->client->delete([
            'index' => self::INDEX,
            'id' => $user->id()->toString(),
            'refresh' => 'true',
        ]);
    }

    public function transform(array $response): UserCollection
    {
        $users = new UserCollection();

        foreach ($response['hits']['hits'] ?? [] as $hit) {
            $source = $hit['_source'] ?? [];

            $users->add(
                new UserDto(
                    id: (string) ($source['id'] ?? ''),
                    email: (string) ($source['email'] ?? ''),
                    role: (string) ($source['role'] ?? ''),
                    enabled: (bool) ($source['enabled'] ?? false),
                    deleted: (bool) ($source['deleted'] ?? false),
                    createdAt: (string) ($source['created_at'] ?? ''),
                )
            );
        }

        return $users;
    }
}

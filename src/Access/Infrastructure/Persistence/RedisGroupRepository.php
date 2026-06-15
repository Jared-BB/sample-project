<?php

namespace App\Access\Infrastructure\Persistence;

use App\Access\Domain\Group;
use App\Access\Domain\GroupPermission\GroupPermission;
use App\Access\Domain\GroupReadRepository;
use Predis\ClientInterface;
use Symfony\Component\Uid\Uuid;

final readonly class RedisGroupRepository implements GroupReadRepository
{
    private const string KEY = 'GROUP:USER:';

    public function __construct(
        private ClientInterface $redis,
    ) {
    }

    public function findByUserId(Uuid $userId): array
    {
        $data = $this->redis->get($this->key($userId));

        if ( ! $data) {
            return [];
        }

        return json_decode($data, true);
    }

    public function saveForUser(Uuid $userId, array $groups): void
    {
        $payload = array_map(
            static fn (Group $group): array => [
                'id' => $group->id()->toString(),
                'name' => $group->name()->asString(),
                'permissions' => array_map(
                    static fn (GroupPermission $permission): string => $permission->permission()->value,
                    $group->permissions(),
                ),
            ],
            $groups,
        );

        $this->redis->set(
            $this->key($userId),
            json_encode($payload, JSON_THROW_ON_ERROR),
        );
    }

    public function deleteForUser(Uuid $userId): void
    {
        $this->redis->del([$this->key($userId)]);
    }

    private function key(Uuid $userId): string
    {
        return self::KEY . $userId->toString();
    }
}

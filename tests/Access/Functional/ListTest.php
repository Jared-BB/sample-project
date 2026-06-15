<?php

namespace App\Tests\Access\Functional;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Access\Application\Command\UpdateGroupProjectionCommand;
use App\Access\Application\Command\UpdateGroupProjectionCommandHandler;
use App\Access\Domain\GroupPermission\ValueObject\Permission;
use App\Access\Infrastructure\Persistence\RedisGroupRepository;
use App\Tests\FunctionalTestCase;
use App\Tests\Story\User\UserWithUserPermissions;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\Response;

class ListTest extends FunctionalTestCase
{
    private Client $client;
    private ?User $user = null;

    private const string ENDPOINT = '/api/v1/groups';

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->client = $container->get('test.api_platform.client');
    }

    protected function tearDown(): void
    {
        if ( ! $this->user) {
            self::getContainer()
                ->get(RedisGroupRepository::class)
                ->deleteForUser($this->user->id());
        }

        parent::tearDown();
    }

    public function test_list_groups_ok(): void
    {
        UserWithUserPermissions::load();

        $this->user = UserWithUserPermissions::get('user');

        self::getContainer()
            ->get(UpdateGroupProjectionCommandHandler::class)(
                new UpdateGroupProjectionCommand($this->user->id())
            );

        $response = $this->client->request('GET', self::ENDPOINT, [
            'headers' => self::headersWithJWTForUser($this->user),
            'query' => [
                'page' => 1,
            ],
        ]);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $json = $response->toArray();

        self::assertCount(1, $json);
        self::assertNotNull($json[0]['id']);
        self::assertSame('TEST DEV', $json[0]['name']);
        self::assertSame(Permission::MANAGE->value, $json[0]['permissions'][0]);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\User\Functional;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\FunctionalTestCase;
use App\Tests\Story\User\ListUsersStory;
use App\User\Application\Command\UpdateUserProjectionCommand;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\Response;

class ListTest extends FunctionalTestCase
{
    private Client $client;
    private array $users = [];

    private const string ENDPOINT = '/api/v1/users';

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->client = $container->get('test.api_platform.client');
    }

    protected function tearDown(): void
    {
        foreach ($this->users as $user) {
            self::getContainer()
                ->get('repository.user.read_repository')
                ->deleteUser($user);
        }

        $this->users = [];

        parent::tearDown();
    }

    public function test_list_users_ok(): void
    {
        ListUsersStory::load();

        /** @var User $user */
        $user = ListUsersStory::get('agent_user_1');

        $this->prepareElasticSearchProjection();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'headers' => self::headersWithJWTForUser($user),
            'query' => [
                'page' => 1,
            ],
        ]);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $json = $response->toArray();

        self::assertCount(3, $json);
        self::assertSame('agent_2@test.com', $json[0]['email']);
        self::assertSame('agent_1@test.com', $json[1]['email']);
        self::assertSame('admin@test.com', $json[2]['email']);
    }

    public function test_list_users_filtered_ok(): void
    {
        ListUsersStory::load();

        /** @var User $user */
        $user = ListUsersStory::get('agent_user_1');

        $this->prepareElasticSearchProjection();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'headers' => self::headersWithJWTForUser($user),
            'query' => [
                'page' => 1,
                'search' => 'agent',
            ],
        ]);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $json = $response->toArray();

        self::assertCount(2, $json);
        self::assertSame('agent_2@test.com', $json[0]['email']);
        self::assertSame('agent_1@test.com', $json[1]['email']);
    }

    public function test_list_users_ok_but_invalid_page(): void
    {
        ListUsersStory::load();

        /** @var User $user */
        $user = ListUsersStory::get('agent_user_1');

        $this->prepareElasticSearchProjection();

        $response = $this->client->request('GET', self::ENDPOINT, [
            'headers' => self::headersWithJWTForUser($user),
            'query' => [
                'page' => 2,
            ],
        ]);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $json = $response->toArray();

        self::assertCount(0, $json);
    }

    public function test_list_users_with_no_permissions(): void
    {
        ListUsersStory::load();

        /** @var User $user */
        $user = ListUsersStory::get('agent_user_2');

        $response = $this->client->request('GET', self::ENDPOINT, [
            'headers' => self::headersWithJWTForUser($user),
        ]);

        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('ACCESS_DENIED', $json['error']);
        self::assertSame('Access denied', $json['message']);
    }

    private function prepareElasticSearchProjection(): void
    {
        $this->users[] = ListUsersStory::get('admin_user');
        $this->users[] = ListUsersStory::get('agent_user_1');
        $this->users[] = ListUsersStory::get('agent_user_2');
        $this->users[] = ListUsersStory::get('agent_user_3');

        foreach ($this->users as $user) {
            self::getContainer()
                ->get('command.user.update_projection')(
                    new UpdateUserProjectionCommand($user->id())
                );
        }
    }
}

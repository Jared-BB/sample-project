<?php

declare(strict_types=1);

namespace App\Tests\User\Functional;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\FunctionalTestCase;
use App\Tests\Story\User\UserStory;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\Response;

class ListTest extends FunctionalTestCase
{
    private Client $client;

    private const string ENDPOINT = '/api/v1/users';

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->client = $container->get('test.api_platform.client');
    }

    public function test_list_users_ok(): void
    {
        UserStory::load();

        /** @var User $user */
        $user = UserStory::get('user');

        $response = $this->client->request('GET', self::ENDPOINT, [
            'headers' => self::headersWithJWTForUser($user),
        ]);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $json = $response->toArray();

        self::assertSame(2, $json['totalItems']);
        self::assertSame('test_admin@attendo.com', $json['member'][0]['email']);
        self::assertSame('test_agent@attendo.com', $json['member'][1]['email']);
    }

    public function test_list_users_with_no_permissions(): void
    {
        UserStory::load();

        /** @var User $user */
        $user = UserStory::get('user');

        $response = $this->client->request('GET', self::ENDPOINT, [
            'headers' => self::headersWithJWTForUser($user),
        ]);

        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('ACCESS_DENIED', $json['error']);
        self::assertSame('Access denied', $json['message']);
    }
}

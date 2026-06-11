<?php

declare(strict_types=1);

namespace App\Tests\User\Functional;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Shared\Domain\EventStore;
use App\Tests\FunctionalTestCase;
use App\Tests\Story\User\UserStory;
use App\Tests\Story\User\UserWithUserPermissions;
use App\User\Domain\Event\UserDeletedEvent;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class DeleteTest extends FunctionalTestCase
{
    private Client $client;

    private const string ENDPOINT = '/api/v1/user/{id}';

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->client = $container->get('test.api_platform.client');
    }

    public function test_delete_user_ok(): void
    {
        UserWithUserPermissions::load();

        /** @var User $user */
        $user = UserWithUserPermissions::get('user');

        EventStore::clear();

        $endpoint = self::prepareEndpoint(self::ENDPOINT, ['{id}' => $user->id()]);

        $response = $this->client->request('DELETE', $endpoint, [
            'headers' =>  self::headersWithJWTForUser($user),
        ]);

        self::assertSame(Response::HTTP_ACCEPTED, $response->getStatusCode());

        $events = self::getEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserDeletedEvent::class, $events[0]);

        $userData = self::selectFrom('"user" WHERE id = \'' . $user->id() . '\'')[0];
        self::assertTrue($userData['deleted']);
    }

    public function test_delete_user_with_no_permissions(): void
    {
        UserStory::load();

        /** @var User $user */
        $user = UserStory::get('user');

        $endpoint = self::prepareEndpoint(self::ENDPOINT, ['{id}' => Uuid::v7()->toString()]);

        $response = $this->client->request('DELETE', $endpoint, [
            'headers' =>  self::headersWithJWTForUser($user),
        ]);

        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('ACCESS_DENIED', $json['error']);
        self::assertSame('Access denied', $json['message']);
    }

    public function test_delete_user_when_user_not_exists(): void
    {
        UserWithUserPermissions::load();

        /** @var User $user */
        $user = UserWithUserPermissions::get('user');

        $userId = Uuid::v7();

        $endpoint = self::prepareEndpoint(self::ENDPOINT, ['{id}' => $userId->toString()]);

        $response = $this->client->request('DELETE', $endpoint, [
            'headers' =>  self::headersWithJWTForUser($user),
        ]);

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('USER_NOT_FOUND', $json['error']);
        self::assertSame("User {$userId->toString()} not found", $json['message']);
    }
}

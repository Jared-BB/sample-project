<?php

namespace App\Tests\User\Functional;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Shared\Domain\EventStore;
use App\Tests\FunctionalTestCase;
use App\Tests\Story\User\UserStory;
use App\Tests\Story\User\UserWithUserPermissions;
use App\User\Domain\Event\UserUpdatedEvent;
use App\User\Domain\User;
use App\User\Domain\ValueObject\Role;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class UpdateTest extends FunctionalTestCase
{
    private Client $client;

    private const string ENDPOINT = '/api/v1/user/{id}';

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->client = $container->get('test.api_platform.client');
    }

    public function test_update_user_ok(): void
    {
        UserWithUserPermissions::load();

        /** @var User $user */
        $user = UserWithUserPermissions::get('user');

        EventStore::clear();

        $endpoint = self::prepareEndpoint(self::ENDPOINT, ['{id}' => $user->id()]);

        $response = $this->client->request('PATCH', $endpoint, [
            'headers' =>  self::headersWithJWTForUser($user, true),
            'json' => [
                'email' => 'jared@test.com',
                'password' => 'PasswordOk1',
                'role' => Role::AGENT->value,
            ],
        ]);

        self::assertSame(Response::HTTP_ACCEPTED, $response->getStatusCode());

        $events = self::getEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserUpdatedEvent::class, $events[0]);
    }

    public function test_update_user_bad_request_with_invalid_email(): void
    {
        UserWithUserPermissions::load();

        /** @var User $user */
        $user = UserWithUserPermissions::get('user');

        EventStore::clear();

        $endpoint = self::prepareEndpoint(self::ENDPOINT, ['{id}' => $user->id()]);

        $response = $this->client->request('PATCH', $endpoint, [
            'headers' =>  self::headersWithJWTForUser($user, true),
            'json' => [
                'email' => 'mail',
                'password' => 'PasswordOk1',
                'role' => Role::AGENT->value,
            ],
        ]);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('BAD_REQUEST', $json['error']);
        self::assertSame('email: This value is not a valid email address.', $json['message']);
    }

    public function test_update_user_bad_request_with_invalid_password(): void
    {
        UserWithUserPermissions::load();

        /** @var User $user */
        $user = UserWithUserPermissions::get('user');

        EventStore::clear();

        $endpoint = self::prepareEndpoint(self::ENDPOINT, ['{id}' => $user->id()]);

        $response = $this->client->request('PATCH', $endpoint, [
            'headers' =>  self::headersWithJWTForUser($user, true),
            'json' => [
                'email' => 'jared@test.com',
                'password' => 'a',
                'role' => Role::AGENT->value,
            ],
        ]);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('BAD_REQUEST', $json['error']);
        self::assertSame('Expected a value to contain between 8 and 128 characters. Got: "a"', $json['message']);
    }

    public function test_update_user_bad_request_with_invalid_role(): void
    {
        UserWithUserPermissions::load();

        /** @var User $user */
        $user = UserWithUserPermissions::get('user');

        EventStore::clear();

        $endpoint = self::prepareEndpoint(self::ENDPOINT, ['{id}' => $user->id()]);

        $response = $this->client->request('PATCH', $endpoint, [
            'headers' =>  self::headersWithJWTForUser($user, true),
            'json' => [
                'email' => 'jared@test.com',
                'password' => 'PasswordOk1',
                'role' => 'wrong_role',
            ],
        ]);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('BAD_REQUEST', $json['error']);
        self::assertSame('role: The value you selected is not a valid choice.', $json['message']);
    }

    public function test_update_user_when_user_not_exists(): void
    {
        UserWithUserPermissions::load();

        /** @var User $user */
        $user = UserWithUserPermissions::get('user');

        EventStore::clear();

        $userId = Uuid::v7();

        $endpoint = self::prepareEndpoint(self::ENDPOINT, ['{id}' => $userId->toString()]);

        $response = $this->client->request('PATCH', $endpoint, [
            'headers' =>  self::headersWithJWTForUser($user, true),
            'json' => [
                'email' => 'jared@test.es',
                'password' => 'PasswordOk1',
                'role' => Role::AGENT->value,
            ],
        ]);

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('USER_NOT_FOUND', $json['error']);
        self::assertSame("User {$userId->toString()} not found", $json['message']);
    }

    public function test_update_user_when_user_has_no_permissions(): void
    {
        UserStory::load();

        /** @var User $user */
        $user = UserStory::get('user');

        EventStore::clear();

        $userId = Uuid::v7();

        $endpoint = self::prepareEndpoint(self::ENDPOINT, ['{id}' => $userId->toString()]);

        $response = $this->client->request('PATCH', $endpoint, [
            'headers' =>  self::headersWithJWTForUser($user, true),
            'json' => [
                'email' => 'jared@test.es',
                'password' => 'PasswordOk1',
                'role' => Role::AGENT->value,
            ],
        ]);

        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('ACCESS_DENIED', $json['error']);
        self::assertSame('Access denied', $json['message']);
    }
}

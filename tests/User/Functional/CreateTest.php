<?php

declare(strict_types=1);

namespace App\Tests\User\Functional;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\FunctionalTestCase;
use App\Tests\Story\User\UserStory;
use App\User\Domain\Event\UserCreatedEvent;
use App\User\Domain\User;
use App\User\Domain\ValueObject\Role;
use Symfony\Component\HttpFoundation\Response;

class CreateTest extends FunctionalTestCase
{
    private Client $client;

    private const string ENDPOINT = '/api/v1/user';

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->client = $container->get('test.api_platform.client');
    }

    public function test_create_user_ok(): void
    {
        $response = $this->client->request('POST', self::ENDPOINT, [
            'headers' => self::basicHeaders(),
            'json' => [
                'email' => 'jared@test.com',
                'password' => 'PasswordOk1',
                'role' => Role::AGENT->value,
            ],
        ]);

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $events = self::getEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserCreatedEvent::class, $events[0]);
    }

    public function test_create_user_bad_request(): void
    {
        $response = $this->client->request('POST', self::ENDPOINT, [
            'headers' => self::basicHeaders(),
            'json' => [
                'email' => 'jared@test.com',
                'password' => 'PasswordOk1',
            ],
        ]);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('BAD_REQUEST', $json['error']);
        self::assertSame("role: This value should not be blank.", $json['message']);
    }

    public function test_create_user_bad_request_with_invalid_email(): void
    {
        $response = $this->client->request('POST', self::ENDPOINT, [
            'headers' => self::basicHeaders(),
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

    public function test_create_user_bad_request_with_invalid_password(): void
    {
        $response = $this->client->request('POST', self::ENDPOINT, [
            'headers' => self::basicHeaders(),
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

    public function test_create_user_bad_request_with_invalid_role(): void
    {
        $response = $this->client->request('POST', self::ENDPOINT, [
            'headers' => self::basicHeaders(),
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

    public function test_create_user_when_already_exists(): void
    {
        UserStory::load();

        /** @var User $user */
        $user = UserStory::get('user');

        $response = $this->client->request('POST', self::ENDPOINT, [
            'headers' => self::basicHeaders(),
            'json' => [
                'email' => $user->email()->asString(),
                'password' => 'PasswordOk1',
                'role' => Role::AGENT->value,
            ],
        ]);

        self::assertSame(Response::HTTP_CONFLICT, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('USER_ALREADY_EXISTS', $json['error']);
        self::assertSame('User test@test.com already exists', $json['message']);
    }
}

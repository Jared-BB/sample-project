<?php

declare(strict_types=1);

namespace App\Tests\Auth\Functional;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\FunctionalTestCase;
use App\Tests\Story\User\UserStory;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends FunctionalTestCase
{
    private const string ENDPOINT = '/api/v1/login';

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->client = $container->get('test.api_platform.client');
    }

    public function test_login_ok(): void
    {
        UserStory::load();

        /** @var User $user */
        $user = UserStory::get('user');

        $response = $this->client->request('POST', self::ENDPOINT, [
            'headers' => self::basicHeaders(),
            'json' => [
                'email' => $user->email()->asString(),
                'password' => 'PasswordOk1',
            ],
        ]);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        $json = $response->toArray();

        self::assertNotNull($json['token']);
    }

    public function test_login_bad_request(): void
    {
        $response = $this->client->request('POST', self::ENDPOINT, [
            'headers' => self::basicHeaders(),
            'json' => [
                'email' => 'jared@test.com',
            ],
        ]);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('BAD_REQUEST', $json['error']);
        self::assertSame('password: This value should not be blank.', $json['message']);
    }

    public function test_login_not_found(): void
    {
        $response = $this->client->request('POST', self::ENDPOINT, [
            'headers' => self::basicHeaders(),
            'json' => [
                'email' => 'user_not_found@test.com',
                'password' => 'Asdf1234',
            ],
        ]);

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('USER_NOT_FOUND', $json['error']);
        self::assertSame('User user_not_found@test.com not found', $json['message']);
    }

    public function test_login_password_ko(): void
    {
        UserStory::load();

        /** @var User $user */
        $user = UserStory::get('user');

        $response = $this->client->request('POST', self::ENDPOINT, [
            'headers' => self::basicHeaders(),
            'json' => [
                'email' => $user->email()->asString(),
                'password' => 'WrongPassword1',
            ],
        ]);

        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $json = $response->toArray(false);

        self::assertSame('BAD_CREDENTIALS', $json['error']);
        self::assertSame('Bad credentials', $json['message']);
    }
}

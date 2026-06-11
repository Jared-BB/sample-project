<?php

declare(strict_types=1);

namespace App\Tests;

use App\Shared\Domain\EventStore;
use App\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Zenstruck\Foundry\Test\Factories;

class FunctionalTestCase extends KernelTestCase
{
    use Factories;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var InMemoryTransport $transport */
        $transport = static::getContainer()->get('messenger.transport.events');
        $transport->reset();

        EventStore::clear();

        $container = self::getContainer();

        $wsEm = $container->get('doctrine.orm.entity_manager');
        $conn = $wsEm->getConnection();

        $conn->beginTransaction();
        $conn->executeStatement('SET CONSTRAINTS ALL DEFERRED');
    }

    protected function tearDown(): void
    {
        $container = self::getContainer();
        $wsEm = $container->get('doctrine.orm.entity_manager');
        $conn = $wsEm->getConnection();

        if ($conn->isTransactionActive()) {
            $conn->rollBack();
        }

        $wsEm->clear();
        self::_bootFoundry();

        parent::tearDown();
    }

    protected static function basicHeaders(): array
    {
        return [
            'Content-Type' => 'application/ld+json',
            'Accept' => 'application/ld+json',
        ];
    }

    protected static function headersWithJWTForUser(User $user): array
    {
        $container = static::getContainer();

        /** @var JWTTokenManagerInterface $jwtManager */
        $jwtManager = $container->get(JWTTokenManagerInterface::class);

        $jwt = $jwtManager->createFromPayload($user);

        return [
            'Content-Type' => 'application/ld+json',
            'Accept' => 'application/ld+json',
            'Authorization' => 'Bearer ' . $jwt,
        ];
    }

    public static function getEvents(): array
    {
        /** @var InMemoryTransport $transport */
        $transport = static::getContainer()->get('messenger.transport.events');
        $envelopes = $transport->getSent();

        return array_map(
            static fn (Envelope $envelope) => $envelope->getMessage(),
            $envelopes
        );
    }

    public static function prepareEndpoint(string $endpoint, array $arguments): string
    {
        foreach ($arguments as $key => $value) {
            $endpoint = str_replace($key, (string) $value, $endpoint);
        }

        return $endpoint;
    }

    public static function selectFrom(string $query): array
    {
        /** @var EntityManagerInterface $wsEm */
        $wsEm = self::getContainer()->get('doctrine.orm.entity_manager');
        $conn = $wsEm->getConnection();

        return $conn->fetchAllAssociative('SELECT * FROM ' . $query);
    }
}

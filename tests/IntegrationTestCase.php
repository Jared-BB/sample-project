<?php

namespace App\Tests;

use App\Shared\Domain\EventStore;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Zenstruck\Foundry\Test\Factories;

class IntegrationTestCase extends KernelTestCase
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
}

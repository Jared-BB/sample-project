<?php

namespace App\Tests\Access\Integration;

use App\Access\Application\Command\UpdateGroupProjectionCommand;
use App\Access\Infrastructure\Persistence\RedisGroupRepository;
use App\Tests\IntegrationTestCase;
use App\Tests\Story\User\UserWithUserPermissions;
use App\User\Domain\User;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateGroupProjectionCommandHandlerTest extends IntegrationTestCase
{
    private ?User $user = null;

    protected function tearDown(): void
    {
        if ( ! $this->user) {
            self::getContainer()
                ->get(RedisGroupRepository::class)
                ->deleteForUser($this->user->id());
        }

        parent::tearDown();
    }

    public function test_update_projection_ok(): void
    {
        UserWithUserPermissions::load();

        $this->user = UserWithUserPermissions::get('user');

        /** @var MessageBusInterface $bus */
        $bus = self::getContainer()->get(MessageBusInterface::class);

        /** @var RedisGroupRepository $repo */
        $repo = self::getContainer()->get(RedisGroupRepository::class);

        $groups = $repo->findByUserId($this->user->id());
        self::assertCount(0, $groups);

        $bus->dispatch(new UpdateGroupProjectionCommand($this->user->id()));

        $groups = $repo->findByUserId($this->user->id());
        self::assertCount(1, $groups);
    }
}

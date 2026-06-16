<?php

namespace App\Tests\User\Integration;

use App\Tests\IntegrationTestCase;
use App\Tests\Story\User\UserStory;
use App\User\Application\Command\UpdateUserProjectionCommand;
use App\User\Domain\User;
use App\User\Infrastructure\Persistence\ElasticSearchUserRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateUserProjectionCommandHandlerTest extends IntegrationTestCase
{
    private User $user;

    protected function tearDown(): void
    {
        self::getContainer()
            ->get(ElasticSearchUserRepository::class)
            ->deleteUser($this->user);

        parent::tearDown();
    }

    public function test_update_projection_ok(): void
    {
        UserStory::load();

        $this->user = UserStory::get('user');

        /** @var MessageBusInterface $bus */
        $bus = self::getContainer()->get(MessageBusInterface::class);

        /** @var ElasticSearchUserRepository $repo */
        $repo = self::getContainer()->get(ElasticSearchUserRepository::class);

        $userCollection = $repo->searchUser(
            search: $this->user->email()->asString(),
            page: 1,
        );
        self::assertCount(0, $userCollection->items());
        self::assertSame(0, $userCollection->total());

        $bus->dispatch(new UpdateUserProjectionCommand($this->user->id()));

        $userCollection = $repo->searchUser(
            search: $this->user->email()->asString(),
            page: 1,
        );
        self::assertCount(1, $userCollection->items());
        self::assertSame(1, $userCollection->total());
    }
}

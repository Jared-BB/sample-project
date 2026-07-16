<?php

namespace App\Tests\User\Integration;

use App\Tests\IntegrationTestCase;
use App\Tests\Story\User\UserStory;
use App\User\Application\Command\SendWelcomeEmailToUserCommand;
use App\User\Domain\User;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class SendWelcomeEmailToUserCommandHandlerTest extends IntegrationTestCase
{
    use MailerAssertionsTrait;

    public function test_send_welcome_email_ok(): void
    {
        UserStory::load();

        /** @var User $user */
        $user = UserStory::get('user');

        /** @var MessageBusInterface $bus */
        $bus = self::getContainer()->get('commands.bus');

        $bus->dispatch(
            new SendWelcomeEmailToUserCommand(
                userId: $user->id(),
            ),
        );

        $email = $this->getMailerMessage();

        $this->assertEmailAddressContains($email, 'To', $user->email()->asString());
        $this->assertEmailTextBodyContains($email, 'Welcome');
    }
}

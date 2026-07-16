<?php

namespace App\User\Application\Command;

use App\Shared\Infrastructure\Mail\EmailSender;
use App\User\Domain\UserRepository;
use App\User\Infrastructure\Mail\WelcomeEmailBuilder;

final readonly class SendWelcomeEmailToUserCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private WelcomeEmailBuilder $welcomeEmailBuilder,
        private EmailSender $emailSender,
    ) {
    }

    public function __invoke(SendWelcomeEmailToUserCommand $command): void
    {
        $user = $this->userRepository->findByIdOrFail($command->userId);

        $template = $this->welcomeEmailBuilder->build($user);

        $this->emailSender->send($template);
    }
}

<?php

namespace App\User\Infrastructure\Mail;

use App\User\Domain\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

final readonly class WelcomeEmailBuilder
{
    public function __construct(
        private string $fromEmail,
        private string $fromName,
    ) {
    }

    public function build(User $user): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($user->email()->asString())
            ->subject('Welcome!')
            ->htmlTemplate('emails/welcome_user.html.twig')
            ->context([
                'user' => $user,
            ]);
    }
}

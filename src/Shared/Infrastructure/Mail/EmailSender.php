<?php

namespace App\Shared\Infrastructure\Mail;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;

final readonly class EmailSender
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function send(RawMessage $email): void
    {
        $this->mailer->send($email);
    }
}

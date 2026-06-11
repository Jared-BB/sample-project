<?php

declare(strict_types=1);

namespace App\Shared\Application;

use App\Shared\Domain\EventStore;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final readonly class EventDispatcherMiddleware implements MiddlewareInterface
{
    public function __construct(
        private MessageBusInterface $eventsBus,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $envelope = $stack->next()->handle($envelope, $stack);

        foreach (EventStore::events() as $event) {
            $this->eventsBus->dispatch($event);
        }

        EventStore::clear();

        return $envelope;
    }
}

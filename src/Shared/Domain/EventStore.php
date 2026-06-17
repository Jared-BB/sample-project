<?php

namespace App\Shared\Domain;

final class EventStore
{
    /**
     * @var EventInterface[]
     */
    private static array $events = [];

    public static function addEvent(EventInterface $event): void
    {
        self::$events[] = $event;
    }

    /**
     * @return EventInterface[]
     */
    public static function events(): array
    {
        return self::$events;
    }

    public static function clear(): void
    {
        self::$events = [];
    }

    public static function has(string $eventClass): bool
    {
        return array_any(self::$events, fn ($event) => $event instanceof $eventClass);
    }
}

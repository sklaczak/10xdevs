<?php

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\QueryBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class MessengerQueryBus implements QueryBus
{
    public function __construct(private MessageBusInterface $bus) {}

    public function ask(object $query): mixed
    {
        $envelope = $this->bus->dispatch($query);

        /** @var HandledStamp|null $stamp */
        $stamp = $envelope->last(HandledStamp::class);

        if (!$stamp) {
            throw new \RuntimeException('Brak HandledStamp dla query. Sprawdź routing na sync i handler.');
        }

        return $stamp->getResult();
    }
}

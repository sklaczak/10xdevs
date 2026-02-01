<?php


namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\CommandBus;
use App\Shared\Domain\Exception\DomainException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class MessengerCommandBus implements CommandBus
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function dispatch(object $command): mixed
    {
        try {
            $envelope = $this->bus->dispatch($command);
        } catch (HandlerFailedException $e) {
            // Messenger owija wyjątki z handlera - wyciągamy "prawdziwy" błąd domenowy
            foreach ($e->getWrappedExceptions() as $wrapped) {
                if ($wrapped instanceof DomainException) {
                    throw $wrapped;
                }
            }
            throw $e; // inne wyjątki puszczamy dalej
        }

        /** @var HandledStamp|null $stamp */
        $stamp = $envelope->last(HandledStamp::class);

        // Dla commandów typu void to OK (stamp może być, ale result null).
        return $stamp?->getResult();
    }
}

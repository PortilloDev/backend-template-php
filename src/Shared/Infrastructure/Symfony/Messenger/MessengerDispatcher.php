<?php

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Domain\Bus\AsyncMessageInterface;
use App\Shared\Domain\Bus\DispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class MessengerDispatcher implements DispatcherInterface
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    public function dispatch(object $object): mixed
    {
        $envelope = $this->messageBus->dispatch($object);

        if ($object instanceof AsyncMessageInterface) {
            return null;
        }

        /** @var HandledStamp $stamp */
        $stamp = $envelope->last(HandledStamp::class);

        return $stamp->getResult();
    }
}

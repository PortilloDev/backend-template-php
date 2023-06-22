<?php

namespace App\Shared\Infrastructure\Symfony\Messenger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Serializer\SerializerInterface;

class AuditMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly LoggerInterface $logger, private readonly SerializerInterface $serializer)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (null !== $envelope->last(ReceivedStamp::class)) {
            $data = $this->serializer->serialize($envelope->getMessage(), 'json');
            $class = $envelope->getMessage()::class;
            $this->logger->info(
                sprintf(
                    "\n\n-------- Received Message -----------\n\nClass: %s\n\n%s\n-------------------\n",
                    $class,
                    $data
                )
            );
        }

        return $stack->next()->handle($envelope, $stack);
    }
}

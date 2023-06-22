<?php

namespace App\Shared\Infrastructure\Logging;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LogResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => ['onKernelResponse', 270]];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ('application/json' !== $event->getResponse()->headers->get('Content-Type')) {
            return;
        }

        $this->logger->debug(
            sprintf(
                "\n\n-------- Returning HTTP response -----------\n\n%s\n-------------------\n",
                $event->getResponse()
            )
        );
    }
}

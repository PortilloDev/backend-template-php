<?php

namespace App\Shared\Infrastructure\Logging;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LogRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 270]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->logger->debug(
            sprintf(
                "\n\n-------- Received HTTP request -----------\n\n%s\n-------------------\n",
                $event->getRequest()
            )
        );
    }
}

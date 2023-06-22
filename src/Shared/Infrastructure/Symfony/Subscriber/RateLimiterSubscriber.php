<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimiterSubscriber implements EventSubscriberInterface
{
    private array $headers = [];

    public function __construct(
        private readonly RateLimiterFactory $authenticatedRequestLimiter,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $limiter = $this->authenticatedRequestLimiter->create($event->getRequest()->getClientIp());

        $limit = $limiter->consume();
        $this->headers = [
            'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
            'X-RateLimit-Retry-After' => $limit->getRetryAfter()->getTimestamp(),
            'X-RateLimit-Limit' => $limit->getLimit(),
        ];

        $limit->ensureAccepted();
    }

    public function onResponse(ResponseEvent $event): void
    {
        $event->getResponse()->headers->add($this->headers);
    }
}

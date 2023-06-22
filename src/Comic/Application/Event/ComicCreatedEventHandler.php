<?php

namespace App\Comic\Application\Event;

use App\Comic\Domain\Event\ComicCreatedEvent;
use App\Shared\Domain\Bus\HandlerInterface;
use Psr\Log\LoggerInterface;

class ComicCreatedEventHandler implements HandlerInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(ComicCreatedEvent $event): void
    {
        $this->logger->info(sprintf('Comic %s created', $event->comicId));
    }
}

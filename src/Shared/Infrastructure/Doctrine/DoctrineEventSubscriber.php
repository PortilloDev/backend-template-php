<?php

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\Bus\DispatcherInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

class DoctrineEventSubscriber implements EventSubscriberInterface
{
    protected array $events = [];

    public function __construct(private readonly DispatcherInterface $messageBus)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postFlush,
        ];
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $object = $args->getObject();

        if ($object instanceof AggregateRoot) {
            array_push($this->events, ...$object->pullEvents());
        }
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof AggregateRoot) {
            array_push($this->events, ...$object->pullEvents());
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        foreach ($this->events as $key => $event) {
            unset($this->events[$key]);
            $this->messageBus->dispatch($event);
        }
    }
}

<?php

namespace App\Comic\Domain\Model;

use App\Comic\Domain\Event\ComicCreatedEvent;
use App\Shared\Domain\AggregateRoot;

class Comic extends AggregateRoot
{
    /**
     * Comic constructor.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $publisher,
        public readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable()
    ) {
    }

    public static function create(string $id, string $title, string $publisher): self
    {
        $comic = new self($id, $title, $publisher);
        $comic->events[] = new ComicCreatedEvent($id);

        return $comic;
    }
}

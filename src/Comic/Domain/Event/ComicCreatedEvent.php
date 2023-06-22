<?php

namespace App\Comic\Domain\Event;

class ComicCreatedEvent
{
    public function __construct(public readonly string $comicId)
    {
    }
}

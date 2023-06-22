<?php

namespace App\Shared\Domain;

abstract class AggregateRoot
{
    protected array $events = [];

    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}

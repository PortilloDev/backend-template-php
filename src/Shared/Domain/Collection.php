<?php

declare(strict_types=1);

namespace App\Shared\Domain;

/**
 * @template T
 *
 * @template-implements \IteratorAggregate<int|string,T>
 */
abstract class Collection implements \Countable, \IteratorAggregate
{
    /**
     * @param array<int|string, mixed> $items
     */
    public function __construct(protected array $items)
    {
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items());
    }

    public function count(): int
    {
        return \count($this->items());
    }

    public function items(): array
    {
        return $this->items;
    }

    abstract public function type(): string;
}

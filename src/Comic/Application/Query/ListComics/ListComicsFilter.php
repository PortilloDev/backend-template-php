<?php

namespace App\Comic\Application\Query\ListComics;

class ListComicsFilter
{
    private array $filters = [];

    public function withPublisher(?string $publisher): self
    {
        $this->setFilterValue('publisher', $publisher);

        return $this;
    }

    public function publisherFilter(): ?string
    {
        return $this->getFilterValue('publisher');
    }

    /**
     * @return mixed
     */
    private function getFilterValue(string $name)
    {
        return $this->filters[$name] ?? null;
    }

    private function setFilterValue(string $name, mixed $value): void
    {
        $this->filters[$name] = is_string($value) ? trim($value) : $value;
    }
}

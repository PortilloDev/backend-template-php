<?php

namespace App\Shared\Domain\Pagination;

class Page
{
    private readonly int $number;

    private readonly int $results;

    public function __construct(int $number, int $results)
    {
        assert($number > 0);
        assert($results > 0);

        $this->number = $number;
        $this->results = $results;
    }

    public function pageNumber(): int
    {
        return $this->number;
    }

    public function limit(): int
    {
        return $this->results;
    }

    public function offset(): int
    {
        return $this->results * ($this->number - 1);
    }
}

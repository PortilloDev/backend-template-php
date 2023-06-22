<?php

namespace App\Comic\Application\Query\ListComics;

use App\Shared\Domain\Pagination\Page;

class ListComicsQuery
{
    public function __construct(private readonly Page $page, private readonly ?string $publisher)
    {
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }
}

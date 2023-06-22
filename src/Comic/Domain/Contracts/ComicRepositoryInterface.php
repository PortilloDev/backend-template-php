<?php

namespace App\Comic\Domain\Contracts;

use App\Comic\Application\Query\ListComics\ListComicsFilter;
use App\Comic\Domain\Model\Comic;
use App\Comic\Domain\Model\ComicCollection;
use App\Shared\Domain\Pagination\Page;

interface ComicRepositoryInterface
{
    public function save(Comic $comic): void;

    public function all(): ComicCollection;

    public function paginatedFilter(ListComicsFilter $filters, Page $page): ComicCollection;

    public function countByFilters(ListComicsFilter $filters): int;
}

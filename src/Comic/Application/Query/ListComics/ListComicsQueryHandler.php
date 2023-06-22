<?php

declare(strict_types=1);

namespace App\Comic\Application\Query\ListComics;

use App\Comic\Domain\Contracts\ComicRepositoryInterface;
use App\Shared\Domain\Bus\HandlerInterface;
use App\Shared\Domain\Pagination\PaginationResults;

final readonly class ListComicsQueryHandler implements HandlerInterface
{
    public function __construct(private ComicRepositoryInterface $comicRepository)
    {
    }

    public function __invoke(ListComicsQuery $query): PaginationResults
    {
        $filters = new ListComicsFilter();
        $filters->withPublisher($query->getPublisher());

        $collection = $this->comicRepository->paginatedFilter($filters, $query->getPage());
        $total = $this->comicRepository->countByFilters($filters);

        return new PaginationResults($query->getPage(), $total, $collection->items());
    }
}

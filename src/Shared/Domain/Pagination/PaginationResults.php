<?php

namespace App\Shared\Domain\Pagination;

class PaginationResults
{
    public int $pageNumber;

    public int $totalPages;

    public int $currentPageElements;

    public function __construct(
        Page $page,
        public int $totalElements,
        public array $results
    ) {
        $this->pageNumber = $page->pageNumber();
        $this->totalPages = 0 === $totalElements ? 1 : (int) (ceil($totalElements / $page->limit()));
        $this->currentPageElements = count($results);
    }
}

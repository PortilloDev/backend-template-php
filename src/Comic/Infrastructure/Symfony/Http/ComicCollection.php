<?php

namespace App\Comic\Infrastructure\Symfony\Http;

use App\Comic\Domain\Model\Comic;
use App\Shared\Domain\Pagination\PaginationResults;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'ComicCollection',
    properties: [
        new OA\Property(property: 'page', description: 'Current page', type: 'integer'),
        new OA\Property(property: 'total', description: 'Total elements', type: 'integer'),
        new OA\Property(property: 'pages', description: 'Total pages', type: 'integer'),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(ref: new Model(type: ComicSchema::class))
        ),
    ]
)]
class ComicCollection implements \JsonSerializable
{
    public function __construct(private readonly PaginationResults $results)
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'page' => $this->results->pageNumber,
            'total' => $this->results->totalElements,
            'pages' => $this->results->totalPages,
            'items' => array_map(static fn (Comic $comic) => new ComicSchema($comic), $this->results->results),
        ];
    }
}

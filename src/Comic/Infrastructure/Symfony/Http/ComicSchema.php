<?php

namespace App\Comic\Infrastructure\Symfony\Http;

use App\Comic\Domain\Model\Comic as DomainComic;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Comic',
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'published', type: 'string'),
    ]
)]
class ComicSchema implements \JsonSerializable
{
    public function __construct(private readonly DomainComic $comic)
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->comic->id,
            'title' => $this->comic->title,
            'publisher' => $this->comic->publisher,
        ];
    }
}

<?php

namespace App\Comic\Application\Command\CreateComic;

use App\Shared\Domain\Bus\AsyncMessageInterface;

readonly class CreateComicCommand implements AsyncMessageInterface
{
    public function __construct(private string $title, private string $publisher)
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }
}

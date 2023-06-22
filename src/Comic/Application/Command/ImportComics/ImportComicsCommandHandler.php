<?php

namespace App\Comic\Application\Command\ImportComics;

use App\Comic\Application\Command\CreateComic\CreateComicCommand;
use App\Comic\Domain\Contracts\ComicStoreInterface;
use App\Shared\Domain\Bus\DispatcherInterface;
use App\Shared\Domain\Bus\HandlerInterface;

final readonly class ImportComicsCommandHandler implements HandlerInterface
{
    public function __construct(
        private ComicStoreInterface $comicStore,
        private DispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(ImportComicsCommand $command): void
    {
        $response = $this->comicStore->all();

        foreach ($response['comics'] as $comic) {
            $this->dispatcher->dispatch(new CreateComicCommand($comic['title'], $comic['publisher']));
        }
    }
}

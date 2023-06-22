<?php

namespace App\Comic\Application\Command\CreateComic;

use App\Comic\Domain\Contracts\ComicRepositoryInterface;
use App\Comic\Domain\Model\Comic;
use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Shared\Domain\Bus\HandlerInterface;

final readonly class CreateComicCommandHandler implements HandlerInterface
{
    public function __construct(
        private UuidGeneratorInterface $uuidGenerator,
        private ComicRepositoryInterface $comicRepository,
    ) {
    }

    public function __invoke(CreateComicCommand $command): void
    {
        $uuid = $this->uuidGenerator->generate();
        $comic = Comic::create($uuid, $command->getTitle(), $command->getPublisher());

        $this->comicRepository->save($comic);
    }
}

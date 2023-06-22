<?php

namespace App\Tests\Unit\Comic\Application\Command;

use App\Comic\Application\Command\CreateComic\CreateComicCommand;
use App\Comic\Application\Command\CreateComic\CreateComicCommandHandler;
use App\Comic\Domain\Contracts\ComicRepositoryInterface;
use App\Shared\Application\Service\UuidGeneratorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class CreateComicCommandHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        $uuidGenerator = $this->createMock(UuidGeneratorInterface::class);
        $comicRepository = $this->createMock(ComicRepositoryInterface::class);

        $uuidGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn(Uuid::v7())
        ;

        $comicRepository
            ->expects($this->once())
            ->method('save');

        $handler = new CreateComicCommandHandler($uuidGenerator, $comicRepository);
        $handler->__invoke(new CreateComicCommand('test', 'test'));
    }
}

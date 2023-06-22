<?php

namespace App\Comic\Infrastructure\Rest;

use App\Comic\Domain\Contracts\ComicStoreInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[AsDecorator(decorates: MarvelComicStore::class)]
class CacheComicStore implements ComicStoreInterface
{
    /**
     * @var int
     */
    private const CACHE_TTL = 1800; // 30 min

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly MarvelComicStore $comicStore,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function all(): array
    {
        return $this->cache->get('comics-all', function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_TTL);
            $this->logger->info('Caching objects..');

            return $this->comicStore->all();
        });
    }
}

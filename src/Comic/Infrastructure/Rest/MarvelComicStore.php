<?php

namespace App\Comic\Infrastructure\Rest;

use App\Comic\Domain\Contracts\ComicStoreInterface;
use App\Comic\Infrastructure\Guzzle\ComicClientInterface;

class MarvelComicStore implements ComicStoreInterface
{
    /**
     * @var string
     */
    private const URL = 'https://api.shortboxed.com';

    public function __construct(private readonly ComicClientInterface $client)
    {
    }

    public function all(): array
    {
        $uri = self::URL.'/comics/v1/query';
        $response = $this->client->get($uri);

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }
}
